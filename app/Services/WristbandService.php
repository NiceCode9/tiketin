<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Wristband;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WristbandService
{
    /**
     * Exchange ticket for wristband
     */
    public function exchangeTicketForWristband(Ticket $ticket, User $officer, string $wristbandCode): Wristband
    {
        $ticketService = app(TicketService::class);

        if (!$ticketService->canExchangeForWristband($ticket)) {
            throw new \Exception('Tiket tidak dapat ditukarkan saat ini.');
        }

        // Validate wristband code uniqueness for the event
        $eventId = $ticket->order->event_id;
        $exists = Wristband::whereHas('ticket.order', function($q) use ($eventId) {
            $q->where('event_id', $eventId);
        })->where('uuid', $wristbandCode)->exists();

        if ($exists) {
            throw new \Exception('Gelang dengan kode ini sudah terdaftar untuk event ini. Gunakan gelang lain.');
        }

        return DB::transaction(function () use ($ticket, $officer, $wristbandCode) {
            // Create wristband
            $wristband = Wristband::create([
                'ticket_id' => $ticket->id,
                'uuid' => $wristbandCode, // Store the physical QR code
                'status' => 'active',
                'exchanged_at' => now(),
                'exchanged_by' => $officer->id,
            ]);

            // Update ticket status
            $ticket->update(['status' => 'exchanged']);
            
            // Note: Scan is logged in the controller or should be here?
            // Existing service code has it here, but controller also has it.
            // Let's keep it consistent: Service handles internal logging.
            $scanService = app(ScanService::class);
            $scanService->logScan(
                $officer,
                $wristband,
                'exchange',
                true
            );

            return $wristband;
        });
    }

    /**
     * Validate wristband for entry
     */
    public function validateWristbandEntry(string $uuid, User $validator): bool
    {
        $wristband = Wristband::where('uuid', $uuid)->firstOrFail();

        if (!$wristband->isActive()) {
            throw new \Exception('Wristband is not active');
        }

        if ($wristband->isRevoked()) {
            throw new \Exception('Wristband has been revoked');
        }

        if ($wristband->isValidated()) {
            throw new \Exception('Wristband has already been used for entry');
        }

        DB::transaction(function () use ($wristband, $validator) {
            // Mark as validated
            $wristband->update([
                'status' => 'validated',
                'validated_at' => now(),
                'validated_by' => $validator->id,
            ]);

            // Log the validation
            $scanService = app(ScanService::class);
            $scanService->logScan(
                $validator,
                $wristband,
                'validation',
                true
            );
        });

        return true;
    }

    /**
     * Reissue wristband (for lost/damaged wristbands)
     */
    public function reissueWristband(Wristband $oldWristband, User $officer, string $newWristbandCode): Wristband
    {
        return DB::transaction(function () use ($oldWristband, $officer, $newWristbandCode) {
            // Revoke old wristband
            $oldWristband->update(['status' => 'revoked']);

            // Create new wristband with the new physical code
            $newWristband = Wristband::create([
                'ticket_id' => $oldWristband->ticket_id,
                'uuid' => $newWristbandCode,
                'status' => 'active',
                'exchanged_at' => now(),
                'exchanged_by' => $officer->id,
            ]);

            return $newWristband;
        });
    }

    /**
     * Validate wristband QR code (Simple lookup for external codes)
     */
    public function validateWristbandQR(string $qrCode): Wristband
    {
        // For external codes, we just search by the raw string
        return Wristband::where('uuid', $qrCode)->firstOrFail();
    }

    /**
     * Parse and validate QR code string (External QR version)
     */
    public function validateQR(string $qrCode): array
    {
        try {
            // Simplify: The whole string is the code
            $wristband = $this->validateWristbandQR($qrCode);

            return [
                'valid' => true,
                'wristband' => $wristband,
                'message' => 'Wristband validated successfully'
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => 'Gelang tidak terdaftar atau tidak valid: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get wristband QR code data as string
     */
    public function getQRCodeData(Wristband $wristband): string
    {
        return $wristband->uuid . '|' . $wristband->checksum;
    }
}
