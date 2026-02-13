@extends('layouts.app')

@section('title', $event->title)

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="container mx-auto px-4 max-w-7xl">

            <!-- Timer Warning (Sticky Top) -->
            <div id="timer-banner"
                class="fixed top-16 left-0 right-0 z-40 bg-gradient-to-r from-red-600 to-red-700 text-white py-3 shadow-lg transition-all duration-300">
                <div class="container mx-auto px-4">
                    <div class="flex items-center justify-center gap-4">
                        <i class="fas fa-clock text-2xl animate-pulse"></i>
                        <div class="text-center">
                            <p class="text-sm font-semibold">Selesaikan pembelian Anda dalam:</p>
                            <p class="text-2xl font-bold" id="countdown-display">10:00</p>
                        </div>
                        <i class="fas fa-exclamation-triangle text-2xl animate-pulse"></i>
                    </div>
                </div>
            </div>

            <!-- Spacer for fixed timer -->
            <div class="h-20 mb-4"></div>

            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-center">
                    <div class="flex items-center">
                        <div class="flex items-center text-brand-yellow relative">
                            <div class="rounded-full h-12 w-12 flex items-center justify-center border-2 border-brand-yellow bg-brand-yellow text-black font-bold"
                                id="step-1-circle">
                                1
                            </div>
                            <div
                                class="absolute top-14 left-1/2 transform -translate-x-1/2 whitespace-nowrap text-sm font-semibold">
                                Pilih Tiket
                            </div>
                        </div>
                        <div class="w-32 h-1 bg-gray-300" id="progress-line"></div>
                        <div class="flex items-center text-gray-400 relative">
                            <div class="rounded-full h-12 w-12 flex items-center justify-center border-2 border-gray-300 bg-white font-bold"
                                id="step-2-circle">
                                2
                            </div>
                            <div class="absolute top-14 left-1/2 transform -translate-x-1/2 whitespace-nowrap text-sm">
                                Data Pembeli
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Display Errors --}}
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded max-w-4xl mx-auto">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <p class="font-bold text-red-700">Terdapat kesalahan:</p>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded max-w-4xl mx-auto">
                    <div class="flex items-center">
                        <i class="fas fa-times-circle text-red-500 mr-2"></i>
                        <p class="text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- STEP 1: Ticket Selection -->
            <div id="step-1" class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- LEFT: Event Detail -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <!-- Event Image -->
                        <div class="relative overflow-hidden" style="aspect-ratio: 16 / 9;">
                            <img src="{{ $event->poster_image ? asset('storage/' . $event->poster_image) : asset('images/default-event.jpg') }}"
                                class="w-full h-full object-cover" alt="{{ $event->name }}">
                        </div>

                        <!-- Event Info -->
                        <div class="p-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                                    <i class="fas fa-ticket-alt mr-1"></i> Tersedia
                                </span>
                                @if ($event->status === 'published')
                                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                                        Published
                                    </span>
                                @endif
                            </div>

                            <h1 class="text-3xl font-bold text-slate-900 mb-4">{{ $event->name }}</h1>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="flex items-center gap-3 text-gray-600">
                                    <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                                        <i class="far fa-calendar text-purple-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Tanggal</p>
                                        <p class="font-semibold">{{ $event->event_date->format('d F Y') }}</p>
                                        @if ($event->event_end_date)
                                            <p class="text-xs text-gray-500">s/d
                                                {{ $event->event_end_date->format('d F Y') }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 text-gray-600">
                                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Lokasi</p>
                                        <p class="font-semibold">{{ $event->venue }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="font-bold text-lg mb-3">Tentang Event</h3>
                                <div class="text-gray-600 leading-relaxed prose max-w-none">
                                    {!! nl2br(e($event->description)) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Layout Venue -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-8 p-2">
                        <h3 class="font-bold text-lg mb-6 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            Layout Venue
                        </h3>
                        <div class="relative overflow-hidden rounded-lg" style="aspect-ratio: 16 / 9;">
                            <a href="#"
                                onclick="openImagePreview('{{ $event->venue_image ? asset('storage/' . $event->venue_image) : asset('images/default-event.jpg') }}')">
                                <img src="{{ $event->venue_image ? asset('storage/' . $event->venue_image) : asset('images/default-event.jpg') }}"
                                    class="w-full h-full object-cover rounded-lg" alt="{{ $event->name }}">
                            </a>
                        </div>
                    </div>

                    <!-- Syarat dan Ketentuan -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-8 p-8">
                        <h3 class="font-bold text-lg mb-6 flex items-center gap-2">
                            <i class="fas fa-file-contract text-red-600"></i>
                            Syarat dan Ketentuan
                        </h3>
                        <ul class="space-y-3 text-gray-700">
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-600 mt-1 flex-shrink-0"></i>
                                <span>Tiket yang sudah dibeli tidak dapat dikembalikan atau ditukar dengan uang.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-600 mt-1 flex-shrink-0"></i>
                                <span>Setiap pengunjung wajib membawa invoice tiket (cetak atau digital) dan identitas
                                    diri yang sah.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-600 mt-1 flex-shrink-0"></i>
                                <span>Tiket hanya berlaku untuk satu kali masuk dan tidak dapat digunakan ulang.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-600 mt-1 flex-shrink-0"></i>
                                <span>Penyelenggara berhak menolak pengunjung yang tidak memenuhi syarat atau melanggar
                                    peraturan.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-600 mt-1 flex-shrink-0"></i>
                                <span>Dilarang membawa senjata tajam, obat-obatan terlarang, atau benda berbahaya
                                    lainnya.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-600 mt-1 flex-shrink-0"></i>
                                <span>Penyelenggara tidak bertanggung jawab atas kehilangan barang berharga milik
                                    pengunjung.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-600 mt-1 flex-shrink-0"></i>
                                <span>Harap datang 30 menit sebelum acara dimulai untuk proses registrasi.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-600 mt-1 flex-shrink-0"></i>
                                <span>Dengan membeli tiket, pengunjung dianggap telah membaca dan menyetujui semua
                                    syarat dan ketentuan yang berlaku.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- RIGHT: Ticket Selection -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-28">
                        <h3 class="font-bold text-xl mb-6 text-slate-900">
                            <i class="fas fa-ticket-alt text-brand-yellow mr-2"></i> Pilih Tiket
                        </h3>

                        @if ($event->ticketTypes->count() > 0)
                            @foreach ($event->ticketTypes as $ticketType)
                                <div
                                    class="border border-gray-200 rounded-xl p-4 mb-4 hover:border-brand-yellow transition">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-slate-900">{{ $ticketType->name }}</h4>
                                            @if ($ticketType->description)
                                                <p class="text-xs text-gray-500 mt-1">{{ $ticketType->description }}</p>
                                            @endif

                                            @if ($ticketType->war_ticket)
                                                <span
                                                    class="inline-block mt-2 bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded">
                                                    <i class="fas fa-fire mr-1"></i> WAR TICKET!
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-right ml-4">
                                            @if ($ticketType->war_ticket)
                                                <p class="text-xs text-gray-400 line-through">
                                                    Rp {{ number_format($ticketType->price, 0, ',', '.') }}
                                                </p>
                                                <p class="font-bold text-lg text-red-600">
                                                    Rp {{ number_format($ticketType->current_price, 0, ',', '.') }}
                                                </p>
                                            @else
                                                <p class="font-bold text-lg text-slate-900">
                                                    Rp {{ number_format($ticketType->current_price, 0, ',', '.') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-users mr-1"></i> Tersisa {{ $ticketType->stock }} tiket
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <button type="button" onclick="updateQuantity({{ $ticketType->id }}, -1)"
                                                class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-lg flex items-center justify-center transition">
                                                <i class="fas fa-minus text-xs"></i>
                                            </button>
                                            <span class="w-8 text-center font-bold"
                                                id="qty-{{ $ticketType->id }}">0</span>
                                            <button type="button" onclick="updateQuantity({{ $ticketType->id }}, 1)"
                                                class="w-8 h-8 bg-brand-yellow hover:bg-yellow-400 rounded-lg flex items-center justify-center transition">
                                                <i class="fas fa-plus text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-ticket-alt text-4xl mb-3"></i>
                                <p>Belum ada tiket yang tersedia</p>
                            </div>
                        @endif

                        <!-- Summary -->
                        <div class="border-t border-gray-200 pt-4 mt-6">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Total Tiket</span>
                                <span class="font-semibold" id="total-tickets">0</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold mb-4">
                                <span>Total</span>
                                <span class="text-brand-yellow" id="total-price">Rp 0</span>
                            </div>
                            <button id="btn-next-step" onclick="goToStep2()" disabled
                                class="w-full bg-gray-300 text-gray-500 font-bold py-4 rounded-xl cursor-not-allowed">
                                Lanjut ke Data Pembeli
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Customer Data -->
            <div id="step-2" class="hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- LEFT: Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-lg p-8">
                            <h2 class="text-2xl font-bold text-slate-900 mb-6">
                                <i class="fas fa-user-edit text-brand-yellow mr-2"></i> Data Pembeli
                            </h2>

                            <form action="{{ route('checkout.process', $event->slug) }}" method="POST"
                                id="checkout-form">
                                @csrf

                                <div id="hidden-ticket-inputs"></div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Nama Lengkap <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="full_name" required value="{{ old('full_name') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-yellow transition"
                                            placeholder="Masukkan nama lengkap">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Email <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" name="email" required value="{{ old('email') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-yellow transition"
                                            placeholder="contoh@email.com">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            No. Telepon <span class="text-red-500">*</span>
                                        </label>
                                        <input type="tel" name="phone_number" required
                                            value="{{ old('phone_number') }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-yellow transition"
                                            placeholder="08xxxxxxxxxx">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Jenis Identitas <span class="text-red-500">*</span>
                                        </label>
                                        <select name="identity_type" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-yellow transition">
                                            <option value="">Pilih Jenis Identitas</option>
                                            <option value="ktp" {{ old('identity_type') == 'ktp' ? 'selected' : '' }}>
                                                KTP</option>
                                            <option value="sim" {{ old('identity_type') == 'sim' ? 'selected' : '' }}>
                                                SIM</option>
                                            <option value="passport"
                                                {{ old('identity_type') == 'passport' ? 'selected' : '' }}>Passport
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nomor Identitas <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="identity_number" required
                                        value="{{ old('identity_number') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-yellow transition"
                                        placeholder="Masukkan nomor identitas">
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-info-circle mr-1"></i> Nomor identitas diperlukan untuk
                                        verifikasi
                                        di lokasi event
                                    </p>
                                </div>

                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                                    <div class="flex items-start">
                                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                                        <div class="text-sm text-yellow-800">
                                            <p class="font-semibold mb-1">Penting!</p>
                                            <p>Pastikan data yang Anda masukkan sudah benar. Data ini akan digunakan
                                                untuk
                                                verifikasi tiket dan tidak dapat diubah setelah pembayaran.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-4">
                                    <button type="button" onclick="goToStep1()"
                                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-4 rounded-xl transition">
                                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                                    </button>
                                    <button type="submit" id="pay-button"
                                        class="flex-1 bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 rounded-xl transition transform hover:scale-105 shadow-lg">
                                        <i class="fas fa-lock mr-2"></i> Lanjut ke Pembayaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- RIGHT: Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-28">
                            <h3 class="font-bold text-xl mb-6 text-slate-900">
                                <i class="fas fa-receipt text-brand-yellow mr-2"></i> Ringkasan Pesanan
                            </h3>

                            <div class="mb-4 pb-4 border-b border-gray-200">
                                <p class="text-sm text-gray-600 mb-1">Event</p>
                                <p class="font-semibold text-slate-900">{{ $event->name }}</p>
                            </div>

                            <div class="space-y-3 mb-4 pb-4 border-b border-gray-200" id="summary-items">
                                <!-- Will be populated by JS -->
                            </div>

                            <div class="flex justify-between text-lg font-bold mb-4">
                                <span>Total Bayar</span>
                                <span class="text-brand-yellow" id="summary-total">Rp 0</span>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-shield-alt text-blue-600 mt-1 mr-3"></i>
                                    <div class="text-xs text-blue-800">
                                        <p class="font-semibold mb-1">Transaksi Aman</p>
                                        <p>Pembayaran Anda dilindungi oleh sistem keamanan Midtrans</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Timeout Modal -->
    <div id="timeout-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md mx-4 p-8 text-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-clock text-red-600 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-4">Waktu Habis!</h3>
            <p class="text-gray-600 mb-6">
                Maaf, waktu pembelian Anda telah habis. Anda akan diarahkan ke halaman utama.
            </p>
            <div class="text-3xl font-bold text-red-600 mb-6" id="redirect-countdown">5</div>
            <button onclick="redirectToHome()"
                class="w-full bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-6 rounded-xl transition">
                Kembali ke Beranda
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Timer Configuration
        const TIMER_DURATION = 10 * 60; // 10 minutes in seconds
        let timeRemaining = TIMER_DURATION;
        let timerInterval;
        let redirectCountdown = 5;
        let isTimerActive = true;

        // Ticket data
        const ticketPrices = {
            @foreach ($event->ticketTypes as $ticketType)
                {{ $ticketType->id }}: {{ $ticketType->current_price }},
            @endforeach
        };

        const ticketNames = {
            @foreach ($event->ticketTypes as $ticketType)
                {{ $ticketType->id }}: "{{ $ticketType->name }}",
            @endforeach
        };

        const quantities = {
            @foreach ($event->ticketTypes as $ticketType)
                {{ $ticketType->id }}: 0,
            @endforeach
        };

        // Format Rupiah
        function formatRupiah(amount) {
            return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Format time display
        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${minutes}:${secs.toString().padStart(2, '0')}`;
        }

        // Start Timer
        function startTimer() {
            timerInterval = setInterval(() => {
                if (!isTimerActive) return;

                timeRemaining--;
                updateTimerDisplay();

                // Change color when time is low
                const banner = document.getElementById('timer-banner');
                if (timeRemaining <= 60) { // Last minute
                    banner.className =
                        'fixed top-16 left-0 right-0 z-40 bg-gradient-to-r from-red-700 to-red-900 text-white py-3 shadow-lg transition-all duration-300';
                } else if (timeRemaining <= 180) { // Last 3 minutes
                    banner.className =
                        'fixed top-16 left-0 right-0 z-40 bg-gradient-to-r from-orange-600 to-red-600 text-white py-3 shadow-lg transition-all duration-300';
                }

                // Time's up!
                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    showTimeoutModal();
                }
            }, 1000);
        }

        // Update timer display
        function updateTimerDisplay() {
            document.getElementById('countdown-display').textContent = formatTime(timeRemaining);
        }

        // Show timeout modal
        function showTimeoutModal() {
            isTimerActive = false;
            document.getElementById('timeout-modal').classList.remove('hidden');

            const redirectInterval = setInterval(() => {
                redirectCountdown--;
                document.getElementById('redirect-countdown').textContent = redirectCountdown;

                if (redirectCountdown <= 0) {
                    clearInterval(redirectInterval);
                    redirectToHome();
                }
            }, 1000);
        }

        // Redirect to home
        function redirectToHome() {
            window.location.href = "{{ route('home') }}";
        }

        // Ticket quantity functions
        function updateQuantity(ticketId, change) {
            quantities[ticketId] = Math.max(0, quantities[ticketId] + change);
            updateUI();
        }

        function updateUI() {
            let totalTickets = 0;
            let totalPrice = 0;

            Object.keys(quantities).forEach(ticketId => {
                const qty = quantities[ticketId];
                document.getElementById('qty-' + ticketId).textContent = qty;
                totalTickets += qty;
                totalPrice += qty * ticketPrices[ticketId];
            });

            document.getElementById('total-tickets').textContent = totalTickets;
            document.getElementById('total-price').textContent = formatRupiah(totalPrice);

            const btn = document.getElementById('btn-next-step');
            if (totalTickets > 0) {
                btn.disabled = false;
                btn.className =
                    "w-full bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 rounded-xl transition transform hover:scale-105 shadow-lg";
            } else {
                btn.disabled = true;
                btn.className = "w-full bg-gray-300 text-gray-500 font-bold py-4 rounded-xl cursor-not-allowed";
            }
        }

        function goToStep2() {
            // Validate at least 1 ticket
            const totalTickets = Object.values(quantities).reduce((a, b) => a + b, 0);
            if (totalTickets === 0) {
                alert('Silakan pilih minimal 1 tiket');
                return;
            }

            // Update progress
            document.getElementById('step-1-circle').classList.remove('bg-brand-yellow', 'text-black');
            document.getElementById('step-1-circle').classList.add('bg-white', 'text-brand-yellow');
            document.getElementById('step-2-circle').classList.remove('bg-white', 'text-gray-400', 'border-gray-300');
            document.getElementById('step-2-circle').classList.add('bg-brand-yellow', 'text-black', 'border-brand-yellow');
            document.getElementById('progress-line').classList.remove('bg-gray-300');
            document.getElementById('progress-line').classList.add('bg-brand-yellow');

            // Hide step 1, show step 2
            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.remove('hidden');

            // Populate summary
            updateOrderSummary();

            // Add hidden inputs for tickets
            addHiddenInputs();

            // Scroll to top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function goToStep1() {
            // Update progress
            document.getElementById('step-1-circle').classList.add('bg-brand-yellow', 'text-black');
            document.getElementById('step-1-circle').classList.remove('bg-white', 'text-brand-yellow');
            document.getElementById('step-2-circle').classList.add('bg-white', 'text-gray-400', 'border-gray-300');
            document.getElementById('step-2-circle').classList.remove('bg-brand-yellow', 'text-black',
                'border-brand-yellow');
            document.getElementById('progress-line').classList.add('bg-gray-300');
            document.getElementById('progress-line').classList.remove('bg-brand-yellow');

            // Show step 1, hide step 2
            document.getElementById('step-1').classList.remove('hidden');
            document.getElementById('step-2').classList.add('hidden');

            // Scroll to top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function updateOrderSummary() {
            const summaryContainer = document.getElementById('summary-items');
            summaryContainer.innerHTML = '';

            let totalAmount = 0;
            let index = 0;

            Object.keys(quantities).forEach(ticketId => {
                const qty = quantities[ticketId];
                if (qty > 0) {
                    const price = ticketPrices[ticketId];
                    const subtotal = qty * price;
                    totalAmount += subtotal;

                    const itemHtml = `
                        <div class="flex justify-between text-sm">
                            <div>
                                <p class="text-gray-600 font-medium">${ticketNames[ticketId]}</p>
                                <p class="text-xs text-gray-500">${qty} x Rp ${formatRupiah(price)}</p>
                            </div>
                            <p class="font-semibold text-slate-900">Rp ${formatRupiah(subtotal)}</p>
                        </div>
                    `;
                    summaryContainer.innerHTML += itemHtml;
                }
            });

            // Service fee
            const serviceFee = 5000;
            totalAmount += serviceFee;

            summaryContainer.innerHTML += `
                <div class="flex justify-between text-sm">
                    <p class="text-gray-600">Biaya Layanan</p>
                    <p class="font-semibold text-slate-900">Rp ${formatRupiah(serviceFee)}</p>
                </div>
            `;

            document.getElementById('summary-total').textContent = 'Rp ' + formatRupiah(totalAmount);
        }

        function addHiddenInputs() {
            const container = document.getElementById('hidden-ticket-inputs');
            container.innerHTML = '';

            let index = 0;
            Object.keys(quantities).forEach(ticketId => {
                const qty = quantities[ticketId];
                if (qty > 0) {
                    container.innerHTML += `
                        <input type="hidden" name="items[${index}][ticket_type_id]" value="${ticketId}">
                        <input type="hidden" name="items[${index}][quantity]" value="${qty}">
                    `;
                    index++;
                }
            });
        }

        // Form submission - stop timer
        document.addEventListener('DOMContentLoaded', function() {
            // Start timer when page loads
            startTimer();
            updateUI();

            const form = document.getElementById('checkout-form');
            if (form) {
                form.addEventListener('submit', function() {
                    // Stop timer when form is submitted
                    isTimerActive = false;
                    clearInterval(timerInterval);

                    const btn = document.getElementById('pay-button');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
                });
            }
        });

        // Warn user before leaving page
        window.addEventListener('beforeunload', function(e) {
            if (isTimerActive) {
                const totalTickets = Object.values(quantities).reduce((a, b) => a + b, 0);
                if (totalTickets > 0) {
                    e.preventDefault();
                    e.returnValue =
                        'Anda memiliki tiket yang belum selesai dibeli. Yakin ingin meninggalkan halaman?';
                    return e.returnValue;
                }
            }
        });

        function openImagePreview(imageUrl) {
            const previewWindow = window.open('', '_blank');
            previewWindow.document.write(
                '<html><head><title>Image Preview</title></head><body style="margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: rgba(0, 0, 0, 0.8);">'
            );
            previewWindow.document.write('<img src="' + imageUrl + '" style="max-width: 100%; max-height: 100%;" />');
            previewWindow.document.write('</body></html>');
            previewWindow.document.close();
        }
    </script>
@endpush

@push('styles')
    <style>
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
@endpush
@endsection
