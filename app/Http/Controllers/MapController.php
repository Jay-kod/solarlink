<?php

namespace App\Http\Controllers;

use App\Models\TechnicianProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MapController extends Controller
{
    public function index(Request $request)
    {
        $lat = $request->filled('lat') ? (float) $request->input('lat') : 37.7749;
        $lng = $request->filled('lng') ? (float) $request->input('lng') : -122.4194;
        $query = trim((string) $request->input('q', ''));

        $profiles = TechnicianProfile::with('user')->get();

        $technicians = $profiles
            ->filter(fn ($profile) => $profile->user)
            ->map(function ($profile) use ($lat, $lng) {
                $distanceKm = $this->haversine($lat, $lng, (float) $profile->lat, (float) $profile->lng);

                return [
                    'id' => (string) $profile->id,
                    'name' => $profile->user->name,
                    'avatar' => $profile->avatar ?: $profile->user->avatar,
                    'status' => $profile->status ?: 'offline',
                    'rating' => (float) $profile->rating,
                    'reviewCount' => (int) $profile->review_count,
                    'skills' => $profile->skills ?: [],
                    'pricePerHour' => (float) ($profile->hourly_rate ?? 0),
                    'eta' => $profile->eta ?: 'N/A',
                    'experience' => $profile->experience ?: 'N/A',
                    'lat' => (float) $profile->lat,
                    'lng' => (float) $profile->lng,
                    'distanceKm' => round($distanceKm, 2),
                    'distance' => number_format($distanceKm, 2) . ' km',
                ];
            })
            ->when($query !== '', fn ($collection) => $collection->filter(function ($tech) use ($query) {
                $hay = strtolower($tech['name'] . ' ' . implode(' ', $tech['skills']));
                return str_contains($hay, strtolower($query));
            }))
            ->sortBy('distanceKm')
            ->values();

        return Inertia::render('Customer/Map', [
            'technicians' => $technicians,
            'search' => [
                'lat' => $lat,
                'lng' => $lng,
                'q' => $query,
            ],
        ]);
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
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
