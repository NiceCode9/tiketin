<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\EventResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RecentOrdersTableWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Orders';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->latest()
                    ->when(
                        Auth::user()->hasRole('client_admin'),
                        fn ($q) => $q->whereHas('event', fn ($sq) => $sq->where('client_id', Auth::user()->client_id))
                    )
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_token')
                    ->label('Order ID')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('personal_detail.name')
                    ->label('Customer'),
                Tables\Columns\TextColumn::make('event.name')
                    ->label('Event'),
                Tables\Columns\TextColumn::make(Auth::user()->hasRole('client_admin') ? 'subtotal' : 'total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->colors([
                        'danger' => ['expired', 'canceled', 'failed'],
                        'warning' => 'pending',
                        'success' => 'paid',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('View')
                    ->url(fn (Order $record): string => EventResource::getUrl('view', ['record' => $record->event_id])) // Ideally link to Order resource if exists
                    ->icon('heroicon-m-eye'),
            ]);
    }
}
