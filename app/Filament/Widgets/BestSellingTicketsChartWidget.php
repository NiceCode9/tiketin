<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BestSellingTicketsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Best Selling Ticket Categories';
    
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $user = Auth::user();
        $isClientAdmin = $user->hasRole('client_admin');
        $clientId = $user->client_id;

        $query = Ticket::query()
            ->join('ticket_categories', 'tickets.ticket_category_id', '=', 'ticket_categories.id')
            ->join('orders', 'tickets.order_id', '=', 'orders.id') // Ensure it's sold (part of an order)
            ->where('orders.payment_status', 'paid');

        if ($isClientAdmin) {
            $query->whereHas('order.event', fn ($q) => $q->where('client_id', $clientId));
        }

        // Aggregate by Category Name
        $data = $query
            ->select('ticket_categories.name', DB::raw('count(*) as total'))
            ->groupBy('ticket_categories.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Tickets Sold',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'
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
