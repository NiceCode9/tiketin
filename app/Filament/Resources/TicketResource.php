<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ticket Status Management')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending_payment' => 'Pending Payment',
                                'paid' => 'Paid',
                                'exchanged' => 'Exchanged / Wristband Issued',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    ->visible(fn ($livewire) => $livewire instanceof Pages\EditTicket),

                Forms\Components\Section::make('Consumer Details')
                    ->schema([
                        Forms\Components\TextInput::make('consumer_name')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('consumer_identity_number')
                            ->label('ID Number')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->visible(fn ($livewire) => $livewire instanceof Pages\EditTicket),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid')
                    ->label('Ticket ID / UUID')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->size('xs'),

                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => OrderResource::getUrl('view', ['record' => $record->order_id])),

                Tables\Columns\TextColumn::make('order.event.name')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ticketCategory.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('consumer_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending_payment',
                        'success' => 'paid',
                        'info' => 'exchanged',
                        'danger' => 'cancelled',
                    ]),

                Tables\Columns\TextColumn::make('seat.name')
                    ->label('Seat')
                    ->placeholder('N/A')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending_payment' => 'Pending Payment',
                        'paid' => 'Paid',
                        'exchanged' => 'Exchanged',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('event')
                    ->relationship('order.event', 'name')
                    ->label('Event'),

                Tables\Filters\SelectFilter::make('ticketCategory')
                    ->relationship('ticketCategory', 'name')
                    ->label('Ticket Category'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->label('Change Status')
                    ->icon('heroicon-o-pencil-square'),
            ])
            ->bulkActions([
                // No bulk actions for tickets
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Ticket Status')
                    ->schema([
                        Infolists\Components\TextEntry::make('uuid')
                            ->label('UUID / Data QR')
                            ->copyable()
                            ->fontFamily('mono'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending_payment' => 'warning',
                                'paid' => 'success',
                                'exchanged' => 'info',
                                'cancelled' => 'danger',
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Order & Event')
                    ->schema([
                        Infolists\Components\TextEntry::make('order.order_number')
                            ->label('Order Number'),
                        Infolists\Components\TextEntry::make('order.event.name')
                            ->label('Event Name'),
                        Infolists\Components\TextEntry::make('ticketCategory.name')
                            ->label('Category'),
                        Infolists\Components\TextEntry::make('seat.name')
                            ->label('Seat / Number')
                            ->placeholder('No Assigned Seating'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Consumer Identity')
                    ->schema([
                        Infolists\Components\TextEntry::make('consumer_name'),
                        Infolists\Components\TextEntry::make('consumer_identity_type')
                            ->label('ID Type'),
                        Infolists\Components\TextEntry::make('consumer_identity_number')
                            ->label('ID Number'),
                    ])
                    ->columns(3),
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
            'index' => Pages\ListTickets::route('/'),
            'view' => Pages\ViewTicket::route('/{record}'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Tickets are generated by service on payment
    }
}
