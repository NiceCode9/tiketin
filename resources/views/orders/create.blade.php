@extends('layouts.app')

@section('title', 'Select Tickets - ' . $event->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Select Your Tickets</h1>

    <form action="{{ route('orders.store', $event->slug) }}" method="POST" id="orderForm">
        @csrf

        <!-- Ticket Selection -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Ticket Categories</h2>
            
            <div class="space-y-4">
                @foreach($event->ticketCategories as $category)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $category->name }}</h3>
                                <p class="text-sm text-gray-600">
                                    Rp {{ number_format($category->price, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <label for="quantity_{{ $category->id }}" class="text-sm text-gray-600">Quantity:</label>
                                <input type="number" 
                                       name="items[{{ $loop->index }}][quantity]" 
                                       id="quantity_{{ $category->id }}"
                                       min="0" 
                                       max="{{ $category->available_count }}"
                                       value="0"
                                       class="w-20 px-3 py-2 border rounded-md"
                                       {{ !$category->hasAvailableTickets() ? 'disabled' : '' }}>
                                <input type="hidden" 
                                       name="items[{{ $loop->index }}][ticket_category_id]" 
                                       value="{{ $category->id }}">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">
                            {{ $category->available_count }} tickets available
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Consumer Information -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Your Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="consumer_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name *
                    </label>
                    <input type="text" 
                           name="consumer_name" 
                           id="consumer_name"
                           required
                           class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('consumer_name') }}">
                    @error('consumer_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="consumer_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email *
                    </label>
                    <input type="email" 
                           name="consumer_email" 
                           id="consumer_email"
                           required
                           class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('consumer_email') }}">
                    @error('consumer_email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="consumer_whatsapp" class="block text-sm font-medium text-gray-700 mb-2">
                        WhatsApp Number *
                    </label>
                    <input type="text" 
                           name="consumer_whatsapp" 
                           id="consumer_whatsapp"
                           required
                           placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('consumer_whatsapp') }}">
                    @error('consumer_whatsapp')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="consumer_identity_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Identity Type *
                    </label>
                    <select name="consumer_identity_type" 
                            id="consumer_identity_type"
                            required
                            class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="ktp">KTP</option>
                        <option value="sim">SIM</option>
                        <option value="passport">Passport</option>
                    </select>
                    @error('consumer_identity_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="consumer_identity_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Identity Number *
                    </label>
                    <input type="text" 
                           name="consumer_identity_number" 
                           id="consumer_identity_number"
                           required
                           class="w-full px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                           value="{{ old('consumer_identity_number') }}">
                    @error('consumer_identity_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-between items-center">
            <a href="{{ route('events.show', $event->slug) }}" 
               class="text-gray-600 hover:text-gray-900">
                &larr; Back to Event
            </a>
            <button type="submit" 
                    class="bg-indigo-600 text-white py-3 px-8 rounded-md hover:bg-indigo-700 transition font-semibold">
                Continue to Checkout
            </button>
        </div>
    </form>
</div>
@endsection
