<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PromoCodesRelationManager extends RelationManager
{
    protected static string $relationship = 'promoCodes';

    protected static ?string $recordTitleAttribute = 'code';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255)
                    ->alphaNum()
                    ->uppercase(),
                
                Forms\Components\Select::make('discount_type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                    ])
                    ->required(),
                
                Forms\Components\TextInput::make('discount_value')
                    ->numeric()
                    ->required(),
                
                Forms\Components\TextInput::make('max_uses')
                    ->numeric()
                    ->nullable(),
                
                Forms\Components\DateTimePicker::make('valid_from'),
                Forms\Components\DateTimePicker::make('valid_until'),
                
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('discount_type')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                
                Tables\Columns\TextColumn::make('discount_value')
                    ->formatStateUsing(fn ($record) => $record->discount_type === 'percentage' 
                        ? $record->discount_value . '%' 
                        : 'IDR ' . number_format($record->discount_value)),
                
                Tables\Columns\TextColumn::make('usage_summary')
                    ->state(fn ($record) => $record->used_count . ' / ' . ($record->max_uses ?? '∞'))
                    ->label('Usage'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('valid_until')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
