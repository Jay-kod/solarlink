<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\TechnicianProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index()
    {
        $user = Auth::user();

        // Load only the current user's bookings (or all if admin)
        $query = Booking::with(['user', 'technicianProfile.user']);
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $bookings = $query->get()->map(function ($booking) {
            return [
                'id' => $booking->id,
                'customerName' => $booking->user->name,
                'serviceType' => $booking->service_type,
                'technicianName' => $booking->technicianProfile ? $booking->technicianProfile->user->name : 'N/A',
                'technicianAvatar' => $booking->technicianProfile && $booking->technicianProfile->avatar 
                    ? $booking->technicianProfile->avatar 
                    : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150',
                'date' => $booking->date->format('Y-m-d'),
                'time' => $booking->time,
                'status' => $booking->status,
                'cost' => (float)$booking->cost,
                'payment_status' => $booking->payment_status ?? 'unpaid',
                'location' => $booking->location,
                'notes' => $booking->notes,
            ];
        });

        return Inertia::render('Customer/Booking/Index', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        $request->validate([
            'technician_profile_id' => ['required', 'exists:technician_profiles,id'],
            'service_type' => ['required', 'string'],
            'date' => ['required', 'date'],
            'time' => ['required', 'string'],
            'cost' => ['required', 'numeric', 'min:0'],
            'location' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $user = Auth::user();

        Booking::create([
            'user_id' => $user->id,
            'technician_profile_id' => $request->technician_profile_id,
            'service_type' => $request->service_type,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending',
            'cost' => $request->cost,
            'payment_status' => 'unpaid',
            'location' => $request->location,
            'notes' => $request->notes,
        ]);

        return redirect()->route('bookings.index')->with('success', 'Service booking created successfully.');
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Booking $booking)
    {
        $user = Auth::user();
        if ($booking->user_id !== $user->id && $user->role !== 'admin') {
            abort(403);
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Pay for a booking.
     */
    public function pay(Booking $booking)
    {
        $user = Auth::user();
        if ($booking->user_id !== $user->id && $user->role !== 'admin') {
            abort(403);
        }

        $booking->update(['payment_status' => 'paid']);

        return redirect()->back()->with('success', 'Booking invoice paid successfully.');
    }
}
