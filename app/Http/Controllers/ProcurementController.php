<?php

namespace App\Http\Controllers;

use App\Models\ProcurementQuote;
use App\Models\ProcurementRequest;
use App\Models\ServiceNotification;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProcurementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $suppliers = Supplier::with('user')
            ->where('is_verified', true)
            ->get()
            ->map(fn ($supplier) => [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'address' => $supplier->address,
                'contact' => $supplier->contact_email,
            ]);

        $rfqs = ProcurementRequest::with(['items', 'quotes.supplier'])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($rfq) {
                return [
                    'id' => $rfq->id,
                    'title' => $rfq->title,
                    'description' => $rfq->description,
                    'status' => $rfq->status,
                    'items' => $rfq->items,
                    'quotes' => $rfq->quotes->map(fn ($quote) => [
                        'id' => $quote->id,
                        'supplier' => $quote->supplier->name,
                        'amount' => (float) $quote->amount,
                        'lead_time_days' => $quote->lead_time_days,
                        'notes' => $quote->notes,
                        'status' => $quote->status,
                    ]),
                ];
            });

        return Inertia::render('Customer/Procurement/Index', [
            'suppliers' => $suppliers,
            'rfqs' => $rfqs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'part_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $rfq = ProcurementRequest::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        $rfq->items()->create([
            'part_name' => $validated['part_name'],
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $vendors = Supplier::where('is_verified', true)->pluck('user_id');
        foreach ($vendors as $vendorUserId) {
            ServiceNotification::create([
                'user_id' => $vendorUserId,
                'title' => 'New RFQ Available',
                'body' => 'RFQ #' . $rfq->id . ' is open for supplier quotes.',
                'type' => 'info',
                'link' => '/vendor/rfqs',
            ]);
        }

        return redirect()->route('procurement.index')->with('success', 'RFQ created successfully.');
    }

    public function approveQuote(ProcurementQuote $quote)
    {
        $rfq = $quote->procurementRequest;
        $user = auth()->user();

        if ($rfq->user_id !== $user->id) {
            abort(403);
        }

        $rfq->quotes()->where('id', '!=', $quote->id)->update(['status' => 'rejected']);
        $quote->update(['status' => 'selected']);

        $rfq->update([
            'status' => 'approved',
            'selected_quote_id' => $quote->id,
        ]);

        ServiceNotification::create([
            'user_id' => $quote->supplier->user_id,
            'title' => 'Quote Approved',
            'body' => 'Your quote for RFQ #' . $rfq->id . ' has been approved by the customer.',
            'type' => 'success',
            'link' => '/vendor/rfqs',
        ]);

        return redirect()->back()->with('success', 'Quote approved.');
    }
}
