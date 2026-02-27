{{-- resources/views/procurement/suppliers/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Supplier')
@section('page-title', 'Suppliers')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-secondary">Edit Supplier</h2>
            <p class="text-sm text-gray-500 mt-1">Update supplier information</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('procurement.suppliers.update', $supplier) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-secondary mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Basic Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Supplier Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name"
                                value="{{ old('name', $supplier->name) }}"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('name') border-red-500 @enderror"
                                placeholder="e.g., ABC Trading Ltd">
                            @error('name')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Person -->
                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Person
                            </label>
                            <input 
                                type="text" 
                                name="contact_person" 
                                id="contact_person"
                                value="{{ old('contact_person', $supplier->contact_person) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('contact_person') border-red-500 @enderror"
                                placeholder="e.g., John Doe">
                            @error('contact_person')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input 
                                type="text" 
                                name="phone" 
                                id="phone"
                                value="{{ old('phone', $supplier->phone) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('phone') border-red-500 @enderror"
                                placeholder="e.g., +255 123 456 789">
                            @error('phone')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email"
                                value="{{ old('email', $supplier->email) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('email') border-red-500 @enderror"
                                placeholder="e.g., supplier@example.com">
                            @error('email')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Physical Address
                            </label>
                            <textarea 
                                name="address" 
                                id="address"
                                rows="3"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('address') border-red-500 @enderror"
                                placeholder="Full physical address">{{ old('address', $supplier->address) }}</textarea>
                            @error('address')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Tax Information -->
                <div>
                    <h3 class="text-lg font-semibold text-secondary mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Tax Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- TIN Number -->
                        <div>
                            <label for="tin_number" class="block text-sm font-medium text-gray-700 mb-2">
                                TIN Number
                            </label>
                            <input 
                                type="text" 
                                name="tin_number" 
                                id="tin_number"
                                value="{{ old('tin_number', $supplier->tin_number) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('tin_number') border-red-500 @enderror"
                                placeholder="Tax Identification Number">
                            @error('tin_number')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- VRN Number -->
                        <div>
                            <label for="vrn_number" class="block text-sm font-medium text-gray-700 mb-2">
                                VRN Number
                            </label>
                            <input 
                                type="text" 
                                name="vrn_number" 
                                id="vrn_number"
                                value="{{ old('vrn_number', $supplier->vrn_number) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('vrn_number') border-red-500 @enderror"
                                placeholder="VAT Registration Number">
                            @error('vrn_number')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Additional Information -->
                <div>
                    <h3 class="text-lg font-semibold text-secondary mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        Additional Notes
                    </h3>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea 
                            name="notes" 
                            id="notes"
                            rows="4"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors @error('notes') border-red-500 @enderror"
                            placeholder="Any additional notes about this supplier...">{{ old('notes', $supplier->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="mt-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="is_active" 
                                value="1"
                                {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}
                                class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-2 focus:ring-primary">
                            <span class="text-sm font-medium text-gray-700">Active Supplier</span>
                        </label>
                        <p class="text-xs text-gray-500 ml-8">Inactive suppliers will not appear in dropdown lists</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                @if(auth()->user()->hasAnyRole(['store_manager', 'admin']))
                <form method="POST" action="{{ route('procurement.suppliers.destroy', $supplier) }}" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                        Delete Supplier
                    </button>
                </form>
                @else
                <div></div>
                @endif

                <div class="flex items-center gap-3">
                    <a href="{{ route('procurement.suppliers.index') }}" 
                       class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        Cancel
                    </a>
                    <button 
                        type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-primary to-blue-600 rounded-lg hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                        Update Supplier
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection