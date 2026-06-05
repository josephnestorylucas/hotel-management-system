@extends('layouts.app')

@section('title', 'Chart of Accounts')
@section('page-title', 'Chart of Accounts')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Chart of Accounts</h2>
            <p class="mt-1 text-sm text-gray-500">Review active account codes, parent groupings, and accounting balances.</p>
        </div>
        <a href="{{ route('accounting.accounts.create') }}" class="inline-flex rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white">New account</a>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-6 py-4 font-semibold">Code</th>
                    <th class="px-6 py-4 font-semibold">Name</th>
                    <th class="px-6 py-4 font-semibold">Type</th>
                    <th class="px-6 py-4 font-semibold">Parent</th>
                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($accounts as $account)
                    <tr>
                        <td class="px-6 py-4 font-semibold text-secondary">{{ $account->code }}</td>
                        <td class="px-6 py-4">{{ $account->name }}</td>
                        <td class="px-6 py-4 capitalize">{{ $account->type }}</td>
                        <td class="px-6 py-4">{{ $account->parent?->name ?: '-' }}</td>
                        <td class="px-6 py-4 text-right"><a href="{{ route('accounting.accounts.edit', $account) }}" class="font-semibold text-indigo-600">Edit</a></td>
                    </tr>
                @empty
                    <x-empty-state table colspan="5" title="No accounts available" message="Create the first account to begin structuring the chart of accounts." />
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
