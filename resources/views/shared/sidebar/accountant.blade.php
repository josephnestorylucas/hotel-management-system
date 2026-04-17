<div class="flex-1 overflow-y-auto p-4 space-y-2">
    <a href="{{ route('accountant.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span>{{ __('accountant.sidebar.dashboard') }}</span>
    </a>

    <a href="{{ route('accountant.overview') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.overview') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span>{{ __('accountant.sidebar.financial_overview') }}</span>
    </a>

    <a href="{{ route('accountant.transactions') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.transactions') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span>{{ __('accountant.sidebar.transactions') }}</span>
    </a>

    <a href="{{ route('accountant.journal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.journal.*') || request()->routeIs('accounting.journal.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span>{{ __('accountant.sidebar.journal_entries') }}</span>
    </a>

    <a href="{{ route('accountant.payables.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.payables.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ __('accountant.sidebar.accounts_payable') }}</span>
    </a>

    <a href="{{ route('accountant.accounts-receivable') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.accounts-receivable') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ __('accountant.sidebar.accounts_receivable') }}</span>
    </a>

    @if(auth()->user()?->hasRole('ACCOUNTANT'))
        <a href="{{ route('accountant.payments.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.payments.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
            </svg>
            <span>{{ __('accountant.sidebar.supplier_payments') }}</span>
        </a>
    @endif

    <a href="{{ route('accountant.receipts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.receipts.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2l-2-1-2 1-2-1-2 1-2-1-2 1V6a2 2 0 012-2z"/>
        </svg>
        <span>{{ __('accountant.sidebar.receipts') }}</span>
    </a>

    <a href="{{ route('accountant.expenses') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.expenses') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m-7 5h8a2 2 0 002-2V7.414a2 2 0 00-.586-1.414l-2.414-2.414A2 2 0 0015.586 3H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
        <span>{{ __('accountant.sidebar.expense_management') }}</span>
    </a>

    <a href="{{ route('accountant.reports') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.reports') || request()->routeIs('accounting.reports.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span>{{ __('accountant.sidebar.reports_center') }}</span>
    </a>

    <a href="{{ route('accountant.audit-logs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('accountant.audit-logs') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }} transition-colors font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h4l2 2h4a2 2 0 012 2v12a2 2 0 01-2 2z"/>
        </svg>
        <span>{{ __('accountant.sidebar.audit_logs') }}</span>
    </a>
</div>
