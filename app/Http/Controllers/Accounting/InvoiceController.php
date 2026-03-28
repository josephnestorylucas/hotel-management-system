<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = Invoice::with(['issuer'])
            ->orderBy('invoice_date', 'desc')
            ->paginate(20);
        return view('accounting.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['lines', 'issuer']);
        return view('accounting.invoices.show', compact('invoice'));
    }
}
