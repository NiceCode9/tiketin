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
                })" @submit="validateForm($event)">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- LEFT: Seat Map --}}
                    <div class="lg:col-span-2 space-y-6">
                        {{-- Event Info Card --}}
                        <div class="card animate-slide-up">
                            <div class="p-6">
                                <div class="flex items-start gap-4">
                                    @if ($event->banner_image)
                                        <img src="{{ Storage::url($event->banner_image) }}" alt="{{ $event->name }}"
                                            class="w-24 h-24 object-cover rounded-lg">
                                    @else
                                        <div
                                            class="w-24 h-24 bg-gradient-to-r from-brand-primary to-brand-secondary rounded-lg">
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h2 class="text-xl font-bold text-slate-900 mb-2">{{ $event->name }}</h2>
                                        <div class="flex flex-col gap-2 text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar-alt text-brand-primary mr-2"></i>
                                                {{ $event->event_date->format('l, d F Y - H:i') }} WIB
                                            </div>
                                            @if ($event->venue)
                                                <div class="flex items-center">
                                                    <i class="fas fa-map-marker-alt text-brand-primary mr-2"></i>
                                                    {{ $event->venue->name }}, {{ $event->venue->city }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Seat Map --}}
                        <div class="card animate-slide-up">
                            <div class="p-6">
                                <h3 class="text-2xl font-bold text-slate-900 mb-6">
                                    <i class="fas fa-chair text-brand-yellow mr-2"></i>
                                    Seat Map
                                </h3>

                                @foreach ($event->ticketCategories->where('is_seated', true)->groupBy('venue_section_id') as $sectionId => $categories)
                                    @php
                                        $section = $categories->first()->venueSection;
                                    @endphp

                                    <div class="mb-8">
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="text-lg font-bold text-gray-800">
                                                <i class="fas fa-layer-group text-brand-primary mr-2"></i>
                                                {{ $section->name }}
                                            </h4>
                                            <span class="badge-info text-sm">
                                                Rp {{ number_format($categories->first()->price, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        {{-- Stage Indicator --}}
                                        <div class="mb-4">
                                            <div
                                                class="bg-gradient-to-r from-gray-700 to-gray-900 text-white text-center py-3 rounded-lg shadow-lg">
                                                <i class="fas fa-music mr-2"></i>
                                                <span class="font-bold">STAGE</span>
                                            </div>
                                        </div>

                                        {{-- Seat Grid --}}
                                        <div class="border-2 border-gray-200 rounded-xl p-6 bg-white">
                                            @php
                                                $seats = $section->seats->groupBy('row_label');
                                            @endphp

                                            @foreach ($seats as $row => $rowSeats)
                                                <div class="flex items-center justify-center mb-3">
                                                    <span
                                                        class="w-10 text-sm font-bold text-gray-700 text-center">{{ $row }}</span>
                                                    <div class="flex gap-2 flex-wrap justify-center">
                                                        @foreach ($rowSeats->sortBy('seat_number') as $seat)
                                                            <button type="button"
                                                                class="seat-button w-12 h-12 rounded-lg text-sm font-bold transition-all duration-200 transform hover:scale-110"
                                                                :class="getSeatClass({{ $seat->id }},
                                                                    '{{ $seat->status }}')"
                                                                @click="toggleSeat({{ $seat->id }}, '{{ $seat->full_seat }}', {{ $categories->first()->id }}, {{ $categories->first()->price }}, {{ $categories->first()->biaya_layanan ?? 0 }}, {{ $categories->first()->biaya_admin_payment ?? 0 }})"
                                                                @if ($seat->status !== 'available') disabled @endif>
                                                                {{ $seat->seat_number }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Legend --}}
                                        <div class="flex flex-wrap gap-4 mt-4 text-sm">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-green-500 rounded-lg mr-2 shadow-md"></div>
                                                <span class="font-medium">Available</span>
                                            </div>
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-blue-600 rounded-lg mr-2 shadow-md"></div>
                                                <span class="font-medium">Selected</span>
                                            </div>
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-yellow-500 rounded-lg mr-2 shadow-md"></div>
                                                <span class="font-medium">Reserved</span>
                                            </div>
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gray-400 rounded-lg mr-2 shadow-md"></div>
                                                <span class="font-medium">Sold</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Non-seated tickets --}}
                                @if ($event->ticketCategories->where('is_seated', false)->count() > 0)
                                    <div class="mt-8 pt-8 border-t-2 border-gray-200">
                                        <h4 class="text-lg font-bold text-gray-800 mb-4">
                                            <i class="fas fa-users text-brand-primary mr-2"></i>
                                            General Admission
                                        </h4>

                                        <div class="space-y-4">
                                            @foreach ($event->ticketCategories->where('is_seated', false) as $category)
                                                <div x-data="{ category: generalTickets.find(c => c.id === {{ $category->id }}) }"
                                                    class="border-2 border-gray-200 rounded-xl p-6 hover:border-brand-primary transition-all duration-300"
                                                    :class="category.quantity > 0 ? 'border-brand-primary bg-brand-primary/5' :
                                                        ''">
                                                    <div
                                                        class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                                        <div class="flex-1">
                                                            <h5 class="text-xl font-bold text-slate-900 mb-1">
                                                                {{ $category->name }}</h5>
                                                            <p class="text-2xl font-bold text-brand-primary mb-2">
                                                                Rp {{ number_format($category->price, 0, ',', '.') }}
                                                            </p>
                                                            <span class="badge-success text-xs">
                                                                <i class="fas fa-check-circle mr-1"></i>
                                                                {{ $category->available_count }} available
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center gap-3">
                                                            <button type="button"
                                                                @click="updateGeneralQty({{ $category->id }}, -1)"
                                                                class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-lg flex items-center justify-center transition disabled:opacity-50"
                                                                :disabled="category.quantity <= 0">
                                                                <i class="fas fa-minus"></i>
                                                            </button>

                                                            <span class="w-12 text-center text-2xl font-bold"
                                                                x-text="category.quantity">0</span>

                                                            <button type="button"
                                                                @click="updateGeneralQty({{ $category->id }}, 1)"
                                                                class="w-10 h-10 bg-brand-yellow hover:bg-yellow-400 rounded-lg flex items-center justify-center transition disabled:opacity-50"
                                                                :disabled="category.quantity >= category.available">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>
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
                        <div class="card sticky top-28 animate-scale-in">
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-slate-900 mb-6">
                                    <i class="fas fa-receipt text-brand-yellow mr-2"></i>
                                    Order Summary
                                </h3>

                                <div class="space-y-3 mb-6 min-h-[150px] max-h-[400px] overflow-y-auto">
                                    <template x-if="totalTickets === 0">
                                        <p class="text-gray-500 text-sm text-center py-8">
                                            <i class="fas fa-chair text-4xl text-gray-300 mb-2 block"></i>
                                            No seats selected
                                        </p>
                                    </template>

                                    {{-- Seated Summary --}}
                                    <template x-if="selectedSeats.length > 0">
                                        <div class="space-y-4">
                                            <div class="font-semibold text-gray-900 text-sm">
                                                <i class="fas fa-chair text-brand-primary mr-1"></i> Seated Tickets
                                            </div>
                                            <template x-for="seat in selectedSeats" :key="seat.id">
                                                <div class="pl-4 space-y-1 animate-slide-up">
                                                    <div class="flex justify-between text-sm">
                                                        <span class="font-medium text-gray-900" x-text="seat.label"></span>
                                                        <span class="font-bold text-gray-900" x-text="`Rp ${formatRupiah(seat.price + seat.biaya_layanan + seat.biaya_admin_payment)}`"></span>
                                                    </div>
                                                    <div class="pl-2 space-y-0.5 border-l-2 border-gray-100">
                                                        <div class="flex justify-between text-[11px] text-gray-500">
                                                            <span>Price</span>
                                                            <span x-text="`Rp ${formatRupiah(seat.price)}`"></span>
                                                        </div>
                                                        <template x-if="seat.biaya_layanan > 0">
                                                            <div class="flex justify-between text-[11px] text-gray-500">
                                                                <span>Service Fee</span>
                                                                <span x-text="`Rp ${formatRupiah(seat.biaya_layanan)}`"></span>
                                                            </div>
                                                        </template>
                                                        <template x-if="seat.biaya_admin_payment > 0">
                                                            <div class="flex justify-between text-[11px] text-gray-500">
                                                                <span>Admin Fee</span>
                                                                <span x-text="`Rp ${formatRupiah(seat.biaya_admin_payment)}`"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- General Summary --}}
                                    <template x-if="generalTickets.some(g => g.quantity > 0)">
                                        <div class="mt-6 space-y-4 border-t border-gray-100 pt-4">
                                            <div class="font-semibold text-gray-900 text-sm">
                                                <i class="fas fa-users text-brand-primary mr-1"></i> General Admission
                                            </div>
                                            <template x-for="cat in generalTickets" :key="cat.id">
                                                <template x-if="cat.quantity > 0">
                                                    <div class="pl-4 space-y-1 animate-slide-up">
                                                        <div class="flex justify-between text-sm">
                                                            <span class="font-medium text-gray-900" x-text="cat.name"></span>
                                                            <span class="font-bold text-gray-900" x-text="`Rp ${formatRupiah(cat.quantity * (cat.price + cat.biaya_layanan + cat.biaya_admin_payment))}`"></span>
                                                        </div>
                                                        <div class="pl-2 space-y-0.5 border-l-2 border-gray-100">
                                                            <div class="flex justify-between text-[11px] text-gray-500">
                                                                <span x-text="`${cat.quantity} x Price`"></span>
                                                                <span x-text="`Rp ${formatRupiah(cat.quantity * cat.price)}`"></span>
                                                            </div>
                                                            <template x-if="cat.biaya_layanan > 0">
                                                                <div class="flex justify-between text-[11px] text-gray-500">
                                                                    <span x-text="`${cat.quantity} x Service Fee`"></span>
                                                                    <span x-text="`Rp ${formatRupiah(cat.quantity * cat.biaya_layanan)}`"></span>
                                                                </div>
                                                            </template>
                                                            <template x-if="cat.biaya_admin_payment > 0">
                                                                <div class="flex justify-between text-[11px] text-gray-500">
                                                                    <span x-text="`${cat.quantity} x Admin Fee`"></span>
                                                                    <span x-text="`Rp ${formatRupiah(cat.quantity * cat.biaya_admin_payment)}`"></span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </div>

                                <div class="border-t border-gray-200 pt-4 mb-6 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Total Tickets</span>
                                        <span class="font-semibold" x-text="totalTickets">0</span>
                                    </div>
                                    <template x-if="totalServiceFee > 0">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Service Fee</span>
                                            <span class="font-semibold" x-text="`Rp ${formatRupiah(totalServiceFee)}`"></span>
                                        </div>
                                    </template>
                                    <template x-if="totalAdminFee > 0">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Admin Fee</span>
                                            <span class="font-semibold" x-text="`Rp ${formatRupiah(totalAdminFee)}`"></span>
                                        </div>
                                    </template>
                                    <div class="flex justify-between text-xl font-bold pt-2 border-t border-gray-100">
                                        <span>Total</span>
                                        <span class="text-brand-yellow" x-text="`Rp ${formatRupiah(totalAmount)}`">Rp 0</span>
                                    </div>
                                </div>

                                {{-- Customer Information --}}
                                <div class="space-y-4 mb-6 pb-6 border-b border-gray-200">
                                    <h4 class="font-bold text-gray-900">
                                        <i class="fas fa-user-edit text-brand-primary mr-2"></i>
                                        Your Information
                                    </h4>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Full Name *</label>
                                        <input type="text" name="consumer_name" required class="input text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Email *</label>
                                        <input type="email" name="consumer_email" required class="input text-sm">
                                    </div>
                                    <div x-ignore>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kota Domisili *</label>
                                        <select name="consumer_city" id="consumer_city" required class="select2-select text-sm">
                                            {{-- AJAX Populated --}}
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Lahir *</label>
                                        <input type="date" name="consumer_birth_date" required class="input text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">WhatsApp *</label>
                                        <input type="text" name="consumer_whatsapp" required
                                            placeholder="08xxxxxxxxxx" class="input text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Identity Type
                                            *</label>
                                        <select name="consumer_identity_type" required class="input text-sm">
                                            <option value="">Select</option>
                                            <option value="KTP">KTP</option>
                                            <option value="SIM">SIM</option>
                                            <option value="Student Card">Student Card</option>
                                            <option value="Passport">Passport</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Identity Number
                                            *</label>
                                        <input type="text" name="consumer_identity_number" required
                                            class="input text-sm">
                                    </div>
                                </div>

                                <button type="submit" :disabled="totalTickets === 0"
                                    :class="totalTickets > 0 ?
                                        'bg-brand-yellow hover:bg-yellow-400 text-black shadow-lg scale-105' :
                                        'bg-gray-300 text-gray-500 cursor-not-allowed'"
                                    class="w-full font-bold py-4 rounded-xl transition transform">
                                    <i class="fas" :class="totalTickets > 0 ? 'fa-arrow-right' : 'fa-lock'"
                                        class="mr-2"></i>
                                    <span
                                        x-text="totalTickets > 0 ? 'Continue to Checkout' : 'Continue to Checkout'"></span>
                                </button>

                                <p class="text-xs text-gray-500 text-center mt-4">
                                    <i class="fas fa-shield-alt mr-1"></i> Secure checkout powered by Midtrans
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hidden inputs for selected seats --}}
                {{-- Hidden inputs for form submission --}}
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
                                <template x-if="cat.seat_id">
                                    <input type="hidden" :name="`items[${selectedSeats.length + index}][seat_id]`"
                                        :value="cat.seat_id">
                                </template>
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
                selectedSeats: [],
                generalTickets: config.generalCategories,

                get totalTickets() {
                    let total = this.selectedSeats.length;
                    this.generalTickets.forEach(t => total += t.quantity);
                    return total;
                },

                get totalAmount() {
                    let total = 0;
                    this.selectedSeats.forEach(s => total += (s.price + s.biaya_layanan + s.biaya_admin_payment));
                    this.generalTickets.forEach(t => total += (t.quantity * (t.price + t.biaya_layanan + t.biaya_admin_payment)));
                    return total;
                },

                get totalServiceFee() {
                    let total = 0;
                    this.selectedSeats.forEach(s => total += s.biaya_layanan);
                    this.generalTickets.forEach(t => total += (t.quantity * t.biaya_layanan));
                    return total;
                },

                get totalAdminFee() {
                    let total = 0;
                    this.selectedSeats.forEach(s => total += s.biaya_admin_payment);
                    this.generalTickets.forEach(t => total += (t.quantity * t.biaya_admin_payment));
                    return total;
                },

                toggleSeat(id, label, categoryId, price, biaya_layanan, biaya_admin_payment) {
                    const index = this.selectedSeats.findIndex(s => s.id === id);
                    if (index >= 0) {
                        this.selectedSeats.splice(index, 1);
                    } else {
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
                        cat.quantity = newQty;
                    }
                },

                getSeatClass(id, status) {
                    if (this.selectedSeats.some(s => s.id === id)) {
                        return 'bg-blue-600 text-white shadow-xl scale-110';
                    }
                    if (status === 'available')
                    return 'bg-green-500 hover:bg-green-600 text-white shadow-md hover:shadow-lg';
                    if (status === 'reserved') return 'bg-yellow-500 text-white cursor-not-allowed opacity-60';
                    return 'bg-gray-400 text-white cursor-not-allowed opacity-50';
                },

                formatRupiah(amount) {
                    return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                },

                validateForm(e) {
                    if (this.totalTickets === 0) {
                        e.preventDefault();
                        alert('Please select at least one ticket');
                        return false;
                    }
                }
            }
        }
    </script>
@endpush
