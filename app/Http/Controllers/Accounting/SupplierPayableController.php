<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierPayable;
use App\Models\SupplierPayment;
use App\Services\SupplierPayablesService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SupplierPayableController extends Controller
{
    public function dashboard(): View
    {
        abort_unless($this->canViewAp(), 403, __('general.messages.unauthorized'));

        $this->syncApprovedGrnPayablesSafely();

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

        $canManageAp = $this->canManageAp();

        return view('accountant.payables.dashboard', compact('openPayables', 'aging', 'recentPayments', 'totalOutstanding', 'canManageAp'));
    }

    public function index(Request $request): View
    {
        abort_unless($this->canViewAp(), 403, __('general.messages.unauthorized'));

        $this->syncApprovedGrnPayablesSafely();

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
        abort_unless($this->canViewAp(), 403, __('general.messages.unauthorized'));

        $supplierPayable->load([
            'supplier',
            'creator',
            'journalEntry',
            'allocations.payment.supplier',
            'allocations.creator',
        ]);

        $canManageAp = $this->canManageAp();

        return view('accountant.payables.show', compact('supplierPayable', 'canManageAp'));
    }

    public function createPayment(): View
    {
        abort_unless($this->canManageAp(), 403, __('general.messages.unauthorized'));

        $this->syncApprovedGrnPayablesSafely();

        $suppliers = Supplier::active()
            ->whereHas('payables', fn ($query) => $query->whereIn('status', ['unpaid', 'partial']))
            ->orderBy('name')
            ->get();

        $grnPayables = SupplierPayable::query()
            ->with('supplier')
            ->where('source_module', 'procurement')
            ->where('source_reference_type', 'grn')
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('payable_date')
            ->orderBy('reference')
            ->get();

        $generatedReference = SupplierPayment::generateReference();

        return view('accountant.payments.create', compact('suppliers', 'grnPayables', 'generatedReference'));
    }

    public function storePayment(Request $request): RedirectResponse
    {
        abort_unless($this->canManageAp(), 403, __('general.messages.unauthorized'));

        $data = $request->validate([
            'supplier_id' => ['required', 'uuid', 'exists:suppliers,id'],
            'supplier_payable_id' => ['nullable', 'uuid', 'exists:supplier_payables,id'],
            'payment_date' => ['required', 'date'],
            'currency' => ['required', 'in:USD,TZS'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'in:cash,bank,mobile,card'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['reference'] = filled($data['reference'] ?? null)
            ? trim((string) $data['reference'])
            : null;

        if (filled($data['reference']) && SupplierPayment::query()->where('reference', $data['reference'])->exists()) {
            if (str_starts_with($data['reference'], 'SUPPAY-')) {
                $data['reference'] = null;
            } else {
                throw ValidationException::withMessages([
                    'reference' => __('accountant.ap.payment_reference_taken'),
                ]);
            }
        }

        $this->syncApprovedGrnPayablesSafely((string) $data['supplier_id']);

        if (! empty($data['supplier_payable_id'])) {
            $selectedPayable = SupplierPayable::query()
                ->where('id', $data['supplier_payable_id'])
                ->whereIn('status', ['unpaid', 'partial'])
                ->first();

            if (! $selectedPayable) {
                throw ValidationException::withMessages([
                    'supplier_payable_id' => __('accountant.ap.selected_grn_payable_invalid'),
                ]);
            }

            if ($selectedPayable->supplier_id !== $data['supplier_id']) {
                throw ValidationException::withMessages([
                    'supplier_payable_id' => __('accountant.ap.selected_grn_payable_supplier_mismatch'),
                ]);
            }

            if ($selectedPayable->source_module !== 'procurement' || $selectedPayable->source_reference_type !== 'grn') {
                throw ValidationException::withMessages([
                    'supplier_payable_id' => __('accountant.ap.selected_grn_payable_invalid'),
                ]);
            }
        }

        $hasOpenPayables = SupplierPayable::query()
            ->where('supplier_id', $data['supplier_id'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->exists();

        if (! $hasOpenPayables) {
            throw ValidationException::withMessages([
                'supplier_id' => __('accountant.ap.no_open_payables_for_supplier'),
            ]);
        }

        $payload = $data + [
            'status' => 'draft',
            'created_by' => auth()->id(),
        ];

        $payment = null;

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $payment = SupplierPayment::create($payload);
                break;
            } catch (QueryException $exception) {
                $message = strtolower($exception->getMessage());
                $hasReferenceConflict = str_contains($message, 'supplier_payments_reference_unique')
                    || str_contains($message, 'supplier_payments.reference');

                if (! $hasReferenceConflict) {
                    throw $exception;
                }

                $payload['reference'] = SupplierPayment::generateReference();
            }
        }

        if (! $payment) {
            throw ValidationException::withMessages([
                'reference' => __('accountant.ap.payment_reference_regenerate_failed'),
            ]);
        }

        if (! empty($data['supplier_payable_id'])) {
            return redirect()
                ->route('accountant.payments.apply', $payment)
                ->with('prefill_payable_id', $data['supplier_payable_id'])
                ->with('success', __('accountant.messages.payment_draft_saved'));
        }

        return redirect()
            ->route('accountant.payments.apply', $payment)
            ->with('success', __('accountant.messages.payment_draft_saved'));
    }

    public function destroyPayment(SupplierPayment $supplierPayment, SupplierPayablesService $service): RedirectResponse
    {
        abort_unless($this->canManageAp(), 403, __('general.messages.unauthorized'));

        try {
            $service->deletePayment($supplierPayment, (string) auth()->id());
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->with('error', collect($exception->errors())->flatten()->first());
        }

        return redirect()
            ->route('accountant.payables.dashboard')
            ->with('success', __('accountant.messages.payment_archived'));
    }

    public function applyPayment(SupplierPayment $supplierPayment): View
    {
        abort_unless($this->canViewAp() && $this->canManageAp(), 403, __('general.messages.unauthorized'));

        $supplierPayment->load(['supplier', 'allocations.payable']);
        $allocatedAmount = round((float) $supplierPayment->allocations->sum('allocated_amount'), 2);
        $remainingAmount = round((float) $supplierPayment->amount - $allocatedAmount, 2);
        if (abs($remainingAmount) <= 0.01) {
            $remainingAmount = 0.0;
        }

        $payables = SupplierPayable::where('supplier_id', $supplierPayment->supplier_id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->orWhere(function ($query) use ($supplierPayment) {
                $query->where('supplier_id', $supplierPayment->supplier_id)
                    ->whereHas('allocations', fn ($allocationQuery) => $allocationQuery->where('supplier_payment_id', $supplierPayment->id));
            })
            ->orderBy('payable_date')
            ->get();

        $prefillPayableId = session('prefill_payable_id');

        $canPostAp = $this->canPostAp();

        return view('accountant.payments.apply', compact('supplierPayment', 'payables', 'allocatedAmount', 'remainingAmount', 'canPostAp', 'prefillPayableId'));
    }

    public function allocatePayment(Request $request, SupplierPayment $supplierPayment, SupplierPayablesService $service): RedirectResponse
    {
        abort_unless($this->canManageAp(), 403, __('general.messages.unauthorized'));

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
        abort_unless($this->canPostAp(), 403, __('general.messages.unauthorized'));

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

    public function cancelPayment(Request $request, SupplierPayment $supplierPayment, SupplierPayablesService $service): RedirectResponse
    {
        abort_unless($this->canPostAp(), 403, __('general.messages.unauthorized'));

        $data = $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        try {
            $service->cancelPayment(
                payment: $supplierPayment,
                actorId: (string) auth()->id(),
                reason: $data['cancellation_reason'],
            );
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->with('error', collect($exception->errors())->flatten()->first());
        }

        return redirect()
            ->route('accountant.payments.apply', $supplierPayment)
            ->with('success', __('accountant.messages.payment_cancelled'));
    }

    private function canViewAp(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['ACCOUNTANT', 'manager']);
    }

    private function canManageAp(): bool
    {
        return auth()->check() && auth()->user()->hasRole('ACCOUNTANT');
    }

    private function canPostAp(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['ACCOUNTANT', 'manager']);
    }

    private function syncApprovedGrnPayablesSafely(?string $supplierId = null): void
    {
        try {
            app(SupplierPayablesService::class)->syncApprovedGrnPayables(
                actorId: (string) auth()->id(),
                supplierId: $supplierId,
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to sync approved GRN payables', [
                'actor_id' => auth()->id(),
                'supplier_id' => $supplierId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
