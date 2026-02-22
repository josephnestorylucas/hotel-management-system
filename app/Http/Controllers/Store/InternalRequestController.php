<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\InternalUsageRequest;
use App\Models\Product;
use App\Models\StoreNotification;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InternalRequestController extends Controller
{
    // GET /store/internal-requests
    public function index(Request $request): View
    {
        $user = auth()->user();
        $role = $user->role->name;

        $requests = InternalUsageRequest::with(['product', 'requester', 'approver', 'fulfiller'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($role === 'house_help', fn ($q) => $q->where('requested_by', $user->id))
            ->latest()
            ->paginate(20);

        return view('store.internal-requests.index', compact('requests'));
    }

    // GET /store/internal-requests/create
    public function create(): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('store.internal-requests.create', compact('products'));
    }

    // POST /store/internal-requests
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|uuid|exists:products,id',
            'quantity'   => 'required|numeric|min:0.001',
            'department' => 'required|string|max:100',
            'reason'     => 'nullable|string|max:500',
        ]);

        $req = InternalUsageRequest::create([
            ...$data,
            'status'       => 'pending',
            'requested_by' => auth()->id(),
        ]);

        User::whereHas('role', fn ($q) => $q->where('name', 'supervisor'))
            ->get()
            ->each(fn ($sup) => StoreNotification::create([
                'user_id'        => $sup->id,
                'type'           => 'pending_request',
                'title'          => 'New Internal Usage Request',
                'body'           => "{$req->department} needs: {$req->product->name} × {$req->quantity} {$req->product->unit}",
                'reference_type' => 'internal_usage_request',
                'reference_id'   => $req->id,
                'action_url'     => route('store.internal-requests.index'),
                'created_at'     => now(),
            ]));

        return redirect()
            ->route('store.internal-requests.index')
            ->with('success', 'Request submitted successfully.');
    }

    // POST /store/internal-requests/{internalUsageRequest}/approve
    public function approve(InternalUsageRequest $internalUsageRequest): RedirectResponse
    {
        abort_if($internalUsageRequest->status !== 'pending', 422, 'Only pending requests can be approved.');

        $internalUsageRequest->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        User::whereHas('role', fn ($q) => $q->where('name', 'store_keeper'))
            ->get()
            ->each(fn ($k) => StoreNotification::create([
                'user_id'        => $k->id,
                'type'           => 'request_approved',
                'title'          => 'Request Ready to Fulfill',
                'body'           => "{$internalUsageRequest->product->name} × {$internalUsageRequest->quantity} for {$internalUsageRequest->department}",
                'reference_type' => 'internal_usage_request',
                'reference_id'   => $internalUsageRequest->id,
                'action_url'     => route('store.internal-requests.index'),
                'created_at'     => now(),
            ]));

        return redirect()
            ->route('store.internal-requests.index')
            ->with('success', 'Request approved.');
    }

    // POST /store/internal-requests/{internalUsageRequest}/reject
    public function reject(Request $request, InternalUsageRequest $internalUsageRequest): RedirectResponse
    {
        abort_if($internalUsageRequest->status !== 'pending', 422, 'Only pending requests can be rejected.');

        $request->validate(['reason' => 'required|string|max:500']);

        $internalUsageRequest->update([
            'status'          => 'rejected',
            'approved_by'     => auth()->id(),
            'rejected_reason' => $request->reason,
        ]);

        StoreNotification::create([
            'user_id'        => $internalUsageRequest->requested_by,
            'type'           => 'request_rejected',
            'title'          => 'Your Request Was Rejected',
            'body'           => "Request for {$internalUsageRequest->product->name} rejected. Reason: {$request->reason}",
            'reference_type' => 'internal_usage_request',
            'reference_id'   => $internalUsageRequest->id,
            'action_url'     => route('store.internal-requests.index'),
            'created_at'     => now(),
        ]);

        return redirect()
            ->route('store.internal-requests.index')
            ->with('success', 'Request rejected.');
    }

    // POST /store/internal-requests/{internalUsageRequest}/fulfill
    public function fulfill(InternalUsageRequest $internalUsageRequest): RedirectResponse
    {
        // HARD RULE: Cannot fulfill without supervisor approval
        abort_if(
            $internalUsageRequest->status !== 'approved',
            422,
            'Request must be approved by a Supervisor before fulfillment.'
        );

        DB::transaction(function () use ($internalUsageRequest) {
            StockMovement::record([
                'product_id'     => $internalUsageRequest->product_id,
                'location_id'    => StockLocation::mainStore()->id,
                'type'           => 'internal_use',
                'quantity'       => $internalUsageRequest->quantity,
                'reference_type' => 'internal_request',
                'reference_id'   => $internalUsageRequest->id,
                'approved_by'    => $internalUsageRequest->approved_by,
                'notes'          => "Dispatched to {$internalUsageRequest->department}",
            ], auth()->id());

            $internalUsageRequest->update([
                'status'       => 'fulfilled',
                'fulfilled_by' => auth()->id(),
                'fulfilled_at' => now(),
            ]);
        });

        StoreNotification::create([
            'user_id'        => $internalUsageRequest->requested_by,
            'type'           => 'request_fulfilled',
            'title'          => 'Your Request Has Been Fulfilled',
            'body'           => "{$internalUsageRequest->product->name} dispatched to {$internalUsageRequest->department}.",
            'reference_type' => 'internal_usage_request',
            'reference_id'   => $internalUsageRequest->id,
            'action_url'     => route('store.internal-requests.index'),
            'created_at'     => now(),
        ]);

        return redirect()
            ->route('store.internal-requests.index')
            ->with('success', 'Request fulfilled. Stock updated.');
    }

    // POST /store/internal-requests/{internalUsageRequest}/cancel
    public function cancel(InternalUsageRequest $internalUsageRequest): RedirectResponse
    {
        abort_if($internalUsageRequest->requested_by !== auth()->id(), 403);
        abort_if($internalUsageRequest->status !== 'pending', 422, 'Only pending requests can be cancelled.');

        $internalUsageRequest->update(['status' => 'cancelled']);

        return redirect()
            ->route('store.internal-requests.index')
            ->with('success', 'Request cancelled.');
    }
}
