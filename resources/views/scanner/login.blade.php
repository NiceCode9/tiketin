<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login Petugas - Tiketin Scanner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Animated Background Gradients -->
    <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-900 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute top-0 -right-4 w-72 h-72 bg-indigo-900 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-8 left-20 w-72 h-72 bg-blue-900 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

    <div class="w-full max-w-md z-10">
        <div class="glass rounded-3xl p-8 shadow-2xl">
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-indigo-500/30">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold tracking-tight mb-2">Login Petugas</h1>
                <p class="text-gray-400 text-sm">Masukkan kredensial Anda untuk mengakses sistem scanner Tiketin.</p>
            </div>

            @if($errors->any())
                <div class="bg-red-900/30 border border-red-500/50 text-red-200 px-4 py-3 rounded-xl mb-6 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('scanner.login.post') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Alamat Email</label>
                    <input type="email" 
                           name="email" 
                           id="email"
                           required
                           autofocus
                           value="{{ old('email') }}"
                           placeholder="staff@tiketin.id"
                           class="w-full bg-gray-900/50 border border-gray-700 text-white rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition placeholder-gray-600">
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Kata Sandi</label>
                    <input type="password" 
                           name="password" 
                           id="password"
                           required
                           placeholder="••••••••"
                           class="w-full bg-gray-900/50 border border-gray-700 text-white rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition placeholder-gray-600">
                </div>

                <div class="flex items-center justify-between py-1">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" 
                               name="remember" 
                               class="hidden peer">
                        <div class="w-5 h-5 border-2 border-gray-700 rounded-md mr-2 flex items-center justify-center peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition">
                            <svg class="w-3.5 h-3.5 text-white hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-sm text-gray-400 group-hover:text-gray-300 transition">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-600/20 transform active:scale-[0.98] transition duration-150">
                    Masuk ke Sistem
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-[10px] uppercase tracking-[0.2em] font-bold text-gray-600">Tiketin.id Security Layer 1.0</p>
            </div>
        </div>
    </div>

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2000ms;
        }
        .animation-delay-4000 {
            animation-delay: 4000ms;
        }
    </style>
</body>
</html>
