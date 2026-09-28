<?php

namespace Tests\Feature;

use App\Models\MaintenanceRequest;
use App\Models\Product;
use App\Models\SolarAppliance;
use App\Models\TechnicianProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceAndVendorAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_maintenance_request_for_owned_appliance(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $appliance = SolarAppliance::factory()->create(['user_id' => $customer->id]);

        $this->actingAs($customer);

        $response = $this->post('/user/maintenance', [
            'solar_appliance_id' => $appliance->id,
            'fault_type' => 'inverter',
            'priority' => 'high',
            'description' => 'Battery output drops intermittently.',
            'location' => 'Lagos Island, Lagos',
            'latitude' => 6.4541,
            'longitude' => 3.3947,
        ]);

        $response->assertRedirect('/user/maintenance');
        $this->assertDatabaseHas('maintenance_requests', [
            'user_id' => $customer->id,
            'solar_appliance_id' => $appliance->id,
            'fault_type' => 'inverter',
            'status' => 'reported',
        ]);
    }

    public function test_customer_cannot_submit_maintenance_for_another_customers_appliance(): void
    {
        $customerA = User::factory()->create(['role' => 'customer']);
        $customerB = User::factory()->create(['role' => 'customer']);
        $appliance = SolarAppliance::factory()->create(['user_id' => $customerB->id]);

        $this->actingAs($customerA);

        $response = $this->post('/user/maintenance', [
            'solar_appliance_id' => $appliance->id,
            'fault_type' => 'battery',
            'priority' => 'medium',
            'description' => 'Not allowed',
            'location' => 'Lekki Phase 1',
        ]);

        $response->assertSessionHasErrors(['solar_appliance_id']);
    }

    public function test_technician_can_see_assigned_request(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $technicianUser = User::factory()->create(['role' => 'technician']);
        $profile = TechnicianProfile::factory()->create(['user_id' => $technicianUser->id, 'verification_status' => 'verified']);
        $appliance = SolarAppliance::factory()->create(['user_id' => $customer->id]);
        MaintenanceRequest::factory()->create([
            'user_id' => $customer->id,
            'solar_appliance_id' => $appliance->id,
            'technician_profile_id' => $profile->id,
            'status' => 'assigned',
        ]);

        $this->actingAs($technicianUser);

        $response = $this->get('/technician/jobs');
        $response->assertOk();
        $response->assertSee('assigned');
    }

    public function test_vendor_can_only_modify_own_products(): void
    {
        $vendorA = User::factory()->create(['role' => 'vendor']);
        $vendorB = User::factory()->create(['role' => 'vendor']);
        $product = Product::factory()->create(['vendor_id' => $vendorA->id]);

        $this->actingAs($vendorB);

        $response = $this->put('/vendor/products/' . $product->id, [
            'name' => 'Test Product',
            'price' => 255,
            'stock' => 8,
            'category' => 'panels',
        ]);

        $response->assertStatus(403);
    }
}
