<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\PettyCash;
use App\Services\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PettyCashController extends Controller
{
    // Approve a petty cash expense and post to accounting journal
    public function approve(PettyCash $pettyCash, AccountingService $accounting): RedirectResponse
    {
        abort_if($pettyCash->status !== 'draft', 422, 'Only draft petty cash expenses can be approved.');

        // Map category to expense account code
        $expenseCode = match($pettyCash->category) {
            'transport' => '6600',
            'repairs'   => '6400',
            'office'    => '6500',
            default     => '6800',
        };

        DB::transaction(function () use ($pettyCash, $accounting, $expenseCode) {
            $pettyCash->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $accounting->postPettyCash(
                reference:          $pettyCash->reference_no,
                sourceId:           $pettyCash->id,
                amount:             (float) $pettyCash->amount,
                expenseAccountCode: $expenseCode,
                actorId:            auth()->id()
            );
        });

        return back()->with('success', "Petty cash {$pettyCash->reference_no} approved and posted to ledger.");
    }

    // Optional: reject
    public function reject(Request $request, PettyCash $pettyCash): RedirectResponse
    {
        abort_if($pettyCash->status !== 'draft', 422, 'Only draft petty cash expenses can be rejected.');

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:5',
        ]);

        $pettyCash->update([
            'status'           => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by'      => auth()->id(),
        ]);

        return back()->with('success', 'Petty cash expense rejected.');
    }
}
