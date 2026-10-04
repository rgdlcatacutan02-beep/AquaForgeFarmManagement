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

class Phase2FarmManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_create_and_update_species(): void
    {
        // 1. Create Species
        $response = $this->actingAs($this->user)->post(route('species.store'), [
            'name' => 'Angelfish',
            'scientific_name' => 'Pterophyllum scalare',
            'description' => 'South American cichlid species requiring warm soft water.',
            'active' => 1,
        ]);

        $response->assertRedirect(route('species.index'));
        $this->assertDatabaseHas('species', [
            'name' => 'Angelfish',
            'scientific_name' => 'Pterophyllum scalare',
            'active' => 1,
        ]);

        $species = Species::where('name', 'Angelfish')->first();

        // 2. View Species Detail
        $detailRes = $this->actingAs($this->user)->get(route('species.show', $species));
        $detailRes->assertStatus(200);
        $detailRes->assertSee('Angelfish');
        $detailRes->assertSee('Pterophyllum scalare');

        // 3. Update Species
        $updateRes = $this->actingAs($this->user)->put(route('species.update', $species), [
            'name' => 'Altum Angelfish',
            'scientific_name' => 'Pterophyllum altum',
            'description' => 'Wild type deep body angelfish.',
            'active' => 1,
        ]);

        $updateRes->assertRedirect(route('species.index'));
        $this->assertDatabaseHas('species', [
            'name' => 'Altum Angelfish',
            'scientific_name' => 'Pterophyllum altum',
        ]);
    }

    public function test_cannot_delete_species_with_linked_livestock(): void
    {
        $species = Species::create([
            'name' => 'Guppy',
            'active' => true,
        ]);

        $tank = Tank::create([
            'tank_code' => 'T001',
            'name' => 'Breeding Tank 1',
            'tank_type' => 'GLASS',
            'volume_liters' => 50,
            'purpose' => 'BREEDING',
            'status' => 'ACTIVE',
        ]);

        Livestock::create([
            'livestock_code' => 'G001',
            'species_id' => $species->id,
            'variety' => 'Blue Grass',
            'sex' => 'MALE',
            'status' => 'BREEDER',
            'tank_id' => $tank->id,
        ]);

        // Attempt delete
        $response = $this->actingAs($this->user)->delete(route('species.destroy', $species));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('species', ['id' => $species->id]);
    }

    public function test_can_create_and_manage_tanks_with_qr_code(): void
    {
        // 1. Create Tank
        $response = $this->actingAs($this->user)->post(route('tanks.store'), [
            'tank_code' => 'T099',
            'name' => 'Quarantine Tub A',
            'tank_type' => 'TUB',
            'length' => 60,
            'width' => 40,
            'height' => 30,
            'volume_liters' => 72,
            'location' => 'Shed Rack 2',
            'purpose' => 'QUARANTINE',
            'status' => 'ACTIVE',
            'notes' => 'Air sponge filter driven by central blower.',
        ]);

        $tank = Tank::where('tank_code', 'T099')->first();
        $this->assertNotNull($tank);
        $response->assertRedirect(route('tanks.show', $tank));

        // 2. View Tank Hub & QR code
        $response->assertSessionHas('show_qr_print_modal', true);

        $hubResponse = $this->actingAs($this->user)->get(route('tanks.show', $tank));
        $hubResponse->assertStatus(200);
        $hubResponse->assertSee('T099');
        $hubResponse->assertSee('Quarantine Tub A');
        $hubResponse->assertSee('Liters');
        $hubResponse->assertSee('<svg', false); // SVG QR code rendered

        // 3. Printable Tank QR Label
        $labelResponse = $this->actingAs($this->user)->get(route('tanks.print-label', $tank));
        $labelResponse->assertStatus(200);
        $labelResponse->assertSee('Print Tank Sticker Tag');
        $labelResponse->assertSee('T099');
        $labelResponse->assertSee('<svg', false);

        // 3. Update Tank
        $updateResponse = $this->actingAs($this->user)->put(route('tanks.update', $tank), [
            'tank_code' => 'T099',
            'name' => 'Hospital Tub A',
            'tank_type' => 'TUB',
            'volume_liters' => 72,
            'purpose' => 'SICK',
            'status' => 'ACTIVE',
        ]);

        $updateResponse->assertRedirect(route('tanks.show', $tank));
        $this->assertDatabaseHas('tanks', [
            'id' => $tank->id,
            'name' => 'Hospital Tub A',
            'purpose' => 'SICK',
        ]);
    }

    public function test_can_create_livestock_with_photo_and_grading_scores(): void
    {
        Storage::fake('public');

        $species = Species::create(['name' => 'Guppy', 'active' => true]);
        $tank = Tank::create([
            'tank_code' => 'T005',
            'name' => 'Show Tank',
            'tank_type' => 'GLASS',
            'volume_liters' => 30,
            'purpose' => 'DISPLAY',
            'status' => 'ACTIVE',
        ]);

        $fakePhoto = UploadedFile::fake()->image('guppy_breeder.jpg', 600, 400);

        $response = $this->actingAs($this->user)->post(route('livestock.store'), [
            'livestock_code' => 'G101',
            'species_id' => $species->id,
            'variety' => 'Albino Full Red',
            'sex' => 'MALE',
            'status' => 'BREEDER',
            'tank_id' => $tank->id,
            'grade' => 'SHOW',
            'purchase_price' => 45.00,
            'source' => 'World Guppy Contest Line',
            'grading_scores' => [
                'body' => 5,
                'color' => 5,
                'tail' => 4,
                'dorsal' => 5,
                'pattern' => 4,
                'overall' => 5,
            ],
            'notes' => 'High dorsal spread and intense ruby red coloration.',
            'photo' => $fakePhoto,
        ]);

        $livestock = Livestock::where('livestock_code', 'G101')->first();
        $this->assertNotNull($livestock);
        $response->assertRedirect(route('livestock.show', $livestock));

        // Assert database record
        $this->assertDatabaseHas('livestock', [
            'livestock_code' => 'G101',
            'variety' => 'Albino Full Red',
            'grade' => 'SHOW',
            'sex' => 'MALE',
            'tank_id' => $tank->id,
        ]);

        $this->assertEquals(5, $livestock->grading_scores['body']);
        $this->assertEquals(4, $livestock->grading_scores['tail']);

        // Assert photo stored on disk
        $this->assertNotNull($livestock->photo_path);
        Storage::disk('public')->assertExists($livestock->photo_path);

        // View specimen page
        $showRes = $this->actingAs($this->user)->get(route('livestock.show', $livestock));
        $showRes->assertStatus(200);
        $showRes->assertSee('G101');
        $showRes->assertSee('Albino Full Red');
        $showRes->assertSee('SHOW');
        $showRes->assertSee('T005');

        // Verify photo replacement
        $replacementPhoto = UploadedFile::fake()->image('guppy_new.jpg');
        $updateRes = $this->actingAs($this->user)->put(route('livestock.update', $livestock), [
            'livestock_code' => 'G101',
            'species_id' => $species->id,
            'variety' => 'Albino Full Red - Proven',
            'sex' => 'MALE',
            'status' => 'BREEDER',
            'tank_id' => $tank->id,
            'grade' => 'SHOW',
            'photo' => $replacementPhoto,
        ]);

        $updateRes->assertRedirect(route('livestock.show', $livestock));
        $livestock->refresh();
        $this->assertEquals('Albino Full Red - Proven', $livestock->variety);
        Storage::disk('public')->assertExists($livestock->photo_path);
    }

    public function test_can_reassign_livestock_tank_and_update_status(): void
    {
        $species = Species::create(['name' => 'Flowerhorn', 'active' => true]);
        $tankA = Tank::create(['tank_code' => 'T010', 'name' => 'Tank A', 'tank_type' => 'GLASS', 'volume_liters' => 150, 'purpose' => 'GROWOUT', 'status' => 'ACTIVE']);
        $tankB = Tank::create(['tank_code' => 'T020', 'name' => 'Tank B', 'tank_type' => 'GLASS', 'volume_liters' => 200, 'purpose' => 'DISPLAY', 'status' => 'ACTIVE']);

        $livestock = Livestock::create([
            'livestock_code' => 'FH99',
            'species_id' => $species->id,
            'variety' => 'Kamfa Classic',
            'sex' => 'MALE',
            'status' => 'GROWOUT',
            'tank_id' => $tankA->id,
        ]);

        // Move to Tank B and change status to DISPLAY
        $response = $this->actingAs($this->user)->put(route('livestock.update', $livestock), [
            'livestock_code' => 'FH99',
            'species_id' => $species->id,
            'variety' => 'Kamfa Classic',
            'sex' => 'MALE',
            'status' => 'DISPLAY',
            'tank_id' => $tankB->id,
        ]);

        $response->assertRedirect(route('livestock.show', $livestock));
        $livestock->refresh();
        $this->assertEquals($tankB->id, $livestock->tank_id);
        $this->assertEquals('DISPLAY', $livestock->status);
    }

    public function test_livestock_code_must_be_unique(): void
    {
        $species = Species::create(['name' => 'Molly', 'active' => true]);

        Livestock::create([
            'livestock_code' => 'M001',
            'species_id' => $species->id,
            'sex' => 'FEMALE',
            'status' => 'BREEDER',
        ]);

        $response = $this->actingAs($this->user)->post(route('livestock.store'), [
            'livestock_code' => 'M001',
            'species_id' => $species->id,
            'sex' => 'MALE',
            'status' => 'BREEDER',
        ]);

        $response->assertSessionHasErrors('livestock_code');
    }
}
