<?php

namespace App\Http\Controllers;

use App\Models\ProcurementQuote;
use App\Models\ProcurementRequest;
use App\Models\ServiceNotification;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VendorProcurementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $supplier = Supplier::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->vendorProfile?->store_name ?: ($user->name . ' Supply'),
                'address' => $user->vendorProfile?->company_address,
                'contact_email' => $user->email,
                'is_verified' => true,
            ]
        );

        $rfqs = ProcurementRequest::with(['items', 'user', 'quotes' => function ($query) use ($supplier) {
            $query->where('supplier_id', $supplier->id);
        }])
            ->whereIn('status', ['open', 'quoted', 'approved'])
            ->latest()
            ->get()
            ->map(function ($rfq) {
                return [
                    'id' => $rfq->id,
                    'customer' => $rfq->user->name,
                    'title' => $rfq->title,
                    'description' => $rfq->description,
                    'status' => $rfq->status,
                    'items' => $rfq->items,
                    'myQuote' => $rfq->quotes->first(),
                ];
            });

        return Inertia::render('Vendor/RFQs/Index', [
            'rfqs' => $rfqs,
            'supplier' => [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'is_verified' => $supplier->is_verified,
            ],
        ]);
    }

    public function quote(Request $request, ProcurementRequest $procurementRequest)
    {
        $user = $request->user();
        $supplier = Supplier::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'lead_time_days' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        ProcurementQuote::updateOrCreate(
            [
                'procurement_request_id' => $procurementRequest->id,
                'supplier_id' => $supplier->id,
            ],
            [
                'amount' => $validated['amount'],
                'lead_time_days' => $validated['lead_time_days'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'submitted',
            ]
        );

        if ($procurementRequest->status === 'open') {
            $procurementRequest->update(['status' => 'quoted']);
        }

        ServiceNotification::create([
            'user_id' => $procurementRequest->user_id,
            'title' => 'New Supplier Quote Received',
            'body' => 'A supplier quote has been submitted for RFQ #' . $procurementRequest->id . '.',
            'type' => 'info',
            'link' => '/user/procurement',
        ]);

        return redirect()->back()->with('success', 'Quote submitted.');
    }
}
