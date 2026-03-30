{{-- resources/views/store/layout.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Store') — Hotel Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#005eb8',
                        secondary: '#000000',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                }
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans antialiased">

{{-- Nav --}}
<nav class="bg-white shadow px-6 py-4 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="text-primary hover:text-blue-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/></svg>
        </a>
        <span class="font-bold text-lg text-gray-800">Store Module</span>
    </div>
    <div class="flex gap-4 text-sm">
        <a href="{{ route('store.products.index') }}"
           class="{{ request()->routeIs('store.products.*') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary' }}">Products</a>
        <a href="{{ route('store.stock.levels') }}"
           class="{{ request()->routeIs('store.stock.*') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary' }}">Stock</a>
        <a href="{{ route('store.adjustments.index') }}"
           class="{{ request()->routeIs('store.adjustments.*') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary' }}">Adjustments</a>
        <a href="{{ route('store.internal-requests.index') }}"
           class="{{ request()->routeIs('store.internal-requests.*') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary' }}">Requests</a>
        <a href="{{ route('store.transfers.index') }}"
           class="{{ request()->routeIs('store.transfers.*') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary' }}">Transfers</a>
        @if(auth()->user()->hasAnyRole(['STORE_MANAGER', 'STORE_KEEPER', 'SUPERVISOR']))
        <a href="{{ route('store.reports.movements') }}"
           class="{{ request()->routeIs('store.reports.*') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary' }}">Reports</a>
        @endif
    </div>
    <div class="flex items-center gap-3">
        @include('partials.notification-bell')
        <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
        <span class="text-xs bg-primary/10 text-primary px-2 py-1 rounded-full font-medium">{{ str_replace('_', ' ', auth()->user()->role->name) }}</span>
    </div>
</nav>

{{-- Flash messages --}}
<div class="max-w-7xl mx-auto px-6 mt-4">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-medium">{{ session('info') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

{{-- Page content --}}
<main class="max-w-7xl mx-auto px-6 py-6">
    @yield('content')
</main>

@stack('scripts')
</body>
</html>
