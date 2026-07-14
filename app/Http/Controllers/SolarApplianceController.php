<?php

namespace App\Http\Controllers;

use App\Models\SolarAppliance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SolarApplianceController extends Controller
{
    /**
     * Display a listing of the user's solar appliances.
     */
    public function index()
    {
        $user = Auth::user();
        
        $appliances = $user->solarAppliances()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'name' => $app->name,
                    'type' => $app->type,
                    'brand' => $app->brand,
                    'model' => $app->model,
                    'capacity' => $app->capacity,
                    'install_date' => $app->install_date ? $app->install_date->format('Y-m-d') : null,
                    'serial_number' => $app->serial_number,
                    'status' => $app->status,
                    'notes' => $app->notes,
                ];
            });

        return Inertia::render('Customer/Appliances/Index', [
            'appliances' => $appliances,
        ]);
    }

    /**
     * Store a newly registered solar appliance.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:panel,inverter,battery,other'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'capacity' => ['nullable', 'string', 'max:255'],
            'install_date' => ['nullable', 'date'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,standby,offline'],
            'notes' => ['nullable', 'string'],
        ]);

        $user = Auth::user();

        $user->solarAppliances()->create([
            'name' => $request->name,
            'type' => $request->type,
            'brand' => $request->brand,
            'model' => $request->model,
            'capacity' => $request->capacity,
            'install_date' => $request->install_date,
            'serial_number' => $request->serial_number,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('appliances.index')->with('success', 'Solar appliance registered successfully.');
    }

    /**
     * Remove the specified solar appliance.
     */
    public function destroy(SolarAppliance $appliance)
    {
        $user = Auth::user();
        if ($appliance->user_id !== $user->id) {
            abort(403);
        }

        $appliance->delete();

        return redirect()->route('appliances.index')->with('success', 'Solar appliance unregisters successfully.');
    }
}
