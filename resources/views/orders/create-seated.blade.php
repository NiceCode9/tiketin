@extends('layouts.app')

@section('title', 'Select Seats - ' . $event->name)

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

            <form action="{{ route('orders.store', $event->slug) }}" method="POST" id="seatSelectionForm">
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
                                                                class="seat-button w-12 h-12 rounded-lg text-sm font-bold transition-all duration-200 transform hover:scale-110
                                                                    @if ($seat->status === 'available') bg-green-500 hover:bg-green-600 text-white shadow-md hover:shadow-lg
                                                                    @elseif($seat->status === 'reserved')
                                                                        bg-yellow-500 text-white cursor-not-allowed opacity-60
                                                                    @else
                                                                        bg-gray-400 text-white cursor-not-allowed opacity-50 @endif"
                                                                data-seat-id="{{ $seat->id }}"
                                                                data-seat-label="{{ $seat->full_seat }}"
                                                                data-category-id="{{ $categories->first()->id }}"
                                                                data-price="{{ $categories->first()->price }}"
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
                                                <div
                                                    class="border-2 border-gray-200 rounded-xl p-6 hover:border-brand-primary transition-all duration-300">
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
                                                                onclick="updateGeneralQty('{{ $category->id }}', -1)"
                                                                class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-lg flex items-center justify-center transition">
                                                                <i class="fas fa-minus"></i>
                                                            </button>

                                                            <span class="w-12 text-center text-2xl font-bold"
                                                                id="general-qty-display-{{ $category->id }}">0</span>

                                                            <button type="button"
                                                                onclick="updateGeneralQty('{{ $category->id }}', 1)"
                                                                class="w-10 h-10 bg-brand-yellow hover:bg-yellow-400 rounded-lg flex items-center justify-center transition">
                                                                <i class="fas fa-plus"></i>
                                                            </button>

                                                            <input type="hidden" id="general-qty-{{ $category->id }}"
                                                                class="general-ticket-input"
                                                                data-category-id="{{ $category->id }}"
                                                                data-price="{{ $category->price }}"
                                                                data-name="{{ $category->name }}"
                                                                data-max="{{ $category->available_count }}" value="0">
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

                                <div id="selectedSeatsList"
                                    class="space-y-3 mb-6 min-h-[150px] max-h-[400px] overflow-y-auto">
                                    <p class="text-gray-500 text-sm text-center py-8">
                                        <i class="fas fa-chair text-4xl text-gray-300 mb-2 block"></i>
                                        No seats selected
                                    </p>
                                </div>

                                <div class="border-t border-gray-200 pt-4 mb-6">
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="text-gray-600">Total Tickets</span>
                                        <span class="font-semibold" id="total-tickets">0</span>
                                    </div>
                                    <div class="flex justify-between text-xl font-bold">
                                        <span>Total</span>
                                        <span class="text-brand-yellow" id="totalAmount">Rp 0</span>
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

                                <button type="submit" id="checkoutButton" disabled
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

                {{-- Hidden inputs for selected seats --}}
                <div id="hiddenInputs"></div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const selectedSeats = new Map();
        const generalTickets = {};

        // Initialize general tickets
        document.querySelectorAll('.general-ticket-input').forEach(input => {
            const categoryId = input.dataset.categoryId;
            generalTickets[categoryId] = {
                quantity: 0,
                price: parseFloat(input.dataset.price),
                name: input.dataset.name,
                max: parseInt(input.dataset.max)
            };
        });

        // Seat selection handling
        document.querySelectorAll('.seat-button').forEach(button => {
            button.addEventListener('click', function() {
                if (this.disabled) return;

                const seatId = this.dataset.seatId;
                const seatLabel = this.dataset.seatLabel;
                const categoryId = this.dataset.categoryId;
                const price = parseFloat(this.dataset.price);

                if (selectedSeats.has(seatId)) {
                    // Deselect
                    selectedSeats.delete(seatId);
                    this.classList.remove('bg-blue-600', 'shadow-xl', 'scale-110');
                    this.classList.add('bg-green-500', 'hover:bg-green-600', 'shadow-md');
                } else {
                    // Select
                    selectedSeats.set(seatId, {
                        seatLabel,
                        categoryId,
                        price
                    });
                    this.classList.remove('bg-green-500', 'hover:bg-green-600', 'shadow-md');
                    this.classList.add('bg-blue-600', 'shadow-xl', 'scale-110');
                }

                updateSummary();
            });
        });

        function updateGeneralQty(categoryId, change) {
            const current = generalTickets[categoryId].quantity;
            const newQty = current + change;

            if (newQty < 0 || newQty > generalTickets[categoryId].max) {
                return;
            }

            generalTickets[categoryId].quantity = newQty;
            document.getElementById('general-qty-' + categoryId).value = newQty;
            document.getElementById('general-qty-display-' + categoryId).textContent = newQty;

            updateSummary();
        }

        function updateSummary() {
            const listContainer = document.getElementById('selectedSeatsList');
            const hiddenInputsContainer = document.getElementById('hiddenInputs');
            const checkoutButton = document.getElementById('checkoutButton');

            // Clear previous content
            listContainer.innerHTML = '';
            hiddenInputsContainer.innerHTML = '';

            let total = 0;
            let totalTickets = 0;
            let itemIndex = 0;

            // Add selected seats
            if (selectedSeats.size > 0) {
                const seatsHeader = document.createElement('div');
                seatsHeader.className = 'font-semibold text-gray-900 mb-2 text-sm';
                seatsHeader.innerHTML = '<i class="fas fa-chair text-brand-primary mr-1"></i> Seated Tickets';
                listContainer.appendChild(seatsHeader);

                selectedSeats.forEach((seat, seatId) => {
                    const div = document.createElement('div');
                    div.className = 'flex justify-between text-sm mb-2 pl-4';
                    div.innerHTML = `
                    <span class="text-gray-700">${seat.seatLabel}</span>
                    <span class="font-semibold text-gray-900">Rp ${formatRupiah(seat.price)}</span>
                `;
                    listContainer.appendChild(div);

                    // Add hidden inputs
                    hiddenInputsContainer.innerHTML += `
                    <input type="hidden" name="items[${itemIndex}][ticket_category_id]" value="${seat.categoryId}">
                    <input type="hidden" name="items[${itemIndex}][quantity]" value="1">
                    <input type="hidden" name="items[${itemIndex}][seat_id]" value="${seatId}">
                `;
                    itemIndex++;
                    total += seat.price;
                    totalTickets++;
                });
            }

            // Add general admission tickets
            let hasGeneral = false;
            Object.keys(generalTickets).forEach(categoryId => {
                const ticket = generalTickets[categoryId];
                if (ticket.quantity > 0) {
                    if (!hasGeneral) {
                        const generalHeader = document.createElement('div');
                        generalHeader.className = 'font-semibold text-gray-900 mb-2 text-sm mt-4';
                        generalHeader.innerHTML =
                            '<i class="fas fa-users text-brand-primary mr-1"></i> General Admission';
                        listContainer.appendChild(generalHeader);
                        hasGeneral = true;
                    }

                    const subtotal = ticket.quantity * ticket.price;
                    const div = document.createElement('div');
                    div.className = 'flex justify-between text-sm mb-2 pl-4';
                    div.innerHTML = `
                    <div>
                        <p class="text-gray-700">${ticket.name}</p>
                        <p class="text-xs text-gray-500">${ticket.quantity} x Rp ${formatRupiah(ticket.price)}</p>
                    </div>
                    <span class="font-semibold text-gray-900">Rp ${formatRupiah(subtotal)}</span>
                `;
                    listContainer.appendChild(div);

                    hiddenInputsContainer.innerHTML += `
                    <input type="hidden" name="items[${itemIndex}][ticket_category_id]" value="${categoryId}">
                    <input type="hidden" name="items[${itemIndex}][quantity]" value="${ticket.quantity}">
                `;
                    itemIndex++;
                    total += subtotal;
                    totalTickets += ticket.quantity;
                }
            });

            // Update display
            if (listContainer.children.length === 0) {
                listContainer.innerHTML = `
                <p class="text-gray-500 text-sm text-center py-8">
                    <i class="fas fa-chair text-4xl text-gray-300 mb-2 block"></i>
                    No seats selected
                </p>
            `;
                checkoutButton.disabled = true;
                checkoutButton.className =
                    'w-full bg-gray-300 text-gray-500 font-bold py-4 rounded-xl cursor-not-allowed transition';
                checkoutButton.innerHTML = '<i class="fas fa-lock mr-2"></i> Continue to Checkout';
            } else {
                checkoutButton.disabled = false;
                checkoutButton.className =
                    'w-full bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 rounded-xl transition transform hover:scale-105 shadow-lg';
                checkoutButton.innerHTML = '<i class="fas fa-arrow-right mr-2"></i> Continue to Checkout';
            }

            document.getElementById('total-tickets').textContent = totalTickets;
            document.getElementById('totalAmount').textContent = 'Rp ' + formatRupiah(total);
        }

        function formatRupiah(amount) {
            return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    </script>
@endpush
