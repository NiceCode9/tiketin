@extends('layouts.app')

@section('title', $event->name)

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="container mx-auto px-4 max-w-7xl">

            <!-- Timer Warning (Sticky Top) -->
            <div id="timer-banner"
                class="fixed top-16 left-0 right-0 z-40 bg-gradient-to-r from-red-600 to-red-700 text-white py-3 shadow-lg transition-all duration-300 hidden">
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

            <!-- Spacer for fixed timer (hidden by default) -->
            <div id="timer-spacer" class="h-20 mb-4 hidden"></div>

            <!-- Progress Steps (Show only during order process) -->
            <div id="booking-steps" class="mb-12 hidden">
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

            <!-- MAIN CONTENT -->
            <div id="step-1" class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- LEFT: Event Detail -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <!-- Event Image -->
                        <div class="relative overflow-hidden" style="aspect-ratio: 16 / 9;">
                            <img src="{{ $event->poster_image ? asset('storage/' . $event->poster_image) : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=1200\u0026h=675\u0026fit=crop' }}"
                                class="w-full h-full object-cover" alt="{{ $event->name }}">
                        </div>

                        <!-- Event Info -->
                        <div class="p-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                                    <i class="fas fa-ticket-alt mr-1"></i> Tersedia
                                </span>
                                @if($event->eventCategory)
                                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                                    {{ $event->eventCategory->name }}
                                </span>
                                @endif
                            </div>

                            <h1 class="text-3xl font-bold text-slate-900 mb-4">{{ $event->name }}</h1>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-sm">
                                <div class="flex items-center gap-3 text-gray-600">
                                    <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="far fa-calendar text-purple-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Tanggal</p>
                                        <p class="font-semibold">{{ $event->event_date->format('d F Y') }}</p>
                                        <p class="text-xs text-gray-400">{{ $event->event_date->format('H:i') }} WIB</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 text-gray-600">
                                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Lokasi</p>
                                        <p class="font-semibold">{{ $event->venue->name ?? 'TBA' }}</p>
                                        <p class="text-xs text-gray-400">{{ $event->venue->address ?? '' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-6">
                                <h3 class="font-bold text-lg mb-3">Tentang Event</h3>
                                <div class="text-gray-600 leading-relaxed prose max-w-none prose-sm">
                                    {!! nl2br(e($event->description)) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Layout Venue -->
                    @if($event->layout_image)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-8 p-6">
                        <h3 class="font-bold text-lg mb-4 flex items-center gap-2 text-slate-900">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            Layout Venue
                        </h3>
                        <div class="relative overflow-hidden rounded-xl border border-gray-100">
                            <img src="{{ asset('storage/' . $event->layout_image) }}"
                                class="w-full h-auto cursor-pointer hover:opacity-90 transition"
                                onclick="openImagePreview(this.src)"
                                alt="Layout Venue">
                        </div>
                    </div>
                    @endif

                    <!-- Syarat dan Ketentuan -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-8 p-8">
                        <h3 class="font-bold text-lg mb-6 flex items-center gap-2 text-slate-900">
                            <i class="fas fa-file-contract text-red-600"></i>
                            Syarat dan Ketentuan
                        </h3>
                        <ul class="space-y-4 text-sm text-gray-700">
                            <li class="flex items-start gap-4">
                                <div class="w-6 h-6 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fas fa-check text-green-600 text-[10px]"></i>
                                </div>
                                <span>Tiket yang sudah dibeli tidak dapat dikembalikan atau ditukar dengan uang.</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="w-6 h-6 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fas fa-check text-green-600 text-[10px]"></i>
                                </div>
                                <span>Pastikan data diri sesuai dengan identitas asli (KTP/SIM/Paspor) untuk verifikasi di lokasi.</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="w-6 h-6 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fas fa-check text-green-600 text-[10px]"></i>
                                </div>
                                <span>Tiket digital akan dikirimkan melalui email setelah pembayaran dikonfirmasi.</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="w-6 h-6 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fas fa-check text-green-600 text-[10px]"></i>
                                </div>
                                <span>Satu email/nomor identitas dapat digunakan untuk membeli maksimal 5 tiket per transaksi.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- RIGHT: Ticket Selection -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-28 border border-white">
                        <h3 class="font-bold text-xl mb-6 text-slate-900">
                            <i class="fas fa-ticket-alt text-brand-yellow mr-2"></i> Pilih Tiket
                        </h3>

                        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                            @forelse ($event->ticketCategories as $category)
                                <div class="border border-gray-100 rounded-2xl p-4 hover:border-brand-yellow transition bg-gray-50/50">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-slate-900">{{ $category->name }}</h4>
                                            @if($category->description)
                                                <p class="text-[10px] text-gray-500 mt-1 leading-relaxed">{{ $category->description }}</p>
                                            @endif
                                            @if($category->is_seated)
                                                <span class="inline-block mt-2 bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-0.5 rounded">
                                                    <i class="fas fa-chair mr-1"></i> Dengan Tempat Duduk
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-right ml-4">
                                            <p class="font-bold text-lg text-slate-900">
                                                Rp {{ number_format($category->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between mt-4">
                                        {{-- <span class="text-[10px] text-gray-500">
                                            <i class="fas fa-users mr-1"></i> Tersedia
                                        </span> --}}
                                        <div class="flex items-center gap-3 bg-white rounded-xl border border-gray-200 p-1 ml-auto">
                                            <button type="button" onclick="updateQuantity({{ $category->id }}, -1)"
                                                class="w-8 h-8 hover:bg-gray-100 rounded-lg flex items-center justify-center transition text-gray-400 hover:text-red-500">
                                                <i class="fas fa-minus text-[10px]"></i>
                                            </button>
                                            <span class="w-6 text-center font-bold text-sm" id="qty-{{ $category->id }}">0</span>
                                            <button type="button" onclick="updateQuantity({{ $category->id }}, 1)"
                                                class="w-8 h-8 hover:bg-gray-100 rounded-lg flex items-center justify-center transition text-gray-400 hover:text-green-500">
                                                <i class="fas fa-plus text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-2xl">
                                    <i class="fas fa-ticket-alt text-4xl mb-3 opacity-20"></i>
                                    <p class="text-sm">Tiket belum tersedia.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Summary Area (Mobile Hidden/Show based on JS) -->
                        <div class="border-t border-gray-100 pt-6 mt-6">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-500">Total Tiket</span>
                                <span class="font-bold text-slate-900" id="total-tickets">0</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold mb-6">
                                <span class="text-slate-900">Total Bayar</span>
                                <span class="text-brand-yellow" id="total-price">Rp 0</span>
                            </div>
                            
                            <button id="btn-next-step" onclick="goToStep2()" disabled
                                class="w-full bg-gray-200 text-gray-400 font-bold py-4 rounded-2xl cursor-not-allowed transition duration-300">
                                Beli Tiket Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Customer Data (Hidden by default) -->
            <div id="step-2" class="hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- LEFT: Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-lg p-8">
                            <h2 class="text-2xl font-bold text-slate-900 mb-8 border-b border-gray-100 pb-4">
                                <i class="fas fa-user-edit text-brand-yellow mr-2"></i> Data Informasi Pembeli
                            </h2>

                            <form action="{{ route('orders.store', $event->slug) }}" method="POST" id="checkout-form">
                                @csrf

                                <div id="hidden-ticket-inputs"></div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                            Nama Lengkap <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="consumer_name" required value="{{ old('consumer_name') }}"
                                            class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-xl focus:bg-white focus:border-brand-yellow transition-all duration-300 outline-none"
                                            placeholder="Masukkan nama sesuai identitas">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                            Email Aktif <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" name="consumer_email" required value="{{ old('consumer_email') }}"
                                            class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-xl focus:bg-white focus:border-brand-yellow transition-all duration-300 outline-none"
                                            placeholder="E-ticket akan dikirim ke sini">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                            Nomor WhatsApp <span class="text-red-500">*</span>
                                        </label>
                                        <input type="tel" name="consumer_whatsapp" required value="{{ old('consumer_whatsapp') }}"
                                            class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-xl focus:bg-white focus:border-brand-yellow transition-all duration-300 outline-none"
                                            placeholder="Contoh: 08123456789">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                            Jenis Identitas <span class="text-red-500">*</span>
                                        </label>
                                        <select name="consumer_identity_type" required
                                            class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-xl focus:bg-white focus:border-brand-yellow transition-all duration-300 outline-none appearance-none">
                                            <option value="">Pilih Jenis Identitas</option>
                                            <option value="KTP" {{ old('consumer_identity_type') == 'KTP' ? 'selected' : '' }}>KTP (Kartu Tanda Penduduk)</option>
                                            <option value="SIM" {{ old('consumer_identity_type') == 'SIM' ? 'selected' : '' }}>SIM (Surat Izin Mengemudi)</option>
                                            <option value="Student Card" {{ old('consumer_identity_type') == 'Student Card' ? 'selected' : '' }}>Student Card</option>
                                            <option value="Passport" {{ old('consumer_identity_type') == 'Passport' ? 'selected' : '' }}>Paspor (Passport)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-10">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                        Nomor Identitas <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="consumer_identity_number" required value="{{ old('consumer_identity_number') }}"
                                        class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-xl focus:bg-white focus:border-brand-yellow transition-all duration-300 outline-none"
                                        placeholder="Masukkan NIK / No. SIM / No. Paspor">
                                    <p class="text-[10px] text-gray-400 mt-2 flex items-center">
                                        <i class="fas fa-info-circle mr-1 text-blue-500"></i> Digunakan untuk proses check-in di venue acara.
                                    </p>
                                </div>

                                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 mb-10">
                                    <div class="flex items-start">
                                        <i class="fas fa-shield-alt text-blue-600 mt-1 mr-4 text-xl"></i>
                                        <div class="text-sm">
                                            <p class="font-bold text-blue-900 mb-1">Privasi \u0026 Keamanan Data</p>
                                            <p class="text-blue-700/80 leading-relaxed">Data Anda akan dijaga kerahasiaannya dan hanya digunakan untuk kepentingan verifikasi tiket serta pengiriman informasi terkait event ini.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row gap-4">
                                    <button type="button" onclick="goToStep1()"
                                        class="flex-1 bg-white border border-gray-200 text-gray-500 font-bold py-4 rounded-2xl hover:bg-gray-50 hover:text-gray-700 transition order-2 md:order-1">
                                        <i class="fas fa-arrow-left mr-2 text-xs"></i> Kembali Pilih Tiket
                                    </button>
                                    <button type="submit" id="pay-button"
                                        class="flex-[2] bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 rounded-2xl transition transform active:scale-95 shadow-lg shadow-yellow-500/20 order-1 md:order-2">
                                        Selesaikan Pesanan \u0026 Bayar <i class="fas fa-chevron-right ml-2 text-xs"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- RIGHT: Summary (Step 2) -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-28 border border-white">
                            <h3 class="font-bold text-xl mb-6 text-slate-900 shadow-sm border-b border-gray-50 pb-3">
                                <i class="fas fa-receipt text-brand-yellow mr-2"></i> Rincian Pesanan
                            </h3>

                            <div class="mb-6">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter mb-1">Nama Acara</p>
                                <p class="font-bold text-slate-900 text-lg leading-tight">{{ $event->name }}</p>
                            </div>

                            <div class="space-y-4 mb-8 pb-6 border-b border-gray-50" id="summary-items">
                                <!-- Will be populated by JS -->
                            </div>

                            <div class="flex justify-between text-xl font-bold mb-8">
                                <span class="text-slate-900">Total Harga</span>
                                <span class="text-brand-yellow" id="summary-total">Rp 0</span>
                            </div>

                            <div class="flex items-center justify-center p-4 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/d/d1/Midtrans.png" alt="Midtrans Secure" class="h-6 grayscale opacity-60">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Timeout Modal -->
    <div id="timeout-modal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-10 text-center transform transition-all duration-500 scale-90 opacity-0" id="modal-container">
            <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-8">
                <i class="fas fa-hourglass-end text-red-500 text-4xl animate-bounce"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-4">Sesi Habis</h3>
            <p class="text-gray-500 mb-8 leading-relaxed">
                Waktu pemesanan Anda telah berakhir. Harga dan ketersediaan tiket mungkin telah berubah.
            </p>
            <div class="relative w-16 h-16 mx-auto mb-8">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="4" fill="transparent" class="text-gray-100" />
                    <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="4" fill="transparent" class="text-red-500" stroke-dasharray="175.9" stroke-dashoffset="0" id="circle-progress" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center font-bold text-xl text-red-500" id="redirect-countdown">5</div>
            </div>
            <button onclick="redirectToHome()"
                class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 px-6 rounded-2xl transition active:scale-95">
                Balik ke Beranda
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Timer Configuration
        const TIMER_DURATION = 15 * 60; // 15 minutes
        let timeRemaining = TIMER_DURATION;
        let timerInterval;
        let redirectCountdown = 5;
        let isTimerActive = false;

        // Ticket data from Laravel
        const ticketPrices = {
            @foreach ($event->ticketCategories as $category)
                {{ $category->id }}: {{ $category->price }},
            @endforeach
        };

        const ticketNames = {
            @foreach ($event->ticketCategories as $category)
                {{ $category->id }}: "{{ $category->name }}",
            @endforeach
        };

        const quantities = {
            @foreach ($event->ticketCategories as $category)
                {{ $category->id }}: 0,
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

        // Start Timer flow
        function startBookingTimer() {
            if(isTimerActive) return;
            
            isTimerActive = true;
            document.getElementById('timer-banner').classList.remove('hidden');
            document.getElementById('timer-spacer').classList.remove('hidden');
            document.getElementById('booking-steps').classList.remove('hidden');

            timerInterval = setInterval(() => {
                timeRemaining--;
                document.getElementById('countdown-display').textContent = formatTime(timeRemaining);

                if (timeRemaining <= 60) {
                    document.getElementById('timer-banner').classList.replace('from-red-600', 'from-red-800');
                }

                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    showTimeoutModal();
                }
            }, 1000);
        }

        function showTimeoutModal() {
            const modal = document.getElementById('timeout-modal');
            const container = document.getElementById('modal-container');
            const progress = document.getElementById('circle-progress');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                container.classList.remove('scale-90', 'opacity-0');
            }, 10);

            let offset = 0;
            const step = 175.9 / 5;

            const interval = setInterval(() => {
                redirectCountdown--;
                offset += step;
                progress.style.strokeDashoffset = offset;
                document.getElementById('redirect-countdown').textContent = redirectCountdown;

                if (redirectCountdown <= 0) {
                    clearInterval(interval);
                    redirectToHome();
                }
            }, 1000);
        }

        function redirectToHome() {
            window.location.href = "{{ route('home') }}";
        }

        // UI Interactions
        function updateQuantity(id, change) {
            const current = quantities[id];
            const newVal = Math.max(0, current + change);
            
            // Per-ticket limit (example: max 5)
            if(newVal > 5) return;

            quantities[id] = newVal;
            document.getElementById('qty-' + id).textContent = newVal;
            
            renderSummary();
            
            // Start timer if any ticket selected
            const total = Object.values(quantities).reduce((a, b) => a + b, 0);
            if(total > 0) startBookingTimer();
        }

        function renderSummary() {
            let totalQty = 0;
            let totalAmount = 0;

            Object.keys(quantities).forEach(id => {
                totalQty += quantities[id];
                totalAmount += (quantities[id] * ticketPrices[id]);
            });

            document.getElementById('total-tickets').textContent = totalQty;
            document.getElementById('total-price').textContent = 'Rp ' + formatRupiah(totalAmount);

            const btn = document.getElementById('btn-next-step');
            if (totalQty > 0) {
                btn.disabled = false;
                btn.classList.replace('bg-gray-200', 'bg-brand-yellow');
                btn.classList.replace('text-gray-400', 'text-black');
                btn.classList.replace('cursor-not-allowed', 'hover:bg-yellow-400');
            } else {
                btn.disabled = true;
                btn.classList.replace('bg-brand-yellow', 'bg-gray-200');
                btn.classList.replace('text-black', 'text-gray-400');
                btn.classList.replace('hover:bg-yellow-400', 'cursor-not-allowed');
            }
        }

        function goToStep2() {
            // UI Transition
            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.remove('hidden');
            
            // Circles
            document.getElementById('step-1-circle').classList.replace('bg-brand-yellow', 'bg-white');
            document.getElementById('step-1-circle').classList.replace('text-black', 'text-brand-yellow');
            document.getElementById('step-2-circle').classList.replace('bg-white', 'bg-brand-yellow');
            document.getElementById('step-2-circle').classList.replace('text-gray-400', 'text-black');
            document.getElementById('step-2-circle').classList.add('border-brand-yellow');
            document.getElementById('progress-line').classList.replace('bg-gray-300', 'bg-brand-yellow');

            // Populate Hidden Inputs
            const container = document.getElementById('hidden-ticket-inputs');
            container.innerHTML = '';
            
            const summaryItems = document.getElementById('summary-items');
            summaryItems.innerHTML = '';

            let idx = 0;
            Object.keys(quantities).forEach(id => {
                const q = quantities[id];
                if(q > 0) {
                    // Hidden Form Inputs
                    container.innerHTML += `
                        <input type="hidden" name="items[${idx}][ticket_category_id]" value="${id}">
                        <input type="hidden" name="items[${idx}][quantity]" value="${q}">
                    `;
                    
                    // Summary Display
                    summaryItems.innerHTML += `
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-bold text-slate-800 text-sm">${ticketNames[id]}</p>
                                <p class="text-[10px] text-gray-400">${q} tiket x Rp ${formatRupiah(ticketPrices[id])}</p>
                            </div>
                            <p class="font-bold text-slate-900 text-sm">Rp ${formatRupiah(q * ticketPrices[id])}</p>
                        </div>
                    `;
                    idx++;
                }
            });

            const total = Object.values(quantities).reduce((acc, q, i) => acc + (q * ticketPrices[Object.keys(quantities)[i]]), 0);
            document.getElementById('summary-total').textContent = 'Rp ' + formatRupiah(total);

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function goToStep1() {
            document.getElementById('step-2').classList.add('hidden');
            document.getElementById('step-1').classList.remove('hidden');
            
            // Circles Reset
            document.getElementById('step-1-circle').classList.replace('bg-white', 'bg-brand-yellow');
            document.getElementById('step-1-circle').classList.replace('text-brand-yellow', 'text-black');
            document.getElementById('step-2-circle').classList.replace('bg-brand-yellow', 'bg-white');
            document.getElementById('step-2-circle').classList.replace('text-black', 'text-gray-400');
            document.getElementById('progress-line').classList.replace('bg-brand-yellow', 'bg-gray-300');

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function openImagePreview(url) {
            window.open(url, '_blank');
        }

        document.getElementById('checkout-form').addEventListener('submit', function() {
            const btn = document.getElementById('pay-button');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
        });
    </script>
@endpush

@push('styles')
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #FCD34D;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
@endpush
