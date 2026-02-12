<?php

namespace App\Filament\Resources\VenueResource\RelationManagers;

use App\Models\Seat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('capacity')
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('capacity'),
                Tables\Columns\TextColumn::make('seats_count')
                    ->counts('seats')
                    ->label('Generated Seats'),
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
                
                // Seat Generator Action
                Tables\Actions\Action::make('generate_seats')
                    ->label('Generate Seats')
                    ->icon('heroicon-o-squares-plus')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('row_start')
                                    ->label('Start Row')
                                    ->placeholder('A')
                                    ->required()
                                    ->maxLength(2),
                                Forms\Components\TextInput::make('row_end')
                                    ->label('End Row')
                                    ->placeholder('Z')
                                    ->required()
                                    ->maxLength(2),
                                Forms\Components\TextInput::make('seat_start')
                                    ->label('Start Seat Number')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),
                                Forms\Components\TextInput::make('seat_count')
                                    ->label('Seats Per Row')
                                    ->numeric()
                                    ->default(20)
                                    ->required(),
                            ]),
                    ])
                    ->action(function (Model $record, array $data) {
                        $this->generateSeats($record, $data);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Generate Seats Bulk')
                    ->modalDescription('This will generate seats for this section. Existing seats with same labels will be skipped.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Logic to generate seats
    protected function generateSeats(Model $section, array $data)
    {
        $startRow = strtoupper($data['row_start']);
        $endRow = strtoupper($data['row_end']);
        $seatStart = (int) $data['seat_start'];
        $seatCount = (int) $data['seat_count'];

        $count = 0;
        
        // Simple alpha range generator (A-Z)
        // Note: This simple version handles single letters A-Z. 
        // For AA, AB it needs more logic, but keeping it simple for now.
        $rows = range($startRow, $endRow);

        foreach ($rows as $row) {
            for ($i = 0; $i < $seatCount; $i++) {
                $seatNum = $seatStart + $i;
                
                // Create seat if not exists
                $exists = Seat::where('venue_section_id', $section->id)
                    ->where('row_label', $row)
                    ->where('seat_number', (string)$seatNum)
                    ->exists();

                if (!$exists) {
                    Seat::create([
                        'venue_section_id' => $section->id,
                        'row_label' => $row,
                        'seat_number' => (string)$seatNum,
                        'status' => 'available',
                        'is_accessible' => false, // Default
                    ]);
                    $count++;
                }
            }
        }

        Notification::make()
            ->title("Generated $count seats successfully")
            ->success()
            ->send();
    }
}
