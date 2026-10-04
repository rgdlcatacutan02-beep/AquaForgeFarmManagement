<?php

namespace Tests\Feature;

use App\Models\Tank;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaAndScannerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_pwa_manifest_and_service_worker_files_are_available(): void
    {
        $manifestPath = public_path('manifest.json');
        $this->assertFileExists($manifestPath);

        $manifestContent = json_decode(file_get_contents($manifestPath), true);
        $this->assertEquals('AquaForge', $manifestContent['short_name']);
        $this->assertEquals('standalone', $manifestContent['display']);
        $this->assertCount(3, $manifestContent['icons']);

        $swPath = public_path('sw.js');
        $this->assertFileExists($swPath);

        $this->assertFileExists(public_path('icons/icon-192.png'));
        $this->assertFileExists(public_path('icons/icon-512.png'));
        $this->assertFileExists(public_path('icons/icon.svg'));
    }

    public function test_authenticated_user_can_access_dedicated_tank_scanner_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('tanks.scan'));
        $response->assertStatus(200);
        $response->assertSee('Scan Tank QR Code');
        $response->assertSee('dedicated-qr-reader');
    }

    public function test_tank_lookup_by_tank_code_redirects_to_tank_hub(): void
    {
        $tank = Tank::create([
            'tank_code' => 'T-PWA-01',
            'name' => 'PWA Verification Tank',
            'volume_liters' => 60,
            'purpose' => 'BREEDING',
            'status' => 'ACTIVE',
        ]);

        $response = $this->actingAs($this->user)->get(route('tanks.lookup', 'T-PWA-01'));
        $response->assertRedirect(route('tanks.show', $tank));
    }

    public function test_tank_lookup_with_invalid_code_redirects_with_error(): void
    {
        $response = $this->actingAs($this->user)->get(route('tanks.lookup', 'NON_EXISTENT_CODE'));
        $response->assertRedirect(route('tanks.index'));
        $response->assertSessionHas('error');
    }
}
