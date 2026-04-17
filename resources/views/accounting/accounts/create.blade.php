@extends('layouts.app')

@section('title', 'Create Account')
@section('page-title', 'Create Account')

@section('content')
<form method="POST" action="{{ route('accounting.accounts.store') }}" class="mx-auto max-w-4xl space-y-6 rounded-2xl bg-white p-6 shadow-sm">
    @csrf
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Code</label>
            <input type="text" name="code" value="{{ old('code') }}" class="w-full rounded-xl border-gray-200 text-sm" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border-gray-200 text-sm" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Type</label>
            <select name="type" class="w-full rounded-xl border-gray-200 text-sm" required>
                @foreach(['asset', 'liability', 'equity', 'revenue', 'expense', 'cogs'] as $type)
                    <option value="{{ $type }}" @selected(old('type') === $type)>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Normal balance</label>
            <select name="normal_balance" class="w-full rounded-xl border-gray-200 text-sm" required>
                @foreach(['debit', 'credit'] as $normalBalance)
                    <option value="{{ $normalBalance }}" @selected(old('normal_balance') === $normalBalance)>{{ ucfirst($normalBalance) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Parent account</label>
            <select name="parent_id" class="w-full rounded-xl border-gray-200 text-sm">
                <option value="">None</option>
                @foreach($parentAccounts as $parentAccount)
                    <option value="{{ $parentAccount->id }}" @selected(old('parent_id') === $parentAccount->id)>{{ $parentAccount->code }} - {{ $parentAccount->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <label class="flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', '1') == '1')>
                Active
            </label>
            <label class="flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700">
                <input type="hidden" name="is_system" value="0">
                <input type="checkbox" name="is_system" value="1" @checked(old('is_system') == '1')>
                System account
            </label>
        </div>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">Description</label>
        <textarea name="description" rows="4" class="w-full rounded-xl border-gray-200 text-sm">{{ old('description') }}</textarea>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('accounting.accounts.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700">Cancel</a>
        <button class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white">Save account</button>
    </div>
</form>
@endsection
