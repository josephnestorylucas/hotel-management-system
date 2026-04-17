@extends('layouts.app')

@section('title', 'Payroll Runs')
@section('page-title', 'Payroll Runs')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Payroll Runs</h2>
            <p class="mt-1 text-sm text-gray-500">Review prepared payroll periods and approval status.</p>
        </div>
        <a href="{{ route('accounting.payroll.create') }}" class="inline-flex rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white">New payroll</a>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-6 py-4 font-semibold">Reference</th>
                    <th class="px-6 py-4 font-semibold">Period</th>
                    <th class="px-6 py-4 font-semibold">Pay date</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Net total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payrolls as $payroll)
                    <tr>
                        <td class="px-6 py-4"><a href="{{ route('accounting.payroll.show', $payroll) }}" class="font-semibold text-indigo-600">{{ $payroll->reference_no }}</a></td>
                        <td class="px-6 py-4">{{ $payroll->period_month }}</td>
                        <td class="px-6 py-4">{{ $payroll->pay_date?->format('M d, Y') }}</td>
                        <td class="px-6 py-4"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($payroll->status) }}</span></td>
                        <td class="px-6 py-4 text-right font-semibold"><x-money :amount="$payroll->total_net" /></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">No payroll runs available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $payrolls->links() }}</div>
</div>
@endsection
