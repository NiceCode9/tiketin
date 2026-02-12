<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use App\Models\Client;
use App\Models\Venue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Events';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Event Information')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->visible(fn () => auth()->user()->hasRole('super_admin'))
                            ->default(fn () => auth()->user()->client_id),
                        
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $state, Forms\Set $set) => 
                                $set('slug', Str::slug($state))
                            ),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\RichEditor::make('description')
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('banner_image')
                            ->image()
                            ->imageEditor()
                            ->directory('event-banners')
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('additional_images')
                            ->image()
                            ->multiple()
                            ->directory('event-gallery')
                            ->columnSpanFull(),
                        
                        Forms\Components\Select::make('venue_id')
                            ->label('Venue')
                            ->relationship('venue', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('address'),
                                Forms\Components\TextInput::make('city'),
                                Forms\Components\TextInput::make('capacity')
                                    ->numeric(),
                                Forms\Components\Toggle::make('has_seating')
                                    ->label('Has Assigned Seating'),
                                Forms\Components\FileUpload::make('image')
                                    ->image()
                                    ->directory('venue-images')
                                    ->columnSpanFull(),
                            ]),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'closed' => 'Closed',
                            ])
                            ->required()
                            ->default('draft'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Event Schedule')
                    ->schema([
                        Forms\Components\DateTimePicker::make('event_date')
                            ->required()
                            ->native(false),
                        
                        Forms\Components\DateTimePicker::make('event_end_date')
                            ->native(false),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Seating Configuration')
                    ->schema([
                        Forms\Components\Toggle::make('has_assigned_seating')
                            ->label('Enable Assigned Seating')
                            ->default(false),
                    ]),
                
                Forms\Components\Section::make('Wristband Exchange Window')
                    ->schema([
                        Forms\Components\DateTimePicker::make('wristband_exchange_start')
                            ->native(false),
                        
                        Forms\Components\DateTimePicker::make('wristband_exchange_end')
                            ->native(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('venue.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'published',
                        'danger' => 'closed',
                    ]),
                
                Tables\Columns\IconColumn::make('has_assigned_seating')
                    ->boolean()
                    ->label('Seated')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('event_date')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('ticket_categories_count')
                    ->counts('ticketCategories')
                    ->label('Categories'),
                
                Tables\Columns\TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Orders'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'closed' => 'Closed',
                    ]),
                
                Tables\Filters\SelectFilter::make('client')
                    ->relationship('client', 'name')
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // Client isolation - already handled by ClientScope
        // But we can add additional filtering here if needed
        
        return $query;
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\EventResource\RelationManagers\TicketCategoriesRelationManager::class,
            \App\Filament\Resources\EventResource\RelationManagers\TicketsRelationManager::class,
            \App\Filament\Resources\EventResource\RelationManagers\OrdersRelationManager::class,
            \App\Filament\Resources\EventResource\RelationManagers\PromoCodesRelationManager::class,
            \App\Filament\Resources\EventResource\RelationManagers\ScanLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
