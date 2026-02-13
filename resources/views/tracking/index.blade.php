@extends('layouts.app')

@section('title', 'Lacak Pesanan')

@section('content')
    <div class="py-20 bg-gray-50 flex-grow flex items-center">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                    <div class="bg-brand-yellow px-8 py-10 text-center">
                        <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-search text-black text-3xl"></i>
                        </div>
                        <h1 class="text-3xl font-bold text-black mb-2">Lacak Pesanan Anda</h1>
                        <p class="text-black/70">Masukkan nomor identitas yang Anda gunakan saat memesan.</p>
                    </div>

                    <div class="p-8 md:p-12">
                        <form action="{{ route('tracking.track') }}" method="POST">
                            @csrf
                            <div class="mb-8">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">
                                    Nomor Identitas (NIK/SIM/Paspor)
                                </label>
                                <div class="relative">
                                    <input type="text" name="identity_number" required
                                        class="w-full pl-14 pr-6 py-5 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-brand-yellow transition-all duration-300 outline-none text-lg font-semibold"
                                        placeholder="Contoh: 3275xxxxxxxxxxxx">
                                    <i class="fas fa-id-card absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 text-xl"></i>
                                </div>
                                <p class="text-xs text-gray-400 mt-4 leading-relaxed">
                                    <i class="fas fa-info-circle mr-1 text-blue-500"></i> 
                                    Gunakan nomor identitas yang sama dengan yang Anda isi pada formulir pemesanan tiket.
                                </p>
                            </div>

                            <button type="submit"
                                class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-5 rounded-2xl transition transform active:scale-95 shadow-lg flex items-center justify-center gap-3">
                                Temukan Pesanan Saya <i class="fas fa-arrow-right text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-12 text-center text-gray-400 text-sm">
                    <p>Butuh bantuan? Hubungi WhatsApp Customer Service kami di</p>
                    <a href="https://wa.me/628123456789" class="text-slate-600 font-bold hover:text-brand-yellow transition">
                        +62 812 3456 789
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
