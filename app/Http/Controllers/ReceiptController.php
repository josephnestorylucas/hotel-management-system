<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\LaundryOrder;
use App\Models\Order;
use App\Models\Receipt;
use App\Models\WalkinTransaction;
use App\Services\ReceiptService;
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
    public function show(string $uuid): View
    {
        $receipt = $this->receiptService->findByUuid($uuid);
        
        if (!$receipt) {
            abort(404, 'Receipt not found');
        }

        // Authorization: user must have access to the source module
        $this->authorizeReceiptAccess($receipt);

        return view('receipts.print', compact('receipt'));
    }

    /**
     * Print receipt for a laundry order (get or create).
     * GET /receipts/laundry/{laundryOrder}
     */
    public function laundry(LaundryOrder $laundryOrder): View
    {
        $this->authorizeModuleAccess('laundry');

        $receipt = $this->receiptService->getOrCreateReceipt($laundryOrder);

        return view('receipts.print', compact('receipt'));
    }

    /**
     * Print receipt for a restaurant/bar order (get or create).
     * GET /receipts/order/{order}
     */
    public function order(Order $order): View
    {
        $module = $order->location?->slug === 'bar' ? 'bar' : 'restaurant';
        $this->authorizeModuleAccess($module);

        $receipt = $this->receiptService->getOrCreateReceipt($order);

        return view('receipts.print', compact('receipt'));
    }

    /**
     * Print receipt for a hotel checkout (get or create).
     * GET /receipts/checkout/{checkout}
     */
    public function checkout(Checkout $checkout): View
    {
        $this->authorizeModuleAccess('checkout');

        $receipt = $this->receiptService->getOrCreateReceipt($checkout);

        return view('receipts.print', compact('receipt'));
    }

    /**
     * Print receipt for a walk-in transaction (get or create).
     * GET /receipts/walkin/{walkinTransaction}
     */
    public function walkin(WalkinTransaction $walkinTransaction): View
    {
        $this->authorizeModuleAccess('walkin');

        $receipt = $this->receiptService->getOrCreateReceipt($walkinTransaction);

        return view('receipts.print', compact('receipt'));
    }

    /**
     * Reprint receipt by receipt number (same data, increments print count).
     * GET /receipts/reprint/{receiptNumber}
     */
    public function reprint(string $receiptNumber): View
    {
        $receipt = $this->receiptService->findByNumber($receiptNumber);

        if (!$receipt) {
            abort(404, 'Receipt not found');
        }

        $this->authorizeReceiptAccess($receipt);

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
    public function refresh(string $uuid): View
    {
        $receipt = $this->receiptService->findByUuid($uuid);

        if (!$receipt) {
            abort(404, 'Receipt not found');
        }

        $this->authorizeReceiptAccess($receipt);

        // Refresh data from source model
        $receipt = $this->receiptService->refreshReceipt($receipt);

        return view('receipts.print', compact('receipt'));
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

        $this->authorizeReceiptAccess($receipt);

        $this->receiptService->markPrinted($receipt);

        return response()->json([
            'success'     => true,
            'print_count' => $receipt->fresh()->print_count,
        ]);
    }

    /**
     * Get module role mapping.
     */
    protected function getModuleRoles(): array
    {
        return [
            'laundry'    => ['laundry_manager', 'house_help', 'front_desk', 'supervisor', 'manager', 'cashier', 'admin'],
            'restaurant' => ['restaurant_manager', 'bar_tender', 'cashier', 'waiter', 'manager', 'admin'],
            'bar'        => ['restaurant_manager', 'bar_tender', 'cashier', 'waiter', 'manager', 'admin'],
            'checkout'   => ['front_desk', 'cashier', 'manager', 'admin'],
            'walkin'     => ['cashier', 'front_desk', 'bar_tender', 'restaurant_manager', 'manager', 'admin'],
            'conference' => ['front_desk', 'supervisor', 'manager', 'admin'],
        ];
    }

    /**
     * Authorize access to a specific module.
     */
    protected function authorizeModuleAccess(string $module): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $moduleRoles = $this->getModuleRoles();
        $allowedRoles = $moduleRoles[$module] ?? [];
        $userRole = strtolower($user->role?->slug ?? '');

        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'You do not have permission to access this module');
        }
    }

    /**
     * Authorize access to a receipt based on its module.
     */
    protected function authorizeReceiptAccess(Receipt $receipt): void
    {
        $this->authorizeModuleAccess($receipt->module);
    }
}
