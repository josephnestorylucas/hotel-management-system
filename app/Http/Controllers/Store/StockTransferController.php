<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StoreNotification;
use App\Models\StockLevel;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockTransferController extends Controller
{
    // GET /store/transfers
    public function index(Request $request): View
    {
        $transfers = StockTransfer::with(['product', 'fromLocation', 'toLocation', 'requester'])
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

        $transfer = StockTransfer::create([
            'product_id'       => $data['product_id'],
            'from_location_id' => $fromLocation->id,
            'to_location_id'   => $toLocation->id,
            'quantity'         => $data['quantity'],
            'status'           => 'pending',
            'reason'           => $data['reason'] ?? null,
            'requested_by'     => auth()->id(),
        ]);

        User::whereHas('role', fn ($q) => $q->whereIn('name', ['store_keeper', 'store_manager']))
            ->get()
            ->each(fn ($m) => StoreNotification::create([
                'user_id'        => $m->id,
                'type'           => 'pending_transfer',
                'title'          => 'Stock Transfer Requested',
                'body'           => "{$transfer->product->name} × {$transfer->quantity} requested for {$toLocation->name}",
                'reference_type' => 'stock_transfer',
                'reference_id'   => $transfer->id,
                'action_url'     => route('store.transfers.index'),
                'created_at'     => now(),
            ]));

        return redirect()
            ->route('store.transfers.index')
            ->with('success', 'Transfer request submitted.');
    }

    // POST /store/transfers/{stockTransfer}/fulfill
    public function fulfill(StockTransfer $stockTransfer): RedirectResponse
    {
        abort_if(
            ! in_array($stockTransfer->status, ['pending', 'approved']),
            422,
            'Only pending or approved transfers can be fulfilled.'
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
                'fulfilled_by' => auth()->id(),
                'completed_at' => now(),
            ]);
        });

        return redirect()
            ->route('store.transfers.index')
            ->with('success', 'Transfer completed. Stock moved.');
    }

    // POST /store/transfers/{stockTransfer}/reject
    public function reject(StockTransfer $stockTransfer): RedirectResponse
    {
        abort_if($stockTransfer->status !== 'pending', 422, 'Only pending transfers can be rejected.');

        $stockTransfer->update(['status' => 'rejected']);

        return redirect()
            ->route('store.transfers.index')
            ->with('success', 'Transfer rejected.');
    }
}
