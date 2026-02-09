@extends('scanner.layout')

@section('title', 'Wristband Exchange')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Wristband Exchange</h2>

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
            <h3 class="text-lg font-semibold mb-4">Scan Ticket QR Code</h3>
            
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

        <!-- Ticket Information -->
        <div id="ticketInfo" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Ticket Information</h3>
            <div id="ticketDetails"></div>
            <button onclick="issueWristband()" 
                    class="w-full mt-4 bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 font-semibold text-lg">
                Issue Wristband
            </button>
        </div>

        <!-- Wristband QR Code -->
        <div id="wristbandQR" class="hidden bg-white rounded-lg shadow-md p-6 text-center">
            <h3 class="text-lg font-semibold mb-4 text-green-600">✓ Wristband Issued Successfully!</h3>
            <div id="wristbandDetails" class="mb-4"></div>
            <button onclick="resetScanner()" 
                    class="bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 font-semibold">
                Scan Next Ticket
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
    let currentTicketId = null;
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
        // Ignore scan errors (happens frequently)
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
                displayTicketInfo(data.ticket, data.consumer);
                currentTicketId = data.ticket.id;
                stopScanner();
            } else {
                showStatus(data.message, 'error');
            }
        })
        .catch(error => {
            showStatus('Error: ' + error.message, 'error');
        });
    }

    function displayTicketInfo(ticket, consumer) {
        const html = `
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="font-semibold">Category:</span>
                    <span>${ticket.ticket_category.name}</span>
                </div>
                ${ticket.seat ? `
                <div class="flex justify-between">
                    <span class="font-semibold">Seat:</span>
                    <span>${ticket.seat.full_seat}</span>
                </div>
                ` : ''}
                <div class="flex justify-between">
                    <span class="font-semibold">Consumer:</span>
                    <span>${consumer.name}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold">Email:</span>
                    <span>${consumer.email}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold">Identity:</span>
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

        showStatus('Issuing wristband...', 'info');

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
                displayWristband(data.wristband, data.qr_data);
                document.getElementById('ticketInfo').classList.add('hidden');
                showStatus(data.message, 'success');
            } else {
                showStatus(data.message, 'error');
            }
        })
        .catch(error => {
            showStatus('Error: ' + error.message, 'error');
        });
    }

    function displayWristband(wristband, qrData) {
        const html = `
            <div class="text-lg font-semibold mb-2">Wristband ID: ${wristband.id}</div>
            <div class="text-sm text-gray-600 mb-4">QR Code: ${qrData}</div>
            <div class="bg-gray-100 p-4 rounded">
                <p class="text-sm">Show this to the attendee or print the wristband</p>
            </div>
        `;
        document.getElementById('wristbandDetails').innerHTML = html;
        document.getElementById('wristbandQR').classList.remove('hidden');
    }

    function resetScanner() {
        currentTicketId = null;
        document.getElementById('manualQR').value = '';
        document.getElementById('ticketInfo').classList.add('hidden');
        document.getElementById('wristbandQR').classList.add('hidden');
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
