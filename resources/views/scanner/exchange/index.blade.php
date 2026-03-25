@extends('scanner.layout')

@section('title', 'Penukaran Tiket')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Penukaran Tiket ke Wristband</h2>

    <!-- Event Selection -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <label for="eventSelect" class="block text-sm font-medium text-gray-700 mb-2">Pilih Event</label>
        <select id="eventSelect" class="w-full px-4 py-3 border rounded-lg text-lg">
            <option value="">-- Pilih Event --</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}">{{ $event->name }} - {{ $event->event_date->format('d M Y') }}</option>
            @endforeach
        </select>
    </div>

    <!-- QR Scanner -->
    <div id="scannerSection" class="hidden">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Scan QR Code Tiket</h3>
            
            <!-- Camera Scanner -->
            <div id="qr-reader" class="mb-4"></div>
            
            <!-- Manual Input -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Atau masukkan manual:</label>
                <div class="flex gap-2">
                    <input type="text" 
                           id="manualQR" 
                           placeholder="Tempel QR code di sini"
                           class="flex-1 px-4 py-3 border rounded-lg text-lg">
                    <button onclick="scanManual()" 
                            class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold">
                        Cek
                    </button>
                </div>
            </div>
        </div>

        <!-- Ticket Information -->
        <div id="ticketInfo" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Informasi Tiket</h3>
            <div id="ticketDetails"></div>
            <button onclick="issueWristband()" 
                    class="w-full mt-4 bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 font-semibold text-lg">
                Terbitkan Wristband
            </button>
        </div>

        <!-- Wristband QR Code -->
        <div id="wristbandQR" class="hidden bg-white rounded-lg shadow-md p-6 text-center">
            <h3 class="text-lg font-semibold mb-4 text-green-600">✓ Wristband Berhasil Diterbitkan!</h3>
            <div id="wristbandDetails" class="mb-4"></div>
            <button onclick="resetScanner()" 
                    class="bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 font-semibold">
                Scan Tiket Berikutnya
            </button>
        </div>
    </div>

    <!-- Status Messages -->
    <div id="statusMessage" class="hidden rounded-lg p-4 mb-6"></div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrcodeScanner = null;
    let currentTicketId = null;
    let selectedEventId = null;
    let isProcessing = false;

    document.getElementById('eventSelect').addEventListener('change', function() {
        selectedEventId = this.value;
        if (selectedEventId) {
            document.getElementById('scannerSection').classList.remove('hidden');
            resetScanner();
        } else {
            document.getElementById('scannerSection').classList.add('hidden');
            stopScanner();
        }
    });

    function startScanner() {
        if (html5QrcodeScanner) return;

        html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader",
            { 
                fps: 10, 
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            },
            /* verbose= */ false
        );
        html5QrcodeScanner.render(onScanSuccess, onScanError);
    }

    async function stopScanner() {
        if (html5QrcodeScanner) {
            try {
                await html5QrcodeScanner.clear();
                html5QrcodeScanner = null;
            } catch (err) {
                console.error("Failed to clear scanner:", err);
            }
        }
    }

    function onScanSuccess(decodedText) {
        if (isProcessing) return;
        processQR(decodedText);
    }

    function onScanError(error) {
        // Ignored to avoid console spam during search
    }

    function scanManual() {
        const qrCode = document.getElementById('manualQR').value.trim();
        if (qrCode) {
            processQR(qrCode);
        }
    }

    function processQR(qrCode) {
        if (!selectedEventId) {
            Swal.fire('Peringatan', 'Silakan pilih event terlebih dahulu', 'warning');
            return;
        }

        if (isProcessing) return;
        isProcessing = true;
        
        showStatus('Memproses...', 'info');

        fetch('{{ route("scanner.exchange.scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                qr_code: qrCode,
                event_id: selectedEventId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Tiket Ditemukan!',
                    text: 'Tiket terdaftar atas nama ' + data.consumer.name,
                    timer: 2000,
                    showConfirmButton: false
                });
                displayTicketInfo(data.ticket, data.consumer);
                currentTicketId = data.ticket.id;
                stopScanner();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menemukan Tiket',
                    text: data.message
                });
                showStatus(data.message, 'error');
                isProcessing = false;
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error Sistem',
                text: error.message
            });
            showStatus('Kesalahan: ' + error.message, 'error');
            isProcessing = false;
        });
    }

    function displayTicketInfo(ticket, consumer) {
        const html = `
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="font-semibold">Kategori:</span>
                    <span>${ticket.ticket_category.name}</span>
                </div>
                ${ticket.seat ? `
                <div class="flex justify-between">
                    <span class="font-semibold">Kursi:</span>
                    <span>${ticket.seat.full_seat}</span>
                </div>
                ` : ''}
                <div class="flex justify-between">
                    <span class="font-semibold">Nama Konsumen:</span>
                    <span>${consumer.name}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold">Email:</span>
                    <span>${consumer.email}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold">Identitas:</span>
                    <span>${consumer.identity}</span>
                </div>
            </div>
        `;
        document.getElementById('ticketDetails').innerHTML = html;
        document.getElementById('ticketInfo').classList.remove('hidden');
        document.getElementById('qr-reader').style.display = 'none';
    }

    function issueWristband() {
        if (!currentTicketId) return;

        showStatus('Menerbitkan wristband...', 'info');

        fetch('{{ route("scanner.exchange.issue") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                ticket_id: currentTicketId,
                event_id: selectedEventId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                displayWristband(data.wristband, data.qr_data);
                document.getElementById('ticketInfo').classList.add('hidden');
                showStatus(data.message, 'success');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menerbitkan',
                    text: data.message
                });
                showStatus(data.message, 'error');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message
            });
            showStatus('Kesalahan: ' + error.message, 'error');
        });
    }

    function displayWristband(wristband, qrData) {
        const html = `
            <div class="text-lg font-semibold mb-2">ID Wristband: ${wristband.id}</div>
            <div class="text-sm text-gray-600 mb-4">QR Code: ${qrData}</div>
            <div class="bg-gray-100 p-4 rounded">
                <p class="text-sm">Tunjukkan ini ke pengunjung atau cetak wristband</p>
            </div>
        `;
        document.getElementById('wristbandDetails').innerHTML = html;
        document.getElementById('wristbandQR').classList.remove('hidden');
    }

    function resetScanner() {
        currentTicketId = null;
        isProcessing = false;
        document.getElementById('manualQR').value = '';
        document.getElementById('ticketInfo').classList.add('hidden');
        document.getElementById('wristbandQR').classList.add('hidden');
        document.getElementById('qr-reader').style.display = 'block';
        document.getElementById('statusMessage').classList.add('hidden');
        
        stopScanner().then(() => {
            startScanner();
        });
    }

    function showStatus(message, type) {
        const statusDiv = document.getElementById('statusMessage');
        statusDiv.classList.remove('hidden', 'bg-red-100', 'bg-green-100', 'bg-yellow-100', 'bg-blue-100');
        statusDiv.classList.remove('text-red-700', 'text-green-700', 'text-yellow-700', 'text-blue-700');
        
        if (type === 'error') {
            statusDiv.classList.add('bg-red-100', 'text-red-700');
        } else if (type === 'success') {
            statusDiv.classList.add('bg-green-100', 'text-green-700');
        } else if (type === 'warning') {
            statusDiv.classList.add('bg-yellow-100', 'text-yellow-700');
        } else {
            statusDiv.classList.add('bg-blue-100', 'text-blue-700');
        }
        
        statusDiv.textContent = message;
    }
</script>
@endpush
