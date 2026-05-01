<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use App\Models\Order;
use App\Services\ReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function __construct(
        protected ReceiptService $receiptService
    ) {}

    /**
     * GET /finance/receipts/guest/{checkout}
     * Redirects to unified receipt system.
     */
    public function guest(Checkout $checkout): RedirectResponse
    {
        // Ensure a receipt exists for this checkout
        $receipt = $this->receiptService->getOrCreateReceipt($checkout);

        return redirect()->route('receipts.show', $receipt->uuid);
    }

    /**
     * GET /finance/receipts/walkin?order_id=...
     * Redirects to unified receipt system.
     */
    public function walkin(Request $request): RedirectResponse
    {
        $orderId = $request->order_id;
        $order   = Order::findOrFail($orderId);

        // Ensure a receipt exists for this order
        $receipt = $this->receiptService->getOrCreateReceipt($order);

        return redirect()->route('receipts.show', $receipt->uuid);
    }
}
