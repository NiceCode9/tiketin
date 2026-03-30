<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Scanner - Tiketin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-dark {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen pb-20">
    <!-- Header -->
    <header class="sticky top-0 z-40 glass-dark">
        <div class="px-4 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-sm tracking-tight">TIKETIN<span class="text-indigo-400">SCANNER</span></h1>
                    <div class="flex items-center text-[10px] text-gray-400 uppercase tracking-widest font-semibold">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                        Sistem Aktif
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="hidden sm:block text-right">
                    <p class="text-xs font-medium">{{ auth()->guard('scanner')->user()->name }}</p>
                    <p class="text-[10px] text-gray-500">{{ ucfirst(auth()->guard('scanner')->user()->roles->first()->name ?? 'Petugas') }}</p>
                </div>
                <form action="{{ route('scanner.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="p-2 text-gray-400 hover:text-white transition rounded-full hover:bg-gray-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-md mx-auto py-6 px-4">
        @yield('content')
    </main>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 z-50 glass-dark safe-bottom">
        <div class="flex items-center justify-around h-16">
            @php
                $isExchange = request()->is('scanner/exchange*');
                $isValidate = request()->is('scanner/validate*');
            @endphp

            @if(auth()->guard('scanner')->user()->hasRole(['wristband_exchange_officer', 'super_admin']))
                <a href="{{ route('scanner.exchange') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ $isExchange && !request()->is('scanner/exchange/history') ? 'text-indigo-400' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    <span class="text-[10px] font-bold">EXCHANGE</span>
                </a>
                <a href="{{ route('scanner.exchange.history') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->is('scanner/exchange/history') ? 'text-indigo-400' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-[10px] font-bold">HISTORY SITE</span>
                </a>
            @endif

            @if(auth()->guard('scanner')->user()->hasRole(['wristband_validator', 'super_admin']))
                <a href="{{ route('scanner.validate') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ $isValidate && !request()->is('scanner/validate/history') ? 'text-indigo-400' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-[10px] font-bold">VALIDATE</span>
                </a>
                <a href="{{ route('scanner.validate.history') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->is('scanner/validate/history') ? 'text-indigo-400' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="text-[10px] font-bold">LOGS</span>
                </a>
            @endif
        </div>
    </nav>

    @stack('scripts')
</body>
</html>
