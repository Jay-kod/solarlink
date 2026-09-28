<?php

namespace Tests\Feature;

use App\Models\LiveLocation;
use App\Models\SavedLocation;
use App\Models\TechnicianProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\LocationService;
use Tests\TestCase;

class LocationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_a_primary_location_and_sync_profile_coordinates(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->from('/locations')->post('/locations', [
            'label' => 'Home',
            'address' => '12 Solar Way',
            'latitude' => 37.7749,
            'longitude' => -122.4194,
            'is_primary' => true,
            'share_with_users' => false,
        ]);

        $response->assertRedirect('/locations');
        $this->assertDatabaseHas('saved_locations', [
            'user_id' => $customer->id,
            'label' => 'Home',
            'is_primary' => true,
        ]);
        $this->assertSame(37.7749, (float) $customer->fresh()->latitude);
        $this->assertSame(-122.4194, (float) $customer->fresh()->longitude);
    }

    public function test_location_sharing_is_opt_in_and_admin_can_view_private_saved_pins(): void
    {
        $owner = User::factory()->create(['role' => 'technician']);
        $customer = User::factory()->create(['role' => 'customer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $privatePin = SavedLocation::create([
            'user_id' => $owner->id,
            'label' => 'Private base',
            'address' => 'Private address',
            'latitude' => 37.7749,
            'longitude' => -122.4194,
            'is_primary' => true,
            'share_with_users' => false,
        ]);
        $sharedPin = SavedLocation::create([
            'user_id' => $owner->id,
            'label' => 'Public depot',
            'address' => 'Shared address',
            'latitude' => 37.7849,
            'longitude' => -122.4094,
            'is_primary' => false,
            'share_with_users' => true,
        ]);

        $customerResponse = $this->actingAs($customer)->getJson('/locations/data');
        $customerResponse->assertOk()->assertJsonFragment(['label' => 'Public depot']);
        $customerResponse->assertJsonMissing(['label' => 'Private base']);

        $adminResponse = $this->actingAs($admin)->getJson('/locations/data');
        $adminResponse->assertOk()->assertJsonFragment(['label' => 'Private base']);
        $adminResponse->assertJsonFragment(['label' => 'Public depot']);

        $this->actingAs($admin)->get('/admin/locations')->assertOk();
        $this->actingAs($customer)->get('/admin/locations')->assertForbidden();

        $this->assertNotSame($privatePin->id, $sharedPin->id);
    }

    public function test_live_location_is_visible_while_shared_and_removed_when_sharing_stops(): void
    {
        $technician = User::factory()->create(['role' => 'technician']);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($technician)->putJson('/locations/live', [
            'sharing' => true,
            'latitude' => 37.7749,
            'longitude' => -122.4194,
        ])->assertOk()->assertJson(['sharing' => true]);

        $this->actingAs($customer)->getJson('/locations/data')
            ->assertOk()
            ->assertJsonFragment(['kind' => 'live', 'name' => $technician->name]);

        $this->actingAs($technician)->putJson('/locations/live', ['sharing' => false])
            ->assertOk()->assertJson(['sharing' => false]);

        $this->actingAs($customer)->getJson('/locations/data')
            ->assertOk()
            ->assertJsonMissing(['kind' => 'live', 'name' => $technician->name]);

        $this->assertDatabaseHas('live_locations', [
            'user_id' => $technician->id,
            'is_sharing' => false,
            'latitude' => null,
            'longitude' => null,
        ]);
    }

    public function test_user_cannot_edit_another_users_saved_location(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $otherUser = User::factory()->create(['role' => 'customer']);
        $location = SavedLocation::create([
            'user_id' => $owner->id,
            'label' => 'Home',
            'address' => '12 Solar Way',
            'latitude' => 37.7749,
            'longitude' => -122.4194,
        ]);

        $this->actingAs($otherUser)->patchJson('/locations/' . $location->id, [
            'label' => 'Taken over',
            'address' => 'Other address',
            'latitude' => 38,
            'longitude' => -122,
        ])->assertNotFound();

        $this->assertSame('Home', $location->fresh()->label);
    }

    public function test_area_only_sharing_hides_street_address_and_exact_coordinates_from_other_users(): void
    {
        $owner = User::factory()->create(['role' => 'vendor']);
        $customer = User::factory()->create(['role' => 'customer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($owner)->post('/locations', [
            'label' => 'Shop',
            'address' => '12 Private Solar Way',
            'state' => 'Lagos State',
            'local_government' => 'Eti-Osa',
            'latitude' => 6.4281,
            'longitude' => 3.4219,
            'is_primary' => true,
            'share_with_users' => true,
            'share_detail' => 'admin_area',
        ]);
        $response->assertRedirect();

        $location = SavedLocation::where('user_id', $owner->id)->firstOrFail();
        $this->assertSame('12 Private Solar Way', $location->address);

        $customerResponse = $this->actingAs($customer)->getJson('/locations/data');
        $customerResponse->assertOk()
            ->assertJsonFragment(['state' => 'Lagos State'])
            ->assertJsonFragment(['localGovernment' => 'Eti-Osa'])
            ->assertJsonFragment(['latitude' => null, 'longitude' => null]);
        $this->assertStringNotContainsString('12 Private Solar Way', $customerResponse->getContent());
        $this->assertStringNotContainsString('6.4281', $customerResponse->getContent());

        $this->actingAs($owner)->getJson('/locations/data')
            ->assertOk()
            ->assertJsonFragment(['latitude' => 6.4281, 'longitude' => 3.4219]);

        $this->actingAs($admin)->getJson('/locations/data')
            ->assertOk()
            ->assertJsonFragment(['latitude' => 6.4281, 'longitude' => 3.4219]);
    }

    public function test_setting_a_new_primary_location_unsets_the_previous_primary(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $oldPrimary = SavedLocation::create([
            'user_id' => $customer->id,
            'label' => 'Home',
            'address' => 'Old address',
            'latitude' => 37.7749,
            'longitude' => -122.4194,
            'is_primary' => true,
        ]);
        $newLocation = SavedLocation::create([
            'user_id' => $customer->id,
            'label' => 'Office',
            'address' => 'New address',
            'latitude' => 37.7849,
            'longitude' => -122.4094,
        ]);

        $this->actingAs($customer)->patch('/locations/' . $newLocation->id, [
            'label' => 'Office',
            'address' => 'New address',
            'latitude' => 37.7849,
            'longitude' => -122.4094,
            'is_primary' => true,
            'share_with_users' => false,
        ])->assertRedirect();

        $this->assertFalse($oldPrimary->fresh()->is_primary);
        $this->assertTrue($newLocation->fresh()->is_primary);
        $this->assertSame(37.7849, (float) $customer->fresh()->latitude);
    }

    public function test_customer_dispatch_map_does_not_return_private_or_area_only_technician_coordinates(): void
    {
        $privateProfile = TechnicianProfile::factory()->create();
        $areaProfile = TechnicianProfile::factory()->create();
        $streetProfile = TechnicianProfile::factory()->create();
        $legacyProfile = TechnicianProfile::factory()->create();

        SavedLocation::create([
            'user_id' => $privateProfile->user_id,
            'label' => 'Private base',
            'address' => 'Private address',
            'state' => 'Lagos State',
            'local_government' => 'Eti-Osa',
            'latitude' => 6.5244,
            'longitude' => 3.3792,
            'is_primary' => true,
            'share_with_users' => false,
            'share_detail' => 'street',
        ]);
        SavedLocation::create([
            'user_id' => $areaProfile->user_id,
            'label' => 'Area base',
            'address' => 'Street in LGA',
            'state' => 'Lagos State',
            'local_government' => 'Eti-Osa',
            'latitude' => 6.5244,
            'longitude' => 3.3792,
            'is_primary' => true,
            'share_with_users' => true,
            'share_detail' => 'admin_area',
        ]);
        SavedLocation::create([
            'user_id' => $streetProfile->user_id,
            'label' => 'Shared street base',
            'address' => 'Shared street',
            'state' => 'Lagos State',
            'local_government' => 'Eti-Osa',
            'latitude' => 6.5244,
            'longitude' => 3.3792,
            'is_primary' => true,
            'share_with_users' => true,
            'share_detail' => 'street',
        ]);

        $visibleIds = app(LocationService::class)
            ->nearbyTechnicians(6.5244, 3.3792, 40)
            ->pluck('profile.id')
            ->all();

        $this->assertNotContains($privateProfile->id, $visibleIds);
        $this->assertNotContains($areaProfile->id, $visibleIds);
        $this->assertContains($streetProfile->id, $visibleIds);
        $this->assertContains($legacyProfile->id, $visibleIds);
    }
}
