@extends('scanner.layout')

@section('title', 'Riwayat Validasi')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col space-y-1">
        <h2 class="text-xl font-bold text-emerald-500">Log Validasi Masuk</h2>
        <p class="text-xs text-gray-500">Daftar pengunjung yang telah divalidasi masuk ke area event.</p>
    </div>

    <!-- Filter -->
    <div class="glass-dark rounded-2xl p-4">
        <form action="{{ route('scanner.validate.history') }}" method="GET">
            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">Filter Event</label>
            <div class="flex gap-2">
                <select name="event_id" class="flex-1 bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition">
                    <option value="">Semua Event</option>
                    @foreach(\App\Models\Event::where('client_id', auth()->guard('scanner')->user()->client_id)->get() as $event)
                        <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-sm font-bold">Cari</button>
            </div>
        </form>
    </div>

    <!-- History List -->
    <div class="space-y-3">
        @forelse($wristbands as $wristband)
            <div class="glass-dark rounded-2xl p-4 border-l-4 border-emerald-500">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest block">CHECK-IN ID</span>
                        <h3 class="text-lg font-black text-white italic">#{{ $wristband->id }}</h3>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest block">VALIDATED AT</span>
                        <span class="text-xs font-black text-emerald-400">{{ $wristband->validated_at->format('H:i:s') }}</span>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Customer:</span>
                        <span class="text-white font-semibold">{{ $wristband->ticket->consumer_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Kategori:</span>
                        <span class="text-emerald-400 font-bold uppercase text-xs">{{ $wristband->ticket->ticketCategory->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Validator:</span>
                        <span class="text-gray-400 italic text-xs">{{ $wristband->validatedBy->name }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 glass-dark rounded-3xl">
                <div class="w-16 h-16 bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-gray-500 text-sm font-medium">Belum ada pengunjung masuk hari ini.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $wristbands->links('pagination::simple-tailwind') }}
    </div>
</div>
@endsection
