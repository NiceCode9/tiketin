@extends('layouts.app')

@section('title', 'Select Tickets - ' . $event->name)

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 46px !important;
            padding: 8px 12px !important;
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
            padding-left: 4px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px !important;
            top: 1px !important;
            right: 8px !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #4f46e5 !important;
            ring: 2px !important;
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2) !important;
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
            padding: 8px 12px !important;
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
                <h1 class="text-4xl font-bold text-slate-900 mb-2">Select Your Tickets</h1>
                <p class="text-gray-600">{{ $event->name }}</p>
            </div>

            <form action="{{ route('orders.store', $event->slug) }}" method="POST" id="orderForm" x-data="ticketSelection({
                categories: [
                    @foreach ($event->ticketCategories as $category)
                        {
                            id: {{ $category->id }},
                            name: '{{ $category->name }}',
                            price: {{ $category->price }},
                            available: {{ $category->available_count }},
                            quantity: 0
                        }, @endforeach
                ]
            })"
                @submit="validateForm($event)">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- LEFT: Ticket Selection --}}
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

                        {{-- Ticket Categories --}}
                        <div class="card animate-slide-up">
                            <div class="p-6">
                                <h3 class="text-2xl font-bold text-slate-900 mb-6">
                                    <i class="fas fa-ticket-alt text-brand-yellow mr-2"></i>
                                    Ticket Categories
                                </h3>

                                <div class="space-y-4">
                                    @foreach ($event->ticketCategories as $category)
                                        <div x-data="{ category: categories[{{ $loop->index }}] }"
                                        class="border-2 border-gray-200 rounded-xl p-6 hover:border-brand-primary transition-all duration-300"
                                        :class="category.quantity > 0 ? 'border-brand-primary bg-brand-primary/5' : ''">
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                            <div class="flex-1">
                                                <h4 class="text-xl font-bold text-slate-900 mb-1">{{ $category->name }}
                                                </h4>
                                                <p class="text-2xl font-bold text-brand-primary mb-2">
                                                    Rp {{ number_format($category->price, 0, ',', '.') }}
                                                </p>
                                                <div class="flex items-center gap-2">
                                                    @if ($category->hasAvailableTickets())
                                                        <span class="badge-success text-xs">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            {{ $category->available_count }} available
                                                        </span>
                                                    @else
                                                        <span class="badge-danger text-xs">
                                                            <i class="fas fa-times-circle mr-1"></i>
                                                            Sold Out
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-3">
                                                <button type="button" @click="updateQuantity({{ $loop->index }}, -1)"
                                                    class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-lg flex items-center justify-center transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                    :disabled="!category.available || category.quantity <= 0">
                                                    <i class="fas fa-minus"></i>
                                                </button>

                                                <span class="w-12 text-center text-2xl font-bold"
                                                    x-text="category.quantity">0</span>

                                                <button type="button" @click="updateQuantity({{ $loop->index }}, 1)"
                                                    class="w-10 h-10 bg-brand-yellow hover:bg-yellow-400 rounded-lg flex items-center justify-center transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                    :disabled="!category.available || category.quantity >= category.available">
                                                    <i class="fas fa-plus"></i>
                                                </button>

                                                <template x-if="category.quantity > 0">
                                                    <div>
                                                        <input type="hidden" :name="`items[${{{ $loop->index }}}][quantity]`"
                                                            :value="category.quantity">
                                                        <input type="hidden"
                                                            :name="`items[${{{ $loop->index }}}][ticket_category_id]`"
                                                            :value="category.id">
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Customer Information --}}
                        <div class="card animate-slide-up">
                            <div class="p-6">
                                <h3 class="text-2xl font-bold text-slate-900 mb-6">
                                    <i class="fas fa-user-edit text-brand-yellow mr-2"></i>
                                    Your Information
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="consumer_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Full Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="consumer_name" id="consumer_name" required
                                            class="input @error('consumer_name') input-error @enderror"
                                            value="{{ old('consumer_name') }}" placeholder="Enter your full name">
                                        @error('consumer_name')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="consumer_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Email <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" name="consumer_email" id="consumer_email" required
                                            class="input @error('consumer_email') input-error @enderror"
                                            value="{{ old('consumer_email') }}" placeholder="your@email.com">
                                        @error('consumer_email')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div x-ignore>
                                        <label for="consumer_city" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Kota Domisili <span class="text-red-500">*</span>
                                        </label>
                                        <select name="consumer_city" id="consumer_city" required
                                            class="select2-select @error('consumer_city') input-error @enderror">
                                            {{-- <option value="">Pilih Kota</option>
                                            @foreach($cities as $city)
                                                <option value="{{ $city->name }}" {{ old('consumer_city') == $city->name ? 'selected' : '' }}>
                                                    {{ $city->name }}
                                                </option>
                                            @endforeach --}}
                                        </select>
                                        @error('consumer_city')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="consumer_birth_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Tanggal Lahir <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="consumer_birth_date" id="consumer_birth_date" required
                                            class="input @error('consumer_birth_date') input-error @enderror"
                                            value="{{ old('consumer_birth_date') }}">
                                        @error('consumer_birth_date')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="consumer_whatsapp"
                                            class="block text-sm font-semibold text-gray-700 mb-2">
                                            WhatsApp Number <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="consumer_whatsapp" id="consumer_whatsapp" required
                                            placeholder="08xxxxxxxxxx"
                                            class="input @error('consumer_whatsapp') input-error @enderror"
                                            value="{{ old('consumer_whatsapp') }}">
                                        @error('consumer_whatsapp')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="consumer_identity_type"
                                            class="block text-sm font-semibold text-gray-700 mb-2">
                                            Identity Type <span class="text-red-500">*</span>
                                        </label>
                                        <select name="consumer_identity_type" id="consumer_identity_type" required
                                            class="input @error('consumer_identity_type') input-error @enderror">
                                            <option value="">Select Identity Type</option>
                                            <option value="KTP"
                                                {{ old('consumer_identity_type') == 'KTP' ? 'selected' : '' }}>KTP</option>
                                            <option value="SIM"
                                                {{ old('consumer_identity_type') == 'SIM' ? 'selected' : '' }}>SIM</option>
                                            <option value="Student Card"
                                                {{ old('consumer_identity_type') == 'Student Card' ? 'selected' : '' }}>
                                                Student Card</option>
                                            <option value="Passport"
                                                {{ old('consumer_identity_type') == 'Passport' ? 'selected' : '' }}>
                                                Passport</option>
                                        </select>
                                        @error('consumer_identity_type')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="consumer_identity_number"
                                            class="block text-sm font-semibold text-gray-700 mb-2">
                                            Identity Number <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="consumer_identity_number"
                                            id="consumer_identity_number" required
                                            class="input @error('consumer_identity_number') input-error @enderror"
                                            value="{{ old('consumer_identity_number') }}"
                                            placeholder="Enter your identity number">
                                        @error('consumer_identity_number')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                        <p class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-info-circle mr-1"></i> Required for verification at the event
                                            venue
                                        </p>
                                    </div>
                                </div>
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

                                <div class="space-y-3 mb-6 min-h-[100px]">
                                    <template x-if="totalTickets === 0">
                                        <p class="text-gray-500 text-sm text-center py-8">
                                            <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-2 block"></i>
                                            No tickets selected
                                        </p>
                                    </template>
                                    <template x-for="cat in categories" :key="cat.id">
                                        <template x-if="cat.quantity > 0">
                                            <div class="flex justify-between text-sm animate-slide-up">
                                                <div>
                                                    <p class="font-medium text-gray-900" x-text="cat.name"></p>
                                                    <p class="text-xs text-gray-500">
                                                        <span x-text="cat.quantity"></span> x Rp <span
                                                            x-text="formatRupiah(cat.price)"></span>
                                                    </p>
                                                </div>
                                                <p class="font-semibold text-gray-900">Rp <span
                                                        x-text="formatRupiah(cat.quantity * cat.price)"></span></p>
                                            </div>
                                        </template>
                                    </template>
                                </div>

                                <div class="border-t border-gray-200 pt-4 mb-6">
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="text-gray-600">Total Tickets</span>
                                        <span class="font-semibold" x-text="totalTickets">0</span>
                                    </div>
                                    <div class="flex justify-between text-xl font-bold">
                                        <span>Total</span>
                                        <span class="text-brand-yellow" x-text="`Rp ${formatRupiah(totalAmount)}`">Rp 0</span>
                                    </div>
                                </div>

                                <button type="submit" :disabled="totalTickets === 0"
                                    :class="totalTickets > 0 ? 'bg-brand-yellow hover:bg-yellow-400 text-black shadow-lg scale-105' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                                    class="w-full font-bold py-4 rounded-xl transition transform">
                                    <i class="fas" :class="totalTickets > 0 ? 'fa-arrow-right' : 'fa-lock'" class="mr-2"></i>
                                    <span x-text="totalTickets > 0 ? 'Continue to Checkout' : 'Continue to Checkout'"></span>
                                </button>

                                <p class="text-xs text-gray-500 text-center mt-4">
                                    <i class="fas fa-shield-alt mr-1"></i> Secure checkout powered by Midtrans
                                </p>
                            </div>
                        </div>
                    </div>
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
                            q: params.term, // search term
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
                width: '100%'
            });
        });

        function ticketSelection(config) {
            return {
                categories: config.categories,

                get totalTickets() {
                    return this.categories.reduce((acc, cat) => acc + cat.quantity, 0);
                },

                get totalAmount() {
                    return this.categories.reduce((acc, cat) => acc + (cat.quantity * cat.price), 0);
                },

                updateQuantity(index, change) {
                    const cat = this.categories[index];
                    const newQty = cat.quantity + change;

                    if (newQty >= 0 && newQty <= cat.available) {
                        cat.quantity = newQty;
                    }
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
