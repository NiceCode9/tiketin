@extends('layouts.app')

@section('title', 'Lacak Pesanan')

@section('content')
    <div class="py-12 bg-gradient-to-br from-slate-50 to-gray-100 min-h-screen">
        <div class="container mx-auto px-4 max-w-2xl">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-brand-yellow rounded-full mb-6 shadow-lg">
                    <i class="fas fa-search text-3xl text-black"></i>
                </div>
                <h1 class="text-4xl font-bold text-slate-900 mb-4">Lacak Pesanan Anda</h1>
                <p class="text-lg text-gray-600">
                    Masukkan nomor identitas yang digunakan saat pemesanan
                </p>
            </div>

            <!-- Tracking Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                @if (session('error'))
                    <div class="bg-red-50 border-2 border-red-200 rounded-xl p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-600 text-xl mr-3"></i>
                            <p class="text-red-800 font-semibold">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('tracking.track') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label for="identity_number" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-2 text-brand-yellow"></i>
                            Nomor Identitas
                        </label>
                        <input type="text" id="identity_number" name="identity_number"
                            value="{{ old('identity_number') }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-brand-yellow focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:ring-opacity-50 transition @error('identity_number') border-red-500 @enderror"
                            placeholder="Contoh: 3578123456789012" required autofocus>

                        @error('identity_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Masukkan nomor KTP, SIM, atau Passport yang Anda gunakan saat pemesanan
                        </p>
                    </div>

                    <button type="submit"
                        class="w-full bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 px-6 rounded-xl transition transform hover:scale-105 shadow-lg flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>
                        Lacak Pesanan
                    </button>
                </form>
            </div>

            <!-- Info Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl p-6 shadow-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                                <i class="fas fa-ticket-alt text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-bold text-gray-900 mb-1">Cek Status Pesanan</h3>
                            <p class="text-xs text-gray-600">
                                Lihat status pembayaran dan detail pesanan tiket Anda
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                                <i class="fas fa-download text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-bold text-gray-900 mb-1">Download Invoice</h3>
                            <p class="text-xs text-gray-600">
                                Unduh invoice dengan QR code untuk tukar gelang
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600 mb-4">
                    Tidak dapat menemukan pesanan Anda?
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="https://wa.me/6281234567890" target="_blank"
                        class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                        <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                    </a>
                    <a href="mailto:support@example.com"
                        class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                        <i class="fas fa-envelope mr-2"></i> Email Support
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
