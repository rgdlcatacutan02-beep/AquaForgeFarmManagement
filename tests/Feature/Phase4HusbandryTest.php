<?php

namespace Tests\Feature;

use App\Models\FeedingLog;
use App\Models\MaintenanceLog;
use App\Models\Tank;
use App\Models\User;
use App\Models\WaterLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase4HusbandryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Tank $tank;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->tank = Tank::create([
            'tank_code' => 'T001',
            'name' => 'Main Display Tank',
            'tank_type' => 'GLASS',
            'volume_liters' => 120,
            'purpose' => 'DISPLAY',
            'status' => 'ACTIVE',
        ]);
    }

    public function test_can_record_water_test_and_computes_good_status(): void
    {
        $response = $this->actingAs($this->user)->post(route('water-logs.store'), [
            'tank_id' => $this->tank->id,
            'recorded_at' => now()->format('Y-m-d H:i:s'),
            'temperature' => 26.2,
            'ph' => 7.2,
            'ammonia' => 0.0,
            'nitrite' => 0.0,
            'nitrate' => 15,
            'tds' => 180,
            'notes' => 'Perfect parameters after weekly water change.',
        ]);

        $response->assertRedirect(route('tanks.show', $this->tank));

        $this->assertDatabaseHas('water_logs', [
            'tank_id' => $this->tank->id,
            'status' => 'GOOD',
            'notes' => 'Perfect parameters after weekly water change.',
        ]);

        $log = WaterLog::where('tank_id', $this->tank->id)->first();
        $this->assertEquals('GOOD', $log->status);
    }

    public function test_computes_warning_and_check_statuses(): void
    {
        // 1. Warning status (elevated ammonia or nitrate)
        $this->actingAs($this->user)->post(route('water-logs.store'), [
            'tank_id' => $this->tank->id,
            'recorded_at' => now()->subDay()->format('Y-m-d H:i:s'),
            'temperature' => 26.5,
            'ph' => 7.4,
            'ammonia' => 0.30,
            'nitrite' => 0.10,
            'nitrate' => 30,
        ]);

        $warningLog = WaterLog::where('tank_id', $this->tank->id)->latest('id')->first();
        $this->assertEquals('WARNING', $warningLog->status);

        // 2. Check status (critical ammonia > 0.5 or nitrate > 80)
        $this->actingAs($this->user)->post(route('water-logs.store'), [
            'tank_id' => $this->tank->id,
            'recorded_at' => now()->format('Y-m-d H:i:s'),
            'temperature' => 27.0,
            'ph' => 7.8,
            'ammonia' => 0.75,
            'nitrite' => 0.60,
            'nitrate' => 90,
        ]);

        $checkLog = WaterLog::where('tank_id', $this->tank->id)->latest('id')->first();
        $this->assertEquals('CHECK', $checkLog->status);
    }

    public function test_can_record_feeding_event(): void
    {
        $response = $this->actingAs($this->user)->post(route('feeding.store'), [
            'tank_id' => $this->tank->id,
            'food' => 'Live Baby Brine Shrimp (BBS)',
            'quantity' => '2 pipettes',
            'fed_at' => now()->format('Y-m-d H:i:s'),
            'notes' => 'Fed eagerly by all juveniles.',
        ]);

        $response->assertRedirect(route('tanks.show', $this->tank));

        $this->assertDatabaseHas('feeding_logs', [
            'tank_id' => $this->tank->id,
            'food' => 'Live Baby Brine Shrimp (BBS)',
            'quantity' => '2 pipettes',
        ]);
    }

    public function test_can_record_water_change_and_filter_maintenance(): void
    {
        // 1. Water Change
        $response = $this->actingAs($this->user)->post(route('maintenance.store'), [
            'tank_id' => $this->tank->id,
            'maintenance_type' => 'WATER_CHANGE',
            'water_change_percentage' => 30,
            'performed_at' => now()->format('Y-m-d H:i:s'),
            'notes' => '30% water change with treated tap water.',
        ]);

        $response->assertRedirect(route('tanks.show', $this->tank));

        $this->assertDatabaseHas('maintenance_logs', [
            'tank_id' => $this->tank->id,
            'maintenance_type' => 'WATER_CHANGE',
            'water_change_percentage' => 30,
        ]);

        // 2. Filter Cleaning
        $filterRes = $this->actingAs($this->user)->post(route('maintenance.store'), [
            'tank_id' => $this->tank->id,
            'maintenance_type' => 'FILTER_CLEANING',
            'performed_at' => now()->format('Y-m-d H:i:s'),
            'notes' => 'Squeezed sponge filter in aquarium water.',
        ]);

        $filterRes->assertRedirect(route('tanks.show', $this->tank));

        $this->assertDatabaseHas('maintenance_logs', [
            'tank_id' => $this->tank->id,
            'maintenance_type' => 'FILTER_CLEANING',
        ]);
    }

    public function test_tank_hub_displays_husbandry_history(): void
    {
        // Add water log
        WaterLog::create([
            'tank_id' => $this->tank->id,
            'recorded_at' => now(),
            'temperature' => 26.5,
            'ph' => 7.3,
            'ammonia' => 0.0,
            'nitrite' => 0.0,
            'nitrate' => 20,
            'status' => 'GOOD',
        ]);

        // Add water change
        MaintenanceLog::create([
            'tank_id' => $this->tank->id,
            'maintenance_type' => 'WATER_CHANGE',
            'water_change_percentage' => 40,
            'performed_at' => now(),
            'notes' => '40% water refresh',
        ]);

        // Add feeding
        FeedingLog::create([
            'tank_id' => $this->tank->id,
            'food' => 'Spirulina Flakes',
            'quantity' => '1 pinch',
            'fed_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get(route('tanks.show', $this->tank));
        $response->assertStatus(200);
        $response->assertSee('26.5');
        $response->assertSee('7.3');
        $response->assertSee('GOOD');
        $response->assertSee('WATER_CHANGE');
        $response->assertSee('40%');
        $response->assertSee('Spirulina Flakes');
    }
}
