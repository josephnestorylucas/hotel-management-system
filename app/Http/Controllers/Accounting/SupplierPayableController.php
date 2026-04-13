<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierPayable;
use App\Models\SupplierPayment;
use App\Services\SupplierPayablesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SupplierPayableController extends Controller
{
    public function dashboard(): View
    {
        $openPayables = SupplierPayable::with('supplier')
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('payable_date')
            ->get();

        $aging = [
            '0_30' => 0.0,
            '31_60' => 0.0,
            '61_90' => 0.0,
            '90_plus' => 0.0,
        ];

        foreach ($openPayables as $payable) {
            $days = (int) $payable->payable_date?->diffInDays(now()) ?: 0;
            $bucket = match (true) {
                $days <= 30 => '0_30',
                $days <= 60 => '31_60',
                $days <= 90 => '61_90',
                default => '90_plus',
            };

            $aging[$bucket] += (float) $payable->balance;
        }

        $recentPayments = SupplierPayment::with('supplier')
            ->latest('payment_date')
            ->limit(8)
            ->get();

        $totalOutstanding = (float) $openPayables->sum('balance');

        return view('accountant.payables.dashboard', compact('openPayables', 'aging', 'recentPayments', 'totalOutstanding'));
    }

    public function index(Request $request): View
    {
        $query = SupplierPayable::with(['supplier', 'creator'])
            ->when($request->filled('supplier_id'), fn ($query) => $query->where('supplier_id', $request->string('supplier_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('payable_date', '>=', $request->string('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('payable_date', '<=', $request->string('date_to')))
            ->orderByDesc('payable_date');

        $payables = (clone $query)
            ->paginate(20)
            ->withQueryString();

        $suppliers = Supplier::active()->orderBy('name')->get();
        $totals = [
            'amount_total' => (float) (clone $query)->sum('amount_total'),
            'amount_paid' => (float) (clone $query)->sum('amount_paid'),
            'balance' => (float) (clone $query)->sum('balance'),
        ];

        return view('accountant.payables.index', compact('payables', 'suppliers', 'totals'));
    }

    public function show(SupplierPayable $supplierPayable): View
    {
        $supplierPayable->load([
            'supplier',
            'creator',
            'journalEntry',
            'allocations.payment.supplier',
            'allocations.creator',
        ]);

        return view('accountant.payables.show', compact('supplierPayable'));
    }

    public function createPayment(): View
    {
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('accountant.payments.create', compact('suppliers'));
    }

    public function storePayment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'uuid', 'exists:suppliers,id'],
            'payment_date' => ['required', 'date'],
            'currency' => ['required', 'in:USD,TZS'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'in:cash,bank,mobile,card'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $payment = SupplierPayment::create($data + [
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('accountant.payments.apply', $payment)
            ->with('success', __('accountant.messages.payment_draft_saved'));
    }

    public function applyPayment(SupplierPayment $supplierPayment): View
    {
        $supplierPayment->load(['supplier', 'allocations.payable']);
        $allocatedAmount = (float) $supplierPayment->allocations->sum('allocated_amount');
        $remainingAmount = round((float) $supplierPayment->amount - $allocatedAmount, 2);

        $payables = SupplierPayable::where('supplier_id', $supplierPayment->supplier_id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->orWhere(function ($query) use ($supplierPayment) {
                $query->where('supplier_id', $supplierPayment->supplier_id)
                    ->whereHas('allocations', fn ($allocationQuery) => $allocationQuery->where('supplier_payment_id', $supplierPayment->id));
            })
            ->orderBy('payable_date')
            ->get();

        return view('accountant.payments.apply', compact('supplierPayment', 'payables', 'allocatedAmount', 'remainingAmount'));
    }

    public function allocatePayment(Request $request, SupplierPayment $supplierPayment, SupplierPayablesService $service): RedirectResponse
    {
        $data = $request->validate([
            'allocations' => ['nullable', 'array'],
            'allocations.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $service->allocatePayment($supplierPayment, $data['allocations'] ?? [], (string) auth()->id());
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->with('error', collect($exception->errors())->flatten()->first())
                ->withInput();
        }

        return redirect()
            ->route('accountant.payments.apply', $supplierPayment)
            ->with('success', __('accountant.messages.payment_allocated'));
    }

    public function postPayment(SupplierPayment $supplierPayment, SupplierPayablesService $service): RedirectResponse
    {
        try {
            $service->postPayment($supplierPayment, (string) auth()->id());
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->with('error', collect($exception->errors())->flatten()->first());
        }

        return redirect()
            ->route('accountant.payables.dashboard')
            ->with('success', __('accountant.messages.payment_posted'));
    }
}
