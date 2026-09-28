<?php

namespace App\Services;

use App\Models\TechnicianProfile;
use App\Models\User;
use App\Models\VendorProfile;

class LocationService
{
    public function nearbyTechnicians(float $latitude, float $longitude, float $radiusKm = 30.0, array $filters = []): \Illuminate\Support\Collection
    {
        $results = TechnicianProfile::query()
            ->with('user')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereDoesntHave('user.savedLocations', function ($query) {
                $query->where('is_primary', true)
                    ->where(function ($query) {
                        $query->where('share_with_users', false)
                            ->orWhere('share_detail', 'admin_area');
                    });
            })
            ->where(function ($query) {
                $query->where('verification_status', 'verified')->orWhere('verification_status', null);
            })
            ->get()
            ->filter(fn ($profile) => $profile->user)
            ->map(function ($profile) use ($latitude, $longitude) {
                $distance = $this->distanceKm($latitude, $longitude, (float) $profile->latitude, (float) $profile->longitude);

                return [
                    'profile' => $profile,
                    'distance' => $distance,
                ];
            })
            ->filter(fn ($row) => $row['distance'] <= $radiusKm)
            ->values();

        if (!empty($filters['skill'])) {
            $skill = strtolower((string) $filters['skill']);
            $results = $results->filter(function ($row) use ($skill) {
                $skills = array_map('strtolower', (array) ($row['profile']->skills ?? []));
                return in_array($skill, $skills, true) || str_contains(strtolower($row['profile']->cert_name ?? ''), $skill);
            });
        }

        if (!empty($filters['availability'])) {
            $results = $results->filter(function ($row) use ($filters) {
                $status = strtolower($row['profile']->status ?? 'offline');
                return $status === strtolower((string) $filters['availability']);
            });
        }

        if (!empty($filters['verification_status'])) {
            $status = strtolower((string) $filters['verification_status']);
            $results = $results->filter(fn ($row) => strtolower((string) ($row['profile']->verification_status ?? 'pending')) === $status);
        }

        return $results->sortBy('distance')->values();
    }

    public function nearbyVendors(float $latitude, float $longitude, float $radiusKm = 40.0): \Illuminate\Support\Collection
    {
        return VendorProfile::query()
            ->with('user')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where(function ($query) {
                $query->where('verification_status', 'verified')->orWhere('verification_status', null);
            })
            ->get()
            ->filter(fn ($profile) => $profile->user)
            ->map(function ($profile) use ($latitude, $longitude) {
                return [
                    'profile' => $profile,
                    'distance' => $this->distanceKm($latitude, $longitude, (float) $profile->latitude, (float) $profile->longitude),
                ];
            })
            ->filter(fn ($row) => $row['distance'] <= $radiusKm)
            ->sortBy('distance')
            ->values();
    }

    public function distanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
