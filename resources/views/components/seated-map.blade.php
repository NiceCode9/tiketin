@props(['section', 'categories', 'selectedSeats'])

<div class="mb-12 animate-slide-up" x-data="{
    localSeats: [],
    loading: true,
    async fetchStatus() {
        try {
            const response = await fetch('{{ route('events.seat-status', $section->venue->event->slug ?? 'slug') }}');
            const data = await response.json();
            this.localSeats = data.seats.filter(s => s.venue_section_id == {{ $section->id }});
            this.loading = false;
        } catch (e) {
            console.error('Failed to fetch seat status', e);
        }
    }
}" x-init="fetchStatus(); setInterval(() => fetchStatus(), 15000)">

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-brand-primary/10 rounded-lg flex items-center justify-center text-brand-primary">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <h4 class="text-xl font-bold text-slate-900">{{ $section->name }}</h4>
                <p class="text-xs text-gray-500">Premium Seating Area</p>
            </div>
        </div>
        <div class="flex flex-col items-end">
            <span class="text-brand-primary font-bold">
                Rp {{ number_format($categories->first()->price, 0, ',', '.') }}
            </span>
            <span class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Price per Seat</span>
        </div>
    </div>

    {{-- Stage Indicator --}}
    <div class="relative mb-16 px-4">
        <div class="absolute inset-x-0 -bottom-4 h-8 bg-brand-primary/5 blur-2xl rounded-full"></div>
        <div class="relative mx-auto max-w-2xl bg-gradient-to-b from-slate-800 to-slate-950 text-white text-center py-5 rounded-t-3xl shadow-2xl overflow-hidden border-x-[20px] border-x-transparent border-b-4 border-slate-700/50">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
            <div class="relative flex items-center justify-center gap-6">
                <i class="fas fa-bolt text-brand-yellow/50 text-xs animate-pulse"></i>
                <span class="font-black tracking-[0.5em] text-sm uppercase text-slate-100 drop-shadow-lg">S T A G E</span>
                <i class="fas fa-bolt text-brand-yellow/50 text-xs animate-pulse"></i>
            </div>
            {{-- Searchlight effect --}}
            <div class="absolute -top-10 left-1/4 w-20 h-40 bg-white/5 -rotate-45 blur-2xl group-hover:left-1/3 transition-all duration-1000"></div>
            <div class="absolute -top-10 right-1/4 w-20 h-40 bg-white/5 rotate-45 blur-2xl group-hover:right-1/3 transition-all duration-1000"></div>
        </div>
    </div>

    {{-- Seat Grid Container --}}
    <div class="relative overflow-x-auto pb-4 custom-scrollbar">
        <div class="inline-block min-w-full align-middle">
            <div class="flex flex-col items-center gap-4">
                @php
                    $seatsByRow = $section->seats->groupBy('row_label');
                @endphp

                @foreach ($seatsByRow as $row => $rowSeats)
                    <div class="flex items-center gap-6">
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-400 border border-slate-200">
                            {{ $row }}
                        </div>

                        <div class="flex gap-2">
                            @foreach ($rowSeats->sortBy('seat_number') as $seat)
                                <button type="button"
                                    class="relative group w-10 h-10 sm:w-12 sm:h-12 rounded-xl text-xs font-bold transition-all duration-300 flex items-center justify-center"
                                    :class="getSeatClass({{ $seat->id }}, '{{ $seat->status }}')"
                                    @click="toggleSeat({{ $seat->id }}, '{{ $seat->full_seat }}', {{ $categories->first()->id }}, {{ $categories->first()->price }}, {{ $categories->first()->biaya_layanan ?? 0 }}, {{ $categories->first()->biaya_admin_payment ?? 0 }})"
                                    :disabled="'{{ $seat->status }}' !== 'available'">

                                    {{-- Seat Number --}}
                                    <span class="relative z-10" x-text="'{{ $seat->seat_number }}'"></span>

                                    {{-- Tooltip (Desktop) --}}
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-slate-900 text-white text-[10px] rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">
                                        Seat {{ $seat->full_seat }} • Rp {{ number_format($categories->first()->price, 0, ',', '.') }}
                                    </div>

                                    {{-- Internal Glow for Selected --}}
                                    <template x-if="isSelected({{ $seat->id }})">
                                        <div class="absolute inset-0 bg-white/20 rounded-xl animate-pulse"></div>
                                    </template>
                                </button>
                            @endforeach
                        </div>

                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-400 border border-slate-200">
                            {{ $row }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Legend --}}
    <div class="flex flex-wrap justify-center gap-6 mt-10 p-4 bg-slate-50 rounded-2xl border border-slate-100">
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded-md bg-emerald-500 shadow-sm border border-emerald-600/20"></div>
            <span class="text-[11px] font-bold text-slate-600 uppercase tracking-tight">Available</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded-md bg-brand-primary shadow-sm border border-brand-primary/20"></div>
            <span class="text-[11px] font-bold text-slate-600 uppercase tracking-tight">Selected</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded-md bg-amber-500 shadow-sm border border-amber-600/20"></div>
            <span class="text-[11px] font-bold text-slate-600 uppercase tracking-tight">Pending</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded-md bg-slate-300 shadow-sm border border-slate-400/20"></div>
            <span class="text-[11px] font-bold text-slate-600 uppercase tracking-tight">Sold Out</span>
        </div>
    </div>
</div>
