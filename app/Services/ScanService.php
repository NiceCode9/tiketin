<?php

namespace App\Services;

use App\Models\ScanLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ScanService
{
    /**
     * Log a scan attempt
     */
    public function logScan(
        User $scanner,
        Model $scannable,
        string $scanType,
        bool $success,
        ?string $errorMessage = null
    ): ScanLog {
        // Determine event from scannable
        $event = $scannable instanceof \App\Models\Ticket
            ? $scannable->order->event
            : $scannable->ticket->order->event;

        return ScanLog::create([
            'scanned_by' => $scanner->id,
            'event_id' => $event->id,
            'scannable_type' => get_class($scannable),
            'scannable_id' => $scannable->id,
            'scan_type' => $scanType,
            'status' => $success ? 'success' : 'failed',
            'error_message' => $errorMessage,
            'scanned_at' => now(),
        ]);
    }

    /**
     * Validate scan permission
     */
    public function validateScanPermission(User $scanner, \App\Models\Event $event): bool
    {
        // Super admin can scan anything
        if ($scanner->hasRole('super_admin')) {
            return true;
        }

        // Client can scan their own events
        if ($scanner->hasRole('client')) {
            return $scanner->client_id === $event->client_id;
        }

        // Scanner roles need to be assigned to the event
        // This would require an event_user pivot table for scanner assignments
        // For now, we'll allow all scanner role users
        if ($scanner->hasRole(['wristband_exchange_officer', 'wristband_validator'])) {
            return true;
        }

        return false;
    }

    /**
     * Get scan statistics for an event
     */
    public function getEventScanStats(\App\Models\Event $event): array
    {
        $totalScans = ScanLog::where('event_id', $event->id)->count();
        $successfulScans = ScanLog::where('event_id', $event->id)
            ->where('status', 'success')
            ->count();
        $failedScans = ScanLog::where('event_id', $event->id)
            ->where('status', 'failed')
            ->count();
        $duplicateScans = ScanLog::where('event_id', $event->id)
            ->where('status', 'duplicate')
            ->count();

        $exchangeScans = ScanLog::where('event_id', $event->id)
            ->where('scan_type', 'exchange')
            ->where('status', 'success')
            ->count();

        $validationScans = ScanLog::where('event_id', $event->id)
            ->where('scan_type', 'validation')
            ->where('status', 'success')
            ->count();

        return [
            'total_scans' => $totalScans,
            'successful_scans' => $successfulScans,
            'failed_scans' => $failedScans,
            'duplicate_scans' => $duplicateScans,
            'wristband_exchanges' => $exchangeScans,
            'entry_validations' => $validationScans,
        ];
    }
}
