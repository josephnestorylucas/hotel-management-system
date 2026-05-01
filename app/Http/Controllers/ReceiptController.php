<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\GoodsReceivedNote;
use App\Models\InternalUsageRequest;
use App\Models\LaundryOrder;
use App\Models\LocalPurchaseOrder;
use App\Models\Order;
use App\Models\Receipt;
use App\Models\StockAdjustment;
use App\Models\StockTransfer;
use App\Models\SupplierPayment;
use App\Models\WalkinTransaction;
use App\Services\ReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReceiptController extends Controller
{
    public function __construct(
        protected ReceiptService $receiptService
    ) {}

    /**
     * Show printable receipt by UUID.
     * GET /receipts/{uuid}
     */
    public function show(string $uuid): View|RedirectResponse
    {
        $receipt = $this->receiptService->findByUuid($uuid);
        
        if (!$receipt) {
            abort(404, 'Receipt not found');
        }

        // Authorization: user must have access to the source module
        if (!$this->hasModuleAccess($receipt->module)) {
            return redirect()->route('dashboard')
                ->with('unauthorized', 'You do not have permission to view this receipt.');
        }

        return view('receipts.print', compact('receipt'));
    }

    /**
     * Print receipt for a laundry order (get or create).
     * GET /receipts/laundry/{laundryOrder}
     */
    public function laundry(LaundryOrder $laundryOrder): View|RedirectResponse
    {
        if (!$this->hasModuleAccess('laundry')) {
            return redirect()->route('dashboard')
                ->with('unauthorized', 'You do not have permission to print laundry receipts.');
        }

        $receipt = $this->receiptService->getOrCreateReceipt($laundryOrder);

        return view('receipts.print', compact('receipt'));
    }

    /**
     * Print receipt for a restaurant/bar order (get or create).
     * GET /receipts/order/{order}
     */
    public function order(Order $order): View|RedirectResponse
    {
        $module = $order->location?->slug === 'bar' ? 'bar' : 'restaurant';

        if (!$this->hasModuleAccess($module)) {
            return redirect()->route('dashboard')
                ->with('unauthorized', 'You do not have permission to print this receipt.');
        }

        $receipt = $this->receiptService->getOrCreateReceipt($order);

        return view('receipts.print', compact('receipt'));
    }

    /**
     * Print receipt for a hotel checkout (get or create).
     * GET /receipts/checkout/{checkout}
     */
    public function checkout(Checkout $checkout): View|RedirectResponse
    {
        if (!$this->hasModuleAccess('checkout')) {
            return redirect()->route('dashboard')
                ->with('unauthorized', 'You do not have permission to print checkout receipts.');
        }

        $receipt = $this->receiptService->getOrCreateReceipt($checkout);

        return view('receipts.print', compact('receipt'));
    }

    /**
     * Print receipt for a walk-in transaction (get or create).
     * GET /receipts/walkin/{walkinTransaction}
     */
    public function walkin(WalkinTransaction $walkinTransaction): View|RedirectResponse
    {
        if (!$this->hasModuleAccess('walkin')) {
            return redirect()->route('dashboard')
                ->with('unauthorized', 'You do not have permission to print walk-in receipts.');
        }

        $receipt = $this->receiptService->getOrCreateReceipt($walkinTransaction);

        return view('receipts.print', compact('receipt'));
    }

    /**
     * Reprint receipt by receipt number (same data, increments print count).
     * GET /receipts/reprint/{receiptNumber}
     */
    public function reprint(string $receiptNumber): View|RedirectResponse
    {
        $receipt = $this->receiptService->findByNumber($receiptNumber);

        if (!$receipt) {
            abort(404, 'Receipt not found');
        }

        if (!$this->hasModuleAccess($receipt->module)) {
            return redirect()->route('dashboard')
                ->with('unauthorized', 'You do not have permission to reprint this receipt.');
        }

        // Mark as reprinted
        $this->receiptService->markPrinted($receipt);

        return view('receipts.print', [
            'receipt'   => $receipt,
            'isReprint' => true,
        ]);
    }

    /**
     * Refresh receipt data from source model and show.
     * POST /receipts/{uuid}/refresh
     */
    public function refresh(string $uuid): View|RedirectResponse
    {
        $receipt = $this->receiptService->findByUuid($uuid);

        if (!$receipt) {
            abort(404, 'Receipt not found');
        }

        if (!$this->hasModuleAccess($receipt->module)) {
            return redirect()->route('dashboard')
                ->with('unauthorized', 'You do not have permission to refresh this receipt.');
        }

        // Refresh data from source model
        $receipt = $this->receiptService->refreshReceipt($receipt);

        return view('receipts.print', compact('receipt'));
    }

    /**
     * Print receipt for a purchase order (get or create).
     * GET /receipts/procurement/lpo/{lpo}
     */
    public function lpo(LocalPurchaseOrder $lpo): View|RedirectResponse
    {
        if (!$this->hasModuleAccess('procurement')) {
            return redirect()->route('dashboard')
                ->with('unauthorized', 'You do not have permission to print procurement receipts.');
        }

        $receipt = $this->receiptService->getOrCreateReceipt($lpo);

        $extraFields = [
            __('general.receipt.lpo_number') => $lpo->lpo_number,
            __('general.receipt.supplier')   => $lpo->supplier_name,
            __('general.receipt.status')     => $lpo->status,
        ];

        return view('receipts.print', compact('receipt', 'extraFields'));
    }

    /**
     * Print receipt for a goods received note (get or create).
     * GET /receipts/procurement/grn/{grn}
     */
    public function grn(GoodsReceivedNote $grn): View|RedirectResponse
    {
        if (!$this->hasModuleAccess('procurement')) {
            return redirect()->route('dashboard')
                ->with('unauthorized', 'You do not have permission to print procurement receipts.');
        }

        $receipt = $this->receiptService->getOrCreateReceipt($grn);

        $extraFields = [
            __('general.receipt.grn_number')    => $grn->grn_number,
            __('general.receipt.lpo_number')    => $grn->lpo?->lpo_number,
            __('general.receipt.supplier')      => $grn->supplier_name,
            __('general.receipt.received_date') => $grn->received_date?->format('d M Y'),
        ];

        return view('receipts.print', compact('receipt', 'extraFields'));
    }

    /**
     * Print receipt for a supplier payment (get or create).
     * GET /receipts/procurement/payment/{payment}
     */
    public function supplierPayment(SupplierPayment $payment): View|RedirectResponse
    {
        if (!$this->hasModuleAccess('procurement')) {
            return redirect()->route('dashboard')
                ->with('unauthorized', 'You do not have permission to print procurement receipts.');
        }

        $receipt = $this->receiptService->getOrCreateReceipt($payment);

        $extraFields = [
            __('general.receipt.supplier')      => $payment->supplier?->name,
            __('general.receipt.reference')     => $payment->reference,
            __('general.receipt.payment_date')  => $payment->payment_date?->format('d M Y'),
            __('general.receipt.status')        => $payment->status,
        ];

        return view('receipts.print', compact('receipt', 'extraFields'));
    }

    /**
     * Print receipt for a stock adjustment (get or create).
     * GET /receipts/store/adjustment/{adjustment}
     */
    public function stockAdjustment(StockAdjustment $adjustment): View|RedirectResponse
    {
        if (!$this->hasModuleAccess('store')) {
            return redirect()->route('dashboard')
                ->with('unauthorized', 'You do not have permission to print store receipts.');
        }

        $receipt = $this->receiptService->getOrCreateReceipt($adjustment);

        $extraFields = [
            __('general.receipt.reason')  => $adjustment->reason,
            __('general.receipt.status')  => $adjustment->status,
        ];

        return view('receipts.print', compact('receipt', 'extraFields'));
    }

    /**
     * Print receipt for a stock transfer (get or create).
     * GET /receipts/store/transfer/{transfer}
     */
    public function stockTransfer(StockTransfer $transfer): View|RedirectResponse
    {
        if (!$this->hasModuleAccess('store')) {
            return redirect()->route('dashboard')
                ->with('unauthorized', 'You do not have permission to print store receipts.');
        }

        $receipt = $this->receiptService->getOrCreateReceipt($transfer);

        $extraFields = [
            __('general.receipt.from_location') => $transfer->fromLocation?->name,
            __('general.receipt.to_location')   => $transfer->toLocation?->name,
            __('general.receipt.reason')        => $transfer->reason,
            __('general.receipt.status')        => $transfer->status,
        ];

        return view('receipts.print', compact('receipt', 'extraFields'));
    }

    /**
     * Print receipt for an internal usage request (get or create).
     * GET /receipts/store/internal-request/{request}
     */
    public function internalRequest(InternalUsageRequest $internalRequest): View|RedirectResponse
    {
        if (!$this->hasModuleAccess('store')) {
            return redirect()->route('dashboard')
                ->with('unauthorized', 'You do not have permission to print store receipts.');
        }

        $receipt = $this->receiptService->getOrCreateReceipt($internalRequest);

        $extraFields = [
            __('general.receipt.department') => $internalRequest->department,
            __('general.receipt.reason')     => $internalRequest->reason,
            __('general.receipt.status')     => $internalRequest->status,
        ];

        return view('receipts.print', compact('receipt', 'extraFields'));
    }

    /**
     * Search receipts.
     * GET /receipts/search
     */
    public function search(Request $request): View
    {
        $query = $request->input('q', '');
        $module = $request->input('module');

        $receipts = $this->receiptService->search($query, $module);

        return view('receipts.search', compact('receipts', 'query', 'module'));
    }

    /**
     * Mark receipt as printed (for AJAX calls).
     * POST /receipts/{uuid}/printed
     */
    public function markPrinted(string $uuid): Response
    {
        $receipt = $this->receiptService->findByUuid($uuid);

        if (!$receipt) {
            return response()->json(['error' => 'Receipt not found'], 404);
        }

        if (!$this->hasModuleAccess($receipt->module)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->receiptService->markPrinted($receipt);

        return response()->json([
            'success'     => true,
            'print_count' => $receipt->fresh()->print_count,
        ]);
    }

    /**
     * Get module role mapping.
     * Front desk has access to ALL receipt types — they handle guest checkouts,
     * laundry charges, and restaurant/bar charges on behalf of guests.
     */
    protected function getModuleRoles(): array
    {
        return [
            'laundry'     => ['laundry_manager', 'house_help', 'front_desk', 'supervisor', 'manager', 'admin', 'accountant'],
            'restaurant'  => ['restaurant_manager', 'bar_tender', 'waiter', 'front_desk', 'manager', 'admin', 'accountant'],
            'bar'         => ['restaurant_manager', 'bar_tender', 'waiter', 'front_desk', 'manager', 'admin', 'accountant'],
            'checkout'    => ['front_desk', 'manager', 'admin', 'accountant'],
            'walkin'      => ['front_desk', 'bar_tender', 'restaurant_manager', 'manager', 'admin', 'accountant'],
            'conference'  => ['front_desk', 'supervisor', 'manager', 'admin', 'accountant'],
            'procurement' => ['supervisor', 'manager', 'admin', 'accountant'],
            'store'       => ['supervisor', 'manager', 'admin', 'accountant'],
            'accounting'  => ['manager', 'admin', 'accountant'],
        ];
    }

    /**
     * Check if current user has access to a module.
     */
    protected function hasModuleAccess(string $module): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        $moduleRoles = $this->getModuleRoles();
        $allowedRoles = $moduleRoles[$module] ?? [];
        $userRole = strtolower($user->role?->name ?? '');

        return in_array($userRole, $allowedRoles);
    }
}
