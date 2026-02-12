<?php

namespace App\Filament\Resources\EventResource\Widgets;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class EventStatsOverview extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        // Ensure record is populated (it should be injected by ViewRecord page)
        if (!$this->record || !($this->record instanceof Event)) {
            return [];
        }

        $event = $this->record;

        // 1. Total Revenue (Paid Orders)
        $revenue = Order::where('event_id', $event->id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // 2. Tickets Sold
        $soldCount = Ticket::whereHas('order', function ($query) use ($event) {
            $query->where('event_id', $event->id)
                  ->whereIn('payment_status', ['paid']);
        })->count();
        
        $totalQuota = $event->ticketCategories()->sum('quota');
        
        // 3. Check-ins (ScanLogs of type 'entry' and status 'success')
        $checkIns = $event->scanLogs()
            ->where('scan_type', 'entry')
            ->where('status', 'success')
            ->count();

        return [
            Stat::make('Total Revenue', 'IDR ' . number_format($revenue))
                ->description('From paid orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Tickets Sold', "$soldCount / $totalQuota")
                ->description('Occupancy rate: ' . ($totalQuota > 0 ? round(($soldCount / $totalQuota) * 100, 1) : 0) . '%')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary')
                ->chart([$soldCount, $totalQuota]), // Simple visual

            Stat::make('Check-ins', "$checkIns")
                ->description('Total successful entries')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
}
