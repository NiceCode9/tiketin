<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Wristband;
use App\Services\TicketService;
use App\Services\WristbandService;
use App\Services\ScanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::guard('scanner')->user();
        $events = Event::where('client_id', '=', $user->client_id)
            ->where('status', '=', 'published')
            ->where('event_date', '>=', now()->subDays(1))
            ->orderBy('event_date', 'asc')
            ->get();

        $selectedEventId = request('event_id');
        $stats = null;
        if ($selectedEventId) {
            $event = Event::where('id', $selectedEventId)->where('client_id', $user->client_id)->first();
            if ($event) {
                $stats = $this->getEventStats($event);
            }
        }

        return view('scanner.exchange.index', compact('events', 'stats'));
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
            // Sanitize event access
            $event = Event::where('id', '=', $request->event_id)
                ->where('client_id', '=', Auth::user()->client_id)
                ->firstOrFail();

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
            if ($ticket->order->event_id != $event->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket ini bukan untuk event yang dipilih.'
                ], 400);
            }

            // Check if wristband already exists
            if ($ticket->wristband) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket ini sudah ditukarkan dengan wristband.',
                    'wristband' => $ticket->wristband
                ], 400);
            }

            // Check if ticket can be exchanged
            if (! $this->ticketService->canExchangeForWristband($ticket)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket ini belum bisa ditukarkan saat ini. Jendela penukaran mungkin belum dibuka.'
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
                'message' => 'Error saat memproses tiket: ' . $e->getMessage()
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
            'wristband_code' => 'required|string|min:3',
        ]);

        try {
            $ticket = Ticket::findOrFail($request->ticket_id);
            
            // Sanitize event access
            $event = Event::where('id', '=', $request->event_id)
                ->where('client_id', '=', Auth::guard('scanner')->user()->client_id)
                ->firstOrFail();

            // Exchange ticket for wristband with external code
            $wristband = $this->wristbandService->exchangeTicketForWristband($ticket, Auth::guard('scanner')->user(), $request->wristband_code);

            return response()->json([
                'success' => true,
                'message' => 'Gelang berhasil diaktivasi!',
                'wristband' => $wristband,
                'qr_data' => $request->wristband_code
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
        $user = Auth::guard('scanner')->user();
        
        $query = Wristband::whereHas('ticket.order.event', function($q) use ($user) {
            $q->where('client_id', '=', $user->client_id);
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
        $user = Auth::guard('scanner')->user();
        $events = Event::where('client_id', '=', $user->client_id)
            ->where('status', '=', 'published')
            ->where('event_date', '>=', now()->subDays(1))
            ->orderBy('event_date', 'asc')
            ->get();

        $selectedEventId = request('event_id');
        $stats = null;
        if ($selectedEventId) {
            $event = Event::where('id', $selectedEventId)->where('client_id', $user->client_id)->first();
            if ($event) {
                $stats = $this->getEventStats($event);
            }
        }

        return view('scanner.validate.index', compact('events', 'stats'));
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
            // Sanitize event access
            $event = Event::where('id', '=', $request->event_id)
                ->where('client_id', '=', Auth::guard('scanner')->user()->client_id)
                ->firstOrFail();

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
            if ($wristband->ticket->order->event_id != $event->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wristband ini bukan untuk event yang dipilih.'
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
                'message' => 'Error saat memproses wristband: ' . $e->getMessage()
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
            
            // Sanitize event access
            $event = Event::where('id', '=', $request->event_id)
                ->where('client_id', '=', Auth::guard('scanner')->user()->client_id)
                ->firstOrFail();

            // Validate entry using raw code
            $result = $this->wristbandService->validateWristbandEntry($wristband->uuid, Auth::guard('scanner')->user());

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Masuk dikonfirmasi! Selamat datang di event.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Wristband tidak valid untuk masuk.'
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
        $user = Auth::guard('scanner')->user();
        
        $query = Wristband::whereHas('ticket.order.event', function($q) use ($user) {
            $q->where('client_id', '=', $user->client_id);
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

    /**
     * Get statistics for an event
     */
    protected function getEventStats(Event $event): array
    {
        $totalTickets = Ticket::whereHas('order', function($q) use ($event) {
            $q->where('event_id', $event->id)->where('payment_status', 'success');
        })->count();

        $totalExchanged = Ticket::whereHas('order', function($q) use ($event) {
            $q->where('event_id', $event->id);
        })->where('status', 'exchanged')->count();

        $totalValidated = Wristband::whereHas('ticket.order', function($q) use ($event) {
            $q->where('event_id', $event->id);
        })->whereNotNull('validated_at')->count();

        return [
            'total_tickets' => $totalTickets,
            'total_exchanged' => $totalExchanged,
            'total_validated' => $totalValidated,
            'exchange_percentage' => $totalTickets > 0 ? round(($totalExchanged / $totalTickets) * 100) : 0,
            'validation_percentage' => $totalExchanged > 0 ? round(($totalValidated / $totalExchanged) * 100) : 0,
        ];
    }
}
