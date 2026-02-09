@extends('layouts.app')

@section('title', 'Select Seats - ' . $event->name)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Select Your Seats</h1>
    <p class="text-gray-600 mb-8">{{ $event->name }}</p>

    <form action="{{ route('orders.store', $event->slug) }}" method="POST" id="seatSelectionForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Seat Map -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Seat Map</h2>

                    @foreach($event->ticketCategories->where('is_seated', true)->groupBy('venue_section_id') as $sectionId => $categories)
                        @php
                            $section = $categories->first()->venueSection;
                        @endphp
                        
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $section->name }}</h3>
                            
                            <!-- Seat Grid -->
                            <div class="border rounded-lg p-4 bg-gray-50">
                                @php
                                    $seats = $section->seats->groupBy('row_label');
                                @endphp
                                
                                @foreach($seats as $row => $rowSeats)
                                    <div class="flex items-center justify-center mb-2">
                                        <span class="w-8 text-sm font-semibold text-gray-600">{{ $row }}</span>
                                        <div class="flex gap-2">
                                            @foreach($rowSeats->sortBy('seat_number') as $seat)
                                                <button type="button"
                                                        class="seat-button w-10 h-10 rounded text-xs font-semibold transition
                                                        @if($seat->status === 'available') bg-green-500 hover:bg-green-600 text-white
                                                        @elseif($seat->status === 'reserved') bg-yellow-500 text-white cursor-not-allowed
                                                        @else bg-gray-400 text-white cursor-not-allowed
                                                        @endif"
                                                        data-seat-id="{{ $seat->id }}"
                                                        data-seat-label="{{ $seat->full_seat }}"
                                                        data-category-id="{{ $categories->first()->id }}"
                                                        data-price="{{ $categories->first()->price }}"
                                                        @if($seat->status !== 'available') disabled @endif>
                                                    {{ $seat->seat_number }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Legend -->
                            <div class="flex gap-4 mt-4 text-sm">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-green-500 rounded mr-2"></div>
                                    <span>Available</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-blue-500 rounded mr-2"></div>
                                    <span>Selected</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-yellow-500 rounded mr-2"></div>
                                    <span>Reserved</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-gray-400 rounded mr-2"></div>
                                    <span>Sold</span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Non-seated tickets -->
                    @if($event->ticketCategories->where('is_seated', false)->count() > 0)
                        <div class="mt-8 pt-8 border-t">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">General Admission</h3>
                            
                            @foreach($event->ticketCategories->where('is_seated', false) as $category)
                                <div class="border rounded-lg p-4 mb-4">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $category->name }}</h4>
                                            <p class="text-sm text-gray-600">
                                                Rp {{ number_format($category->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <label class="text-sm text-gray-600">Qty:</label>
                                            <input type="number" 
                                                   name="general_tickets[{{ $category->id }}]"
                                                   min="0" 
                                                   max="{{ $category->available_count }}"
                                                   value="0"
                                                   class="w-20 px-3 py-2 border rounded-md general-ticket-input"
                                                   data-category-id="{{ $category->id }}"
                                                   data-price="{{ $category->price }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Selected Seats</h2>
                    
                    <div id="selectedSeatsList" class="mb-6 min-h-[100px]">
                        <p class="text-gray-500 text-sm">No seats selected</p>
                    </div>

                    <div class="border-t pt-4 mb-6">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total:</span>
                            <span id="totalAmount">Rp 0</span>
                        </div>
                    </div>

                    <!-- Consumer Information -->
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="consumer_name" required
                                   class="w-full px-3 py-2 border rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="consumer_email" required
                                   class="w-full px-3 py-2 border rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp *</label>
                            <input type="text" name="consumer_whatsapp" required
                                   placeholder="08xxxxxxxxxx"
                                   class="w-full px-3 py-2 border rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Identity Type *</label>
                            <select name="consumer_identity_type" required
                                    class="w-full px-3 py-2 border rounded-md text-sm">
                                <option value="ktp">KTP</option>
                                <option value="sim">SIM</option>
                                <option value="passport">Passport</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Identity Number *</label>
                            <input type="text" name="consumer_identity_number" required
                                   class="w-full px-3 py-2 border rounded-md text-sm">
                        </div>
                    </div>

                    <button type="submit" id="checkoutButton" disabled
                            class="w-full bg-indigo-600 text-white py-3 px-6 rounded-md hover:bg-indigo-700 transition font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed">
                        Continue to Checkout
                    </button>
                </div>
            </div>
        </div>

        <!-- Hidden inputs for selected seats -->
        <div id="hiddenInputs"></div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const selectedSeats = new Map();
    let totalAmount = 0;

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
                this.classList.remove('bg-blue-500');
                this.classList.add('bg-green-500', 'hover:bg-green-600');
                totalAmount -= price;
            } else {
                // Select
                selectedSeats.set(seatId, { seatLabel, categoryId, price });
                this.classList.remove('bg-green-500', 'hover:bg-green-600');
                this.classList.add('bg-blue-500');
                totalAmount += price;
            }

            updateSummary();
        });
    });

    // General ticket quantity handling
    document.querySelectorAll('.general-ticket-input').forEach(input => {
        input.addEventListener('change', function() {
            updateSummary();
        });
    });

    function updateSummary() {
        const listContainer = document.getElementById('selectedSeatsList');
        const hiddenInputsContainer = document.getElementById('hiddenInputs');
        const checkoutButton = document.getElementById('checkoutButton');
        
        // Clear previous content
        listContainer.innerHTML = '';
        hiddenInputsContainer.innerHTML = '';
        
        // Calculate total from seated tickets
        let total = 0;
        let itemIndex = 0;

        // Add selected seats
        if (selectedSeats.size > 0) {
            selectedSeats.forEach((seat, seatId) => {
                const div = document.createElement('div');
                div.className = 'flex justify-between text-sm mb-2';
                div.innerHTML = `
                    <span>${seat.seatLabel}</span>
                    <span>Rp ${seat.price.toLocaleString('id-ID')}</span>
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
            });
        }

        // Add general admission tickets
        document.querySelectorAll('.general-ticket-input').forEach(input => {
            const quantity = parseInt(input.value) || 0;
            if (quantity > 0) {
                const categoryId = input.dataset.categoryId;
                const price = parseFloat(input.dataset.price);
                const subtotal = quantity * price;

                const div = document.createElement('div');
                div.className = 'flex justify-between text-sm mb-2';
                div.innerHTML = `
                    <span>General x${quantity}</span>
                    <span>Rp ${subtotal.toLocaleString('id-ID')}</span>
                `;
                listContainer.appendChild(div);

                hiddenInputsContainer.innerHTML += `
                    <input type="hidden" name="items[${itemIndex}][ticket_category_id]" value="${categoryId}">
                    <input type="hidden" name="items[${itemIndex}][quantity]" value="${quantity}">
                `;
                itemIndex++;
                total += subtotal;
            }
        });

        // Update display
        if (listContainer.children.length === 0) {
            listContainer.innerHTML = '<p class="text-gray-500 text-sm">No seats selected</p>';
            checkoutButton.disabled = true;
        } else {
            checkoutButton.disabled = false;
        }

        document.getElementById('totalAmount').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
</script>
@endpush
