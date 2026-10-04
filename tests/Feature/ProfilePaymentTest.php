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

    public function test_admin_can_upload_custom_farm_logo_and_change_farm_name(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'farm_name' => 'Initial AquaForge Farm',
            'farm_logo_path' => null,
            'role' => 'admin',
        ]);

        $logoFile = UploadedFile::fake()->image('my_farm_crest.png', 500, 500);

        $response = $this->actingAs($user)->patch(route('profile.payment.update'), [
            'farm_name' => 'Blue Lagoon Guppy Farm',
            'farm_location' => 'Rizal, Philippines',
            'farm_logo' => $logoFile,
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('Blue Lagoon Guppy Farm', $user->farm_name);
        $this->assertNotNull($user->farm_logo_path);
        Storage::disk('public')->assertExists($user->farm_logo_path);

        // Check welcome page: displays custom name and logo, locked with Powered by AquaForge
        $welcomeRes = $this->get(url('/'));
        $welcomeRes->assertOk();
        $welcomeRes->assertSee('Blue Lagoon Guppy Farm');
        $welcomeRes->assertSee($user->farm_logo_url);
        $welcomeRes->assertSee('Powered by AquaForge System');

        // Check catalog index: displays custom name and logo with Powered by AquaForge watermark
        $catalogRes = $this->get(route('catalog.index'));
        $catalogRes->assertOk();
        $catalogRes->assertSee('Blue Lagoon Guppy Farm');
        $catalogRes->assertSee($user->farm_logo_url);
        $catalogRes->assertSee('Powered by AquaForge');

        // Check admin sidebar: displays custom logo and Powered by AquaForge subtitle
        $dashRes = $this->actingAs($user)->get(route('dashboard'));
        $dashRes->assertOk();
        $dashRes->assertSee('Blue Lagoon Guppy Farm');
        $dashRes->assertSee($user->farm_logo_url);
        $dashRes->assertSee('Powered by AquaForge');
    }

    public function test_admin_can_remove_custom_farm_logo_to_revert_to_default_crest(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'farm_name' => 'AquaForge Elite',
            'farm_logo_path' => 'farm_logos/existing_logo.png',
            'role' => 'admin',
        ]);
        Storage::disk('public')->put('farm_logos/existing_logo.png', 'fake image content');

        $response = $this->actingAs($user)->patch(route('profile.payment.update'), [
            'farm_name' => 'AquaForge Elite',
            'remove_farm_logo' => '1',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNull($user->farm_logo_path);
        Storage::disk('public')->assertMissing('farm_logos/existing_logo.png');
    }

    public function test_farm_logo_must_be_a_valid_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'admin']);
        $badFile = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->patch(route('profile.payment.update'), [
            'farm_name' => 'Test Farm',
            'farm_logo' => $badFile,
        ]);

        $response->assertSessionHasErrors('farm_logo');
    }

    public function test_admin_can_edit_welcome_trust_features_cards(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'farm_name' => 'RAM Aquatics',
        ]);

        $customFeatures = [
            [
                'title' => '100% DOA Replacement',
                'desc' => 'Clear unboxing video within 1 hour guarantees full refund or specimen replacement.',
                'icon' => 'shield-check',
                'color' => 'cyan',
            ],
            [
                'title' => 'Express Island Cargo',
                'desc' => 'Direct bus cargo & flight courier across Luzon, Visayas, and Mindanao.',
                'icon' => 'truck',
                'color' => 'teal',
            ],
            [
                'title' => 'Pure Lineage DNA',
                'desc' => 'Certified strain purity tracked through AquaForge genealogy logs.',
                'icon' => 'award',
                'color' => 'indigo',
            ],
            [
                'title' => 'Instant Digital Pay',
                'desc' => 'Scan QR to pay securely via GCash, Maya, and BDO online transfer.',
                'icon' => 'credit-card',
                'color' => 'emerald',
            ],
        ];

        $response = $this->actingAs($user)->patch(route('profile.payment.update'), [
            'farm_name' => 'RAM Aquatics',
            'trust_features' => $customFeatures,
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('100% DOA Replacement', $user->trust_features[0]['title']);
        $this->assertEquals('Express Island Cargo', $user->trust_features[1]['title']);
        $this->assertEquals('Pure Lineage DNA', $user->trust_features[2]['title']);
        $this->assertEquals('Instant Digital Pay', $user->trust_features[3]['title']);

        // Check that welcome page renders the customized cards
        $welcomeRes = $this->get(url('/'));
        $welcomeRes->assertOk();
        $welcomeRes->assertSee('100% DOA Replacement');
        $welcomeRes->assertSee('Express Island Cargo');
        $welcomeRes->assertSee('Pure Lineage DNA');
        $welcomeRes->assertSee('Instant Digital Pay');
    }
}
