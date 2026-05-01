{{-- resources/views/procurement/lpo/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Purchase Order Details')
@section('page-title', 'Local Purchase Orders')

@section('content')
@php
    $orderedQty = (float) $localPurchaseOrder->items->sum('quantity');
    $receivedQty = (float) $localPurchaseOrder->items->sum('received_quantity');
    $pendingQty = $localPurchaseOrder->items->sum(fn ($item) => max((float) $item->quantity - (float) $item->received_quantity, 0));
    $receivingPercent = $orderedQty > 0 ? min(100, ($receivedQty / $orderedQty) * 100) : 0;
@endphp
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $localPurchaseOrder->lpo_number }}</h2>
            <p class="text-sm text-gray-500 mt-1">Purchase Order Details</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Status-based Actions -->
            @if($localPurchaseOrder->status === 'draft' && auth()->user()->hasAnyRole(['store_manager', 'store_keeper', 'admin']))
            <form method="POST" action="{{ route('procurement.lpo.submit', $localPurchaseOrder) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white text-sm font-semibold rounded-lg hover:bg-yellow-700 transition-colors">
                    Submit for Approval
                </button>
            </form>
            <a href="{{ route('procurement.lpo.edit', $localPurchaseOrder) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                Edit
            </a>
            @endif

            @if($localPurchaseOrder->status === 'pending_approval' && auth()->user()->hasRole('manager'))
            <form method="POST" action="{{ route('procurement.lpo.approve', $localPurchaseOrder) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                    Approve
                </button>
            </form>
            <button 
                type="button"
                onclick="document.getElementById('reject-modal').classList.remove('hidden')"
                class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                Reject
            </button>
            @endif

            @if($localPurchaseOrder->status === 'approved' && auth()->user()->hasAnyRole(['store_manager', 'store_keeper', 'admin']))
            <form method="POST" action="{{ route('procurement.lpo.sent', $localPurchaseOrder) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                    Mark as Sent
                </button>
            </form>
            @endif

            @if(in_array($localPurchaseOrder->status, ['sent', 'approved', 'partially_received']) && auth()->user()->hasRole('store_keeper'))
            <a href="{{ route('procurement.grn.create', ['lpo_id' => $localPurchaseOrder->id]) }}" class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition-colors">
                Create GRN
            </a>
            @endif

            <!-- Print Button -->
            <button type="button"
                onclick="document.getElementById('print-modal').classList.remove('hidden'); document.getElementById('print-frame').src = '{{ route('procurement.lpo.print', $localPurchaseOrder) }}'"
                class="px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition-colors no-print">
                🖨️ Print
            </button>
        </div>
    </div>

    <!-- LPO Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Ordered Quantity</div>
                    <div class="mt-2 text-3xl font-extrabold text-secondary">{{ number_format($orderedQty, 2) }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Received Quantity</div>
                    <div class="mt-2 text-3xl font-extrabold text-green-600">{{ number_format($receivedQty, 2) }}</div>
                </div>
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Pending Quantity</div>
                    <div class="mt-2 text-3xl font-extrabold text-amber-600">{{ number_format($pendingQty, 2) }}</div>
                </div>
            </div>

            <!-- Basic Info -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Order Information</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">LPO Number</span>
                        <span class="text-sm font-semibold text-secondary">{{ $localPurchaseOrder->lpo_number }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Status</span>
                        <div>@include('components.lpo-status-badge', ['status' => $localPurchaseOrder->status])</div>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Order Date</span>
                        <span class="text-sm text-secondary">{{ $localPurchaseOrder->order_date->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Expected Delivery</span>
                        <span class="text-sm text-secondary">{{ $localPurchaseOrder->expected_delivery_date?->format('M d, Y') ?? 'Not set' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Created By</span>
                        <span class="text-sm font-semibold text-secondary">{{ $localPurchaseOrder->creator->name }}</span>
                    </div>
                    @if($localPurchaseOrder->approver)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Approved By</span>
                        <span class="text-sm font-semibold text-secondary">{{ $localPurchaseOrder->approver->name }}</span>
                    </div>
                    @endif
                    @if($localPurchaseOrder->rejector)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Rejected By</span>
                        <span class="text-sm font-semibold text-secondary">{{ $localPurchaseOrder->rejector->name }}</span>
                    </div>
                    @endif
                </div>

                @if($localPurchaseOrder->rejection_reason)
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="text-sm font-semibold text-red-800 mb-1">Rejection Reason:</div>
                    <div class="text-sm text-red-700">{{ $localPurchaseOrder->rejection_reason }}</div>
                </div>
                @endif

                @if($localPurchaseOrder->notes)
                <div class="mt-4">
                    <div class="text-sm font-semibold text-gray-700 mb-1">Notes:</div>
                    <div class="text-sm text-gray-600">{{ $localPurchaseOrder->notes }}</div>
                </div>
                @endif

                @if($localPurchaseOrder->terms)
                <div class="mt-4">
                    <div class="text-sm font-semibold text-gray-700 mb-1">Terms & Conditions:</div>
                    <div class="text-sm text-gray-600">{{ $localPurchaseOrder->terms }}</div>
                </div>
                @endif
            </div>

            <!-- Items -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-secondary">Order Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gradient-to-r from-blue-50 to-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Item</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Unit</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">Received</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">Pending</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">Unit Price</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($localPurchaseOrder->items as $item)
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="px-6 py-3">
                                    <div class="text-sm font-medium text-secondary">{{ $item->item_name }}</div>
                                    @if($item->product)
                                    <div class="text-xs text-primary">{{ $item->product->sku }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <span class="text-sm text-gray-600">{{ $item->unit }}</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="text-sm font-medium text-secondary">{{ number_format($item->quantity, 2) }}</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="text-sm font-semibold text-green-600">{{ number_format($item->received_quantity, 2) }}</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="text-sm font-semibold text-amber-600">{{ number_format(max((float) $item->quantity - (float) $item->received_quantity, 0), 2) }}</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="text-sm text-secondary"><x-money :amount="$item->unit_price" /></span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="text-sm font-bold text-secondary"><x-money :amount="$item->subtotal" /></span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gradient-to-r from-blue-50 to-white">
                            <tr>
                                <td colspan="6" class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Subtotal:</td>
                                <td class="px-6 py-3 text-right text-sm font-bold text-secondary"><x-money :amount="$localPurchaseOrder->subtotal" /></td>
                            </tr>
                            <tr>
                                <td colspan="6" class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Tax (18%):</td>
                                <td class="px-6 py-3 text-right text-sm font-bold text-secondary"><x-money :amount="$localPurchaseOrder->tax_amount" /></td>
                            </tr>
                            <tr class="bg-gradient-to-r from-primary/10 to-blue-100">
                                <td colspan="6" class="px-6 py-4 text-right text-base font-bold text-gray-800">Grand Total:</td>
                                <td class="px-6 py-4 text-right text-xl font-extrabold text-primary"><x-money :amount="$localPurchaseOrder->grand_total" /></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Supplier Info -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Supplier Information</h3>
                
                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Supplier Name</div>
                        <div class="text-sm font-semibold text-secondary">{{ $localPurchaseOrder->supplierName }}</div>
                    </div>
                    
                    @if($localPurchaseOrder->supplier)
                    @if($localPurchaseOrder->supplier->contact_person)
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Contact Person</div>
                        <div class="text-sm text-secondary">{{ $localPurchaseOrder->supplier->contact_person }}</div>
                    </div>
                    @endif
                    
                    @if($localPurchaseOrder->supplier->phone)
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Phone</div>
                        <div class="text-sm text-secondary">{{ $localPurchaseOrder->supplier->phone }}</div>
                    </div>
                    @endif
                    
                    @if($localPurchaseOrder->supplier->email)
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Email</div>
                        <div class="text-sm text-secondary">{{ $localPurchaseOrder->supplier->email }}</div>
                    </div>
                    @endif
                    
                    @if($localPurchaseOrder->supplier->address)
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Address</div>
                        <div class="text-sm text-secondary">{{ $localPurchaseOrder->supplier->address }}</div>
                    </div>
                    @endif
                    @endif
                </div>
            </div>

            <!-- Goods Received Notes -->
            @if($localPurchaseOrder->goodsReceivedNotes->count() > 0)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Goods Received</h3>
                
                <div class="space-y-2">
                    @foreach($localPurchaseOrder->goodsReceivedNotes as $grn)
                    <a href="{{ route('procurement.grn.show', $grn) }}" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold text-secondary">{{ $grn->grn_number }}</div>
                                <div class="text-xs text-gray-500">{{ $grn->received_date->format('M d, Y') }}</div>
                            </div>
                            <div>@include('components.grn-status-badge', ['status' => $grn->status])</div>
                        </div>
                        <div class="mt-2 text-xs text-primary font-bold">
                            <x-money :amount="$grn->grand_total" />
                        </div>
                        <div class="mt-1 text-[11px] text-gray-500">
                            Accounting: {{ $grn->accounting_journal_entry_id ? 'posted' : 'pending' }}
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Integration Status</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Receiving Progress</span>
                        <span class="font-semibold text-secondary">{{ number_format($receivingPercent, 0) }}%</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">
                        <span>Receiving synchronized</span>
                        <span>{{ number_format($receivedQty, 2) }} / {{ number_format($orderedQty, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Confirmed GRNs</span>
                        <span class="font-semibold text-secondary">{{ $localPurchaseOrder->goodsReceivedNotes->where('status', \App\Models\GoodsReceivedNote::STATUS_APPROVED)->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Accounting Linked GRNs</span>
                        <span class="font-semibold text-secondary">{{ $localPurchaseOrder->goodsReceivedNotes->filter(fn($grn) => !empty($grn->accounting_journal_entry_id))->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('procurement.lpo.index') }}" class="text-primary hover:text-blue-700 font-semibold">
            ← Back to Purchase Orders
        </a>
    </div>
</div>

<!-- Print Modal -->
<div id="print-modal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 no-print">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl mx-4 h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-secondary">Print Preview — {{ $localPurchaseOrder->lpo_number }}</h3>
            <div class="flex items-center gap-3">
                <button type="button"
                    onclick="var f=document.getElementById('print-frame');f.contentWindow.focus();f.contentWindow.print()"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
                    🖨️ Print
                </button>
                <button type="button"
                    onclick="document.getElementById('print-modal').classList.add('hidden');document.getElementById('print-frame').src=''"
                    class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300">
                    ✕ Close
                </button>
            </div>
        </div>
        <iframe id="print-frame" src="" class="flex-1 w-full border-0" style="min-height: 0;"></iframe>
    </div>
</div>

@if($localPurchaseOrder->status === 'pending_approval' && auth()->user()->hasRole('manager'))
<!-- Reject Modal -->
<div id="reject-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 no-print">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-secondary">Reject Purchase Order</h3>
        </div>
        <form method="POST" action="{{ route('procurement.lpo.reject', $localPurchaseOrder) }}" class="p-6">
            @csrf
            <div class="mb-4">
                <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Reason for Rejection <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="rejection_reason" 
                    id="rejection_reason"
                    rows="4"
                    required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                    placeholder="Explain why this LPO is being rejected..."></textarea>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button 
                    type="button"
                    onclick="document.getElementById('reject-modal').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                    Reject LPO
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white; }
    .shadow-lg { box-shadow: none !important; }
}
</style>
@endsection
