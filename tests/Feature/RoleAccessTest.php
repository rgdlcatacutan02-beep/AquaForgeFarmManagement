<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_access_farm_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_customer_user_is_redirected_away_from_dashboard(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)->get(route('dashboard'));
        $response->assertRedirect(route('catalog.index'));
        $response->assertSessionHas('notice');
    }

    public function test_customer_cannot_access_internal_tanks_or_livestock(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $responseTank = $this->actingAs($customer)->get(route('tanks.index'));
        $responseTank->assertRedirect(route('catalog.index'));

        $responseLivestock = $this->actingAs($customer)->get(route('livestock.index'));
        $responseLivestock->assertRedirect(route('catalog.index'));
    }

    public function test_registration_creates_customer_and_redirects_to_catalog(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Aquarium Buyer',
            'email' => 'buyer@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('catalog.index'));

        $user = User::where('email', 'buyer@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
        $this->assertTrue($user->isCustomer());
        $this->assertFalse($user->isAdmin());
    }

    public function test_login_redirects_admin_to_dashboard_and_customer_to_catalog(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@aquaforge.test',
            'password' => bcrypt('adminpass'),
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'email' => 'customer@gmail.com',
            'password' => bcrypt('customerpass'),
            'role' => 'customer',
        ]);

        // Admin login
        $resAdmin = $this->post(route('login'), [
            'email' => 'admin@aquaforge.test',
            'password' => 'adminpass',
        ]);
        $resAdmin->assertRedirect(route('dashboard'));

        // Customer login
        $this->post(route('logout'));
        $resCustomer = $this->post(route('login'), [
            'email' => 'customer@gmail.com',
            'password' => 'customerpass',
        ]);
        $resCustomer->assertRedirect(route('catalog.index'));
    }
}
