<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentStatusChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Order Payment Status';
    
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $user = Auth::user();
        $isClientAdmin = $user->hasRole('client_admin');
        $clientId = $user->client_id;

        $query = Order::query();

        if ($isClientAdmin) {
            $query->whereHas('event', fn ($q) => $q->where('client_id', $clientId));
        }

        $data = $query
            ->select('payment_status', DB::raw('count(*) as count'))
            ->groupBy('payment_status')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => [
                        '#22c55e', // Paid - Green
                        '#eab308', // Pending - Yellow
                        '#ef4444', // Unpaid/Failed - Red
                        '#64748b', // Other
                    ],
                ],
            ],
            'labels' => $data->pluck('payment_status')->map(fn($s) => ucfirst($s))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
