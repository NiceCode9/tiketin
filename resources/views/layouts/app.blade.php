<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Meta CSRF Token (Untuk Laravel) -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Untix') }} - @yield('title', 'E-Ticketing Platform')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Midtrans Snap (Sandbox) -->
    <script type="text/javascript" src="{{ config('midtrans.snap_url') }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

    @stack('styles')
    @stack('head-scripts')
</head>

<body class="font-sans bg-white text-slate-800 flex flex-col min-h-screen">

    <!-- =========================================================================
         NAVBAR
    ========================================================================== -->
    <nav class="fixed w-full z-50 bg-gray-800 border-b border-gray-700 transition-all duration-300" id="navbar">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                    <img src="{{ asset('logo.png') }}" alt="Untix Logo" class="h-10 w-auto">
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('home') }}"
                        class="px-4 py-2 rounded-full font-medium transition {{ request()->routeIs('home') ? 'bg-brand-yellow text-black' : 'text-white hover:text-brand-yellow' }}">
                        Beranda
                    </a>
                    <a href="{{ route('events.index') }}"
                        class="px-4 py-2 rounded-full font-medium transition {{ request()->routeIs('events.*') ? 'bg-brand-yellow text-black' : 'text-white hover:text-brand-yellow' }}">
                        Jelajah
                    </a>
                    <a href="{{ route('tracking.index') }}"
                        class="px-4 py-2 rounded-full {{ request()->routeIs('tracking.*') ? 'bg-brand-yellow text-black' : 'text-white hover:text-brand-yellow' }} font-medium transition">
                        Tracking Pesanan
                    </a>
                    <a href="{{ route('about') }}"
                        class="px-4 py-2 rounded-full {{ request()->routeIs('about') ? 'bg-brand-yellow text-black' : 'text-white hover:text-brand-yellow' }} font-medium transition">
                        Tentang
                    </a>
                    <a href="#"
                        class="px-4 py-2 rounded-full text-white hover:text-brand-yellow font-medium transition">
                        Hubungi Kami
                    </a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <button class="text-white hover:text-brand-yellow transition">
                        <i class="fas fa-search text-lg"></i>
                    </button>
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-700 text-white">
                        <i class="fas fa-globe"></i>
                        <span class="text-sm font-medium">ID</span>
                    </div>
                    @auth
                        <div class="flex items-center space-x-3">
                            <span class="text-white text-sm">{{ Auth::user()->name }}</span>
                            <form method="POST" action="{{ route('filament.admin.auth.logout') }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('filament.admin.auth.login') }}"
                            class="bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-2 px-6 rounded-lg transition transform hover:scale-105">
                            Masuk
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-white hover:text-brand-yellow focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div id="mobile-menu" class="hidden md:hidden bg-gray-800 border-t border-gray-700">
            <div class="px-4 pt-2 pb-4 space-y-1">
                <a href="{{ route('home') }}"
                    class="block px-3 py-2 text-base font-medium rounded-lg {{ request()->routeIs('home') ? 'bg-brand-yellow text-black' : 'text-white hover:bg-gray-700' }}">
                    Beranda
                </a>
                <a href="{{ route('events.index') }}"
                    class="block px-3 py-2 text-base font-medium rounded-lg {{ request()->routeIs('events.*') ? 'bg-brand-yellow text-black' : 'text-white hover:bg-gray-700' }}">
                    Jelajah
                </a>
                <a href="{{ route('tracking.index') }}"
                    class="block px-3 py-2 text-base font-medium text-white hover:bg-gray-700 rounded-lg">
                    Tracking
                </a>
                <a href="{{ route('about') }}"
                    class="block px-3 py-2 text-base font-medium text-white hover:bg-gray-700 rounded-lg">
                    Tentang Kami
                </a>
                <a href="#" class="block px-3 py-2 text-base font-medium text-white hover:bg-gray-700 rounded-lg">
                    Hubungi Kami
                </a>
                @auth
                    <div class="border-t border-gray-700 pt-2 mt-2">
                        <span class="block px-3 py-2 text-sm text-gray-300">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-3 py-2 text-base font-medium text-red-400 hover:text-red-300 rounded-lg">
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('filament.admin.auth.login') }}"
                        class="block px-3 py-2 text-base font-medium bg-brand-yellow text-black hover:bg-yellow-400 rounded-lg mt-2">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </nav>
    <!-- ================= END NAVBAR ================= -->


    <!-- =========================================================================
         MAIN CONTENT
    ========================================================================== -->
    <main class="flex-grow pt-16">
        @yield('content')
    </main>
    <!-- ================= END MAIN CONTENT ================= -->


    <!-- =========================================================================
         FOOTER
    ========================================================================== -->
    <footer class="bg-gray-900 text-gray-400 py-12 border-t border-gray-800">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Brand -->
                <div>
                    <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                        <img src="{{ asset('logo.png') }}" alt="Untix Logo" class="h-10 w-auto">
                    </a>
                    {{-- <p class="text-sm text-gray-400">Platform ticketing event terpercaya di Indonesia.</p> --}}
                </div>

                <!-- Quick Links -->
                <div>
                    <h5 class="text-white font-bold mb-4">Quick Links</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-brand-yellow transition">Beranda</a></li>
                        <li><a href="{{ route('events.index') }}" class="hover:text-brand-yellow transition">Jelajah
                                Event</a></li>
                        <li><a href="#" class="hover:text-brand-yellow transition">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-brand-yellow transition">Cara Pesan</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h5 class="text-white font-bold mb-4">Support</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-brand-yellow transition">FAQ</a></li>
                        <li><a href="#" class="hover:text-brand-yellow transition">Kontak</a></li>
                        <li><a href="#" class="hover:text-brand-yellow transition">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-brand-yellow transition">Kebijakan Privasi</a></li>
                    </ul>
                </div>

                <!-- Follow Us -->
                <div>
                    <h5 class="text-white font-bold mb-4">Follow Us</h5>
                    <p class="text-sm text-gray-400 mb-4">Stay updated with our latest events</p>
                    <div class="flex gap-3">
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-brand-yellow flex items-center justify-center text-black hover:bg-yellow-400 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-brand-yellow flex items-center justify-center text-black hover:bg-yellow-400 transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-brand-yellow flex items-center justify-center text-black hover:bg-yellow-400 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-6 text-center text-sm">
                <p>&copy; {{ date('Y') }} Untix. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <!-- ================= END FOOTER ================= -->


    <!-- Base Scripts -->
    <script>
        // Mobile Menu Toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Helper: Format Rupiah
        function formatRupiah(n) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(n);
        }
    </script>

    @stack('scripts')
</body>

</html>
