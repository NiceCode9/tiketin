<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Promo Code Information')
                    ->schema([
                        Forms\Components\Select::make('event_id')
                            ->label('Event')
                            ->relationship('event', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->uppercase()
                            ->placeholder('e.g., EARLYBIRD2024'),
                        
                        Forms\Components\Select::make('discount_type')
                            ->options([
                                'fixed' => 'Fixed Amount',
                                'percentage' => 'Percentage',
                            ])
                            ->required()
                            ->live(),
                        
                        Forms\Components\TextInput::make('discount_value')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix(fn (Forms\Get $get) => $get('discount_type') === 'fixed' ? 'Rp' : null)
                            ->suffix(fn (Forms\Get $get) => $get('discount_type') === 'percentage' ? '%' : null),
                        
                        Forms\Components\TextInput::make('quota')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Maximum number of times this code can be used'),
                        
                        Forms\Components\TextInput::make('used_count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('Automatically updated when code is used'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Restrictions')
                    ->schema([
                        Forms\Components\TextInput::make('min_purchase_amount')
                            ->numeric()
                            ->prefix('Rp')
                            ->helperText('Minimum purchase amount required (optional)'),
                        
                        Forms\Components\TextInput::make('max_discount_amount')
                            ->numeric()
                            ->prefix('Rp')
                            ->helperText('Maximum discount cap for percentage discounts (optional)')
                            ->visible(fn (Forms\Get $get) => $get('discount_type') === 'percentage'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Validity Period')
                    ->schema([
                        Forms\Components\DateTimePicker::make('valid_from')
                            ->required()
                            ->native(false),
                        
                        Forms\Components\DateTimePicker::make('valid_until')
                            ->required()
                            ->native(false)
                            ->after('valid_from'),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'expired' => 'Expired',
                            ])
                            ->required()
                            ->default('active'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('discount_type')
                    ->badge()
                    ->colors([
                        'primary' => 'fixed',
                        'success' => 'percentage',
                    ]),
                
                Tables\Columns\TextColumn::make('discount_value')
                    ->label('Discount')
                    ->formatStateUsing(fn ($record) => 
                        $record->discount_type === 'fixed' 
                            ? 'Rp ' . number_format($record->discount_value, 0, ',', '.')
                            : $record->discount_value . '%'
                    ),
                
                Tables\Columns\TextColumn::make('quota')
                    ->numeric(),
                
                Tables\Columns\TextColumn::make('used_count')
                    ->numeric()
                    ->badge()
                    ->color(fn ($record) => $record->used_count >= $record->quota ? 'danger' : 'success'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'expired',
                    ]),
                
                Tables\Columns\TextColumn::make('valid_until')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'expired' => 'Expired',
                    ]),
                
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
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
