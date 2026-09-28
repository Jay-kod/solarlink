<?php

namespace App\Http\Controllers;

use App\Models\TechnicianProfile;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MapController extends Controller
{
    public function index(Request $request, LocationService $locationService)
    {
        $user = $request->user();
        $lat = $request->filled('lat') ? (float) $request->input('lat') : ($user?->latitude ?? 6.5244);
        $lng = $request->filled('lng') ? (float) $request->input('lng') : ($user?->longitude ?? 3.3792);
        $query = trim((string) $request->input('q', ''));

        $technicians = $locationService->nearbyTechnicians($lat, $lng, 40.0, ['skill' => $query ?: null])
            ->map(function ($entry) {
                $profile = $entry['profile'];
                $user = $profile->user;

                return [
                    'id' => (string) $profile->id,
                    'name' => $user?->name,
                    'avatar' => $profile->avatar ?: $user?->avatar,
                    'status' => $profile->status ?: 'offline',
                    'rating' => (float) ($profile->rating ?? 0),
                    'reviewCount' => (int) ($profile->review_count ?? 0),
                    'skills' => $profile->skills ?: [],
                    'pricePerHour' => (float) ($profile->hourly_rate ?? 0),
                    'eta' => $profile->eta ?: 'N/A',
                    'experience' => $profile->experience ?: 'N/A',
                    'lat' => (float) ($profile->latitude ?? $profile->lat ?? 0),
                    'lng' => (float) ($profile->longitude ?? $profile->lng ?? 0),
                    'distanceKm' => round($entry['distance'], 2),
                    'distance' => number_format($entry['distance'], 2) . ' km',
                ];
            })
            ->when($query !== '', fn ($collection) => $collection->filter(function ($tech) use ($query) {
                $hay = strtolower(($tech['name'] ?? '') . ' ' . implode(' ', (array) ($tech['skills'] ?? [])));
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
}
