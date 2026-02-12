<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue (Last 30 Days)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $user = Auth::user();
        $isClientAdmin = $user->hasRole('client_admin');
        $clientId = $user->client_id;

        $query = Order::query()->where('payment_status', 'paid');
        
        if ($isClientAdmin) {
            $query->whereHas('event', fn ($q) => $q->where('client_id', $clientId));
        }

        // Manual aggregation because flowframe/laravel-trend requires PHP 8.3
        $data = $query
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as aggregate')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing dates
        $chartData = [];
        $labels = [];
        $startDate = now()->subDays(30);
        
        for ($i = 0; $i <= 30; $i++) {
            $date = $startDate->copy()->addDays($i)->format('Y-m-d');
            $labels[] = $date;
            $record = $data->firstWhere('date', $date);
            $chartData[] = $record ? $record->aggregate : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $chartData,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                    'fill' => 'start',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
