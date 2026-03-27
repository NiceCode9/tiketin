@extends('layouts.app')

@section('title', 'Select Seats - ' . $event->name)

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            padding: 4px 8px !important;
            border-radius: 0.5rem !important;
            border-color: #d1d5db !important;
            background-color: white !important;
            display: flex !important;
            align-items: center !important;
            transition: all 0.2s;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1f2937 !important;
            font-size: 0.875rem !important;
            padding-left: 0 !important;
            line-height: normal !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            top: 1px !important;
            right: 4px !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2) !important;
            outline: none !important;
        }
        .select2-dropdown {
            border-color: #e5e7eb !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            overflow: hidden !important;
            z-index: 9999 !important;
        }
        .select2-search__field {
            border-radius: 0.375rem !important;
            padding: 6px 10px !important;
        }
        .select2-results__option--highlighted[aria-selected] {
            background-color: #4f46e5 !important;
        }
    </style>
@endpush

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <div class="mb-8 animate-fade-in">
                <a href="{{ route('events.show', $event->slug) }}"
                    class="inline-flex items-center text-gray-600 hover:text-brand-primary mb-4 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Event
                </a>
                <h1 class="text-4xl font-bold text-slate-900 mb-2">Select Your Seats</h1>
                <p class="text-gray-600">{{ $event->name }}</p>
            </div>

            <form action="{{ route('orders.store', $event->slug) }}" method="POST" id="seatSelectionForm"
                x-data="seatSelection({
                    eventSlug: '{{ $event->slug }}',
                    generalCategories: [
                        @foreach ($event->ticketCategories->where('is_seated', false) as $category)
                        {
                            id: {{ $category->id }},
                            name: '{{ $category->name }}',
                            price: {{ $category->price }},
                            biaya_layanan: {{ $category->biaya_layanan ?? 0 }},
                            biaya_admin_payment: {{ $category->biaya_admin_payment ?? 0 }},
                            available: {{ $category->available_count }},
                            quantity: 0
                        }, @endforeach
                    ]
                })" x-init="init()" @submit="validateForm($event)">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- LEFT: Seat Map --}}
                    <div class="lg:col-span-2 space-y-6">
                        {{-- Event Info Card --}}
                        <div class="card bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100 animate-slide-up mb-6">
                            <div class="p-8">
                                <div class="flex items-start gap-8">
                                    @if ($event->banner_image)
                                        <div class="relative group">
                                            <div class="absolute -inset-1 bg-gradient-to-r from-brand-primary to-brand-secondary rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                                            <img src="{{ Storage::url($event->banner_image) }}" alt="{{ $event->name }}"
                                                width="128" height="128"
                                                loading="lazy"
                                                class="relative w-32 h-32 object-cover rounded-2xl shadow-lg">
                                        </div>
                                    @else
                                        <div class="w-32 h-32 bg-gradient-to-br from-slate-100 to-slate-200 rounded-2xl flex items-center justify-center text-slate-400">
                                            <i class="fas fa-image text-3xl"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="px-3 py-1 bg-brand-primary/10 text-brand-primary text-[10px] font-bold uppercase tracking-wider rounded-full">Coming Soon</span>
                                            <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-wider rounded-full">{{ $event->category->name ?? 'Event' }}</span>
                                        </div>
                                        <h2 class="text-3xl font-black text-slate-900 mb-3 tracking-tight">{{ $event->name }}</h2>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-500">
                                            <div class="flex items-center group">
                                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center mr-3 group-hover:bg-brand-primary/10 transition-colors">
                                                    <i class="fas fa-calendar-alt text-brand-primary"></i>
                                                </div>
                                                {{ $event->event_date->format('l, d F Y') }} <span class="mx-1">•</span> {{ $event->event_date->format('H:i') }} WIB
                                            </div>
                                            @if ($event->venue)
                                                <div class="flex items-center group">
                                                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center mr-3 group-hover:bg-brand-primary/10 transition-colors">
                                                        <i class="fas fa-map-marker-alt text-brand-primary"></i>
                                                    </div>
                                                    {{ $event->venue->name }}, {{ $event->venue->city }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Seat Map --}}
                        <div class="card bg-white rounded-3xl shadow-xl border border-slate-100 animate-slide-up overflow-hidden">
                            <div class="p-8">
                                <div class="flex items-center justify-between mb-10">
                                    <h3 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                                        <span class="w-1.5 h-8 bg-brand-yellow rounded-full"></span>
                                        Choose Your Seats
                                    </h3>
                                    <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 rounded-full text-xs font-bold animate-pulse">
                                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                        LIVE AVAILABILITY
                                    </div>
                                </div>

                                @foreach ($event->ticketCategories->where('is_seated', true)->groupBy('venue_section_id') as $sectionId => $categories)
                                    <x-seated-map :section="$categories->first()->venueSection" :categories="$categories" />
                                @endforeach

                                {{-- Non-seated tickets --}}
                                @if ($event->ticketCategories->where('is_seated', false)->count() > 0)
                                    <div class="mt-12 pt-12 border-t-2 border-slate-100">
                                        <div class="mb-8">
                                            <h4 class="text-xl font-black text-slate-900 mb-2">General Admission</h4>
                                            <p class="text-sm text-slate-500">Standard entry tickets with free standing or unnumbered seating.</p>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            @foreach ($event->ticketCategories->where('is_seated', false) as $category)
                                                <div x-data="{ cat: generalTickets.find(c => c.id === {{ $category->id }}) }"
                                                    class="group relative bg-white border-2 border-slate-100 rounded-3xl p-6 transition-all duration-300 hover:border-brand-primary hover:shadow-lg"
                                                    :class="cat.quantity > 0 ? 'border-brand-primary bg-brand-primary/[0.02]' : ''">
                                                    
                                                    <div class="flex justify-between items-start mb-6">
                                                        <div>
                                                            <h5 class="text-lg font-black text-slate-900 group-hover:text-brand-primary transition-colors">{{ $category->name }}</h5>
                                                            <div class="flex items-center gap-2 mt-1">
                                                                <span class="text-2xl font-black text-slate-900">Rp {{ number_format($category->price, 0, ',', '.') }}</span>
                                                                <span class="text-[10px] text-slate-400 font-bold uppercase">/ PAX</span>
                                                            </div>
                                                        </div>
                                                        <div class="px-3 py-1 bg-emerald-100 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-wider">
                                                            {{ $category->available_count }} LEFT
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center justify-between bg-slate-50 rounded-2xl p-2 group-hover:bg-white transition-colors border border-transparent group-hover:border-slate-100">
                                                        <button type="button"
                                                            @click="updateGeneralQty({{ $category->id }}, -1)"
                                                            class="w-12 h-12 bg-white text-slate-400 hover:text-brand-primary hover:bg-slate-50 rounded-xl flex items-center justify-center transition shadow-sm disabled:opacity-25"
                                                            :disabled="cat.quantity <= 0">
                                                            <i class="fas fa-minus"></i>
                                                        </button>

                                                        <div class="flex flex-col items-center">
                                                            <span class="text-xl font-black text-slate-900" x-text="cat.quantity">0</span>
                                                            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">Qty</span>
                                                        </div>

                                                        <button type="button"
                                                            @click="updateGeneralQty({{ $category->id }}, 1)"
                                                            class="w-12 h-12 bg-brand-yellow text-slate-900 hover:bg-yellow-400 rounded-xl flex items-center justify-center transition shadow-sm disabled:opacity-50"
                                                            :disabled="cat.quantity >= cat.available">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Order Summary Sidebar --}}
                    <div class="lg:col-span-1">
                        <div class="sticky top-28 space-y-6">
                            {{-- Customer Info Tooltip --}}
                            <div class="bg-indigo-600 text-white p-4 rounded-2xl shadow-lg animate-bounce-subtle hidden md:block">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-lightbulb"></i>
                                    </div>
                                    <p class="text-xs font-bold leading-tight uppercase tracking-wide">Enter valid identity details to ensure a smooth check-in!</p>
                                </div>
                            </div>                            <div class="card bg-white rounded-3xl shadow-2xl border border-slate-100 animate-scale-in overflow-hidden">
                                <div class="p-6">
                                    <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3">
                                        <div class="w-10 h-10 bg-brand-yellow/10 rounded-xl flex items-center justify-center text-brand-yellow">
                                            <i class="fas fa-shopping-cart"></i>
                                        </div>
                                        Order Summary
                                    </h3>
                                    
                                    <div class="space-y-4 mb-8 min-h-[80px] max-h-[350px] overflow-y-auto custom-scrollbar pr-1">
                                        <template x-if="totalTickets === 0">
                                            <div class="flex flex-col items-center justify-center py-10 text-slate-300">
                                                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                                                    <i class="fas fa-ticket-alt text-3xl"></i>
                                                </div>
                                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Select seats to start</p>
                                            </div>
                                        </template>
                                        
                                        {{-- Seated Summary --}}
                                        <template x-if="selectedSeats.length > 0">
                                            <div class="space-y-3">
                                                <template x-for="seat in selectedSeats" :key="seat.id">
                                                    <div class="group relative bg-slate-50 p-4 rounded-2xl border-2 border-transparent hover:border-brand-primary/20 hover:bg-white transition-all animate-slide-up shadow-sm hover:shadow-md">
                                                        <button type="button" @click="toggleSeat(seat.id)" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full text-[10px] flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                        <div class="flex justify-between items-center">
                                                            <div class="flex items-center gap-3">
                                                                <div class="w-10 h-10 bg-brand-primary text-white rounded-xl flex items-center justify-center font-black text-xs shadow-lg shadow-brand-primary/20">
                                                                    <span x-text="seat.label"></span>
                                                                </div>
                                                                <div>
                                                                    <span class="block text-[8px] font-black text-brand-primary uppercase tracking-widest leading-none mb-1">Confirmed Seat</span>
                                                                    <span class="text-xs font-black text-slate-900" x-text="seat.label"></span>
                                                                </div>
                                                            </div>
                                                            <div class="text-right">
                                                                <span class="text-sm font-black text-slate-900" x-text="`Rp ${formatRupiah(seat.price)}`"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
