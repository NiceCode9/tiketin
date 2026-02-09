@extends('scanner.layout')

@section('title', 'Wristband Validation')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Wristband Validation</h2>

    <!-- Event Selection -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <label for="eventSelect" class="block text-sm font-medium text-gray-700 mb-2">Select Event</label>
        <select id="eventSelect" class="w-full px-4 py-3 border rounded-lg text-lg">
            <option value="">-- Select Event --</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}">{{ $event->name }} - {{ $event->event_date->format('d M Y') }}</option>
            @endforeach
        </select>
    </div>

    <!-- QR Scanner -->
    <div id="scannerSection" class="hidden">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Scan Wristband QR Code</h3>
            
            <!-- Camera Scanner -->
            <div id="qr-reader" class="mb-4"></div>
            
            <!-- Manual Input -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Or enter manually:</label>
                <div class="flex gap-2">
                    <input type="text" 
                           id="manualQR" 
                           placeholder="Paste QR code here"
                           class="flex-1 px-4 py-3 border rounded-lg text-lg">
                    <button onclick="scanManual()" 
                            class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold">
                        Scan
                    </button>
                </div>
            </div>
        </div>

        <!-- Wristband Information -->
        <div id="wristbandInfo" class="hidden rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Wristband Information</h3>
            <div id="wristbandDetails"></div>
            <button id="confirmButton" onclick="confirmEntry()" 
                    class="w-full mt-4 py-3 px-6 rounded-lg font-semibold text-lg">
                Confirm Entry
            </button>
        </div>

        <!-- Entry Result -->
        <div id="entryResult" class="hidden rounded-lg shadow-md p-6 text-center">
            <div id="resultIcon" class="text-6xl mb-4"></div>
            <h3 id="resultMessage" class="text-2xl font-bold mb-4"></h3>
            <button onclick="resetScanner()" 
                    class="bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 font-semibold">
                Scan Next Wristband
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
    let html5QrCode;
    let currentWristbandId = null;
    let selectedEventId = null;

    document.getElementById('eventSelect').addEventListener('change', function() {
        selectedEventId = this.value;
        if (selectedEventId) {
            document.getElementById('scannerSection').classList.remove('hidden');
            startScanner();
        } else {
            document.getElementById('scannerSection').classList.add('hidden');
            stopScanner();
        }
    });

    function startScanner() {
        html5QrCode = new Html5Qrcode("qr-reader");
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            onScanSuccess,
            onScanError
        ).catch(err => {
            console.error("Camera error:", err);
            showStatus('Camera not available. Please use manual input.', 'warning');
        });
    }

    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().catch(err => console.error(err));
        }
    }

    function onScanSuccess(decodedText) {
        processQR(decodedText);
    }

    function onScanError(error) {
        // Ignore scan errors
    }

    function scanManual() {
        const qrCode = document.getElementById('manualQR').value.trim();
        if (qrCode) {
            processQR(qrCode);
        }
    }

    function processQR(qrCode) {
        if (!selectedEventId) {
            showStatus('Please select an event first', 'error');
            return;
        }

        showStatus('Processing...', 'info');

        fetch('{{ route("scanner.validate.scan") }}', {
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
                displayWristbandInfo(data.wristband, data.consumer, data.can_enter, data.status);
                currentWristbandId = data.wristband.id;
                stopScanner();
            } else {
                showStatus(data.message, 'error');
            }
        })
        .catch(error => {
            showStatus('Error: ' + error.message, 'error');
        });
    }

    function displayWristbandInfo(wristband, consumer, canEnter, status) {
        const statusColor = status === 'active' ? 'text-green-600' : 'text-red-600';
        const html = `
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="font-semibold">Status:</span>
                    <span class="${statusColor} font-bold">${status.toUpperCase()}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold">Category:</span>
                    <span>${wristband.ticket.ticket_category.name}</span>
                </div>
                ${wristband.ticket.seat ? `
                <div class="flex justify-between">
                    <span class="font-semibold">Seat:</span>
                    <span>${wristband.ticket.seat.full_seat}</span>
                </div>
                ` : ''}
                <div class="flex justify-between">
                    <span class="font-semibold">Consumer:</span>
                    <span>${consumer.name}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold">Identity:</span>
                    <span>${consumer.identity}</span>
                </div>
            </div>
        `;
        document.getElementById('wristbandDetails').innerHTML = html;
        document.getElementById('wristbandInfo').classList.remove('hidden');
        document.getElementById('qr-reader').style.display = 'none';

        const confirmButton = document.getElementById('confirmButton');
        if (canEnter) {
            confirmButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
            confirmButton.classList.add('bg-green-600', 'hover:bg-green-700');
            confirmButton.disabled = false;
        } else {
            confirmButton.classList.remove('bg-green-600', 'hover:bg-green-700');
            confirmButton.classList.add('bg-gray-400', 'cursor-not-allowed');
            confirmButton.disabled = true;
            showStatus('This wristband cannot enter at this time', 'error');
        }
    }

    function confirmEntry() {
        if (!currentWristbandId) return;

        showStatus('Confirming entry...', 'info');

        fetch('{{ route("scanner.validate.confirm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                wristband_id: currentWristbandId,
                event_id: selectedEventId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showEntryResult(true, data.message);
            } else {
                showEntryResult(false, data.message);
            }
            document.getElementById('wristbandInfo').classList.add('hidden');
        })
        .catch(error => {
            showEntryResult(false, 'Error: ' + error.message);
            document.getElementById('wristbandInfo').classList.add('hidden');
        });
    }

    function showEntryResult(success, message) {
        const resultDiv = document.getElementById('entryResult');
        const iconDiv = document.getElementById('resultIcon');
        const messageDiv = document.getElementById('resultMessage');

        resultDiv.classList.remove('hidden', 'bg-green-50', 'bg-red-50');
        
        if (success) {
            resultDiv.classList.add('bg-green-50');
            iconDiv.textContent = '✓';
            iconDiv.className = 'text-6xl mb-4 text-green-600';
            messageDiv.textContent = message;
            messageDiv.className = 'text-2xl font-bold mb-4 text-green-600';
        } else {
            resultDiv.classList.add('bg-red-50');
            iconDiv.textContent = '✗';
            iconDiv.className = 'text-6xl mb-4 text-red-600';
            messageDiv.textContent = message;
            messageDiv.className = 'text-2xl font-bold mb-4 text-red-600';
        }
    }

    function resetScanner() {
        currentWristbandId = null;
        document.getElementById('manualQR').value = '';
        document.getElementById('wristbandInfo').classList.add('hidden');
        document.getElementById('entryResult').classList.add('hidden');
        document.getElementById('qr-reader').style.display = 'block';
        document.getElementById('statusMessage').classList.add('hidden');
        startScanner();
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
