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
    public function exchangeTicketForWristband(Ticket $ticket, User $officer): Wristband
    {
        $ticketService = app(TicketService::class);

        if (!$ticketService->canExchangeForWristband($ticket)) {
            throw new \Exception('Ticket cannot be exchanged for wristband');
        }

        return DB::transaction(function () use ($ticket, $officer) {
            // Create wristband
            $wristband = Wristband::create([
                'ticket_id' => $ticket->id,
                'status' => 'active',
                'exchanged_at' => now(),
                'exchanged_by' => $officer->id,
            ]);

            // Update ticket status
            $ticket->update(['status' => 'exchanged']);

            // Log the exchange
            $scanService = app(ScanService::class);
            $scanService->logScan(
                $officer,
                $ticket,
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
    public function reissueWristband(Wristband $oldWristband, User $officer): Wristband
    {
        return DB::transaction(function () use ($oldWristband, $officer) {
            // Revoke old wristband
            $oldWristband->update(['status' => 'revoked']);

            // Create new wristband
            $newWristband = Wristband::create([
                'ticket_id' => $oldWristband->ticket_id,
                'status' => 'active',
                'exchanged_at' => now(),
                'exchanged_by' => $officer->id,
            ]);

            return $newWristband;
        });
    }

    /**
     * Validate wristband QR code
     */
    public function validateWristbandQR(string $uuid, string $checksum): Wristband
    {
        $wristband = Wristband::where('uuid', $uuid)->firstOrFail();

        if (!$wristband->verifyChecksum($checksum)) {
            throw new \Exception('Invalid QR code checksum');
        }

        return $wristband;
    }

    /**
     * Get wristband QR code data
     */
    public function getQRCodeData(Wristband $wristband): array
    {
        return [
            'type' => 'wristband',
            'id' => $wristband->uuid,
            'ticket_id' => $wristband->ticket->uuid,
            'checksum' => $wristband->checksum,
        ];
    }
}