>

                                        {{-- General Summary --}}
                                        <template x-for="cat in generalTickets" :key="cat.id">
                                            <template x-if="cat.quantity > 0">
                                                <div class="bg-indigo-50 p-4 rounded-2xl border border-indigo-100 animate-slide-up">
                                                    <div class="flex justify-between items-center">
                                                        <div>
                                                            <span class="block text-xs font-bold text-indigo-600 uppercase tracking-wider">General Admission</span>
                                                            <span class="text-lg font-black text-slate-900" x-text="`${cat.quantity}x ${cat.name}`"></span>
                                                        </div>
                                                        <span class="font-black text-slate-900" x-text="`Rp ${formatRupiah(cat.quantity * (cat.price + cat.biaya_layanan + cat.biaya_admin_payment))}`"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </template>
                                    </div>

                                    {{-- Calculation Details --}}
                                    <div class="space-y-3 bg-slate-50 p-6 rounded-3xl border border-slate-100 mb-8">
                                        <div class="flex justify-between text-xs font-bold uppercase tracking-tight text-slate-500">
                                            <span>Base Price</span>
                                            <span class="text-slate-900" x-text="`Rp ${formatRupiah(totalBasePrice)}`"></span>
                                        </div>
                                        <div class="flex justify-between text-xs font-bold uppercase tracking-tight text-slate-500">
                                            <span>Fees & Taxes</span>
                                            <span class="text-slate-900" x-text="`Rp ${formatRupiah(totalServiceFee + totalAdminFee)}`"></span>
                                        </div>
                                        <div class="pt-3 mt-3 border-t border-slate-200 flex justify-between items-end">
                                            <div>
                                                <span class="block text-[10px] font-black text-brand-primary uppercase tracking-widest">Grand Total</span>
                                                <span class="text-3xl font-black text-slate-900 leading-none" x-text="`Rp ${formatRupiah(totalAmount)}`">Rp 0</span>
                                            </div>
                                            <span class="text-xs font-bold text-slate-400" x-text="`${totalTickets} Tickets` text-slate-400"></span>
                                        </div>
                                    </div>

                                    {{-- Customer Information --}}
                                    <div class="space-y-6 mb-8">
                                        <div class="flex items-center gap-3 pb-2 border-b-2 border-slate-50">
                                            <div class="w-10 h-10 bg-brand-primary/10 rounded-xl flex items-center justify-center text-brand-primary">
                                                <i class="fas fa-id-card"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-black text-slate-900 uppercase tracking-widest text-xs">Identity Verification</h4>
                                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter leading-none mt-0.5">Details must match your official ID</p>
                                            </div>
                                        </div>

                                        <div class="space-y-4">
                                            <div class="group">
                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1 group-focus-within:text-brand-primary transition-colors">Full Name (As per ID)</label>
                                                <input type="text" name="consumer_name" required placeholder="John Doe" 
                                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-900 focus:border-brand-primary focus:bg-white outline-none transition-all">
                                            </div>

                                            <div class="group">
                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1 group-focus-within:text-brand-primary transition-colors">Email Address</label>
                                                <input type="email" name="consumer_email" required placeholder="john@example.com"
                                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-900 focus:border-brand-primary focus:bg-white outline-none transition-all">
                                            </div>

                                            <div class="group">
                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1 group-focus-within:text-brand-primary transition-colors">City of Residence</label>
                                                <div x-ignore>
                                                    <select name="consumer_city" id="consumer_city" required class="w-full"></select>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div class="group">
                                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1 group-focus-within:text-brand-primary transition-colors">Birth Date</label>
                                                    <input type="date" name="consumer_birth_date" required 
                                                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-900 focus:border-brand-primary focus:bg-white outline-none transition-all">
                                                </div>
                                                <div class="group">
                                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1 group-focus-within:text-brand-primary transition-colors">WhatsApp</label>
                                                    <input type="text" name="consumer_whatsapp" required placeholder="0812..."
                                                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-900 focus:border-brand-primary focus:bg-white outline-none transition-all">
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div class="group">
                                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1 group-focus-within:text-brand-primary transition-colors">ID Type</label>
                                                    <div class="relative">
                                                        <select name="consumer_identity_type" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-900 focus:border-brand-primary focus:bg-white outline-none transition-all appearance-none cursor-pointer">
                                                            <option value="KTP">KTP</option>
                                                            <option value="SIM">SIM</option>
                                                            <option value="Passport">Passport</option>
                                                        </select>
                                                        <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"></i>
                                                    </div>
                                                </div>
                                                <div class="group">
                                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1 group-focus-within:text-brand-primary transition-colors">ID Number</label>
                                                    <input type="text" name="consumer_identity_number" required placeholder="16-digit number"
                                                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-900 focus:border-brand-primary focus:bg-white outline-none transition-all">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" :disabled="totalTickets === 0"
                                        class="group relative w-full font-black py-5 rounded-3xl transition-all duration-500 overflow-hidden shadow-xl"
                                        :class="totalTickets > 0 ? 'bg-brand-yellow text-slate-900 hover:scale-[1.02] active:scale-95' : 'bg-slate-100 text-slate-300 cursor-not-allowed'">
                                        <div class="relative z-10 flex items-center justify-center gap-3">
                                            <span x-text="totalTickets > 0 ? 'PURCHASE TICKETS' : 'SELECT SEATS'"></span>
                                            <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                                        </div>
                                        <div x-show="totalTickets > 0" class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                                    </button>

                                    <div class="mt-8 pt-8 border-t border-slate-100 text-center">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center justify-center gap-2 mb-4">
                                            <i class="fas fa-lock"></i>
                                            Secured by Bank-Level Encryption
                                        </p>
                                        <img src="https://static.midtrans.com/midtrans/cms/1498457717467_logo_midtrans.png" alt="Midtrans" class="h-6 grayscale opacity-30 mx-auto hover:opacity-100 hover:grayscale-0 transition-all cursor-pointer">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hidden inputs for selected seats --}}
                <div style="display:none">
                    <template x-for="(seat, index) in selectedSeats" :key="seat.id">
                        <div>
                            <input type="hidden" :name="`items[${index}][ticket_category_id]`" :value="seat.categoryId">
                            <input type="hidden" :name="`items[${index}][quantity]`" value="1">
                            <input type="hidden" :name="`items[${index}][seat_id]`" :value="seat.id">
                        </div>
                    </template>
                    <template x-for="(cat, index) in generalTickets" :key="cat.id">
                        <template x-if="cat.quantity > 0">
                            <div>
                                <input type="hidden" :name="`items[${selectedSeats.length + index}][ticket_category_id]`"
                                    :value="cat.id">
                                <input type="hidden" :name="`items[${selectedSeats.length + index}][quantity]`"
                                    :value="cat.quantity">
                            </div>
                        </template>
                    </template>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#consumer_city').select2({
                ajax: {
                    url: '{{ route('events.cities') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                placeholder: 'Cari Kota...',
                minimumInputLength: 0,
                allowClear: true,
                width: '100%',
                dropdownParent: $('body')
            });
        });

        function seatSelection(config) {
            return {
                eventSlug: config.eventSlug,
                allSeats: [],
                selectedSeats: [],
                generalTickets: config.generalCategories,

                init() {
                    this.fetchStatus();
                    setInterval(() => this.fetchStatus(), 15000);
                },

                async fetchStatus() {
                    try {
                        const response = await fetch(`/events/${this.eventSlug}/seat-status`);
                        const data = await response.json();
                        this.allSeats = data.seats;
                    } catch (e) {
                        console.error('Failed to sync seat statuses');
                    }
                },

                get totalTickets() {
                    return this.selectedSeats.length + this.generalTickets.reduce((sum, t) => sum + t.quantity, 0);
                },

                get totalBasePrice() {
                    let total = this.selectedSeats.reduce((sum, s) => sum + s.price, 0);
                    total += this.generalTickets.reduce((sum, t) => sum + (t.quantity * t.price), 0);
                    return total;
                },

                get totalServiceFee() {
                    let total = this.selectedSeats.reduce((sum, s) => sum + s.biaya_layanan, 0);
                    total += this.generalTickets.reduce((sum, t) => sum + (t.quantity * t.biaya_layanan), 0);
                    return total;
                },

                get totalAdminFee() {
                    let total = this.selectedSeats.reduce((sum, s) => sum + s.biaya_admin_payment, 0);
                    total += this.generalTickets.reduce((sum, t) => sum + (t.quantity * t.biaya_admin_payment), 0);
                    return total;
                },

                get totalAmount() {
                    return this.totalBasePrice + this.totalServiceFee + this.totalAdminFee;
                },

                isSelected(id) {
                    return this.selectedSeats.some(s => s.id === id);
                },

                toggleSeat(id, label, categoryId, price, biaya_layanan, biaya_admin_payment) {
                    const index = this.selectedSeats.findIndex(s => s.id === id);
                    if (index >= 0) {
                        this.selectedSeats.splice(index, 1);
                    } else {
                        // Optional: Limit max selection
                        if (this.totalTickets >= 5) {
                            alert('Maximum 5 tickets per order');
                            return;
                        }
                        this.selectedSeats.push({
                            id,
                            label,
                            categoryId,
                            price,
                            biaya_layanan,
                            biaya_admin_payment
                        });
                    }
                },

                updateGeneralQty(categoryId, change) {
                    const cat = this.generalTickets.find(c => c.id === categoryId);
                    if (!cat) return;
                    const newQty = cat.quantity + change;
                    if (newQty >= 0 && newQty <= cat.available) {
                         if (change > 0 && this.totalTickets >= 5) {
                            alert('Maximum 5 tickets per order');
                            return;
                        }
                        cat.quantity = newQty;
                    }
                },

                getSeatClass(id, initialStatus) {
                    // Find actual status from polished allSeats
                    const seatData = this.allSeats.find(s => s.id === id);
                    const status = seatData ? seatData.status : initialStatus;

                    if (this.isSelected(id)) {
                        return 'bg-brand-primary text-white shadow-[0_0_20px_rgba(79,70,229,0.4)] scale-110 z-10';
                    }
                    if (status === 'available') {
                        return 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-md hover:shadow-lg hover:-translate-y-1';
                    }
                    if (status === 'reserved') {
                        return 'bg-amber-500 text-white cursor-not-allowed opacity-60';
                    }
                    return 'bg-slate-200 text-slate-400 cursor-not-allowed';
                },

                formatRupiah(amount) {
                    return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                },

                validateForm(e) {
                    if (this.totalTickets === 0) {
                        e.preventDefault();
                        alert('Please select at least one ticket to proceed.');
                    }
                }
            }
        }
    </script>
@endpush
