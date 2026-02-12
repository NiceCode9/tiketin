<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Models\VenueSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TicketCategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'ticketCategories';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('IDR')
                    ->required(),
                
                Forms\Components\TextInput::make('quota')
                    ->numeric()
                    ->required(),
                
                Forms\Components\Toggle::make('is_seated')
                    ->label('Assigned Seating')
                    ->reactive(),
                
                Forms\Components\Select::make('venue_section_id')
                    ->label('Venue Section')
                    ->relationship('venueSection', 'name', function (Builder $query, RelationManager $livewire) {
                        // Filter sections belonging to the event's venue
                        $event = $livewire->getOwnerRecord();
                        if ($event->venue_id) {
                            $query->where('venue_id', $event->venue_id);
                        } else {
                            $query->whereNull('id'); // No venue, no sections
                        }
                    })
                    ->visible(fn (Forms\Get $get) => $get('is_seated'))
                    ->required(fn (Forms\Get $get) => $get('is_seated')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('quota'),
                Tables\Columns\TextColumn::make('sold_count')
                    ->label('Sold'),
                Tables\Columns\IconColumn::make('is_seated')
                    ->boolean(),
                Tables\Columns\TextColumn::make('venueSection.name')
                    ->label('Section')
                    ->placeholder('-'),
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
