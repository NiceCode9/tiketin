<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        // Orders are read-only in admin panel
        return $form
            ->schema([
                Forms\Components\Placeholder::make('order_number')
                    ->content(fn ($record) => $record->order_number),
                
                Forms\Components\Placeholder::make('consumer_name')
                    ->content(fn ($record) => $record->consumer_name),
                
                Forms\Components\Placeholder::make('total_amount')
                    ->content(fn ($record) => 'Rp ' . number_format($record->total_amount, 0, ',', '.')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('event.name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('consumer_name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('consumer_email')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('consumer_identity_number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'success',
                        'danger' => ['failed', 'expired'],
                    ]),
                
                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'expired' => 'Expired',
                    ]),
                
                Tables\Filters\SelectFilter::make('event')
                    ->relationship('event', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for orders
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Order Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('order_number'),
                        Infolists\Components\TextEntry::make('event.name'),
                        Infolists\Components\TextEntry::make('payment_status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'success' => 'success',
                                'failed', 'expired' => 'danger',
                            }),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                    ])
                    ->columns(2),
                
                Infolists\Components\Section::make('Consumer Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('consumer_name'),
                        Infolists\Components\TextEntry::make('consumer_email'),
                        Infolists\Components\TextEntry::make('consumer_whatsapp'),
                        Infolists\Components\TextEntry::make('consumer_identity_type'),
                        Infolists\Components\TextEntry::make('consumer_identity_number'),
                    ])
                    ->columns(2),
                
                Infolists\Components\Section::make('Payment Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('subtotal')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('discount_amount')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('total_amount')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('payment_method'),
                        Infolists\Components\TextEntry::make('paid_at')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Orders are created by consumers, not admin
    }
}
