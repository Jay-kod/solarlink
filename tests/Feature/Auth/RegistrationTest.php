<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/user/register');

        $response->assertStatus(200);
    }

    public function test_new_customer_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Customer',
            'email' => 'customer_test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'customer',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/user');
    }

    public function test_new_technician_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Technician',
            'email' => 'tech_test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'technician',
            'cert_name' => 'Solar Master Certification',
            'years_experience' => '3-5 Years',
            'hourly_rate' => 75.50,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/technician');

        $user = User::where('email', 'tech_test@example.com')->first();
        $this->assertNotNull($user->technicianProfile);
        $this->assertEquals('Solar Master Certification', $user->technicianProfile->cert_name);
    }

    public function test_new_vendor_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Vendor',
            'email' => 'vendor_test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'vendor',
            'store_name' => 'Solar Warehouse',
            'company_address' => '123 Solar Way',
            'vat_number' => 'VAT123456789',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/vendor');

        $user = User::where('email', 'vendor_test@example.com')->first();
        $this->assertNotNull($user->vendorProfile);
        $this->assertEquals('Solar Warehouse', $user->vendorProfile->store_name);
    }
}
