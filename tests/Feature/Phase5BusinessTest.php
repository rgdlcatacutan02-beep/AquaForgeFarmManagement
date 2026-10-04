<?php

namespace Tests\Feature;

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

class Phase5BusinessTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_create_and_manage_inventory_with_low_stock_detection(): void
    {
        $response = $this->actingAs($this->user)->post(route('inventory.store'), [
            'name' => 'High-Grade Spirulina Flakes 100g',
            'category' => 'FEED',
            'quantity' => 2,
            'unit' => 'tins',
            'minimum_quantity' => 5,
            'cost' => 12.50,
            'supplier' => 'AquaDiet Ltd',
            'notes' => 'Cabinet 1',
        ]);

        $response->assertRedirect(route('inventory.index'));
        $this->assertDatabaseHas('inventory_items', [
            'name' => 'High-Grade Spirulina Flakes 100g',
            'category' => 'FEED',
        ]);

        $item = InventoryItem::where('name', 'High-Grade Spirulina Flakes 100g')->first();
        $this->assertTrue($item->isLowStock());

        // Update quantity above threshold
        $updateResponse = $this->actingAs($this->user)->put(route('inventory.update', $item), [
            'name' => 'High-Grade Spirulina Flakes 100g',
            'category' => 'FEED',
            'quantity' => 10,
            'unit' => 'tins',
            'minimum_quantity' => 5,
            'cost' => 12.50,
            'supplier' => 'AquaDiet Ltd',
            'notes' => 'Cabinet 1 Restocked',
        ]);

        $updateResponse->assertRedirect(route('inventory.index'));
        $item->refresh();
        $this->assertFalse($item->isLowStock());
    }

    public function test_can_manage_customers_and_view_profile(): void
    {
        $response = $this->actingAs($this->user)->post(route('customers.store'), [
            'name' => 'David Tan Aquatics',
            'phone' => '+65 9876 5432',
            'email' => 'david@tan-aquatics.test',
            'address' => 'Blk 123 Ang Mo Kio Ave 4 #01-45',
            'notes' => 'Prefers show guppies',
        ]);

        $response->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('customers', [
            'name' => 'David Tan Aquatics',
            'email' => 'david@tan-aquatics.test',
        ]);

        $customer = Customer::where('email', 'david@tan-aquatics.test')->first();

        $showResponse = $this->actingAs($this->user)->get(route('customers.show', $customer));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('David Tan Aquatics');
    }

    public function test_creating_sale_updates_livestock_status_and_decrements_batch_fry(): void
    {
        $species = Species::create([
            'name' => 'Guppy',
            'scientific_name' => 'Poecilia reticulata',
            'description' => 'Fancy livebearer',
            'active' => true,
        ]);

        $tank = Tank::create([
            'tank_code' => 'T-TEST-01',
            'name' => 'Sales Holding Tank',
            'volume_liters' => 50,
            'purpose' => 'QUARANTINE',
            'status' => 'ACTIVE',
        ]);

        $livestock = Livestock::create([
            'species_id' => $species->id,
            'tank_id' => $tank->id,
            'livestock_code' => 'LIV-SALE-01',
            'sex' => 'MALE',
            'birth_date' => '2026-01-01',
            'quality_tier' => 'SHOW',
            'status' => 'AVAILABLE',
        ]);

        $batch = OffspringBatch::create([
            'species_id' => $species->id,
            'tank_id' => $tank->id,
            'batch_code' => 'BAT-SALE-01',
            'birth_date' => '2026-02-01',
            'initial_count' => 30,
            'current_count' => 30,
            'available_count' => 30,
            'status' => 'ACTIVE',
        ]);

        $customer = Customer::create([
            'name' => 'Alice Wong',
            'phone' => '8888 1234',
        ]);

        $saleResponse = $this->actingAs($this->user)->post(route('sales.store'), [
            'sale_number' => 'SALE-TEST-2026001',
            'customer_id' => $customer->id,
            'sale_date' => '2026-10-04',
            'status' => 'COMPLETED',
            'payment_status' => 'PAID',
            'subtotal' => 60.00,
            'discount' => 5.00,
            'total' => 55.00,
            'notes' => 'Pickup completed',
            'items' => [
                [
                    'item_description' => 'Show Male Guppy LIV-SALE-01',
                    'quantity' => 1,
                    'unit_price' => 35.00,
                    'livestock_id' => $livestock->id,
                ],
                [
                    'item_description' => 'Batch Fry Pack (5 pcs)',
                    'quantity' => 5,
                    'unit_price' => 5.00,
                    'offspring_batch_id' => $batch->id,
                ],
            ],
        ]);

        $saleResponse->assertRedirect(route('sales.index'));

        // 1. Verify sale and items recorded
        $this->assertDatabaseHas('sales', [
            'sale_number' => 'SALE-TEST-2026001',
            'total' => 55.00,
            'payment_status' => 'PAID',
        ]);

        // 2. Verify livestock marked as SOLD
        $livestock->refresh();
        $this->assertEquals('SOLD', $livestock->status);

        // 3. Verify offspring batch counts decremented
        $batch->refresh();
        $this->assertEquals(25, $batch->available_count);
        $this->assertEquals(25, $batch->current_count);

        // 4. View sale invoice
        $sale = Sale::where('sale_number', 'SALE-TEST-2026001')->first();
        $invoiceResponse = $this->actingAs($this->user)->get(route('sales.show', $sale));
        $invoiceResponse->assertStatus(200);
        $invoiceResponse->assertSee('SALE-TEST-2026001');
        $invoiceResponse->assertSee('Alice Wong');
    }

    public function test_expenses_and_farm_reports_aggregation(): void
    {
        Expense::create([
            'category' => 'FEED',
            'description' => 'Bulk Artemia Cysts',
            'amount' => 45.00,
            'expense_date' => '2026-10-01',
        ]);

        Expense::create([
            'category' => 'ELECTRICITY',
            'description' => 'Air Pump Power Consumption',
            'amount' => 30.00,
            'expense_date' => '2026-10-02',
        ]);

        Sale::create([
            'sale_number' => 'SALE-REP-01',
            'sale_date' => '2026-10-03',
            'status' => 'COMPLETED',
            'payment_status' => 'PAID',
            'subtotal' => 150.00,
            'discount' => 0.00,
            'total' => 150.00,
        ]);

        $reportResponse = $this->actingAs($this->user)->get(route('reports.index'));
        $reportResponse->assertStatus(200);

        // Financial Overview calculation: Revenue (150) - Expenses (75) = Net (75)
        $reportResponse->assertSee('150.00');
        $reportResponse->assertSee('75.00');
        $reportResponse->assertSee('Farm Financial Overview');
        $reportResponse->assertSee('Breeding & Survival Performance', false);
        $reportResponse->assertSee('Expense Breakdown by Category');
    }

    public function test_cancelling_and_deleting_sale_restores_inventory(): void
    {
        $species = Species::create([
            'name' => 'Platy',
            'active' => true,
        ]);

        $tank = Tank::create([
            'tank_code' => 'T-RESTORE-01',
            'name' => 'Holding',
            'volume_liters' => 40,
            'purpose' => 'GROWOUT',
            'status' => 'ACTIVE',
        ]);

        $livestock = Livestock::create([
            'species_id' => $species->id,
            'tank_id' => $tank->id,
            'livestock_code' => 'LIV-RESTORE-01',
            'sex' => 'FEMALE',
            'status' => 'AVAILABLE',
        ]);

        $batch = OffspringBatch::create([
            'species_id' => $species->id,
            'tank_id' => $tank->id,
            'batch_code' => 'BAT-RESTORE-01',
            'initial_count' => 20,
            'current_count' => 20,
            'available_count' => 20,
            'status' => 'ACTIVE',
        ]);

        $this->actingAs($this->user)->post(route('sales.store'), [
            'sale_number' => 'SALE-CANCEL-TEST',
            'sale_date' => '2026-10-04',
            'status' => 'COMPLETED',
            'payment_status' => 'PAID',
            'subtotal' => 100.00,
            'total' => 100.00,
            'items' => [
                [
                    'item_description' => 'Platy Female',
                    'quantity' => 1,
                    'unit_price' => 50.00,
                    'livestock_id' => $livestock->id,
                ],
                [
                    'item_description' => 'Platy Fry (10 pcs)',
                    'quantity' => 10,
                    'unit_price' => 5.00,
                    'offspring_batch_id' => $batch->id,
                ],
            ],
        ]);

        $livestock->refresh();
        $this->assertEquals('SOLD', $livestock->status);
        $batch->refresh();
        $this->assertEquals(10, $batch->available_count);

        $sale = Sale::where('sale_number', 'SALE-CANCEL-TEST')->first();

        // 1. Test edit view renders
        $editResponse = $this->actingAs($this->user)->get(route('sales.edit', $sale));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('SALE-CANCEL-TEST');

        // 2. Test status changed to CANCELLED releases stock
        $this->actingAs($this->user)->put(route('sales.update', $sale), [
            'sale_date' => '2026-10-04',
            'status' => 'CANCELLED',
            'payment_status' => 'UNPAID',
            'notes' => 'Customer cancelled',
        ]);

        $livestock->refresh();
        $this->assertEquals('AVAILABLE', $livestock->status);
        $batch->refresh();
        $this->assertEquals(20, $batch->available_count);

        // 3. Test deleting sale also cleans up
        $this->actingAs($this->user)->delete(route('sales.destroy', $sale));
        $this->assertSoftDeleted('sales', ['id' => $sale->id]);
    }

    public function test_cannot_delete_tank_with_assigned_livestock(): void
    {
        $species = Species::create(['name' => 'Molly', 'active' => true]);
        $tank = Tank::create([
            'tank_code' => 'T-GUARD-01',
            'name' => 'Guarded Tank',
            'volume_liters' => 60,
            'purpose' => 'BREEDING',
            'status' => 'ACTIVE',
        ]);

        Livestock::create([
            'species_id' => $species->id,
            'tank_id' => $tank->id,
            'livestock_code' => 'LIV-GUARD-01',
            'sex' => 'MALE',
            'status' => 'AVAILABLE',
        ]);

        $response = $this->actingAs($this->user)->delete(route('tanks.destroy', $tank));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('tanks', ['id' => $tank->id]);
    }

    public function test_cannot_delete_batch_with_recorded_sale_items(): void
    {
        $species = Species::create(['name' => 'Swordtail', 'active' => true]);
        $tank = Tank::create([
            'tank_code' => 'T-BATCH-01',
            'name' => 'Batch Tank',
            'volume_liters' => 50,
            'purpose' => 'GROWOUT',
            'status' => 'ACTIVE',
        ]);

        $batch = OffspringBatch::create([
            'species_id' => $species->id,
            'tank_id' => $tank->id,
            'batch_code' => 'BAT-LOCK-01',
            'initial_count' => 10,
            'current_count' => 10,
            'available_count' => 10,
            'status' => 'ACTIVE',
        ]);

        $sale = Sale::create([
            'sale_number' => 'SALE-BATCH-LOCK',
            'sale_date' => '2026-10-04',
            'status' => 'COMPLETED',
            'payment_status' => 'PAID',
            'subtotal' => 50.00,
            'total' => 50.00,
        ]);

        $sale->items()->create([
            'item_description' => 'Swordtail fry',
            'quantity' => 2,
            'unit_price' => 25.00,
            'subtotal' => 50.00,
            'offspring_batch_id' => $batch->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('batches.destroy', $batch));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('offspring_batches', ['id' => $batch->id]);
    }
}
