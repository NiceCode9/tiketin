@extends('layouts.app')

@section('title', 'Tentang Kami - Untix')

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white py-20 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-brand-yellow rounded-full filter blur-3xl animate-pulse"></div>
            <div
                class="absolute bottom-0 right-0 w-96 h-96 bg-blue-500 rounded-full filter blur-3xl animate-pulse delay-1000">
            </div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <div class="mb-6">
                    <span class="inline-block bg-brand-yellow text-black text-sm font-bold px-4 py-2 rounded-full">
                        Tentang Kami
                    </span>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                    Menjembatani <span class="text-brand-yellow">Event</span> & <span
                        class="text-brand-yellow">Penonton</span>
                </h1>
                <p class="text-xl text-gray-300 mb-8 leading-relaxed">
                    Platform digital pemesanan tiket event yang modern, aman, dan mudah digunakan
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('events.index') }}"
                        class="bg-brand-yellow hover:bg-yellow-400 text-black font-bold px-8 py-4 rounded-xl transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-calendar-alt mr-2"></i> Jelajah Event
                    </a>
                    <a href="#kontak"
                        class="bg-white/10 hover:bg-white/20 text-white font-bold px-8 py-4 rounded-xl transition backdrop-blur border border-white/20">
                        <i class="fas fa-envelope mr-2"></i> Hubungi Kami
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    {{-- <section class="py-12 bg-white border-b border-gray-200">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-5xl mx-auto">
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-brand-yellow mb-2">1000+</div>
                    <div class="text-gray-600 text-sm">Event Terlaksana</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-brand-yellow mb-2">50K+</div>
                    <div class="text-gray-600 text-sm">Pengguna Aktif</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-brand-yellow mb-2">100%</div>
                    <div class="text-gray-600 text-sm">Aman & Terpercaya</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-brand-yellow mb-2">24/7</div>
                    <div class="text-gray-600 text-sm">Dukungan Online</div>
                </div>
            </div>
        </div>
    </section> --}}

    <!-- Latar Belakang -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-4xl font-bold text-slate-900 mb-6">
                            Mengapa <span class="text-brand-yellow">Untix</span> Hadir?
                        </h2>
                        <p class="text-gray-700 text-lg leading-relaxed mb-6">
                            Perkembangan industri event di Indonesia semakin pesat, namun masih banyak penyelenggara dan
                            penonton yang menghadapi berbagai kendala.
                        </p>
                        <p class="text-gray-700 text-lg leading-relaxed mb-8">
                            <strong class="text-slate-900">Untix hadir sebagai solusi</strong> dengan menghadirkan platform
                            ticketing berbasis teknologi yang terintegrasi dari penjualan, pembayaran, hingga validasi
                            tiket.
                        </p>

                        <!-- Problems -->
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-times text-red-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 mb-1">Proses pembelian yang rumit</h4>
                                    <p class="text-gray-600 text-sm">Banyak platform masih menggunakan sistem manual</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-times text-red-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 mb-1">Risiko penipuan tiket</h4>
                                    <p class="text-gray-600 text-sm">Tiket palsu merugikan pengguna dan penyelenggara</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-times text-red-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 mb-1">Kurangnya transparansi</h4>
                                    <p class="text-gray-600 text-sm">Data penjualan tidak real-time dan sulit dipantau</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-times text-red-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 mb-1">Sistem check-in tidak efisien</h4>
                                    <p class="text-gray-600 text-sm">Proses validasi manual memakan waktu lama</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute -top-6 -left-6 w-full h-full bg-brand-yellow rounded-3xl opacity-20"></div>
                        <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=800&q=80"
                            alt="Event" class="relative rounded-3xl shadow-2xl w-full h-auto">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan Kami -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-slate-900 mb-4">Apa yang Kami <span
                            class="text-brand-yellow">Lakukan</span></h2>
                    <p class="text-gray-600 text-lg">Layanan lengkap untuk ekosistem event digital</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Service 1 -->
                    <div
                        class="bg-gradient-to-br from-blue-50 to-white p-8 rounded-2xl border border-blue-100 hover:shadow-xl transition">
                        <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-ticket-alt text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-4">Platform Penjualan Tiket Online</h3>
                        <ul class="space-y-3 text-gray-700">
                            <li class="flex items-start">
                                <i class="fas fa-check text-blue-600 mr-3 mt-1"></i>
                                <span>Pembelian tiket secara digital</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-blue-600 mr-3 mt-1"></i>
                                <span>Pilihan metode pembayaran yang beragam</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-blue-600 mr-3 mt-1"></i>
                                <span>Pengiriman tiket otomatis melalui email</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Service 2 -->
                    <div
                        class="bg-gradient-to-br from-purple-50 to-white p-8 rounded-2xl border border-purple-100 hover:shadow-xl transition">
                        <div class="w-16 h-16 bg-purple-600 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-search text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-4">Jelajah Event</h3>
                        <ul class="space-y-3 text-gray-700">
                            <li class="flex items-start">
                                <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                                <span>Menampilkan berbagai event dari berbagai kategori</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                                <span>Informasi event yang jelas dan terstruktur</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                                <span>Filter berdasarkan waktu, lokasi, dan jenis event</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Service 3 -->
                    <div
                        class="bg-gradient-to-br from-green-50 to-white p-8 rounded-2xl border border-green-100 hover:shadow-xl transition">
                        <div class="w-16 h-16 bg-green-600 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-chart-line text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-4">Manajemen Event untuk Penyelenggara</h3>
                        <ul class="space-y-3 text-gray-700">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                                <span>Dashboard pemantauan penjualan tiket</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                                <span>Laporan transaksi real-time</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                                <span>Sistem validasi tiket saat hari acara</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Service 4 -->
                    <div
                        class="bg-gradient-to-br from-orange-50 to-white p-8 rounded-2xl border border-orange-100 hover:shadow-xl transition">
                        <div class="w-16 h-16 bg-orange-600 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-headset text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-4">Tracking & Dukungan Pengguna</h3>
                        <ul class="space-y-3 text-gray-700">
                            <li class="flex items-start">
                                <i class="fas fa-check text-orange-600 mr-3 mt-1"></i>
                                <span>Fitur pelacakan pesanan</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-orange-600 mr-3 mt-1"></i>
                                <span>Layanan bantuan pelanggan</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-orange-600 mr-3 mt-1"></i>
                                <span>Sistem notifikasi status transaksi</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Nilai-Nilai Kami -->
    <section class="py-20 bg-gradient-to-br from-slate-900 to-slate-800 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div
                class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-brand-yellow rounded-full filter blur-3xl">
            </div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4">Nilai-Nilai <span class="text-brand-yellow">Kami</span></h2>
                    <p class="text-gray-300 text-lg">Prinsip yang memandu setiap langkah kami</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-brand-yellow rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-shield-alt text-black text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Keamanan</h3>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            Mengutamakan keamanan data dan transaksi pengguna melalui sistem yang terpercaya
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="w-20 h-20 bg-brand-yellow rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-bolt text-black text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Efisiensi</h3>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            Menciptakan proses yang cepat dan praktis bagi pengguna maupun penyelenggara
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="w-20 h-20 bg-brand-yellow rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-eye text-black text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Transparansi</h3>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            Informasi event, harga, dan kebijakan ditampilkan secara jelas dan terbuka
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="w-20 h-20 bg-brand-yellow rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-lightbulb text-black text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Inovasi</h3>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            Terus mengembangkan fitur dan teknologi sesuai kebutuhan industri event
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Visi & Misi -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Visi -->
                    <div
                        class="bg-gradient-to-br from-brand-yellow/10 to-orange-50 p-10 rounded-3xl border-2 border-brand-yellow/20">
                        <div class="w-16 h-16 bg-brand-yellow rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-bullseye text-black text-2xl"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-slate-900 mb-6">Visi Kami</h2>
                        <p class="text-gray-700 text-lg leading-relaxed">
                            Menjadi platform ticketing event digital yang <strong>terpercaya, inovatif, dan
                                berkelanjutan</strong> serta mendukung pertumbuhan industri kreatif dan hiburan di
                            Indonesia.
                        </p>
                    </div>

                    <!-- Misi -->
                    <div class="bg-gradient-to-br from-blue-50 to-white p-10 rounded-3xl border-2 border-blue-200">
                        <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-rocket text-white text-2xl"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-slate-900 mb-6">Misi Kami</h2>
                        <ul class="space-y-4 text-gray-700">
                            <li class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-3 mt-0.5 font-bold text-sm">
                                    1</div>
                                <span>Menyediakan layanan pembelian tiket yang mudah dan aman bagi pengguna</span>
                            </li>
                            <li class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-3 mt-0.5 font-bold text-sm">
                                    2</div>
                                <span>Mendukung penyelenggara event dengan sistem manajemen yang efisien</span>
                            </li>
                            <li class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-3 mt-0.5 font-bold text-sm">
                                    3</div>
                                <span>Mengurangi risiko penipuan tiket melalui sistem digital terverifikasi</span>
                            </li>
                            <li class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-3 mt-0.5 font-bold text-sm">
                                    4</div>
                                <span>Memberikan pengalaman terbaik bagi seluruh ekosistem event</span>
                            </li>
                            <li class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-3 mt-0.5 font-bold text-sm">
                                    5</div>
                                <span>Terus berinovasi mengikuti perkembangan teknologi dan kebutuhan pasar</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Target Audience -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-slate-900 mb-4">Untuk <span class="text-brand-yellow">Siapa</span>
                        Untix?</h2>
                    <p class="text-gray-600 text-lg">Kami melayani berbagai pihak dalam ekosistem event</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Untuk Penyelenggara -->
                    <div class="bg-white p-10 rounded-3xl shadow-lg hover:shadow-2xl transition">
                        <div class="flex items-center mb-6">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mr-4">
                                <i class="fas fa-users-cog text-white text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900">Untuk Penyelenggara Event</h3>
                        </div>

                        <p class="text-gray-700 mb-6">
                            Untix membuka peluang kerja sama bagi berbagai pihak:
                        </p>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-purple-50 p-4 rounded-xl text-center">
                                <i class="fas fa-building text-purple-600 text-2xl mb-2"></i>
                                <p class="text-sm font-semibold text-gray-800">Event Organizer</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-xl text-center">
                                <i class="fas fa-users text-purple-600 text-2xl mb-2"></i>
                                <p class="text-sm font-semibold text-gray-800">Komunitas</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-xl text-center">
                                <i class="fas fa-university text-purple-600 text-2xl mb-2"></i>
                                <p class="text-sm font-semibold text-gray-800">Institusi Pendidikan</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-xl text-center">
                                <i class="fas fa-handshake text-purple-600 text-2xl mb-2"></i>
                                <p class="text-sm font-semibold text-gray-800">Brand & Sponsor</p>
                            </div>
                        </div>

                        <p class="text-gray-600 text-sm mt-6">
                            Kami menyediakan solusi ticketing yang dapat disesuaikan dengan kebutuhan event, mulai dari
                            event kecil hingga skala besar.
                        </p>
                    </div>

                    <!-- Untuk Pengguna -->
                    <div class="bg-white p-10 rounded-3xl shadow-lg hover:shadow-2xl transition">
                        <div class="flex items-center mb-6">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-brand-yellow to-orange-400 rounded-2xl flex items-center justify-center mr-4">
                                <i class="fas fa-user-friends text-black text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900">Untuk Pengguna</h3>
                        </div>

                        <p class="text-gray-700 mb-6">
                            Dengan Untix, pengguna mendapatkan kemudahan:
                        </p>

                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-search text-brand-yellow"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 mb-1">Menemukan event favorit dengan mudah</h4>
                                    <p class="text-gray-600 text-sm">Filter lengkap untuk pencarian event</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-ticket-alt text-brand-yellow"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 mb-1">Membeli tiket tanpa antre</h4>
                                    <p class="text-gray-600 text-sm">Proses checkout yang cepat dan mudah</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-mobile-alt text-brand-yellow"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 mb-1">Mengelola tiket secara digital</h4>
                                    <p class="text-gray-600 text-sm">Akses tiket kapan saja, dimana saja</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-bell text-brand-yellow"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 mb-1">Mendapatkan informasi event real-time
                                    </h4>
                                    <p class="text-gray-600 text-sm">Notifikasi update event langsung ke Anda</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-white" id="faq">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-slate-900 mb-4">Pertanyaan yang Sering <span
                            class="text-brand-yellow">Ditanyakan</span></h2>
                    <p class="text-gray-600 text-lg">Temukan jawaban atas pertanyaan Anda</p>
                </div>

                <div class="space-y-4">
                    <!-- FAQ Item 1 -->
                    <div class="bg-gray-50 rounded-2xl overflow-hidden border border-gray-200">
                        <button class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-100 transition"
                            onclick="toggleFaq(1)">
                            <h3 class="font-bold text-lg text-slate-900 pr-4">Apa itu Untix?</h3>
                            <i class="fas fa-chevron-down text-brand-yellow transition-transform" id="faq-icon-1"></i>
                        </button>
                        <div class="hidden px-6 pb-6" id="faq-content-1">
                            <p class="text-gray-700 leading-relaxed">
                                Untix adalah platform online untuk menjelajahi event dan membeli tiket secara digital dengan
                                sistem yang modern, aman, dan mudah digunakan.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="bg-gray-50 rounded-2xl overflow-hidden border border-gray-200">
                        <button class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-100 transition"
                            onclick="toggleFaq(2)">
                            <h3 class="font-bold text-lg text-slate-900 pr-4">Bagaimana cara membeli tiket?</h3>
                            <i class="fas fa-chevron-down text-brand-yellow transition-transform" id="faq-icon-2"></i>
                        </button>
                        <div class="hidden px-6 pb-6" id="faq-content-2">
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Pilih event di menu <strong>Jelajah Event</strong></li>
                                <li>Pilih jenis tiket yang diinginkan</li>
                                <li>Lakukan pembayaran sesuai metode yang dipilih</li>
                                <li>Tiket akan dikirim melalui email atau dapat diakses di akun Anda</li>
                            </ol>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="bg-gray-50 rounded-2xl overflow-hidden border border-gray-200">
                        <button class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-100 transition"
                            onclick="toggleFaq(3)">
                            <h3 class="font-bold text-lg text-slate-900 pr-4">Metode pembayaran apa saja yang tersedia?
                            </h3>
                            <i class="fas fa-chevron-down text-brand-yellow transition-transform" id="faq-icon-3"></i>
                        </button>
                        <div class="hidden px-6 pb-6" id="faq-content-3">
                            <p class="text-gray-700 mb-3">Kami mendukung berbagai metode pembayaran:</p>
                            <ul class="space-y-2 text-gray-700">
                                <li><i class="fas fa-check text-green-600 mr-2"></i> Transfer Bank</li>
                                <li><i class="fas fa-check text-green-600 mr-2"></i> E-wallet (OVO, GoPay, Dana, dll)</li>
                                <li><i class="fas fa-check text-green-600 mr-2"></i> Virtual Account</li>
                                <li><i class="fas fa-check text-green-600 mr-2"></i> Kartu Kredit/Debit</li>
                            </ul>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="bg-gray-50 rounded-2xl overflow-hidden border border-gray-200">
                        <button class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-100 transition"
                            onclick="toggleFaq(4)">
                            <h3 class="font-bold text-lg text-slate-900 pr-4">Bagaimana jika pembayaran gagal?</h3>
                            <i class="fas fa-chevron-down text-brand-yellow transition-transform" id="faq-icon-4"></i>
                        </button>
                        <div class="hidden px-6 pb-6" id="faq-content-4">
                            <p class="text-gray-700 leading-relaxed">
                                Jika pembayaran gagal, Anda dapat mengulang pembayaran atau hubungi tim kami melalui halaman
                                <a href="#kontak" class="text-brand-yellow font-semibold hover:underline">Kontak</a> untuk
                                bantuan lebih lanjut.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="bg-gray-50 rounded-2xl overflow-hidden border border-gray-200">
                        <button class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-100 transition"
                            onclick="toggleFaq(5)">
                            <h3 class="font-bold text-lg text-slate-900 pr-4">Apakah tiket bisa dikembalikan (refund)?</h3>
                            <i class="fas fa-chevron-down text-brand-yellow transition-transform" id="faq-icon-5"></i>
                        </button>
                        <div class="hidden px-6 pb-6" id="faq-content-5">
                            <p class="text-gray-700 leading-relaxed">
                                Kebijakan refund mengikuti ketentuan masing-masing event organizer. Silakan periksa
                                kebijakan refund pada halaman detail event sebelum membeli tiket.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Item 6 -->
                    <div class="bg-gray-50 rounded-2xl overflow-hidden border border-gray-200">
                        <button class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-100 transition"
                            onclick="toggleFaq(6)">
                            <h3 class="font-bold text-lg text-slate-900 pr-4">Saya belum menerima tiket, apa yang harus
                                dilakukan?</h3>
                            <i class="fas fa-chevron-down text-brand-yellow transition-transform" id="faq-icon-6"></i>
                        </button>
                        <div class="hidden px-6 pb-6" id="faq-content-6">
                            <p class="text-gray-700 leading-relaxed">
                                Periksa folder spam di email Anda atau cek menu <a href="{{ route('tracking.index') }}"
                                    class="text-brand-yellow font-semibold hover:underline">Tracking Pesanan</a>. Jika
                                masih bermasalah, silakan hubungi kami untuk bantuan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-20 bg-gradient-to-br from-slate-900 to-slate-800 text-white" id="kontak">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold mb-4">Hubungi <span class="text-brand-yellow">Kami</span></h2>
                    <p class="text-gray-300 text-lg">Ada pertanyaan atau ingin bekerja sama? Kami siap membantu</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Email -->
                    <div
                        class="bg-white/10 backdrop-blur p-8 rounded-2xl text-center border border-white/20 hover:bg-white/20 transition">
                        <div class="w-16 h-16 bg-brand-yellow rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-envelope text-black text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-xl mb-2">Email</h3>
                        <a href="mailto:unoviacreative@gmail.com" class="text-brand-yellow hover:underline">
                            unoviacreative@gmail.com
                        </a>
                    </div>

                    <!-- WhatsApp -->
                    <div
                        class="bg-white/10 backdrop-blur p-8 rounded-2xl text-center border border-white/20 hover:bg-white/20 transition">
                        <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-whatsapp text-white text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-xl mb-2">WhatsApp</h3>
                        <a href="https://wa.me/6285190021551" class="text-brand-yellow hover:underline" target="_blank">
                            085190021551
                        </a>
                    </div>

                    <!-- Jam Operasional -->
                    <div
                        class="bg-white/10 backdrop-blur p-8 rounded-2xl text-center border border-white/20 hover:bg-white/20 transition">
                        <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clock text-white text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-xl mb-2">Jam Operasional</h3>
                        <p class="text-gray-300">Senin - Jumat</p>
                        <p class="text-brand-yellow">09:00 - 17:00 WIB</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-brand-yellow to-orange-400">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-4xl md:text-5xl font-bold text-black mb-6">
                    Siap Memulai Perjalanan Event Anda?
                </h2>
                <p class="text-black/80 text-xl mb-8">
                    Bergabunglah dengan ribuan pengguna yang sudah mempercayai Untix
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('events.index') }}"
                        class="bg-black hover:bg-gray-900 text-white font-bold px-8 py-4 rounded-xl transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-calendar-alt mr-2"></i> Jelajah Event
                    </a>
                    <a href="{{ route('tracking.index') }}"
                        class="bg-white hover:bg-gray-100 text-black font-bold px-8 py-4 rounded-xl transition shadow-lg">
                        <i class="fas fa-search mr-2"></i> Lacak Pesanan
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function toggleFaq(id) {
            const content = document.getElementById(`faq-content-${id}`);
            const icon = document.getElementById(`faq-icon-${id}`);

            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }
    </script>
@endpush

@push('styles')
    <style>
        .animate-pulse {
            animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .delay-1000 {
            animation-delay: 1s;
        }
    </style>
@endpush
