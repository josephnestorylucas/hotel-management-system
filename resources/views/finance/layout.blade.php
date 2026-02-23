<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Finance') — Hotel Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-white shadow px-6 py-4 flex items-center justify-between">
    <div class="font-bold text-lg text-gray-800">💳 Finance</div>
    <div class="flex gap-4 text-sm">
        <a href="{{ route('finance.dashboard') }}"        class="text-gray-600 hover:text-blue-600">Dashboard</a>
        <a href="{{ route('finance.payments.index') }}"   class="text-gray-600 hover:text-blue-600">Payments</a>
    </div>
    <div class="flex items-center gap-3">
        @include('partials.notification-bell')
        <span class="text-sm text-gray-500">{{ auth()->user()->name }} — {{ auth()->user()->role->name }}</span>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-6 mt-4">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif
</div>

<main class="max-w-7xl mx-auto px-6 py-6">
    @yield('content')
</main>

</body>
</html>
