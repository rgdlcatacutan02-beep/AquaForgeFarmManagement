<?php

namespace Tests\Feature;

use App\Models\Livestock;
use App\Models\Species;
use App\Models\Tank;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Species $species;
    protected Tank $tank;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'farm_name' => 'AquaForge Manila',
            'farm_location' => 'Quezon City, PH',
            'messenger_username' => 'AquaForgePH',
            'gcash_name' => 'JUAN D.',
            'gcash_number' => '09171234567',
        ]);

        $this->species = Species::create([
            'name' => 'Guppy',
            'scientific_name' => 'Poecilia reticulata',
            'care_level' => 'EASY',
        ]);

        $this->tank = Tank::create([
            'tank_code' => 'T-01',
            'name' => 'Display Tank',
            'tank_type' => 'GLASS',
            'volume_liters' => 50,
            'status' => 'ACTIVE',
        ]);
    }

    public function test_guest_can_view_public_catalog(): void
    {
        Livestock::create([
            'livestock_code' => 'GUP-001',
            'species_id' => $this->species->id,
            'tank_id' => $this->tank->id,
            'variety' => 'Albino Full Red',
            'sex' => 'MALE',
            'grade' => 'SHOW',
            'status' => 'AVAILABLE',
            'purchase_price' => 2000.00,
        ]);

        Livestock::create([
            'livestock_code' => 'GUP-002',
            'species_id' => $this->species->id,
            'tank_id' => $this->tank->id,
            'variety' => 'Blue Grass',
            'sex' => 'MALE',
            'status' => 'SOLD',
            'purchase_price' => 1200.00,
        ]);

        $response = $this->get(route('catalog.index'));

        $response->assertStatus(200);
        $response->assertSee('AquaForge Manila');
        $response->assertSee('Albino Full Red');
        $response->assertSee('2,000.00');
        $response->assertSee('Order Basket');
        $response->assertSee('Send Order to Facebook Messenger & Await Reply');
        $response->assertSee('Please wait for our confirmation reply and shipping fee before sending any payment');
        $response->assertDontSee('Blue Grass');
    }

    public function test_guest_can_filter_catalog_by_search_and_grade(): void
    {
        Livestock::create([
            'livestock_code' => 'GUP-101',
            'species_id' => $this->species->id,
            'tank_id' => $this->tank->id,
            'variety' => 'Dumbo Ear Mosaic',
            'sex' => 'FEMALE',
            'grade' => 'SHOW',
            'status' => 'AVAILABLE',
            'purchase_price' => 1800.00,
        ]);

        Livestock::create([
            'livestock_code' => 'GUP-102',
            'species_id' => $this->species->id,
            'tank_id' => $this->tank->id,
            'variety' => 'Black Moscow',
            'sex' => 'FEMALE',
            'grade' => 'COMMERCIAL',
            'status' => 'AVAILABLE',
            'purchase_price' => 500.00,
        ]);

        $response = $this->get(route('catalog.index', ['search' => 'Mosaic']));
        $response->assertStatus(200);
        $response->assertSee('Dumbo Ear Mosaic');
        $response->assertDontSee('Black Moscow');

        $responseGrade = $this->get(route('catalog.index', ['grade' => 'SHOW']));
        $responseGrade->assertStatus(200);
        $responseGrade->assertSee('Dumbo Ear Mosaic');
        $responseGrade->assertDontSee('Black Moscow');
    }

    public function test_guest_can_view_single_fish_showcase_with_cart_and_messenger_inquiry(): void
    {
        $fish = Livestock::create([
            'livestock_code' => 'GUP-888',
            'species_id' => $this->species->id,
            'tank_id' => $this->tank->id,
            'variety' => 'Platinum Red Tail Dumbo',
            'sex' => 'MALE',
            'grade' => 'SHOW',
            'status' => 'AVAILABLE',
            'purchase_price' => 3500.00,
            'notes' => 'Pristine dorsal finnage, conditioned on baby brine shrimp.',
        ]);

        $response = $this->get(route('catalog.show', $fish));

        $response->assertStatus(200);
        $response->assertSee('Platinum Red Tail Dumbo');
        $response->assertSee('GUP-888');
        $response->assertSee('3,500.00');
        $response->assertSee('+ Add This Fish to Order List');
        $response->assertSee('Order Basket');
        $response->assertSee('Send Order to Facebook Messenger & Await Reply');
        $response->assertSee('Pristine dorsal finnage');
    }

    public function test_user_messenger_url_attribute_formats_properly(): void
    {
        $u1 = new User(['messenger_username' => 'MyFishFarm']);
        $this->assertEquals('https://m.me/MyFishFarm', $u1->messenger_url);

        $u2 = new User(['messenger_username' => '@AquaStudio']);
        $this->assertEquals('https://m.me/AquaStudio', $u2->messenger_url);

        $u3 = new User(['messenger_username' => 'https://m.me/CustomFarm']);
        $this->assertEquals('https://m.me/CustomFarm', $u3->messenger_url);
    }
}
