<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laundry') — Hotel Management</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
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
<body class="bg-gray-100 min-h-screen">

<nav class="bg-white shadow px-6 py-4 flex items-center justify-between">
    <div class="font-bold text-lg text-gray-800">🧺 Laundry</div>
    <div class="flex gap-4 text-sm">
        <a href="{{ route('laundry.orders.index') }}"   class="text-gray-600 hover:text-blue-600">Orders</a>
        <a href="{{ route('laundry.orders.create') }}"  class="text-gray-600 hover:text-blue-600">New Order</a>
        @if(auth()->user()->hasAnyRole(['laundry_manager', 'supervisor', 'store_manager', 'admin']))
        <a href="{{ route('laundry.services.index') }}" class="text-gray-600 hover:text-blue-600">Price List</a>
        <a href="{{ route('laundry.reports.daily') }}"  class="text-gray-600 hover:text-blue-600">Reports</a>
        @endif
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600">← Dashboard</a>
    </div>
    <div class="flex items-center gap-3">
        @include('partials.notification-bell')
        <span class="text-sm text-gray-500">{{ auth()->user()->name }} — {{ auth()->user()->role->name ?? 'N/A' }}</span>
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

@stack('scripts')
</body>
</html>
