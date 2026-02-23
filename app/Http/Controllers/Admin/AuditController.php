<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\DiscountAuditMail;
use App\Models\Booking;
use App\Models\DiscountAudit;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuditController extends Controller
{
    /**
     * GET /admin/audit/discounts — show discount audit log.
     */
    public function discounts(): View
    {
        $audits = DiscountAudit::with('authorizer')
            ->latest('authorized_at')
            ->paginate(30);

        return view('admin.audit.index', compact('audits'));
    }

    /**
     * POST /admin/bookings/{booking}/discount — apply a discount to a booking (audit logged).
     */
    public function applyDiscount(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'discount_amount' => 'required|numeric|min:1',
            'valid_days'      => 'required|integer|min:1',
            'valid_from'      => 'required|date',
            'reason'          => 'required|string|min:10',
        ]);

        $validUntil = Carbon::parse($data['valid_from'])->addDays($data['valid_days']);

        DiscountAudit::create([
            'booking_id'      => $booking->id,
            'authorized_by'   => auth()->id(),
            'discount_amount' => $data['discount_amount'],
            'valid_days'      => $data['valid_days'],
            'valid_from'      => $data['valid_from'],
            'valid_until'     => $validUntil,
            'reason'          => $data['reason'],
            'authorized_at'   => now(),
        ]);

        // Notify other STORE_MANAGERs and SUPERVISORs by email
        User::whereHas('role', fn ($q) => $q->whereIn('name', [Role::STORE_MANAGER, Role::SUPERVISOR]))
            ->where('id', '!=', auth()->id())
            ->get()
            ->each(fn ($manager) => Mail::to($manager->email)->queue(new DiscountAuditMail([
                'authorized_by'   => auth()->user()->name,
                'booking_id'      => $booking->id,
                'discount_amount' => $data['discount_amount'],
                'valid_days'      => $data['valid_days'],
                'reason'          => $data['reason'],
                'authorized_at'   => now()->format('d M Y H:i:s'),
            ])));

        return redirect()->back()->with('success', 'Discount applied and logged.');
    }
}
