<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Scanner System - Tiketin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-indigo-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-6">
                    <h1 class="text-xl font-bold">Tiketin Scanner</h1>
                    <div class="hidden sm:flex space-x-2">
                        @if(auth()->user()->hasRole(['wristband_exchange_officer', 'super_admin']))
                            <a href="{{ route('scanner.exchange') }}" class="px-3 py-2 text-sm font-medium hover:bg-indigo-500 rounded-md {{ request()->is('scanner/exchange') ? 'bg-indigo-700' : '' }}">Cek Tiket</a>
                            <a href="{{ route('scanner.exchange.history') }}" class="px-3 py-2 text-sm font-medium hover:bg-indigo-500 rounded-md {{ request()->is('scanner/exchange/history') ? 'bg-indigo-700' : '' }}">Riwayat Scan</a>
                        @endif
                        @if(auth()->user()->hasRole(['wristband_validator', 'super_admin']))
                            <a href="{{ route('scanner.validate') }}" class="px-3 py-2 text-sm font-medium hover:bg-indigo-500 rounded-md {{ request()->is('scanner/validate') ? 'bg-indigo-700' : '' }}">Validasi Masuk</a>
                            <a href="{{ route('scanner.validate.history') }}" class="px-3 py-2 text-sm font-medium hover:bg-indigo-500 rounded-md {{ request()->is('scanner/validate/history') ? 'bg-indigo-700' : '' }}">Riwayat Scan</a>
                        @endif
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div id="onlineIndicator" class="flex items-center text-green-600">
                        <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span>Online
                    </div>
                    <span class="text-sm">{{ auth()->user()->name }}</span>
                    <form action="{{ route('scanner.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm hover:text-indigo-200">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
            
        <!-- Mobile Navigation (Visible only on small screens) -->
        <div class="sm:hidden flex justify-around pb-3 border-t border-indigo-500 mt-2 pt-3">
            @if(auth()->user()->hasRole(['wristband_exchange_officer', 'super_admin']))
                <a href="{{ route('scanner.exchange') }}" class="flex flex-col items-center space-y-1 {{ request()->is('scanner/exchange') ? 'text-white' : 'text-indigo-200' }}">
                    <span class="text-[10px] uppercase tracking-wider font-bold">Scanner</span>
                    @if(request()->is('scanner/exchange')) <div class="w-1 h-1 bg-white rounded-full"></div> @endif
                </a>
                <a href="{{ route('scanner.exchange.history') }}" class="flex flex-col items-center space-y-1 {{ request()->is('scanner/exchange/history') ? 'text-white' : 'text-indigo-200' }}">
                    <span class="text-[10px] uppercase tracking-wider font-bold">Riwayat</span>
                    @if(request()->is('scanner/exchange/history')) <div class="w-1 h-1 bg-white rounded-full"></div> @endif
                </a>
            @endif
            @if(auth()->user()->hasRole(['wristband_validator', 'super_admin']))
                <a href="{{ route('scanner.validate') }}" class="flex flex-col items-center space-y-1 {{ request()->is('scanner/validate') ? 'text-white' : 'text-indigo-200' }}">
                    <span class="text-[10px] uppercase tracking-wider font-bold">Scanner</span>
                    @if(request()->is('scanner/validate')) <div class="w-1 h-1 bg-white rounded-full"></div> @endif
                </a>
                <a href="{{ route('scanner.validate.history') }}" class="flex flex-col items-center space-y-1 {{ request()->is('scanner/validate/history') ? 'text-white' : 'text-indigo-200' }}">
                    <span class="text-[10px] uppercase tracking-wider font-bold">Riwayat</span>
                    @if(request()->is('scanner/validate/history')) <div class="w-1 h-1 bg-white rounded-full"></div> @endif
                </a>
            @endif
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
