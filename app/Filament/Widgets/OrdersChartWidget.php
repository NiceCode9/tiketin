<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class OrdersChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Orders (Last 30 Days)';

    protected static ?int $sort = 2; // Position it next to Revenue

    protected function getData(): array
    {
        $user = Auth::user();
        $isClientAdmin = $user->hasRole('client_admin');
        $clientId = $user->client_id;

        $query = Order::query();
        
        if ($isClientAdmin) {
            $query->whereHas('event', fn ($q) => $q->where('client_id', $clientId));
        }

        $data = Trend::query($query)
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#8b5cf6', // Violet
                    'borderColor' => '#a78bfa',
                    'fill' => 'start',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
