<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Scanner System - Tiketin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-indigo-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">Tiketin Scanner</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div id="onlineIndicator" class="flex items-center text-green-600">
                        <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span>Online
                    </div>
                    <span class="text-sm">{{ auth()->user()->name }}</span>
                    <form action="{{ route('scanner.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm hover:text-indigo-200">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="py-6">
        @yield('content')
    </main>

    <script src="{{ asset('js/scanner-offline.js') }}"></script>
    @stack('scripts')
</body>
</html>
