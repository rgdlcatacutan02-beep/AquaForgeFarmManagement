<?php

namespace Tests\Feature;

use App\Models\BreedingEvent;
use App\Models\Livestock;
use App\Models\OffspringBatch;
use App\Models\Species;
use App\Models\Tank;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase3BreedingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Species $species;
    protected Tank $tank;
    protected Livestock $male;
    protected Livestock $female;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->species = Species::create(['name' => 'Guppy', 'active' => true]);
        $this->tank = Tank::create([
            'tank_code' => 'T003',
            'name' => 'Breeding Cube',
            'tank_type' => 'GLASS',
            'volume_liters' => 45,
            'purpose' => 'BREEDING',
            'status' => 'ACTIVE',
        ]);

        $this->male = Livestock::create([
            'livestock_code' => 'G001',
            'species_id' => $this->species->id,
            'variety' => 'Albino Blue Topaz',
            'sex' => 'MALE',
            'status' => 'BREEDER',
            'tank_id' => $this->tank->id,
        ]);

        $this->female = Livestock::create([
            'livestock_code' => 'G002',
            'species_id' => $this->species->id,
            'variety' => 'Albino Blue Topaz',
            'sex' => 'FEMALE',
            'status' => 'BREEDER',
            'tank_id' => $this->tank->id,
        ]);
    }

    public function test_can_create_and_manage_breeding_event(): void
    {
        // 1. Create Breeding Event
        $response = $this->actingAs($this->user)->post(route('breeding.store'), [
            'breeding_code' => 'BR-010',
            'species_id' => $this->species->id,
            'tank_id' => $this->tank->id,
            'male_livestock_id' => $this->male->id,
            'female_livestock_id' => $this->female->id,
            'start_date' => now()->toDateString(),
            'expected_date' => now()->addDays(28)->toDateString(),
            'status' => 'ACTIVE',
            'notes' => 'Selected for high dorsal and clean tail pattern.',
        ]);

        $event = BreedingEvent::where('breeding_code', 'BR-010')->first();
        $this->assertNotNull($event);
        $response->assertRedirect(route('breeding.show', $event));

        $this->assertDatabaseHas('breeding_events', [
            'breeding_code' => 'BR-010',
            'male_livestock_id' => $this->male->id,
            'female_livestock_id' => $this->female->id,
            'status' => 'ACTIVE',
        ]);

        // 2. View Breeding Detail
        $viewRes = $this->actingAs($this->user)->get(route('breeding.show', $event));
        $viewRes->assertStatus(200);
        $viewRes->assertSee('BR-010');
        $viewRes->assertSee('G001');
        $viewRes->assertSee('G002');
        $viewRes->assertSee('T003');

        // 3. Update Breeding Status & Actual Hatch Date
        $updateRes = $this->actingAs($this->user)->put(route('breeding.update', $event), [
            'breeding_code' => 'BR-010',
            'species_id' => $this->species->id,
            'tank_id' => $this->tank->id,
            'male_livestock_id' => $this->male->id,
            'female_livestock_id' => $this->female->id,
            'start_date' => $event->start_date->toDateString(),
            'actual_birth_or_hatch_date' => now()->toDateString(),
            'status' => 'COMPLETED',
        ]);

        $updateRes->assertRedirect(route('breeding.show', $event));
        $event->refresh();
        $this->assertEquals('COMPLETED', $event->status);
        $this->assertNotNull($event->actual_birth_or_hatch_date);
    }

    public function test_can_create_offspring_batch_and_track_population(): void
    {
        $event = BreedingEvent::create([
            'breeding_code' => 'BR-020',
            'species_id' => $this->species->id,
            'tank_id' => $this->tank->id,
            'male_livestock_id' => $this->male->id,
            'female_livestock_id' => $this->female->id,
            'start_date' => now()->subDays(30)->toDateString(),
            'status' => 'COMPLETED',
        ]);

        // 1. Create Offspring Batch
        $response = $this->actingAs($this->user)->post(route('batches.store'), [
            'batch_code' => 'GUP-101',
            'species_id' => $this->species->id,
            'breeding_event_id' => $event->id,
            'variety' => 'Albino Blue Topaz F1',
            'birth_or_hatch_date' => now()->toDateString(),
            'initial_count' => 35,
            'tank_id' => $this->tank->id,
            'status' => 'GROWING',
            'grade' => 'BREEDER',
            'notes' => 'Fed microworms and freshly hatched baby brine shrimp.',
        ]);

        $batch = OffspringBatch::where('batch_code', 'GUP-101')->first();
        $this->assertNotNull($batch);
        $response->assertRedirect(route('batches.show', $batch));

        // Initial headcount checks
        $this->assertEquals(35, $batch->initial_count);
        $this->assertEquals(35, $batch->current_count);
        $this->assertEquals(0, $batch->death_count);
        $this->assertEquals(0, $batch->cull_count);
        $this->assertEquals(35, $batch->available_count);

        // 2. View Batch Hub
        $hubRes = $this->actingAs($this->user)->get(route('batches.show', $batch));
        $hubRes->assertStatus(200);
        $hubRes->assertSee('GUP-101');
        $hubRes->assertSee('Albino Blue Topaz F1');
        $hubRes->assertSee('BR-020');
        $hubRes->assertSee('35');

        // 3. Record Mortality (-3)
        $mortRes = $this->actingAs($this->user)->post(route('batches.record-log', $batch), [
            'type' => 'MORTALITY',
            'quantity' => 3,
            'log_date' => now()->toDateString(),
            'reason' => 'Early fry attrition',
            'notes' => 'Found 3 dead fry during morning feed',
        ]);

        $mortRes->assertRedirect(route('batches.show', $batch));
        $batch->refresh();
        $this->assertEquals(32, $batch->current_count);
        $this->assertEquals(3, $batch->death_count);
        $this->assertEquals(32, $batch->available_count);

        $this->assertDatabaseHas('batch_logs', [
            'offspring_batch_id' => $batch->id,
            'type' => 'MORTALITY',
            'quantity' => 3,
            'reason' => 'Early fry attrition',
        ]);

        // 4. Record Culling (-4)
        $cullRes = $this->actingAs($this->user)->post(route('batches.record-log', $batch), [
            'type' => 'CULLING',
            'quantity' => 4,
            'log_date' => now()->toDateString(),
            'reason' => 'Bent spine defect',
            'notes' => 'Separated to cull feeder tub',
        ]);

        $cullRes->assertRedirect(route('batches.show', $batch));
        $batch->refresh();
        $this->assertEquals(28, $batch->current_count);
        $this->assertEquals(4, $batch->cull_count);
        $this->assertEquals(28, $batch->available_count);

        $this->assertDatabaseHas('batch_logs', [
            'offspring_batch_id' => $batch->id,
            'type' => 'CULLING',
            'quantity' => 4,
            'reason' => 'Bent spine defect',
        ]);
    }

    public function test_cannot_record_losses_exceeding_current_population(): void
    {
        $batch = OffspringBatch::create([
            'batch_code' => 'GUP-EXCEED',
            'species_id' => $this->species->id,
            'initial_count' => 10,
            'current_count' => 10,
            'death_count' => 0,
            'cull_count' => 0,
            'available_count' => 10,
            'status' => 'GROWING',
        ]);

        // Attempting to record 15 casualties when only 10 exist
        $response = $this->actingAs($this->user)->post(route('batches.record-log', $batch), [
            'type' => 'MORTALITY',
            'quantity' => 15,
            'log_date' => now()->toDateString(),
            'reason' => 'Major wipeout attempt',
        ]);

        $response->assertSessionHas('error');
        $batch->refresh();
        // Assert count was NOT decremented to negative
        $this->assertEquals(10, $batch->current_count);
        $this->assertEquals(0, $batch->death_count);
    }

    public function test_cannot_delete_breeding_event_with_linked_batches(): void
    {
        $event = BreedingEvent::create([
            'breeding_code' => 'BR-LINKED',
            'species_id' => $this->species->id,
            'start_date' => now()->toDateString(),
            'status' => 'COMPLETED',
        ]);

        OffspringBatch::create([
            'batch_code' => 'BAT-LINKED',
            'species_id' => $this->species->id,
            'breeding_event_id' => $event->id,
            'initial_count' => 20,
            'current_count' => 20,
            'death_count' => 0,
            'cull_count' => 0,
            'available_count' => 20,
            'status' => 'GROWING',
        ]);

        $response = $this->actingAs($this->user)->delete(route('breeding.destroy', $event));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('breeding_events', ['id' => $event->id]);
    }
}
