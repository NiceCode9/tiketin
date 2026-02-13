@extends('layouts.app')

@section('title', 'Menunggu Pembayaran')

@section('content')
    <div class="py-12 bg-gray-50 min-h-screen flex items-center justify-center">
        <div class="container mx-auto px-4 max-w-2xl">
            <div class="bg-white rounded-2xl shadow-lg p-8">

                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-brand-yellow rounded-full mb-4">
                        <i class="fas fa-credit-card text-3xl text-black"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-3">Selesaikan Pembayaran</h2>
                    <p class="text-gray-600">
                        Silakan selesaikan pembayaran Anda melalui halaman Midtrans yang akan muncul
                    </p>
                </div>

                <!-- Loading Indicator -->
                <div id="loading-indicator" class="text-center mb-8">
                    <div class="flex justify-center mb-4">
                        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-brand-yellow"></div>
                    </div>
                    <p class="text-sm text-gray-500">Membuka halaman pembayaran...</p>
                </div>

                <!-- Order Summary -->
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h3 class="font-bold text-sm text-gray-600 mb-4">RINGKASAN PESANAN</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Event</span>
                            <span class="font-semibold text-slate-900">{{ $order->event->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order Number</span>
                            <span class="font-mono text-xs text-slate-900">{{ $order->order_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nama</span>
                            <span class="text-slate-900">{{ $order->customer->full_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email</span>
                            <span class="text-slate-900">{{ $order->customer->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Tiket</span>
                            <span class="text-slate-900">{{ $order->orderItems->sum('quantity') }} tiket</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2 mt-2 flex justify-between">
                            <span class="font-bold text-slate-900">Total Bayar</span>
                            <span class="font-bold text-lg text-brand-yellow">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Expiry Warning -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-clock text-yellow-600 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm text-yellow-800 font-semibold mb-1">Batas Waktu Pembayaran</p>
                            <p class="text-xs text-yellow-700">
                                Berlaku sampai: <strong>{{ $order->expired_at->format('d M Y, H:i') }} WIB</strong>
                            </p>
                            <p class="text-xs text-yellow-700 mt-1">
                                Pesanan akan otomatis dibatalkan jika tidak dibayar
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm text-blue-800 font-semibold mb-2">Petunjuk:</p>
                            <ul class="text-xs text-blue-700 space-y-1">
                                <li>• Halaman pembayaran Midtrans akan muncul otomatis</li>
                                <li>• Pilih metode pembayaran yang Anda inginkan</li>
                                <li>• Ikuti instruksi pembayaran yang diberikan</li>
                                <li>• Tiket akan dikirim ke email setelah pembayaran berhasil</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons (shown after popup is closed) -->
                <div id="action-buttons" class="hidden">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button onclick="retryPayment()"
                            class="flex-1 bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-6 rounded-xl transition transform hover:scale-105">
                            <i class="fas fa-redo mr-2"></i> Bayar Lagi
                        </button>
                        <a href="{{ route('home') }}"
                            class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-xl transition">
                            <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    </script>
    <script type="text/javascript">
        const snapToken = @json($order->snap_token);
        const orderNumber = @json($order->order_number);
        const checkStatusUrl = @json(route('payment.check-status', $order->order_number));
        const cancelUrl = @json(route('payment.cancel', $order->order_number));
        const successUrl = @json(route('payment.success', $order->order_number));
        const failedUrl = @json(route('payment.failed', $order->order_number));

        let statusCheckInterval;

        // Auto-trigger Midtrans Snap popup when page loads
        window.onload = function() {
            if (snapToken) {
                // Small delay to ensure page is fully loaded
                setTimeout(() => {
                    openPaymentPopup();
                }, 500);
            } else {
                showError('Token pembayaran tidak valid');
            }
        };

        function openPaymentPopup() {
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    // Redirect to success page
                    window.location.href = successUrl;
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    // Start checking status
                    hideLoading();
                    startStatusCheck();
                    showMessage('Pembayaran sedang diproses. Harap tunggu konfirmasi.', 'info');
                },
                onError: function(result) {
                    console.log('Payment error:', result);
                    hideLoading();
                    showActionButtons();
                    showMessage('Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.', 'error');
                },
                onClose: function() {
                    console.log('Payment popup closed by user');
                    hideLoading();
                    cancelOrder();

                    // Check if payment is still pending
                    // checkPaymentStatus().then(status => {
                    //     console.log('Checked payment status after popup closed:', status);
                    //     if (status === 'pending') {
                    //         // User closed popup, but payment might still be processed
                    //         showActionButtons();
                    //         showMessage(
                    //             'Anda menutup halaman pembayaran. Klik "Bayar Lagi" jika ingin melanjutkan pembayaran.',
                    //             'warning');
                    //     } else if (status === 'paid') {
                    //         // Payment was completed
                    //         window.location.href = successUrl;
                    //     } else {
                    //         // Cancel the order
                    //         cancelOrder();
                    //     }
                    // });
                }
            });
        }

        function retryPayment() {
            hideActionButtons();
            showLoading();
            openPaymentPopup();
        }

        function checkPaymentStatus() {
            return fetch(checkStatusUrl)
                .then(response => response.json())
                .then(data => data.status)
                .catch(error => {
                    console.error('Error checking status:', error);
                    return 'unknown';
                });
        }

        function startStatusCheck() {
            // Check payment status every 5 seconds
            statusCheckInterval = setInterval(async () => {
                const status = await checkPaymentStatus();

                if (status === 'paid') {
                    clearInterval(statusCheckInterval);
                    window.location.href = successUrl;
                } else if (['failed', 'cancelled', 'expired'].includes(status)) {
                    clearInterval(statusCheckInterval);
                    window.location.href = failedUrl;
                }
            }, 5000);
        }

        function cancelOrder() {
            // // Redirect to cancel endpoint
            window.location.href = cancelUrl;
        }

        function showLoading() {
            document.getElementById('loading-indicator').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loading-indicator').classList.add('hidden');
        }

        function showActionButtons() {
            document.getElementById('action-buttons').classList.remove('hidden');
        }

        function hideActionButtons() {
            document.getElementById('action-buttons').classList.add('hidden');
        }

        function showMessage(message, type = 'info') {
            const colors = {
                'info': 'bg-blue-50 border-blue-200 text-blue-800',
                'error': 'bg-red-50 border-red-200 text-red-800',
                'warning': 'bg-yellow-50 border-yellow-200 text-yellow-800',
            };

            const icons = {
                'info': 'fa-info-circle',
                'error': 'fa-exclamation-circle',
                'warning': 'fa-exclamation-triangle',
            };

            const messageHtml = `
                <div class="${colors[type]} border rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <i class="fas ${icons[type]} mt-1 mr-3"></i>
                        <p class="text-sm flex-1">${message}</p>
                    </div>
                </div>
            `;

            // Insert before action buttons
            const actionButtons = document.getElementById('action-buttons');
            actionButtons.insertAdjacentHTML('beforebegin', messageHtml);
        }

        function showError(message) {
            hideLoading();
            showActionButtons();
            showMessage(message, 'error');
        }

        // Clean up interval on page unload
        window.addEventListener('beforeunload', () => {
            if (statusCheckInterval) {
                clearInterval(statusCheckInterval);
            }
        });
    </script>
@endpush
