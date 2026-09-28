<?php

namespace App\Http\Controllers;

use App\Models\LiveLocation;
use App\Models\SavedLocation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class LocationController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Locations/Index', [
            'locations' => $this->visibleLocations($user),
            'savedLocations' => $user->savedLocations()->orderByDesc('is_primary')->orderBy('label')->get(),
            'sharingEnabled' => (bool) $user->liveLocation?->is_sharing,
            'isAdmin' => $user->role === 'admin',
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        return response()->json([
            'locations' => $this->visibleLocations($request->user()),
            'updatedAt' => now()->toIso8601String(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedLocation($request);

        DB::transaction(function () use ($request, &$data) {
            if ($data['is_primary']) {
                $request->user()->savedLocations()->update(['is_primary' => false]);
            }
            $location = $request->user()->savedLocations()->create($data);
            if ($location->is_primary) {
                $this->syncPrimaryLocation($request->user(), $location);
            }
        });

        return back()->with('success', 'Location saved.');
    }

    public function update(Request $request, SavedLocation $savedLocation): RedirectResponse
    {
        abort_unless($savedLocation->user_id === $request->user()->id, 404);
        $data = $this->validatedLocation($request);

        DB::transaction(function () use ($request, $savedLocation, $data) {
            if ($data['is_primary']) {
                $request->user()->savedLocations()->whereKeyNot($savedLocation->id)->update(['is_primary' => false]);
            }
            $savedLocation->update($data);
            if ($savedLocation->is_primary) {
                $this->syncPrimaryLocation($request->user(), $savedLocation);
            }
        });

        return back()->with('success', 'Location updated.');
    }

    public function destroy(Request $request, SavedLocation $savedLocation): RedirectResponse
    {
        abort_unless($savedLocation->user_id === $request->user()->id, 404);
        $wasPrimary = $savedLocation->is_primary;
        $savedLocation->delete();
        if ($wasPrimary) {
            $request->user()->forceFill(['latitude' => null, 'longitude' => null, 'location' => null])->save();
            if ($request->user()->role === 'technician') {
                $request->user()->technicianProfile()?->update(['latitude' => null, 'longitude' => null]);
            } elseif ($request->user()->role === 'vendor') {
                $request->user()->vendorProfile()?->update(['latitude' => null, 'longitude' => null]);
            }
        }

        return back()->with('success', 'Location removed.');
    }

    public function updateLive(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sharing' => ['required', 'boolean'],
            'latitude' => ['required_if:sharing,true', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['required_if:sharing,true', 'nullable', 'numeric', 'between:-180,180'],
        ]);

        $sharing = (bool) $data['sharing'];
        $liveLocation = LiveLocation::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'latitude' => $sharing ? $data['latitude'] : null,
                'longitude' => $sharing ? $data['longitude'] : null,
                'is_sharing' => $sharing,
                'last_seen_at' => $sharing ? now() : null,
            ]
        );

        return response()->json([
            'sharing' => $liveLocation->is_sharing,
            'lastSeenAt' => $liveLocation->last_seen_at?->toIso8601String(),
        ]);
    }

    private function validatedLocation(Request $request): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'address' => ['nullable', 'required_if:share_detail,street', 'string', 'max:255'],
            'state' => ['nullable', 'required_if:share_detail,admin_area', 'string', 'max:120'],
            'local_government' => ['nullable', 'required_if:share_detail,admin_area', 'string', 'max:120'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'is_primary' => ['sometimes', 'boolean'],
            'share_with_users' => ['sometimes', 'boolean'],
            'share_detail' => ['sometimes', 'in:street,admin_area'],
        ]);

        return array_merge($data, [
            'address' => $data['address'] ?? '',
            'is_primary' => $request->boolean('is_primary'),
            'share_with_users' => $request->boolean('share_with_users'),
            'share_detail' => $request->input('share_detail', 'street'),
        ]);
    }

    private function syncPrimaryLocation(User $user, SavedLocation $location): void
    {
        if (Schema::hasColumn('users', 'latitude')
            && Schema::hasColumn('users', 'longitude')
            && Schema::hasColumn('users', 'location')) {
            $user->forceFill([
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'location' => $location->address,
            ])->save();
        }

        if ($user->role === 'technician') {
            $profile = $user->technicianProfile;
            if ($profile && Schema::hasColumn('technician_profiles', 'latitude') && Schema::hasColumn('technician_profiles', 'longitude')) {
                $profile->forceFill(['latitude' => $location->latitude, 'longitude' => $location->longitude])->save();
            }
        } elseif ($user->role === 'vendor') {
            $profile = $user->vendorProfile;
            if (!$profile) return;

            $attributes = [
                'company_address' => $location->address,
            ];
            if (Schema::hasColumn('vendor_profiles', 'latitude') && Schema::hasColumn('vendor_profiles', 'longitude')) {
                $attributes['latitude'] = $location->latitude;
                $attributes['longitude'] = $location->longitude;
            }
            $profile->forceFill($attributes)->save();
        }
    }

    private function visibleLocations(User $viewer): array
    {
        $isAdmin = $viewer->role === 'admin';
        $saved = SavedLocation::query()
            ->with('user:id,name,role')
            ->when(!$isAdmin, fn ($query) => $query->where(function ($query) use ($viewer) {
                $query->where('user_id', $viewer->id)->orWhere('share_with_users', true);
            }))
            ->get()
            ->map(function (SavedLocation $location) use ($isAdmin, $viewer) {
                $canSeeExact = $isAdmin || $location->user_id === $viewer->id || $location->share_detail === 'street';

                return [
                    'id' => 'saved-' . $location->id,
                    'userId' => $location->user_id,
                    'name' => $location->user?->name,
                    'role' => $location->user?->role,
                    'label' => $location->label,
                    'address' => $canSeeExact ? $location->address : null,
                    'state' => $location->state,
                    'localGovernment' => $location->local_government,
                    'latitude' => $canSeeExact ? $location->latitude : null,
                    'longitude' => $canSeeExact ? $location->longitude : null,
                    'kind' => 'saved',
                    'sharing' => $location->share_with_users,
                    'shareDetail' => $location->share_detail,
                    'updatedAt' => $location->updated_at?->toIso8601String(),
                ];
            });

        $live = LiveLocation::query()
            ->with('user:id,name,role')
            ->where('is_sharing', true)
            ->whereNotNull('latitude')
            ->where('last_seen_at', '>=', now()->subMinutes(2))
            ->when(!$isAdmin, fn ($query) => $query->where('user_id', '!=', $viewer->id))
            ->get()
            ->map(fn (LiveLocation $location) => [
                'id' => 'live-' . $location->user_id,
                'userId' => $location->user_id,
                'name' => $location->user?->name,
                'role' => $location->user?->role,
                'label' => 'Live location',
                'address' => null,
                'state' => null,
                'localGovernment' => null,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'kind' => 'live',
                'sharing' => true,
                'shareDetail' => 'street',
                'updatedAt' => $location->last_seen_at?->toIso8601String(),
            ]);

        $locations = $saved->concat($live);
        $origin = $viewer->savedLocations()->where('is_primary', true)->first();
        $liveOrigin = $viewer->liveLocation;
        $originLatitude = $liveOrigin?->is_sharing
            ? $liveOrigin->latitude
            : ($origin?->latitude ?? $viewer->latitude);
        $originLongitude = $liveOrigin?->is_sharing
            ? $liveOrigin->longitude
            : ($origin?->longitude ?? $viewer->longitude);

        return $locations->map(function (array $location) use ($originLatitude, $originLongitude) {
            $location['distanceKm'] = $location['latitude'] !== null && $originLatitude !== null && $originLongitude !== null
                ? round($this->distanceKm((float) $originLatitude, (float) $originLongitude, $location['latitude'], $location['longitude']), 2)
                : null;
            return $location;
        })->values()->all();
    }

    private function distanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
