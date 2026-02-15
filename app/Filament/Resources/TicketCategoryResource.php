<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketCategoryResource\Pages;
use App\Models\TicketCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TicketCategoryResource extends Resource
{
    protected static ?string $model = TicketCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Events';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Category Information')
                    ->schema([
                        Forms\Components\Select::make('event_id')
                            ->label('Event')
                            ->relationship('event', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., VIP, Regular, Festival'),
                        
                        Forms\Components\TextInput::make('price')
                            ->label('Base Price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0),

                        Forms\Components\TextInput::make('biaya_layanan')
                            ->label('Service Fee')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\TextInput::make('biaya_admin_payment')
                            ->label('Payment Admin Fee')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->minValue(0),
                        
                        Forms\Components\TextInput::make('quota')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Total number of tickets available'),
                        
                        Forms\Components\TextInput::make('sold_count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('Automatically updated when tickets are sold'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Seating Configuration')
                    ->schema([
                        Forms\Components\Toggle::make('is_seated')
                            ->label('Assigned Seating')
                            ->default(false)
                            ->live(),
                        
                        Forms\Components\Select::make('venue_section_id')
                            ->label('Venue Section')
                            ->relationship('venueSection', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => $get('is_seated')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Base Price')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('biaya_layanan')
                    ->label('Service Fee')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('biaya_admin_payment')
                    ->label('Admin Fee')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('quota')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sold_count')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('available_count')
                    ->label('Available')
                    ->getStateUsing(fn ($record) => $record->available_count)
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
                
                Tables\Columns\IconColumn::make('is_seated')
                    ->boolean()
                    ->label('Seated'),
                
                Tables\Columns\TextColumn::make('venueSection.name')
                    ->label('Section')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->relationship('event', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTicketCategories::route('/'),
            'create' => Pages\CreateTicketCategory::route('/create'),
            'edit' => Pages\EditTicketCategory::route('/{record}/edit'),
        ];
    }
}
