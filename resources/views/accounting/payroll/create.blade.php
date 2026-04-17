@extends('layouts.app')

@section('title', 'Create Payroll')
@section('page-title', 'Create Payroll')

@section('content')
<form method="POST" action="{{ route('accounting.payroll.store') }}" class="space-y-6 rounded-2xl bg-white p-6 shadow-sm">
    @csrf
    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Period month</label>
            <input type="month" name="period_month" value="{{ old('period_month') }}" class="w-full rounded-xl border-gray-200 text-sm" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Pay date</label>
            <input type="date" name="pay_date" value="{{ old('pay_date', now()->toDateString()) }}" class="w-full rounded-xl border-gray-200 text-sm" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Notes</label>
            <input type="text" name="notes" value="{{ old('notes') }}" class="w-full rounded-xl border-gray-200 text-sm">
        </div>
    </div>

    <div>
        <h3 class="text-lg font-extrabold text-secondary">Staff lines</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-gray-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Staff</th>
                        <th class="px-4 py-3 font-semibold text-right">Basic salary</th>
                        <th class="px-4 py-3 font-semibold text-right">Allowances</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($staff->take(5) as $index => $member)
                        <tr>
                            <td class="px-4 py-3">
                                <input type="hidden" name="lines[{{ $index }}][user_id]" value="{{ $member->id }}">
                                <div class="font-semibold text-secondary">{{ $member->name }}</div>
                                <div class="text-xs text-gray-500">{{ $member->role?->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-right"><input type="number" step="0.01" min="0" name="lines[{{ $index }}][basic_salary]" value="{{ old('lines.' . $index . '.basic_salary') }}" class="w-full rounded-xl border-gray-200 text-sm"></td>
                            <td class="px-4 py-3 text-right"><input type="number" step="0.01" min="0" name="lines[{{ $index }}][allowances]" value="{{ old('lines.' . $index . '.allowances', 0) }}" class="w-full rounded-xl border-gray-200 text-sm"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('accounting.payroll.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700">Cancel</a>
        <button class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white">Create payroll</button>
    </div>
</form>
@endsection
