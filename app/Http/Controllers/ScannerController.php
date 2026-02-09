<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Wristband;
use App\Services\TicketService;
use App\Services\WristbandService;
use App\Services\ScanService;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function __construct(
        protected TicketService $ticketService,
        protected WristbandService $wristbandService,
        protected ScanService $scanService
    ) {}

    /**
     * Wristband Exchange Dashboard
     */
    public function exchangeIndex()
    {
        $user = auth()->user();
        $events = Event::where('client_id', $user->client_id)
            ->where('status', 'published')
            ->where('event_date', '>=', now()->subDays(1))
            ->orderBy('event_date', 'asc')
            ->get();

        return view('scanner.exchange.index', compact('events'));
    }

    /**
     * Scan ticket QR code
     */
    public function scanTicket(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'event_id' => 'required|exists:events,id',
        ]);

        try {
            // Parse QR code
            $qrData = $this->ticketService->validateQR($request->qr_code);

            if (!$qrData['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $qrData['message']
                ], 400);
            }

            $ticket = $qrData['ticket'];

            // Check if ticket belongs to the event
            if ($ticket->order->event_id != $request->event_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This ticket does not belong to the selected event.'
                ], 400);
            }

            // Check if wristband already exists
            if ($ticket->wristband) {
                return response()->json([
                    'success' => false,
                    'message' => 'This ticket has already been exchanged for a wristband.',
                    'wristband' => $ticket->wristband
                ], 400);
            }

            // Check if ticket can be exchanged
            if (!$ticket->canExchangeForWristband()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This ticket cannot be exchanged at this time. Exchange window may not be open yet.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'ticket' => $ticket->load(['ticketCategory', 'seat', 'order']),
                'consumer' => [
                    'name' => $ticket->consumer_name,
                    'email' => $ticket->consumer_email,
                    'identity' => $ticket->consumer_identity_type . ': ' . $ticket->consumer_identity_number,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error scanning ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Issue wristband
     */
    public function issueWristband(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'event_id' => 'required|exists:events,id',
        ]);

        try {
            $ticket = Ticket::findOrFail($request->ticket_id);
            $event = Event::findOrFail($request->event_id);

            // Exchange ticket for wristband
            $wristband = $this->wristbandService->exchangeTicket($ticket, auth()->user());

            // Log the scan
            $this->scanService->logScan(
                $wristband,
                auth()->user(),
                $event,
                'exchange',
                'success'
            );

            return response()->json([
                'success' => true,
                'message' => 'Wristband issued successfully!',
                'wristband' => $wristband,
                'qr_data' => $this->wristbandService->getQRData($wristband)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error issuing wristband: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exchange history
     */
    public function exchangeHistory(Request $request)
    {
        $user = auth()->user();
        
        $query = Wristband::whereHas('ticket.order.event', function($q) use ($user) {
            $q->where('client_id', $user->client_id);
        })->with(['ticket.ticketCategory', 'ticket.order.event', 'exchangedBy']);

        if ($request->event_id) {
            $query->whereHas('ticket.order', function($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }

        $wristbands = $query->latest()->paginate(50);

        return view('scanner.exchange.history', compact('wristbands'));
    }

    /**
     * Wristband Validation Dashboard
     */
    public function validateIndex()
    {
        $user = auth()->user();
        $events = Event::where('client_id', $user->client_id)
            ->where('status', 'published')
            ->where('event_date', '>=', now()->subDays(1))
            ->orderBy('event_date', 'asc')
            ->get();

        return view('scanner.validate.index', compact('events'));
    }

    /**
     * Scan wristband QR code
     */
    public function scanWristband(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'event_id' => 'required|exists:events,id',
        ]);

        try {
            // Parse QR code
            $qrData = $this->wristbandService->validateQR($request->qr_code);

            if (!$qrData['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $qrData['message']
                ], 400);
            }

            $wristband = $qrData['wristband'];

            // Check if wristband belongs to the event
            if ($wristband->ticket->order->event_id != $request->event_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This wristband does not belong to the selected event.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'wristband' => $wristband->load(['ticket.ticketCategory', 'ticket.seat']),
                'consumer' => [
                    'name' => $wristband->ticket->consumer_name,
                    'identity' => $wristband->ticket->consumer_identity_type . ': ' . $wristband->ticket->consumer_identity_number,
                ],
                'can_enter' => $wristband->canEnter(),
                'status' => $wristband->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error scanning wristband: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm entry
     */
    public function confirmEntry(Request $request)
    {
        $request->validate([
            'wristband_id' => 'required|exists:wristbands,id',
            'event_id' => 'required|exists:events,id',
        ]);

        try {
            $wristband = Wristband::findOrFail($request->wristband_id);
            $event = Event::findOrFail($request->event_id);

            // Validate entry
            $result = $this->wristbandService->validateEntry($wristband, auth()->user());

            // Log the scan
            $this->scanService->logScan(
                $wristband,
                auth()->user(),
                $event,
                'entry',
                $result ? 'success' : 'failed',
                $result ? null : 'Entry validation failed'
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Entry confirmed! Welcome to the event.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Entry denied. Wristband is not valid for entry.'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error confirming entry: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validation history
     */
    public function validateHistory(Request $request)
    {
        $user = auth()->user();
        
        $query = Wristband::whereHas('ticket.order.event', function($q) use ($user) {
            $q->where('client_id', $user->client_id);
        })
        ->whereNotNull('validated_at')
        ->with(['ticket.ticketCategory', 'ticket.order.event', 'validatedBy']);

        if ($request->event_id) {
            $query->whereHas('ticket.order', function($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }

        $wristbands = $query->latest('validated_at')->paginate(50);

        return view('scanner.validate.history', compact('wristbands'));
    }
}
