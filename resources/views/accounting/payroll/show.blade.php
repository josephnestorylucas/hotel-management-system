@extends('layouts.app')

@section('title', $payrollRun->reference_no)
@section('page-title', 'Payroll Details')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl bg-white p-5 shadow-sm md:col-span-2">
            <div class="text-sm text-gray-500">Payroll</div>
            <div class="mt-2 text-2xl font-extrabold text-secondary">{{ $payrollRun->reference_no }}</div>
            <div class="mt-2 text-sm text-gray-500">{{ $payrollRun->period_month }}</div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">Gross</div><div class="mt-2 text-lg font-bold text-secondary"><x-money :amount="$payrollRun->total_gross" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">Net</div><div class="mt-2 text-lg font-bold text-secondary"><x-money :amount="$payrollRun->total_net" /></div></div>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-6 py-4 font-semibold">Staff</th>
                    <th class="px-6 py-4 font-semibold text-right">Gross</th>
                    <th class="px-6 py-4 font-semibold text-right">PAYE</th>
                    <th class="px-6 py-4 font-semibold text-right">Net</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payrollRun->lines as $line)
                    <tr>
                        <td class="px-6 py-4">{{ $line->staff_name }}</td>
                        <td class="px-6 py-4 text-right"><x-money :amount="$line->gross_salary" /></td>
                        <td class="px-6 py-4 text-right"><x-money :amount="$line->paye" /></td>
                        <td class="px-6 py-4 text-right font-semibold"><x-money :amount="$line->net_salary" /></td>
                    </tr>
                @empty
                    <x-empty-state table colspan="4" title="No payroll lines recorded" message="This payroll run does not yet include any staff lines." />
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
