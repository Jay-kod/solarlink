<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_only_access_customer_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($user);

        // Can access customer dashboard
        $response = $this->get('/user');
        $response->assertStatus(200);

        // Cannot access technician, vendor, or admin dashboards
        $this->get('/technician')->assertStatus(403);
        $this->get('/vendor')->assertStatus(403);
        $this->get('/admin')->assertStatus(403);
    }

    public function test_technician_can_only_access_technician_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'technician',
        ]);

        $this->actingAs($user);

        // Can access technician dashboard
        $response = $this->get('/technician');
        $response->assertStatus(200);

        // Cannot access customer, vendor, or admin dashboards
        $this->get('/user')->assertStatus(403);
        $this->get('/vendor')->assertStatus(403);
        $this->get('/admin')->assertStatus(403);
    }

    public function test_vendor_can_only_access_vendor_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'vendor',
        ]);

        $this->actingAs($user);

        // Can access vendor dashboard
        $response = $this->get('/vendor');
        $response->assertStatus(200);

        // Cannot access customer, technician, or admin dashboards
        $this->get('/user')->assertStatus(403);
        $this->get('/technician')->assertStatus(403);
        $this->get('/admin')->assertStatus(403);
    }

    public function test_admin_can_only_access_admin_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        // Can access admin dashboard
        $response = $this->get('/admin');
        $response->assertStatus(200);

        // Cannot access customer, technician, or vendor dashboards
        $this->get('/user')->assertStatus(403);
        $this->get('/technician')->assertStatus(403);
        $this->get('/vendor')->assertStatus(403);
    }
}
