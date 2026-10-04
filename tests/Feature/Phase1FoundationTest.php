<?php

namespace Tests\Feature;

use App\Models\Species;
use App\Models\Tank;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase1FoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DemoDataSeeder::class);
    }

    public function test_welcome_screen_renders_with_aquaforge_branding(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('AquaForge');
        $response->assertSee('Aquatic Farm & Breeding Management System', false);
    }

    public function test_unauthenticated_user_redirected_from_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_dashboard_with_kpis(): void
    {
        $user = User::first();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Farm Dashboard');
        $response->assertSee('Total Livestock');
        $response->assertSee('Total Tanks');
        $response->assertSee('Aquatic Farm & Breeding Management System', false);
        $response->assertSee('Available for Sale');
        $response->assertSee('Monthly Revenue');
        $response->assertSee('Monthly Expenses');
        $response->assertSee('Livestock Overview');
        $response->assertSee('Tank Status');
    }

    public function test_demo_seeder_populates_required_species_and_tanks(): void
    {
        $this->assertDatabaseHas('species', ['name' => 'Guppy']);
        $this->assertDatabaseHas('species', ['name' => 'Molly']);
        $this->assertDatabaseHas('species', ['name' => 'Flowerhorn']);
        $this->assertDatabaseHas('species', ['name' => 'Crayfish']);

        $this->assertDatabaseHas('tanks', ['tank_code' => 'T001']);
        $this->assertDatabaseHas('tanks', ['tank_code' => 'T002']);
        $this->assertDatabaseHas('tanks', ['tank_code' => 'T003']);
        $this->assertDatabaseHas('tanks', ['tank_code' => 'T004']);
        $this->assertDatabaseHas('tanks', ['tank_code' => 'T005']);
    }

    public function test_tank_detail_page_renders_with_qr_code(): void
    {
        $user = User::first();
        $tank = Tank::where('tank_code', 'T001')->firstOrFail();

        $response = $this->actingAs($user)->get(route('tanks.show', $tank));

        $response->assertStatus(200);
        $response->assertSee('T001');
        $response->assertSee('Tank QR Hub');
        $response->assertSee('Latest Water Chemistry');
        $response->assertSee('Quick Tank Actions');
    }

    public function test_all_sidebar_navigation_endpoints_are_accessible(): void
    {
        $user = User::first();

        $routes = [
            'dashboard',
            'species.index',
            'tanks.index',
            'livestock.index',
            'batches.index',
            'breeding.index',
            'water-logs.index',
            'feeding.index',
            'maintenance.index',
            'inventory.index',
            'sales.index',
            'customers.index',
            'expenses.index',
            'reports.index',
            'profile.edit',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get(route($route));
            $response->assertStatus(200);
        }
    }
}
