<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WristbandResource\Pages;
use App\Models\Wristband;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WristbandResource extends Resource
{
    protected static ?string $model = Wristband::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Wristband Information')
                    ->schema([
                        Forms\Components\TextInput::make('uuid')
                            ->label('Wristband Code (QR/Barcode)')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'validated' => 'Validated (Entered)',
                                'revoked' => 'Revoked (Cancelled)',
                            ])
                            ->required()
                            ->native(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid')
                    ->label('Wristband Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('ticket.order.event.name')
                    ->label('Event')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ticket.consumer_name')
                    ->label('Consumer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'info' => 'validated',
                        'danger' => 'revoked',
                    ]),

                Tables\Columns\TextColumn::make('exchanged_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('validated_at')
                    ->label('Entered At')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not Entered Yet')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'validated' => 'Validated',
                        'revoked' => 'Revoked',
                    ]),
                Tables\Filters\SelectFilter::make('event')
                    ->relationship('ticket.order.event', 'name')
                    ->label('Event'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('exchanged_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Wristband Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('uuid')
                            ->label('Physical Code')
                            ->copyable()
                            ->fontFamily('mono'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'validated' => 'info',
                                'revoked' => 'danger',
                            }),
                    ])->columns(2),

                Infolists\Components\Section::make('Assigned Ticket')
                    ->schema([
                        Infolists\Components\TextEntry::make('ticket.order.order_number')
                            ->label('Order Number'),
                        Infolists\Components\TextEntry::make('ticket.consumer_name')
                            ->label('Consumer Name'),
                        Infolists\Components\TextEntry::make('ticket.ticketCategory.name')
                            ->label('Ticket Category'),
                    ])->columns(3),

                Infolists\Components\Section::make('Activity Logs')
                    ->schema([
                        Infolists\Components\TextEntry::make('exchanged_at')
                            ->dateTime()
                            ->label('Issued At'),
                        Infolists\Components\TextEntry::make('exchangedBy.name')
                            ->label('Issued By'),
                        Infolists\Components\TextEntry::make('validated_at')
                            ->dateTime()
                            ->label('Entered At'),
                        Infolists\Components\TextEntry::make('validatedBy.name')
                            ->label('Validated By'),
                    ])->columns(2),
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
            'index' => Pages\ListWristbands::route('/'),
            'create' => Pages\CreateWristband::route('/create'),
            'view' => Pages\ViewWristband::route('/{record}'),
            'edit' => Pages\EditWristband::route('/{record}/edit'),
        ];
    }
}
