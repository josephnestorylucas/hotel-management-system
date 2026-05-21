<?php

namespace App\Http\Controllers;

use App\Services\Payment\PaymentEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * AzamPesaPaymentController — handles AzamPesa callback from Checkout.
 *
 * Callback endpoint is public (no auth middleware) because AzamPesa's
 * servers call it directly. Signature verification is done via the
 * PaymentEngine → AzamPesaProvider::validateWebhook().
 */
class AzamPesaPaymentController extends Controller
{
    /**
     * Handle incoming AzamPesa checkout callback.
     *
     * POST /payments/callback/azampesa
     */
    public function callback(Request $request)
    {
        $payload = $request->all();
        $headers = $request->headers->all();

        // Flatten header arrays (Laravel returns headers as arrays)
        $flatHeaders = array_map(fn($h) => is_array($h) ? ($h[0] ?? '') : $h, $headers);

        Log::info('AzamPesa callback received', [
            'transactionstatus' => $payload['transactionstatus'] ?? 'unknown',
            'utilityref'        => $payload['utilityref'] ?? null,
            'externalreference' => $payload['externalreference'] ?? null,
            'operator'          => $payload['operator'] ?? null,
        ]);

        try {
            $engine = new PaymentEngine();
            $payment = $engine->handleWebhook($payload, $flatHeaders);

            if ($payment) {
                return response()->json(['status' => 'ok', 'payment_id' => $payment->id], 200);
            }

            // Callback valid but no matching payment found — still return 200
            // to prevent AzamPesa from retrying
            return response()->json(['status' => 'ok', 'message' => 'No matching payment'], 200);

        } catch (\Exception $e) {
            Log::error('AzamPesa callback processing error', ['message' => $e->getMessage()]);
            return response()->json(['status' => 'error'], 500);
        }
    }
}
