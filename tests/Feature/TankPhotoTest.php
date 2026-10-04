<?php

namespace Tests\Feature;

use App\Models\Livestock;
use App\Models\Species;
use App\Models\Tank;
use App\Models\TankPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TankPhotoTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Tank $tank;
    protected Livestock $livestock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $species = Species::create([
            'name' => 'Guppy',
            'scientific_name' => 'Poecilia reticulata',
            'care_level' => 'EASY',
        ]);

        $this->tank = Tank::create([
            'tank_code' => 'T-01',
            'name' => 'Breeder Rack A1',
            'tank_type' => 'GLASS',
            'volume_liters' => 50,
            'status' => 'ACTIVE',
        ]);

        $this->livestock = Livestock::create([
            'livestock_code' => 'GUP-001',
            'species_id' => $species->id,
            'tank_id' => $this->tank->id,
            'variety' => 'Albino Full Red',
            'sex' => 'MALE',
            'status' => 'AVAILABLE',
            'purchase_price' => 1500.00,
        ]);
    }

    public function test_authenticated_user_can_upload_photo_to_tank(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('tank_fish.jpg', 600, 600);

        $response = $this->actingAs($this->user)->post(route('tanks.photos.store', $this->tank), [
            'photo' => $file,
            'caption' => 'High fin male displaying courtship behavior',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tank_photos', [
            'tank_id' => $this->tank->id,
            'caption' => 'High fin male displaying courtship behavior',
        ]);

        $photo = TankPhoto::first();
        Storage::disk('public')->assertExists($photo->photo_path);
    }

    public function test_uploading_photo_can_tag_fish_and_set_as_avatar(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('guppy_avatar.jpg', 600, 600);

        $response = $this->actingAs($this->user)->post(route('tanks.photos.store', $this->tank), [
            'photo' => $file,
            'caption' => 'New profile avatar',
            'livestock_id' => $this->livestock->id,
            'set_as_avatar' => '1',
        ]);

        $response->assertRedirect();

        $this->livestock->refresh();
        $this->assertNotNull($this->livestock->photo_path);

        $photo = TankPhoto::first();
        $this->assertEquals($this->livestock->photo_path, $photo->photo_path);
        $this->assertEquals($this->livestock->id, $photo->livestock_id);
    }

    public function test_user_can_delete_tank_photo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('delete_me.jpg');
        $path = $file->store('tank_photos', 'public');

        $photo = TankPhoto::create([
            'tank_id' => $this->tank->id,
            'photo_path' => $path,
            'caption' => 'Temporary photo',
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->user)->delete(route('tanks.photos.destroy', $photo));

        $response->assertRedirect();
        $this->assertDatabaseMissing('tank_photos', ['id' => $photo->id]);
        Storage::disk('public')->assertMissing($path);
    }
}
