<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DashboardStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $isClientAdmin = $user->hasRole('client_admin');
        $clientId = $user->client_id;

        // 1. Total Revenue
        $revenueQuery = Order::query()->where('payment_status', 'paid');
        
        if ($isClientAdmin) {
            $revenueQuery->whereHas('event', fn ($q) => $q->where('client_id', $clientId));
        }

        $revenue = $revenueQuery->sum($isClientAdmin ? 'subtotal' : 'total_amount');

        // 2. Tickets Sold
        $ticketQuery = Ticket::query();
        if ($isClientAdmin) {
            $ticketQuery->whereHas('order.event', fn ($q) => $q->where('client_id', $clientId));
        }
        $ticketsSold = $ticketQuery->count();

        // 3. Active Events
        $eventQuery = Event::query()->where('status', 'published');
        if ($isClientAdmin) {
            $eventQuery->where('client_id', $clientId);
        }
        $activeEvents = $eventQuery->count();

        return [
            Stat::make('Total Revenue', 'IDR ' . number_format($revenue))
                ->description('Total earnings from paid orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Dummy trend for visual

            Stat::make('Tickets Sold', number_format($ticketsSold))
                ->description('Total tickets generated')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),

            Stat::make('Active Events', $activeEvents)
                ->description('Currently published events')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}
