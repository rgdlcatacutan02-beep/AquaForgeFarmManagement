<?php

namespace Tests\Feature;

use App\Models\Livestock;
use App\Models\Species;
use App\Models\Tank;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvailableFishTest extends TestCase
{
    use RefreshDatabase;

    protected Species $species;
    protected Tank $tank;

    protected function setUp(): void
    {
        parent::setUp();

        $this->species = Species::create([
            'name' => 'Fancy Guppy',
            'scientific_name' => 'Poecilia reticulata',
            'care_level' => 'EASY',
            'active' => true,
        ]);

        $this->tank = Tank::create([
            'tank_code' => 'T-SHOW-1',
            'name' => 'Showcase Tank',
            'tank_type' => 'GLASS',
            'volume_liters' => 50,
            'status' => 'ACTIVE',
        ]);
    }

    public function test_admin_can_view_available_fish_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 2 available, 1 breeder
        Livestock::create([
            'species_id' => $this->species->id,
            'livestock_code' => 'AF-AV-01',
            'sex' => 'MALE',
            'status' => 'AVAILABLE',
            'purchase_price' => 750.00,
        ]);
        Livestock::create([
            'species_id' => $this->species->id,
            'livestock_code' => 'AF-AV-02',
            'sex' => 'FEMALE',
            'status' => 'AVAILABLE',
            'purchase_price' => 1200.00,
        ]);
        Livestock::create([
            'species_id' => $this->species->id,
            'livestock_code' => 'AF-BR-01',
            'sex' => 'MALE',
            'status' => 'BREEDER',
            'purchase_price' => 500.00,
        ]);

        $response = $this->actingAs($admin)->get(route('available-fish.index'));

        $response->assertOk();
        $response->assertSee('Available Fish &amp; Catalog Manager', false);
        $response->assertSee('750.00');
        $response->assertSee('1,950.00'); // Total catalog value
        $response->assertSee('Live on Catalog');
    }

    public function test_admin_can_add_fish_for_sale_directly_to_catalog(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $photo = UploadedFile::fake()->image('dumbo_red.jpg', 600, 600);

        $response = $this->actingAs($admin)->post(route('available-fish.store'), [
            'species_id' => $this->species->id,
            'variety' => 'Albino Full Red Dumbo Ear',
            'livestock_code' => 'AF-AFR-001',
            'sex' => 'MALE',
            'purchase_price' => 850.00,
            'grade' => 'Show Grade',
            'status' => 'AVAILABLE',
            'tank_id' => $this->tank->id,
            'photo' => $photo,
            'notes' => 'High dorsal fin, eating live artemia.',
        ]);

        $response->assertRedirect(route('available-fish.index'));
        $response->assertSessionHas('success');

        $fish = Livestock::where('livestock_code', 'AF-AFR-001')->first();
        $this->assertNotNull($fish);
        $this->assertEquals('AVAILABLE', $fish->status);
        $this->assertEquals(850.00, $fish->purchase_price);
        $this->assertNotNull($fish->photo_path);
        Storage::disk('public')->assertExists($fish->photo_path);

        // Verify it immediately appears on the public customer catalog
        $catalogResponse = $this->get(route('catalog.index'));
        $catalogResponse->assertOk();
        $catalogResponse->assertSee('Albino Full Red Dumbo Ear');
        $catalogResponse->assertSee('AF-AFR-001');
        $catalogResponse->assertSee('850.00');
    }

    public function test_admin_can_toggle_any_fish_onto_or_off_the_catalog_with_one_click(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Start as BREEDER (not for sale on catalog)
        $fish = Livestock::create([
            'species_id' => $this->species->id,
            'livestock_code' => 'AF-TOGGLE-01',
            'sex' => 'MALE',
            'status' => 'BREEDER',
            'purchase_price' => 400.00,
        ]);

        // 1-Click: Put on Catalog
        $toggleOn = $this->actingAs($admin)->post(route('available-fish.toggle-catalog', $fish));
        $toggleOn->assertRedirect();
        $fish->refresh();
        $this->assertEquals('AVAILABLE', $fish->status);

        // Verify now on public catalog
        $this->get(route('catalog.index'))->assertSee('AF-TOGGLE-01');

        // 1-Click: Remove from Catalog
        $toggleOff = $this->actingAs($admin)->post(route('available-fish.toggle-catalog', $fish), [
            'new_status' => 'BREEDER',
        ]);
        $toggleOff->assertRedirect();
        $fish->refresh();
        $this->assertEquals('BREEDER', $fish->status);

        // Verify no longer on public catalog
        $this->get(route('catalog.index'))->assertDontSee('AF-TOGGLE-01');
    }

    public function test_admin_can_quick_update_price_from_available_fish_manager(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $fish = Livestock::create([
            'species_id' => $this->species->id,
            'livestock_code' => 'AF-PRICE-01',
            'sex' => 'FEMALE',
            'status' => 'AVAILABLE',
            'purchase_price' => 300.00,
        ]);

        $response = $this->actingAs($admin)->patch(route('available-fish.update-price', $fish), [
            'purchase_price' => 450.00,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $fish->refresh();
        $this->assertEquals(450.00, $fish->purchase_price);
    }

    public function test_customer_cannot_access_available_fish_admin_manager(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get(route('available-fish.index'));
        $response->assertRedirect(route('catalog.index'));
    }
}
