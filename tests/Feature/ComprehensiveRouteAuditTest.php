<?php

namespace Tests\Feature;

use App\Models\BreedingEvent;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\Livestock;
use App\Models\OffspringBatch;
use App\Models\Sale;
use App\Models\Species;
use App\Models\Tank;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprehensiveRouteAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected Species $species;
    protected Tank $tank;
    protected Livestock $livestock;
    protected BreedingEvent $breeding;
    protected OffspringBatch $batch;
    protected Customer $customerModel;
    protected Sale $sale;
    protected Expense $expense;
    protected InventoryItem $inventory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'farm_name' => 'RAM Aquatics',
        ]);

        $this->customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->species = Species::create([
            'name' => 'Betta Splendens',
            'scientific_name' => 'Betta splendens',
            'optimal_ph_min' => 6.5,
            'optimal_ph_max' => 7.5,
            'optimal_temp_min' => 24.0,
            'optimal_temp_max' => 28.0,
        ]);

        $this->tank = Tank::create([
            'name' => 'Breeding Tank A1',
            'tank_code' => 'TNK-TEST-01',
            'type' => 'breeding',
            'capacity_liters' => 50,
            'status' => 'active',
        ]);

        $this->livestock = Livestock::create([
            'species_id' => $this->species->id,
            'tank_id' => $this->tank->id,
            'livestock_code' => 'BTA-001',
            'variety' => 'Super Red Halfmoon',
            'sex' => 'MALE',
            'status' => 'available',
            'is_featured' => true,
            'purchase_price' => 1500.00,
        ]);

        $this->breeding = BreedingEvent::create([
            'breeding_code' => 'PAIR-001',
            'species_id' => $this->species->id,
            'tank_id' => $this->tank->id,
            'male_livestock_id' => $this->livestock->id,
            'start_date' => now()->toDateString(),
            'status' => 'paired',
        ]);

        $this->batch = OffspringBatch::create([
            'breeding_event_id' => $this->breeding->id,
            'species_id' => $this->species->id,
            'tank_id' => $this->tank->id,
            'batch_code' => 'BAT-001',
            'hatch_date' => now()->toDateString(),
            'initial_count' => 100,
            'current_count' => 95,
            'status' => 'fry',
        ]);

        $this->customerModel = Customer::create([
            'name' => 'Juan Dela Cruz',
            'contact_number' => '09123456789',
            'messenger_handle' => 'juandelacruz',
        ]);

        $this->sale = Sale::create([
            'sale_number' => 'INV-TEST-001',
            'customer_id' => $this->customerModel->id,
            'total_amount' => 1500.00,
            'subtotal' => 1500.00,
            'status' => 'COMPLETED',
            'payment_status' => 'paid',
            'sale_date' => now()->toDateString(),
        ]);

        $this->expense = Expense::create([
            'category' => 'feed',
            'description' => 'Artemia Cysts',
            'amount' => 450.00,
            'expense_date' => now()->toDateString(),
        ]);

        $this->inventory = InventoryItem::create([
            'name' => 'Aquarium Salt 1kg',
            'category' => 'medication',
            'quantity' => 10,
            'unit' => 'packs',
            'min_reorder_level' => 3,
        ]);
    }

    public function test_all_public_guest_routes_render_ok(): void
    {
        $publicRoutes = [
            '/',
            '/catalog',
            '/catalog/' . $this->livestock->id,
            '/login',
            '/forgot-password',
        ];

        foreach ($publicRoutes as $uri) {
            $response = $this->get($uri);
            $response->assertStatus(200);
        }
    }

    public function test_all_admin_get_routes_render_successfully(): void
    {
        $adminRoutes = [
            '/dashboard' => 200,
            '/available-fish' => 200,
            '/available-fish/create' => 200,
            '/species' => 200,
            '/species/create' => 200,
            '/species/' . $this->species->id => 200,
            '/species/' . $this->species->id . '/edit' => 200,
            '/tanks' => 200,
            '/tanks/create' => 200,
            '/tanks/scan' => 200,
            '/tanks/' . $this->tank->id => 200,
            '/tanks/' . $this->tank->id . '/edit' => 200,
            '/tanks/' . $this->tank->id . '/print-label' => 200,
            '/livestock' => 200,
            '/livestock/create' => 200,
            '/livestock/' . $this->livestock->id => 200,
            '/livestock/' . $this->livestock->id . '/edit' => 200,
            '/breeding' => 200,
            '/breeding/create' => 200,
            '/breeding/' . $this->breeding->id => 200,
            '/breeding/' . $this->breeding->id . '/edit' => 200,
            '/batches' => 200,
            '/batches/create' => 200,
            '/batches/' . $this->batch->id => 200,
            '/batches/' . $this->batch->id . '/edit' => 200,
            '/water-logs' => 200,
            '/water-logs/create' => 200,
            '/feeding' => 200,
            '/feeding/create' => 200,
            '/maintenance' => 200,
            '/maintenance/create' => 200,
            '/inventory' => 200,
            '/inventory/create' => 200,
            '/inventory/' . $this->inventory->id . '/edit' => 200,
            '/customers' => 200,
            '/customers/create' => 200,
            '/customers/' . $this->customerModel->id => 200,
            '/customers/' . $this->customerModel->id . '/edit' => 200,
            '/sales' => 200,
            '/sales/create' => 200,
            '/sales/' . $this->sale->id => 200,
            '/sales/' . $this->sale->id . '/edit' => 200,
            '/expenses' => 200,
            '/expenses/create' => 200,
            '/expenses/' . $this->expense->id . '/edit' => 200,
            '/reports' => 200,
            '/profile' => 200,
        ];

        foreach ($adminRoutes as $uri => $expectedStatus) {
            $response = $this->actingAs($this->admin)->get($uri);
            $this->assertEquals(
                $expectedStatus,
                $response->status(),
                "Route [{$uri}] failed with status {$response->status()} instead of {$expectedStatus}"
            );
        }
    }
}
