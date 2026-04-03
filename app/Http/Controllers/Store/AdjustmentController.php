<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockLevel;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdjustmentController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    // GET /store/adjustments
    public function index(Request $request): View
    {
        $adjustments = StockAdjustment::with(['product', 'location', 'creator', 'approver'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('store.adjustments.index', compact('adjustments'));
    }

    // GET /store/adjustments/create
    public function create(): View
    {
        $products  = Product::where('is_active', true)->orderBy('name')->get();
        $locations = StockLocation::where('is_active', true)->get();

        return view('store.adjustments.create', compact('products', 'locations'));
    }

    // POST /store/adjustments
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id'   => 'required|uuid|exists:products,id',
            'location_id'  => 'required|uuid|exists:stock_locations,id',
            'new_quantity' => 'required|numeric|min:0',
            'reason'       => 'required|string|min:5|max:500',
        ]);

        $threshold = (int) (DB::table('system_settings')
                              ->where('key', 'adjustment_approval_threshold')
                              ->value('value') ?? 50);

        $currentQty = (float) (StockLevel::where('product_id', $data['product_id'])
                                        ->where('location_id', $data['location_id'])
                                        ->value('quantity') ?? 0);

        $difference    = $data['new_quantity'] - $currentQty;
        $needsApproval = abs($difference) >= $threshold
                      && auth()->user()->role->name !== 'store_manager';

        $adjustment = StockAdjustment::create([
            'product_id'        => $data['product_id'],
            'location_id'       => $data['location_id'],
            'previous_qty'      => $currentQty,
            'new_qty'           => $data['new_quantity'],
            'difference'        => $difference,
            'reason'            => $data['reason'],
            'requires_approval' => $needsApproval,
            'status'            => $needsApproval ? 'pending' : 'applied',
            'created_by'        => auth()->id(),
        ]);

        if ($needsApproval) {
            // Notify store managers
            $managerIds = User::whereHas('role', fn ($q) => $q->where('name', 'store_manager'))
                ->pluck('id')
                ->toArray();

            $this->notificationService->createForUsers($managerIds, [
                'type'           => 'pending_adjustment',
                'title'          => 'Large Adjustment Needs Approval',
                'body'           => abs($difference) . " unit adjustment pending. Reason: {$data['reason']}",
                'reference_type' => 'stock_adjustment',
                'reference_id'   => $adjustment->id,
                'action_url'     => route('store.adjustments.index'),
            ]);

            return redirect()
                ->route('store.adjustments.index')
                ->with('info', 'Adjustment submitted for manager approval.');
        }

        StockMovement::record([
            'product_id'   => $data['product_id'],
            'location_id'  => $data['location_id'],
            'type'         => 'adjustment',
            'new_quantity' => $data['new_quantity'],
            'notes'        => $data['reason'],
            'approved_by'  => auth()->id(),
        ], auth()->id());

        return redirect()
            ->route('store.adjustments.index')
            ->with('success', 'Adjustment applied.');
    }

    // POST /store/adjustments/{adjustment}/approve
    public function approve(StockAdjustment $adjustment): RedirectResponse
    {
        abort_if($adjustment->status !== 'pending', 422, 'Only pending adjustments can be approved.');

        StockMovement::record([
            'product_id'   => $adjustment->product_id,
            'location_id'  => $adjustment->location_id,
            'type'         => 'adjustment',
            'new_quantity' => (float) $adjustment->new_qty,
            'notes'        => $adjustment->reason,
            'approved_by'  => auth()->id(),
        ], auth()->id());

        $adjustment->update(['status' => 'applied', 'approved_by' => auth()->id()]);

        return redirect()
            ->route('store.adjustments.index')
            ->with('success', 'Adjustment approved and applied.');
    }

    // POST /store/adjustments/{adjustment}/reject
    public function reject(StockAdjustment $adjustment): RedirectResponse
    {
        abort_if($adjustment->status !== 'pending', 422, 'Only pending adjustments can be rejected.');

        $adjustment->update(['status' => 'rejected', 'approved_by' => auth()->id()]);

        return redirect()
            ->route('store.adjustments.index')
            ->with('success', 'Adjustment rejected.');
    }
}
