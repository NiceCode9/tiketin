<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TopClientsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Top Clients by Revenue';
    
    protected static ?int $sort = 6;

    public static function canView(): bool
    {
        return Auth::user()->hasRole('super_admin');
    }

    protected function getData(): array
    {
        // Join orders -> events -> clients to calculate total revenue per client
        $data = Order::query()
            ->join('events', 'orders.event_id', '=', 'events.id')
            ->join('clients', 'events.client_id', '=', 'clients.id')
            ->where('orders.payment_status', 'paid')
            ->select('clients.name', DB::raw('SUM(orders.total_amount) as total_revenue'))
            ->groupBy('clients.name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Revenue (IDR)',
                    'data' => $data->pluck('total_revenue')->toArray(),
                    'backgroundColor' => [
                        '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'
                    ],
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
