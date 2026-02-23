{{-- resources/views/restaurant/layout.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Restaurant') — Hotel Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#005eb8', secondary: '#000000' } } }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-white shadow px-6 py-4 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 text-sm">&larr; Back to Dashboard</a>
        <span class="text-gray-300">|</span>
        <div class="font-bold text-lg text-gray-800">🍸🍽️ Bar & Restaurant</div>
    </div>
    <div class="flex gap-4 text-sm">
        <a href="{{ route('restaurant.orders.index') }}"  class="{{ request()->routeIs('restaurant.orders.*') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-blue-600' }}">Orders</a>
        <a href="{{ route('restaurant.orders.create') }}" class="{{ request()->routeIs('restaurant.orders.create') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-blue-600' }}">New Order</a>
        <a href="{{ route('restaurant.tables.index') }}"  class="{{ request()->routeIs('restaurant.tables.*') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-blue-600' }}">Tables</a>
        @if(auth()->user()->hasRole('RESTAURANT_MANAGER'))
        <a href="{{ route('restaurant.menu.index') }}"    class="{{ request()->routeIs('restaurant.menu.*') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-blue-600' }}">Menu</a>
        <a href="{{ route('restaurant.reports.daily-sales') }}" class="{{ request()->routeIs('restaurant.reports.*') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-blue-600' }}">Reports</a>
        @endif
    </div>
    <div class="flex items-center gap-3">
        @include('partials.notification-bell')
        <span class="text-sm text-gray-500">{{ auth()->user()->name }} — {{ ucwords(str_replace('_', ' ', auth()->user()->role->name)) }}</span>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-6 mt-4">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-800 px-4 py-3 rounded mb-4">
            {{ session('info') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<main class="max-w-7xl mx-auto px-6 py-6">
    @yield('content')
</main>

</body>
</html>
