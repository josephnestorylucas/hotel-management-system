{{-- resources/views/procurement/grn/show.blade.php --}}
@extends('layouts.app')

@section('title', 'GRN Details')
@section('page-title', 'Goods Received Notes')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $goodsReceivedNote->grn_number }}</h2>
            <p class="text-sm text-gray-500 mt-1">Goods Received Note Details</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Status-based Actions -->
            @if($goodsReceivedNote->status === 'draft' && auth()->user()->hasAnyRole(['store_manager', 'store_keeper', 'admin']))
            <form method="POST" action="{{ route('procurement.grn.submit', $goodsReceivedNote) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white text-sm font-semibold rounded-lg hover:bg-yellow-700 transition-colors">
                    Submit for Confirmation
                </button>
            </form>
            @endif

            @if($goodsReceivedNote->status === 'pending_confirmation' && auth()->user()->hasAnyRole(['store_manager', 'supervisor', 'admin']))
            <form method="POST" action="{{ route('procurement.grn.confirm', $goodsReceivedNote) }}" class="inline" onsubmit="return confirm('This will update stock levels. Are you sure?');">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                    ✓ Confirm & Update Stock
                </button>
            </form>
            <button 
                type="button"
                onclick="document.getElementById('reject-modal').classList.remove('hidden')"
                class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                Reject
            </button>
            @endif

            @if($goodsReceivedNote->status === 'draft' && auth()->user()->hasAnyRole(['store_manager', 'store_keeper', 'admin']))
            <button 
                type="button"
                onclick="document.getElementById('upload-modal').classList.remove('hidden')"
                class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                📎 Upload Receipt
            </button>
            @endif

            <!-- Print Button -->
            <button 
                onclick="window.print()"
                class="px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition-colors no-print">
                🖨️ Print
            </button>
        </div>
    </div>

    <!-- GRN Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">GRN Information</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">GRN Number</span>
                        <span class="text-sm font-semibold text-secondary">{{ $goodsReceivedNote->grn_number }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Status</span>
                        <div>@include('components.grn-status-badge', ['status' => $goodsReceivedNote->status])</div>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">LPO Number</span>
                        <a href="{{ route('procurement.lpo.show', $goodsReceivedNote->lpo) }}" class="text-sm font-semibold text-primary hover:text-blue-700">
                            {{ $goodsReceivedNote->lpo->lpo_number }}
                        </a>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Received Date</span>
                        <span class="text-sm text-secondary">{{ $goodsReceivedNote->received_date->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Vehicle</span>
                        <span class="text-sm text-secondary">{{ $goodsReceivedNote->delivery_vehicle ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Driver</span>
                        <span class="text-sm text-secondary">{{ $goodsReceivedNote->driver_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Received By</span>
                        <span class="text-sm font-semibold text-secondary">{{ $goodsReceivedNote->receiver->name }}</span>
                    </div>
                    @if($goodsReceivedNote->confirmer)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Confirmed By</span>
                        <span class="text-sm font-semibold text-secondary">{{ $goodsReceivedNote->confirmer->name }}</span>
                    </div>
                    @endif
                </div>

                @if($goodsReceivedNote->rejection_reason)
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="text-sm font-semibold text-red-800 mb-1">Rejection Reason:</div>
                    <div class="text-sm text-red-700">{{ $goodsReceivedNote->rejection_reason }}</div>
                </div>
                @endif

                @if($goodsReceivedNote->notes)
                <div class="mt-4">
                    <div class="text-sm font-semibold text-gray-700 mb-1">Notes:</div>
                    <div class="text-sm text-gray-600">{{ $goodsReceivedNote->notes }}</div>
                </div>
                @endif

                @if($goodsReceivedNote->receipt_path)
                <div class="mt-4">
                    <div class="text-sm font-semibold text-gray-700 mb-2">Receipt:</div>
                    <a href="{{ Storage::url($goodsReceivedNote->receipt_path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        View Receipt
                    </a>
                </div>
                @endif
            </div>

            <!-- Items -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-secondary">Received Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gradient-to-r from-blue-50 to-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Item</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Unit</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">Ordered</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">Received</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">Unit Price</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-primary uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($goodsReceivedNote->items as $item)
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="px-6 py-3">
                                    <div class="text-sm font-medium text-secondary">{{ $item->item_name }}</div>
                                    @if($item->product)
                                    <div class="text-xs text-primary">{{ $item->product->sku }}</div>
                                    @endif
                                    @if($item->notes)
                                    <div class="text-xs text-gray-500 mt-1">{{ $item->notes }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <span class="text-sm text-gray-600">{{ $item->unit }}</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="text-sm text-gray-600">{{ number_format($item->quantity_ordered, 2) }}</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="text-sm font-medium text-secondary">{{ number_format($item->quantity_received, 2) }}</span>
                                    @if($item->quantity_received < $item->quantity_ordered)
                                    <span class="ml-1 text-xs text-red-600">(short)</span>
                                    @elseif($item->quantity_received > $item->quantity_ordered)
                                    <span class="ml-1 text-xs text-green-600">(extra)</span>
                                    @endif
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
                                <td colspan="5" class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Subtotal:</td>
                                <td class="px-6 py-3 text-right text-sm font-bold text-secondary"><x-money :amount="$goodsReceivedNote->subtotal" /></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Tax (18%):</td>
                                <td class="px-6 py-3 text-right text-sm font-bold text-secondary"><x-money :amount="$goodsReceivedNote->tax_amount" /></td>
                            </tr>
                            <tr class="bg-gradient-to-r from-primary/10 to-blue-100">
                                <td colspan="5" class="px-6 py-4 text-right text-base font-bold text-gray-800">Grand Total:</td>
                                <td class="px-6 py-4 text-right text-xl font-extrabold text-primary"><x-money :amount="$goodsReceivedNote->grand_total" /></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($goodsReceivedNote->status === 'confirmed')
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-green-800">Stock Updated Successfully</div>
                        <div class="text-xs text-green-700">All items have been added to main store inventory on {{ $goodsReceivedNote->confirmed_at->format('M d, Y \a\t H:i') }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Supplier Info -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Supplier Information</h3>
                
                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Supplier Name</div>
                        <div class="text-sm font-semibold text-secondary">{{ $goodsReceivedNote->supplierName }}</div>
                    </div>
                    
                    @if($goodsReceivedNote->supplier)
                    @if($goodsReceivedNote->supplier->contact_person)
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Contact Person</div>
                        <div class="text-sm text-secondary">{{ $goodsReceivedNote->supplier->contact_person }}</div>
                    </div>
                    @endif
                    
                    @if($goodsReceivedNote->supplier->phone)
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Phone</div>
                        <div class="text-sm text-secondary">{{ $goodsReceivedNote->supplier->phone }}</div>
                    </div>
                    @endif
                    @endif
                </div>
            </div>

            <!-- Related LPO -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Purchase Order</h3>
                
                <a href="{{ route('procurement.lpo.show', $goodsReceivedNote->lpo) }}" class="block p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg hover:shadow-md transition-all">
                    <div class="text-sm font-semibold text-secondary mb-1">{{ $goodsReceivedNote->lpo->lpo_number }}</div>
                    <div class="text-xs text-gray-600 mb-2">Order Date: {{ $goodsReceivedNote->lpo->order_date->format('M d, Y') }}</div>
                    <div class="flex items-center justify-between">
                        <div>@include('components.lpo-status-badge', ['status' => $goodsReceivedNote->lpo->status])</div>
                        <div class="text-sm font-bold text-primary"><x-money :amount="$goodsReceivedNote->lpo->grand_total" /></div>
                    </div>
                </a>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Timeline</h3>
                
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-secondary">Created</div>
                            <div class="text-xs text-gray-500">{{ $goodsReceivedNote->created_at->format('M d, Y H:i') }}</div>
                            <div class="text-xs text-gray-600">by {{ $goodsReceivedNote->receiver->name }}</div>
                        </div>
                    </div>

                    @if($goodsReceivedNote->confirmed_at)
                    <div class="flex gap-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-secondary">Confirmed</div>
                            <div class="text-xs text-gray-500">{{ $goodsReceivedNote->confirmed_at->format('M d, Y H:i') }}</div>
                            <div class="text-xs text-gray-600">by {{ $goodsReceivedNote->confirmer->name }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('procurement.grn.index') }}" class="text-primary hover:text-blue-700 font-semibold">
            ← Back to GRNs
        </a>
    </div>
</div>

<!-- Upload Receipt Modal -->
<div id="upload-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 no-print">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-secondary">Upload Receipt</h3>
        </div>
        <form method="POST" action="{{ route('procurement.grn.upload-receipt', $goodsReceivedNote) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <div class="mb-4">
                <label for="receipt" class="block text-sm font-medium text-gray-700 mb-2">
                    Receipt File <span class="text-red-500">*</span>
                </label>
                <input 
                    type="file" 
                    name="receipt" 
                    id="receipt"
                    accept=".pdf,.jpg,.jpeg,.png"
                    required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max: 5MB)</p>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button 
                    type="button"
                    onclick="document.getElementById('upload-modal').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Upload Receipt
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 no-print">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-secondary">Reject GRN</h3>
        </div>
        <form method="POST" action="{{ route('procurement.grn.reject', $goodsReceivedNote) }}" class="p-6">
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
                    placeholder="Explain why this GRN is being rejected..."></textarea>
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
                    Reject GRN
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white; }
    .shadow-lg { box-shadow: none !important; }
}
</style>
@endsection