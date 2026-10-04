<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_payment_and_social_settings(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'farm_name' => 'Original Farm',
        ]);

        $qrFile = UploadedFile::fake()->image('gcash_qr.png', 400, 400);

        $response = $this->actingAs($user)->patch(route('profile.payment.update'), [
            'farm_name' => 'AquaForge Breeder Hub',
            'farm_location' => 'Bulacan, Philippines',
            'messenger_username' => 'AquaForgePH',
            'facebook_page' => 'https://facebook.com/aquaforgeph',
            'contact_number' => '0917-888-9999',
            'gcash_name' => 'JUAN DELA CRUZ',
            'gcash_number' => '0917-123-4567',
            'gcash_qr' => $qrFile,
            'maya_name' => 'JUAN DELA CRUZ',
            'maya_number' => '0918-765-4321',
            'bank_details' => 'BDO 1234-5678-9012',
            'shipping_notes' => 'Lalamove same-day delivery with heat/ice pack.',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('AquaForge Breeder Hub', $user->farm_name);
        $this->assertEquals('Bulacan, Philippines', $user->farm_location);
        $this->assertEquals('AquaForgePH', $user->messenger_username);
        $this->assertEquals('https://m.me/AquaForgePH', $user->messenger_url);
        $this->assertEquals('0917-123-4567', $user->gcash_number);
        $this->assertEquals('0918-765-4321', $user->maya_number);
        $this->assertNotNull($user->gcash_qr_path);

        Storage::disk('public')->assertExists($user->gcash_qr_path);
    }
}
