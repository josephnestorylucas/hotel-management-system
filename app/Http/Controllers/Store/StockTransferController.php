<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockTransferController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    // GET /store/transfers
    public function index(Request $request): View
    {
        $transfers = StockTransfer::with(['product', 'fromLocation', 'toLocation', 'requester', 'approver', 'rejecter', 'fulfiller'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('store.transfers.index', compact('transfers'));
    }

    // GET /store/transfers/create
    public function create(): View
    {
        $products  = Product::where('is_active', true)->orderBy('name')->get();
        $locations = StockLocation::where('code', '!=', 'main_store')->where('is_active', true)->get();

        return view('store.transfers.create', compact('products', 'locations'));
    }

    // POST /store/transfers
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id'       => 'required|uuid|exists:products,id',
            'to_location_code' => 'required|in:bar,kitchen',
            'quantity'         => 'required|numeric|min:0.001',
            'reason'           => 'nullable|string|max:500',
        ]);

        $fromLocation = StockLocation::mainStore();
        $toLocation   = StockLocation::where('code', $data['to_location_code'])->firstOrFail();
        $sourceLevel = StockLevel::where('product_id', $data['product_id'])
            ->where('location_id', $fromLocation->id)
            ->first();

        $available = (float) ($sourceLevel?->available_qty ?? 0);

        if (! $sourceLevel || $available < (float) $data['quantity']) {
            throw ValidationException::withMessages([
                'quantity' => "Insufficient stock at main store. Available: {$available}, requested: {$data['quantity']}.",
            ]);
        }

        $transfer = StockTransfer::create([
            'product_id'       => $data['product_id'],
            'from_location_id' => $fromLocation->id,
            'to_location_id'   => $toLocation->id,
            'quantity'         => $data['quantity'],
            'status'           => 'pending',
            'reason'           => $data['reason'] ?? null,
            'requested_by'     => auth()->id(),
        ]);

        // Notify managers/admins for optional approval visibility.
        $userIds = User::whereHas('role', fn ($q) => $q->whereIn('name', ['manager', 'admin']))
            ->pluck('id')
            ->toArray();

        $this->notificationService->createForUsers($userIds, [
            'type'           => 'pending_transfer',
            'title'          => 'Stock Transfer Requested',
            'body'           => "{$transfer->product->name} × {$transfer->quantity} requested for {$toLocation->name}",
            'reference_type' => 'stock_transfer',
            'reference_id'   => $transfer->id,
            'action_url'     => route('store.transfers.index'),
        ]);

        return redirect()
            ->route('store.transfers.index')
            ->with('success', 'Transfer request submitted.');
    }

    // POST /store/transfers/{stockTransfer}/approve
    public function approve(StockTransfer $stockTransfer): RedirectResponse
    {
        abort_if($stockTransfer->status !== 'pending', 422, 'Only pending transfers can be approved.');

        $sourceLevel = StockLevel::where('product_id', $stockTransfer->product_id)
            ->where('location_id', $stockTransfer->from_location_id)
            ->first();

        abort_if(
            ! $sourceLevel || $sourceLevel->available_qty < $stockTransfer->quantity,
            422,
            'Insufficient stock at main store. Available: ' . ($sourceLevel?->available_qty ?? 0)
        );

        $stockTransfer->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);

        return redirect()
            ->route('store.transfers.index')
            ->with('success', 'Transfer approved and ready for fulfillment.');
    }

    // POST /store/transfers/{stockTransfer}/fulfill
    public function fulfill(StockTransfer $stockTransfer): RedirectResponse
    {
        abort_if(
            ! in_array($stockTransfer->status, ['pending', 'approved'], true),
            422,
            'Only pending or approved transfers can be completed.'
        );

        $sourceLevel = StockLevel::where('product_id', $stockTransfer->product_id)
                                 ->where('location_id', $stockTransfer->from_location_id)
                                 ->first();

        abort_if(
            ! $sourceLevel || $sourceLevel->available_qty < $stockTransfer->quantity,
            422,
            'Insufficient stock at main store. Available: ' . ($sourceLevel?->available_qty ?? 0)
        );

        DB::transaction(function () use ($stockTransfer) {
            StockLevel::firstOrCreate(
                [
                    'product_id' => $stockTransfer->product_id,
                    'location_id' => $stockTransfer->to_location_id,
                ],
                [
                    'quantity' => 0,
                    'reserved_qty' => 0,
                ]
            );

            StockMovement::record([
                'product_id'     => $stockTransfer->product_id,
                'location_id'    => $stockTransfer->from_location_id,
                'type'           => 'transfer_out',
                'quantity'       => $stockTransfer->quantity,
                'reference_type' => 'transfer',
                'reference_id'   => $stockTransfer->id,
                'notes'          => "Transfer out to {$stockTransfer->toLocation->name}",
            ], auth()->id());

            StockMovement::record([
                'product_id'     => $stockTransfer->product_id,
                'location_id'    => $stockTransfer->to_location_id,
                'type'           => 'transfer_in',
                'quantity'       => $stockTransfer->quantity,
                'reference_type' => 'transfer',
                'reference_id'   => $stockTransfer->id,
                'notes'          => "Transfer in from {$stockTransfer->fromLocation->name}",
            ], auth()->id());

            $stockTransfer->update([
                'status'       => 'completed',
                'approved_by'  => $stockTransfer->approved_by ?? auth()->id(),
                'approved_at'  => $stockTransfer->approved_at ?? now(),
                'fulfilled_by' => auth()->id(),
                'completed_at' => now(),
            ]);
        });

        return redirect()
            ->route('store.transfers.index')
            ->with('success', 'Transfer completed. Stock moved.');
    }

    // POST /store/transfers/{stockTransfer}/reject
    public function reject(Request $request, StockTransfer $stockTransfer): RedirectResponse
    {
        abort_if(! in_array($stockTransfer->status, ['pending', 'approved']), 422, 'Only pending or approved transfers can be rejected.');

        $data = $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ]);

        $stockTransfer->update([
            'status' => 'rejected',
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        return redirect()
            ->route('store.transfers.index')
            ->with('success', 'Transfer rejected.');
    }
}
