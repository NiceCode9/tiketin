<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ScanLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'scanLogs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Read-only generally
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('scanned_at')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('scannedBy.name')
                    ->label('Scanner'),
                
                Tables\Columns\TextColumn::make('scan_type')
                    ->badge()
                    ->colors([
                        'primary' => 'exchange',
                        'success' => 'entry',
                    ]),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'success',
                        'danger' => 'failed',
                    ]),
                
                Tables\Columns\TextColumn::make('error_message')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->error_message),
                
                Tables\Columns\TextColumn::make('scannable_type')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->label('Type'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('scan_type')
                    ->options([
                        'exchange' => 'Exchange',
                        'entry' => 'Entry',
                    ]),
            ])
            ->headerActions([
                // No create
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No delete usually
            ])
            ->defaultSort('scanned_at', 'desc');
    }
}
