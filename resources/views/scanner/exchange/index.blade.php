@extends('scanner.layout')

@section('title', 'Penukaran Tiket')

@section('content')
<div x-data="scannerApp()" class="space-y-6">
    <!-- Header Content -->
    <div class="flex flex-col space-y-2">
        <h2 class="text-xl font-bold">Penukaran Tiket</h2>
        <p class="text-xs text-gray-500">Scan QR Code tiket customer untuk menukarkannya dengan Wristband.</p>
    </div>

    <!-- Event Selection -->
    <div class="glass-dark rounded-2xl p-4">
        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">Pilih Event</label>
        <select x-model="selectedEventId" @change="onEventChange()" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500 transition">
            <option value="">-- Pilih Event --</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}">{{ $event->name }}</option>
            @endforeach
        </select>
    </div>

    @if($stats)
    <!-- Quick Stats -->
    <div class="grid grid-cols-2 gap-3">
        <div class="glass-dark rounded-2xl p-4 flex flex-col items-center justify-center text-center">
            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Tiket Terjual</span>
            <span class="text-2xl font-bold text-white">{{ $stats['total_tickets'] }}</span>
        </div>
        <div class="glass-dark rounded-2xl p-4 flex flex-col items-center justify-center text-center border-l-4 border-indigo-500">
            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Sudah Ditukar</span>
            <span class="text-2xl font-bold text-white">{{ $stats['total_exchanged'] }}</span>
            <span class="text-[10px] text-indigo-400 font-bold mt-1">{{ $stats['exchange_percentage'] }}%</span>
        </div>
    </div>
    @endif

    <!-- Scanner Section -->
    <template x-if="selectedEventId">
        <div class="space-y-4">
            <!-- Scanner UI -->
            <div class="glass-dark rounded-3xl overflow-hidden relative">
                <div x-show="!scannerActive" class="p-8 text-center bg-gray-900/50">
                    <div class="w-20 h-20 bg-indigo-600/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <button @click="startScanner()" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg shadow-indigo-600/20">
                        Buka Kamera
                    </button>
                    <p class="text-[10px] text-gray-500 mt-4 uppercase tracking-widest">Atau gunakan scanner hardware</p>
                </div>

                <div x-show="scannerActive" id="qr-reader" class="w-full"></div>
                
                <div x-show="scannerActive" @click="stopScanner()" class="absolute top-4 right-4 z-10 p-2 bg-black/50 rounded-full text-white cursor-pointer hover:bg-black/70 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>

            <!-- Manual Input with better UX -->
            <div class="glass-dark rounded-2xl p-4 flex gap-2">
                <input type="text" 
                       x-model="manualQR"
                       @keyup.enter="processQR(manualQR)"
                       placeholder="Scan/Input manual kode tiket..."
                       class="flex-1 bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition">
                <button @click="processQR(manualQR)" class="p-3 bg-gray-800 text-white rounded-xl hover:bg-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </div>
    </template>

    <!-- Overlay Result (Success/Fail) -->
    <div x-show="showResult" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[60] flex items-center justify-center p-6 bg-gray-950/90 backdrop-blur-md" x-cloak>
        
        <div class="w-full max-w-sm glass-dark rounded-[2.5rem] p-8 text-center relative overflow-hidden">
            <!-- Background Glow -->
            <div :class="resultSuccess ? 'bg-green-500/20' : 'bg-red-500/20'" class="absolute inset-0 z-0"></div>

            <div class="relative z-10">
                <!-- Icon -->
                <div :class="resultSuccess ? 'bg-green-500' : 'bg-red-500'" class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl">
                    <template x-if="resultSuccess">
                        <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </template>
                    <template x-if="!resultSuccess">
                        <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </template>
                </div>

                <h3 x-text="resultTitle" class="text-2xl font-black mb-2 text-white italic tracking-tight"></h3>
                <p x-text="resultMessage" class="text-gray-400 text-sm mb-6"></p>

                <!-- Ticket details if success -->
                <template x-if="resultSuccess && ticket">
                    <div class="bg-gray-900/50 rounded-2xl p-4 mb-6 text-left space-y-2 border border-white/5">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-800">
                            <span class="text-[10px] font-bold text-gray-500 uppercase">Kategori</span>
                            <span x-text="ticket.ticket_category?.name" class="text-xs font-bold text-indigo-400"></span>
                        </div>
                        <div class="flex justify-between items-center" x-show="ticket.seat">
                            <span class="text-[10px] font-bold text-gray-500 uppercase">No. Kursi</span>
                            <span x-text="ticket.seat?.full_seat" class="text-sm font-black text-white"></span>
                        </div>
                        <div class="pt-1">
                            <span class="text-[10px] font-bold text-gray-500 uppercase block mb-1">Customer</span>
                            <p x-text="consumer?.name" class="text-sm font-bold text-white"></p>
                        </div>
                    </div>
                </template>

                <div class="space-y-3">
                    <template x-if="resultSuccess && !wristband">
                        <button @click="prepareWristbandScan()" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-black py-4 rounded-2xl shadow-lg transition active:scale-95 flex items-center justify-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 17h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            SCAN GELANG FISIK
                        </button>
                    </template>
                    
                    <button @click="resetResult()" class="w-full bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 rounded-2xl transition">
                        TUTUP
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    function scannerApp() {
        return {
            selectedEventId: '{{ request("event_id") }}',
            manualQR: '',
            scannerActive: false,
            scanner: null,
            isProcessing: false,
            
            showResult: false,
            resultSuccess: false,
            resultTitle: '',
            resultMessage: '',
            
            ticket: null,
            consumer: null,
            wristband: null,
            awaitingWristband: false, // New state for 2nd scan

            onEventChange() {
                if (this.selectedEventId) {
                    window.location.href = `{{ route('scanner.exchange') }}?event_id=${this.selectedEventId}`;
                }
            },

            async startScanner(mode = 'ticket') {
                this.awaitingWristband = (mode === 'wristband');
                this.scannerActive = true;
                this.$nextTick(() => {
                    this.scanner = new Html5QrcodeScanner(
                        "qr-reader",
                        { 
                            fps: 10, 
                            qrbox: { width: 250, height: 250 },
                            aspectRatio: 1.0
                        },
                        false
                    );
                    this.scanner.render((text) => this.processQR(text), (err) => {});
                });
            },

            async stopScanner() {
                if (this.scanner) {
                    await this.scanner.clear();
                    this.scanner = null;
                }
                this.scannerActive = false;
            },

            processQR(qrCode) {
                if (!qrCode || this.isProcessing) return;
                
                if (this.awaitingWristband) {
                    this.confirmWristbandLink(qrCode);
                    return;
                }

                this.isProcessing = true;
                this.vibrate(50);
                
                fetch('{{ route("scanner.exchange.scan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        qr_code: qrCode,
                        event_id: this.selectedEventId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.isProcessing = false;
                    this.manualQR = '';
                    
                    if (data.success) {
                        this.ticket = data.ticket;
                        this.consumer = data.consumer;
                        this.wristband = null;
                        this.showScanResult(true, 'TIKET VALID!', 'Data ditemukan. Silakan hubungkan gelang fisik.');
                        this.playSound('success');
                        this.vibrate([100, 50, 100]);
                    } else {
                        this.showScanResult(false, 'SCAN GAGAL', data.message);
                        this.playSound('error');
                        this.vibrate(300);
                    }
                })
                .catch(err => {
                    this.isProcessing = false;
                    this.showScanResult(false, 'ERROR SISTEM', err.message);
                });
            },

            prepareWristbandScan() {
                this.showResult = false;
                this.startScanner('wristband');
            },

            confirmWristbandLink(wristbandCode) {
                if (!this.ticket || this.isProcessing) return;
                
                this.isProcessing = true;
                this.vibrate(50);
                this.stopScanner();
                
                fetch('{{ route("scanner.exchange.issue") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        ticket_id: this.ticket.id,
                        event_id: this.selectedEventId,
                        wristband_code: wristbandCode
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.isProcessing = false;
                    this.awaitingWristband = false;
                    if (data.success) {
                        this.wristband = data.wristband;
                        this.showScanResult(true, 'BERHASIL!', 'Gelang telah diaktivasi dan dihubungkan ke tiket.');
                        this.playSound('success');
                    } else {
                        this.showScanResult(false, 'AKTIVASI GAGAL', data.message);
                        this.playSound('error');
                    }
                })
                .catch(err => {
                    this.isProcessing = false;
                    this.showScanResult(false, 'ERROR', err.message);
                });
            },

            showScanResult(success, title, msg) {
                this.resultSuccess = success;
                this.resultTitle = title;
                this.resultMessage = msg;
                this.showResult = true;
                this.stopScanner();
            },

            resetResult() {
                const wasSuccessfulAktivasi = this.resultSuccess && this.wristband;
                this.showResult = false;
                this.ticket = null;
                this.consumer = null;
                this.wristband = null;
                this.awaitingWristband = false;
                
                if (wasSuccessfulAktivasi) {
                    window.location.reload();
                }
            },

            vibrate(pattern) {
                if (navigator.vibrate) navigator.vibrate(pattern);
            },

            playSound(type) {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                
                osc.connect(gain);
                gain.connect(ctx.destination);
                
                if (type === 'success') {
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1760, ctx.currentTime + 0.1);
                    gain.gain.setValueAtTime(0.1, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.2);
                } else {
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(440, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(110, ctx.currentTime + 0.2);
                    gain.gain.setValueAtTime(0.1, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                }
                
                osc.start();
                osc.stop(ctx.currentTime + 0.3);
            }
        };
    }
</script>
@endpush
