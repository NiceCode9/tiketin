@extends('layouts.app')

@section('title', 'Select Tickets - ' . $event->name)

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

            <form action="{{ route('orders.store', $event->slug) }}" method="POST" id="orderForm">
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
                                        <div
                                            class="border-2 border-gray-200 rounded-xl p-6 hover:border-brand-primary transition-all duration-300">
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
                                                    <button type="button"
                                                        onclick="updateQuantity({{ $loop->index }}, -1)"
                                                        class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-lg flex items-center justify-center transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                        {{ !$category->hasAvailableTickets() ? 'disabled' : '' }}>
                                                        <i class="fas fa-minus"></i>
                                                    </button>

                                                    <span class="w-12 text-center text-2xl font-bold"
                                                        id="qty-display-{{ $loop->index }}">0</span>

                                                    <button type="button" onclick="updateQuantity({{ $loop->index }}, 1)"
                                                        class="w-10 h-10 bg-brand-yellow hover:bg-yellow-400 rounded-lg flex items-center justify-center transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                        {{ !$category->hasAvailableTickets() ? 'disabled' : '' }}>
                                                        <i class="fas fa-plus"></i>
                                                    </button>

                                                    <input type="hidden" name="items[{{ $loop->index }}][quantity]"
                                                        id="qty-{{ $loop->index }}" value="0"
                                                        data-max="{{ $category->available_count }}"
                                                        data-price="{{ $category->price }}"
                                                        data-name="{{ $category->name }}">
                                                    <input type="hidden"
                                                        name="items[{{ $loop->index }}][ticket_category_id]"
                                                        value="{{ $category->id }}">
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

                                <div id="summary-items" class="space-y-3 mb-6 min-h-[100px]">
                                    <p class="text-gray-500 text-sm text-center py-8">
                                        <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-2 block"></i>
                                        No tickets selected
                                    </p>
                                </div>

                                <div class="border-t border-gray-200 pt-4 mb-6">
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="text-gray-600">Total Tickets</span>
                                        <span class="font-semibold" id="total-tickets">0</span>
                                    </div>
                                    <div class="flex justify-between text-xl font-bold">
                                        <span>Total</span>
                                        <span class="text-brand-yellow" id="total-amount">Rp 0</span>
                                    </div>
                                </div>

                                <button type="submit" id="submit-btn" disabled
                                    class="w-full bg-gray-300 text-gray-500 font-bold py-4 rounded-xl cursor-not-allowed transition">
                                    <i class="fas fa-lock mr-2"></i> Continue to Checkout
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
    <script>
        const quantities = [];
        const prices = [];
        const names = [];
        const maxQuantities = [];

        // Initialize data
        @foreach ($event->ticketCategories as $category)
            quantities[{{ $loop->index }}] = 0;
            prices[{{ $loop->index }}] = {{ $category->price }};
            names[{{ $loop->index }}] = "{{ $category->name }}";
            maxQuantities[{{ $loop->index }}] = {{ $category->available_count }};
        @endforeach

        function updateQuantity(index, change) {
            const newQty = quantities[index] + change;

            if (newQty < 0 || newQty > maxQuantities[index]) {
                return;
            }

            quantities[index] = newQty;
            document.getElementById('qty-' + index).value = newQty;
            document.getElementById('qty-display-' + index).textContent = newQty;

            updateSummary();
        }

        function updateSummary() {
            let totalTickets = 0;
            let totalAmount = 0;
            let summaryHTML = '';

            quantities.forEach((qty, index) => {
                if (qty > 0) {
                    totalTickets += qty;
                    const subtotal = qty * prices[index];
                    totalAmount += subtotal;

                    summaryHTML += `
                    <div class="flex justify-between text-sm">
                        <div>
                            <p class="font-medium text-gray-900">${names[index]}</p>
                            <p class="text-xs text-gray-500">${qty} x Rp ${formatRupiah(prices[index])}</p>
                        </div>
                        <p class="font-semibold text-gray-900">Rp ${formatRupiah(subtotal)}</p>
                    </div>
                `;
                }
            });

            if (summaryHTML === '') {
                summaryHTML = `
                <p class="text-gray-500 text-sm text-center py-8">
                    <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-2 block"></i>
                    No tickets selected
                </p>
            `;
            }

            document.getElementById('summary-items').innerHTML = summaryHTML;
            document.getElementById('total-tickets').textContent = totalTickets;
            document.getElementById('total-amount').textContent = 'Rp ' + formatRupiah(totalAmount);

            // Enable/disable submit button
            const submitBtn = document.getElementById('submit-btn');
            if (totalTickets > 0) {
                submitBtn.disabled = false;
                submitBtn.className =
                    'w-full bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 rounded-xl transition transform hover:scale-105 shadow-lg';
                submitBtn.innerHTML = '<i class="fas fa-arrow-right mr-2"></i> Continue to Checkout';
            } else {
                submitBtn.disabled = true;
                submitBtn.className =
                    'w-full bg-gray-300 text-gray-500 font-bold py-4 rounded-xl cursor-not-allowed transition';
                submitBtn.innerHTML = '<i class="fas fa-lock mr-2"></i> Continue to Checkout';
            }
        }

        function formatRupiah(amount) {
            return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Form validation and cleanup
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            const totalTickets = quantities.reduce((a, b) => a + b, 0);

            if (totalTickets === 0) {
                e.preventDefault();
                alert('Please select at least one ticket');
                return false;
            }

            // Remove items with quantity 0 before submitting
            quantities.forEach((qty, index) => {
                if (qty === 0) {
                    const qtyInput = document.getElementById('qty-' + index);
                    const categoryInput = document.querySelector(
                        `input[name="items[${index}][ticket_category_id]"]`);
                    if (qtyInput) qtyInput.remove();
                    if (categoryInput) categoryInput.remove();
                }
            });
        });
    </script>
@endpush
