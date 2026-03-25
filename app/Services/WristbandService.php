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
     * Parse and validate QR code string (format: uuid|checksum)
     */
    public function validateQR(string $qrCode): array
    {
        try {
            $parts = explode('|', $qrCode);
            if (count($parts) !== 2) {
                return [
                    'valid' => false,
                    'message' => 'Invalid QR code format. Expected uuid|checksum.'
                ];
            }

            [$uuid, $checksum] = $parts;
            $wristband = $this->validateWristbandQR($uuid, $checksum);

            return [
                'valid' => true,
                'wristband' => $wristband,
                'message' => 'Wristband validated successfully'
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => $e->getMessage()
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
