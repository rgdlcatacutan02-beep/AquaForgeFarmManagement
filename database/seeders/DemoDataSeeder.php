<?php

namespace Database\Seeders;

use App\Models\BatchLog;
use App\Models\BreedingEvent;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\FeedingLog;
use App\Models\InventoryItem;
use App\Models\Livestock;
use App\Models\MaintenanceLog;
use App\Models\OffspringBatch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Species;
use App\Models\Tank;
use App\Models\Task;
use App\Models\User;
use App\Models\WaterLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        $user = User::firstOrCreate(
            ['email' => 'admin@aquaforge.test'],
            [
                'name' => 'AquaForge Admin',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Species
        $guppy = Species::firstOrCreate(['name' => 'Guppy'], [
            'scientific_name' => 'Poecilia reticulata',
            'description' => 'Livebearing freshwater aquarium fish popular for fancy breeding lines.',
            'active' => true,
        ]);

        $molly = Species::firstOrCreate(['name' => 'Molly'], [
            'scientific_name' => 'Poecilia sphenops',
            'description' => 'Hardy, peaceful livebearers with varied fin and color morphs.',
            'active' => true,
        ]);

        $flowerhorn = Species::firstOrCreate(['name' => 'Flowerhorn'], [
            'scientific_name' => 'Cichlasoma hybrid',
            'description' => 'Ornamental cichlid hybrid prized for distinct nuchal hump and vibrant pearls.',
            'active' => true,
        ]);

        $crayfish = Species::firstOrCreate(['name' => 'Crayfish'], [
            'scientific_name' => 'Procambarus clarkii',
            'description' => 'Freshwater decapod crustacean kept for color morphs (Ghost, White, Blue).',
            'active' => true,
        ]);

        // 3. Tanks (at least T001 to T005)
        $t1 = Tank::firstOrCreate(['tank_code' => 'T001'], [
            'name' => 'Breeder Rack A1',
            'tank_type' => 'GLASS',
            'length' => 60,
            'width' => 30,
            'height' => 36,
            'volume_liters' => 65.0,
            'location' => 'Fishroom Rack A - Tier 1',
            'purpose' => 'BREEDING',
            'status' => 'ACTIVE',
            'notes' => '[DEMO] High-flow sponge filter, set up for Blue Topaz guppy pair.',
        ]);

        $t2 = Tank::firstOrCreate(['tank_code' => 'T002'], [
            'name' => 'Breeder Rack A2',
            'tank_type' => 'GLASS',
            'length' => 60,
            'width' => 30,
            'height' => 36,
            'volume_liters' => 65.0,
            'location' => 'Fishroom Rack A - Tier 1',
            'purpose' => 'BREEDING',
            'status' => 'ACTIVE',
            'notes' => '[DEMO] Black Molly breeding colony tank.',
        ]);

        $t3 = Tank::firstOrCreate(['tank_code' => 'T003'], [
            'name' => 'Monster Tank 1',
            'tank_type' => 'GLASS',
            'length' => 120,
            'width' => 50,
            'height' => 50,
            'volume_liters' => 300.0,
            'location' => 'Showroom Display Wall',
            'purpose' => 'DISPLAY',
            'status' => 'ACTIVE',
            'notes' => '[DEMO] Solitary setup for show-grade Kamfa Flowerhorn.',
        ]);

        $t4 = Tank::firstOrCreate(['tank_code' => 'T004'], [
            'name' => 'Crayfish Habitats',
            'tank_type' => 'ACRYLIC',
            'length' => 90,
            'width' => 45,
            'height' => 30,
            'volume_liters' => 120.0,
            'location' => 'Lower Bench B',
            'purpose' => 'GROWOUT',
            'status' => 'ACTIVE',
            'notes' => '[DEMO] Segmented benthic habitat for Ghost Crayfish juveniles.',
        ]);

        $t5 = Tank::firstOrCreate(['tank_code' => 'T005'], [
            'name' => 'Fry Growout Tub 1',
            'tank_type' => 'TUB',
            'length' => 100,
            'width' => 60,
            'height' => 40,
            'volume_liters' => 240.0,
            'location' => 'Greenhouse Section 1',
            'purpose' => 'GROWOUT',
            'status' => 'ACTIVE',
            'notes' => '[DEMO] High aeration tub for juvenile guppy batch growout.',
        ]);

        // 4. Livestock (examples of guppy, molly, flowerhorn, crayfish)
        $gMale = Livestock::firstOrCreate(['livestock_code' => 'G001'], [
            'species_id' => $guppy->id,
            'variety' => 'Albino Blue Topaz',
            'sex' => 'MALE',
            'date_of_birth' => '2026-03-15',
            'date_acquired' => '2026-05-10',
            'source' => 'Elite Guppy Breeder Thailand',
            'purchase_price' => 45.00,
            'status' => 'BREEDER',
            'tank_id' => $t1->id,
            'grade' => 'SHOW',
            'grading_scores' => ['body' => 5, 'color' => 5, 'tail' => 4, 'dorsal' => 5, 'pattern' => 4, 'overall' => 5],
            'notes' => '[DEMO] Exceptional delta tail spread and deep sky-blue sheen.',
        ]);

        $gFemale = Livestock::firstOrCreate(['livestock_code' => 'G002'], [
            'species_id' => $guppy->id,
            'variety' => 'Albino Blue Topaz',
            'sex' => 'FEMALE',
            'date_of_birth' => '2026-03-20',
            'date_acquired' => '2026-05-10',
            'source' => 'Elite Guppy Breeder Thailand',
            'purchase_price' => 35.00,
            'status' => 'BREEDER',
            'tank_id' => $t1->id,
            'grade' => 'BREEDER',
            'grading_scores' => ['body' => 4, 'color' => 4, 'tail' => 4, 'dorsal' => 4, 'pattern' => 4, 'overall' => 4],
            'notes' => '[DEMO] High-fecundity breeder female.',
        ]);

        $mMale = Livestock::firstOrCreate(['livestock_code' => 'M001'], [
            'species_id' => $molly->id,
            'variety' => 'Black Lyretail Molly',
            'sex' => 'MALE',
            'date_of_birth' => '2026-02-10',
            'date_acquired' => '2026-04-12',
            'source' => 'Local Aquatic Hub',
            'purchase_price' => 12.00,
            'status' => 'BREEDER',
            'tank_id' => $t2->id,
            'grade' => 'BREEDER',
            'grading_scores' => ['body' => 4, 'color' => 5, 'tail' => 4, 'dorsal' => 4, 'pattern' => 4, 'overall' => 4],
            'notes' => '[DEMO] Velvet black, elongated tail filaments.',
        ]);

        $mFemale = Livestock::firstOrCreate(['livestock_code' => 'M002'], [
            'species_id' => $molly->id,
            'variety' => 'Black Lyretail Molly',
            'sex' => 'FEMALE',
            'date_of_birth' => '2026-02-15',
            'date_acquired' => '2026-04-12',
            'source' => 'Local Aquatic Hub',
            'purchase_price' => 10.00,
            'status' => 'BREEDER',
            'tank_id' => $t2->id,
            'grade' => 'BREEDER',
            'grading_scores' => ['body' => 4, 'color' => 4, 'tail' => 4, 'dorsal' => 4, 'pattern' => 4, 'overall' => 4],
            'notes' => '[DEMO] Robust pregnant female.',
        ]);

        $fh = Livestock::firstOrCreate(['livestock_code' => 'FH001'], [
            'species_id' => $flowerhorn->id,
            'variety' => 'Kamfa Red Dragon',
            'sex' => 'MALE',
            'date_of_birth' => '2025-11-01',
            'date_acquired' => '2026-01-20',
            'source' => 'Chao Phraya Aqua Farm',
            'purchase_price' => 220.00,
            'status' => 'DISPLAY',
            'tank_id' => $t3->id,
            'grade' => 'SHOW',
            'grading_scores' => ['body' => 5, 'color' => 5, 'tail' => 5, 'dorsal' => 5, 'pattern' => 5, 'overall' => 5],
            'notes' => '[DEMO] Prominent water kok, white eyes, intense pearl coverage.',
        ]);

        $cray = Livestock::firstOrCreate(['livestock_code' => 'CR001'], [
            'species_id' => $crayfish->id,
            'variety' => 'Ghost Tricolor',
            'sex' => 'MALE',
            'date_of_birth' => '2026-04-01',
            'date_acquired' => '2026-06-15',
            'source' => 'Crustacean World',
            'purchase_price' => 25.00,
            'status' => 'AVAILABLE',
            'tank_id' => $t4->id,
            'grade' => 'MATERIAL',
            'grading_scores' => ['body' => 4, 'color' => 4, 'tail' => 4, 'dorsal' => 3, 'pattern' => 4, 'overall' => 4],
            'notes' => '[DEMO] Healthy chelae with distinct red-white-blue patterning.',
        ]);

        // 5. Breeding Events
        $breeding1 = BreedingEvent::firstOrCreate(['breeding_code' => 'BR-001'], [
            'species_id' => $guppy->id,
            'tank_id' => $t1->id,
            'male_livestock_id' => $gMale->id,
            'female_livestock_id' => $gFemale->id,
            'start_date' => '2026-08-01',
            'expected_date' => '2026-08-28',
            'actual_birth_or_hatch_date' => '2026-08-27',
            'status' => 'COMPLETED',
            'notes' => '[DEMO] Successful dropped brood of 38 fry.',
        ]);

        $breeding2 = BreedingEvent::firstOrCreate(['breeding_code' => 'BR-002'], [
            'species_id' => $molly->id,
            'tank_id' => $t2->id,
            'male_livestock_id' => $mMale->id,
            'female_livestock_id' => $mFemale->id,
            'start_date' => '2026-09-10',
            'expected_date' => '2026-10-15',
            'status' => 'ACTIVE',
            'notes' => '[DEMO] Active pair observed; gravidity sign positive.',
        ]);

        // 6. Offspring Batches
        $batch1 = OffspringBatch::firstOrCreate(['batch_code' => 'GUP-2026-001'], [
            'species_id' => $guppy->id,
            'breeding_event_id' => $breeding1->id,
            'variety' => 'Albino Blue Topaz',
            'birth_or_hatch_date' => '2026-08-27',
            'initial_count' => 38,
            'current_count' => 32,
            'death_count' => 3,
            'cull_count' => 3,
            'available_count' => 25,
            'grade' => 'BREEDER',
            'tank_id' => $t5->id,
            'status' => 'READY_FOR_SALE',
            'notes' => '[DEMO] First F1 generation fry from G001 x G002.',
        ]);

        BatchLog::firstOrCreate([
            'offspring_batch_id' => $batch1->id,
            'type' => 'MORTALITY',
            'quantity' => 3,
            'log_date' => '2026-08-30',
        ], [
            'reason' => 'First week fry natural attrition',
            'notes' => '[DEMO] Normal drop survival curve.',
        ]);

        BatchLog::firstOrCreate([
            'offspring_batch_id' => $batch1->id,
            'type' => 'CULLING',
            'quantity' => 3,
            'log_date' => '2026-09-20',
        ], [
            'reason' => 'Spinal curvature anomaly',
            'notes' => '[DEMO] Early sorting for line purity.',
        ]);

        // 7. Water Logs
        WaterLog::firstOrCreate([
            'tank_id' => $t1->id,
            'recorded_at' => '2026-10-04 09:00:00',
        ], [
            'temperature' => 26.5,
            'ph' => 7.2,
            'ammonia' => 0.0,
            'nitrite' => 0.0,
            'nitrate' => 15.0,
            'tds' => 220,
            'status' => 'GOOD',
            'notes' => '[DEMO] Stable breeding parameters.',
        ]);

        WaterLog::firstOrCreate([
            'tank_id' => $t2->id,
            'recorded_at' => '2026-10-04 09:15:00',
        ], [
            'temperature' => 27.0,
            'ph' => 7.5,
            'ammonia' => 0.0,
            'nitrite' => 0.0,
            'nitrate' => 25.0,
            'tds' => 280,
            'status' => 'GOOD',
            'notes' => '[DEMO] Hard water setup optimal for Mollies.',
        ]);

        WaterLog::firstOrCreate([
            'tank_id' => $t3->id,
            'recorded_at' => '2026-10-04 09:30:00',
        ], [
            'temperature' => 28.5,
            'ph' => 7.4,
            'ammonia' => 0.0,
            'nitrite' => 0.0,
            'nitrate' => 30.0,
            'tds' => 260,
            'status' => 'GOOD',
            'notes' => '[DEMO] Warm temp promotes kok development.',
        ]);

        WaterLog::firstOrCreate([
            'tank_id' => $t5->id,
            'recorded_at' => '2026-10-03 14:00:00',
        ], [
            'temperature' => 26.0,
            'ph' => 7.1,
            'ammonia' => 0.1,
            'nitrite' => 0.05,
            'nitrate' => 45.0,
            'tds' => 310,
            'status' => 'WARNING',
            'notes' => '[DEMO] Nitrates climbing due to frequent fry feedings; water change scheduled.',
        ]);

        // 8. Feeding Logs
        FeedingLog::firstOrCreate([
            'tank_id' => $t1->id,
            'fed_at' => '2026-10-04 08:30:00',
        ], [
            'livestock_id' => $gMale->id,
            'food' => 'Decapsulated Artemia',
            'quantity' => 'Small pinch',
            'notes' => '[DEMO] Vigorous feeding reaction.',
        ]);

        FeedingLog::firstOrCreate([
            'tank_id' => $t5->id,
            'fed_at' => '2026-10-04 12:00:00',
        ], [
            'offspring_batch_id' => $batch1->id,
            'food' => 'Baby Brine Shrimp (Live BBS)',
            'quantity' => '5ml pipette',
            'notes' => '[DEMO] Fry fed live bbs 3x daily.',
        ]);

        // 9. Maintenance Logs
        MaintenanceLog::firstOrCreate([
            'tank_id' => $t1->id,
            'performed_at' => '2026-10-01 10:00:00',
        ], [
            'maintenance_type' => 'WATER_CHANGE',
            'water_change_percentage' => 30,
            'notes' => '[DEMO] Routine 30% aged water change, glass wipe.',
        ]);

        MaintenanceLog::firstOrCreate([
            'tank_id' => $t5->id,
            'performed_at' => '2026-10-02 16:30:00',
        ], [
            'maintenance_type' => 'WATER_CHANGE',
            'water_change_percentage' => 50,
            'notes' => '[DEMO] Siphoned bottom detritus in fry tub.',
        ]);

        // 10. Inventory Items
        InventoryItem::firstOrCreate(['name' => 'High-Protein Guppy Micro Pellets'], [
            'category' => 'FOOD',
            'quantity' => 1.5,
            'unit' => 'kg',
            'minimum_quantity' => 1.0,
            'cost' => 28.00,
            'supplier' => 'Hikari Aqua Direct',
            'notes' => '[DEMO] 0.3mm micro granules.',
        ]);

        InventoryItem::firstOrCreate(['name' => 'Grade-A Artemia Cysts (95% Hatch)'], [
            'category' => 'FOOD',
            'quantity' => 0.5,
            'unit' => 'can (425g)',
            'minimum_quantity' => 1.0,
            'cost' => 65.00,
            'supplier' => 'Great Salt Lake Artemia',
            'notes' => '[DEMO] Low stock alert item.',
        ]);

        InventoryItem::firstOrCreate(['name' => 'Seachem Prime Water Conditioner'], [
            'category' => 'WATER_CONDITIONER',
            'quantity' => 2.0,
            'unit' => 'bottle (500ml)',
            'minimum_quantity' => 1.0,
            'cost' => 32.00,
            'supplier' => 'Local Fish Store Wholesale',
            'notes' => '[DEMO] Essential dechlorinator.',
        ]);

        // 11. Customer & Sales
        $customer = Customer::firstOrCreate(['name' => 'Marcus Tan'], [
            'phone' => '+65 9123 4567',
            'email' => 'marcus.tan@example.com',
            'address' => '12 Bishan St 21, Singapore',
            'notes' => '[DEMO] Experienced guppy hobbyist, prefers trio sets.',
        ]);

        $sale = Sale::firstOrCreate(['sale_number' => 'SAL-202610-001'], [
            'customer_id' => $customer->id,
            'sale_date' => '2026-10-02',
            'status' => 'COMPLETED',
            'payment_status' => 'PAID',
            'subtotal' => 60.00,
            'discount' => 5.00,
            'total' => 55.00,
            'notes' => '[DEMO] Sold 1 trio Albino Blue Topaz juveniles.',
        ]);

        SaleItem::firstOrCreate([
            'sale_id' => $sale->id,
            'offspring_batch_id' => $batch1->id,
            'item_description' => 'Albino Blue Topaz Guppy Trio (1M + 2F)',
        ], [
            'quantity' => 3,
            'unit_price' => 20.00,
            'subtotal' => 60.00,
        ]);

        // 12. Expenses
        Expense::firstOrCreate([
            'category' => 'FOOD',
            'description' => 'Hikari Tropical Fancy Guppy food 500g',
            'expense_date' => '2026-10-01',
        ], [
            'amount' => 35.00,
            'notes' => '[DEMO] Monthly feed replenishment.',
        ]);

        Expense::firstOrCreate([
            'category' => 'ELECTRICITY',
            'description' => 'Fishroom heater & pump utility bill share',
            'expense_date' => '2026-10-01',
        ], [
            'amount' => 45.00,
            'notes' => '[DEMO] Oct utility allocation.',
        ]);

        // 13. Tasks
        Task::firstOrCreate([
            'title' => 'Perform 40% water change on Tub 1 (T005)',
            'tank_id' => $t5->id,
        ], [
            'due_date' => '2026-10-05',
            'is_completed' => false,
            'notes' => '[DEMO] Keep nitrates under 30 ppm for fast fry growth.',
        ]);

        Task::firstOrCreate([
            'title' => 'Check Molly gravidity in Tank T002',
            'tank_id' => $t2->id,
        ], [
            'due_date' => '2026-10-06',
            'is_completed' => false,
            'notes' => '[DEMO] Prepare breeding trap or nursery plants.',
        ]);
    }
}
