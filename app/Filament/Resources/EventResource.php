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
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Group;

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
                        
                        Forms\Components\Select::make('event_category_id')
                            ->label('Category')
                            ->relationship('eventCategory', 'name')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                        $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                Forms\Components\TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->unique('event_categories', 'slug'),
                            ]),

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
                        
                        Forms\Components\RichEditor::make('terms_and_conditions')
                            ->label('Terms and Conditions')
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        Split::make([
                            Grid::make(1)
                                ->schema([
                                    ImageEntry::make('banner_image')
                                        ->hiddenLabel()
                                        ->grow(false)
                                        ->width('100%')
                                        ->height('auto')
                                        ->extraImgAttributes([
                                            'class' => 'object-cover w-full rounded-lg shadow-md',
                                            'style' => 'max-height: 400px;',
                                        ]),
                                ]),
                            Group::make([
                                TextEntry::make('name')
                                    ->weight('bold')
                                    ->size('3xl')
                                    ->columnSpanFull(),
                                
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'published' => 'success',
                                        'closed' => 'danger',
                                    }),

                                TextEntry::make('event_date')
                                    ->dateTime('l, d F Y, H:i')
                                    ->icon('heroicon-m-calendar'),
                                
                                TextEntry::make('venue.name')
                                    ->label('Venue')
                                    ->icon('heroicon-m-map-pin'),

                                TextEntry::make('eventCategory.name')
                                    ->label('Category')
                                    ->icon('heroicon-m-tag'),
                                
                                TextEntry::make('client.name')
                                    ->label('Client')
                                    ->visible(fn () => auth()->user()->hasRole('super_admin'))
                                    ->icon('heroicon-m-building-office'),
                            ])
                            ->grow(false),
                        ])->from('md'),
                    ]),

                Section::make('Description')
                    ->schema([
                        TextEntry::make('description')
                            ->prose()
                            ->markdown()
                            ->hiddenLabel(),
                    ])
                    ->collapsible(),

                Section::make('Event Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('has_assigned_seating')
                                    ->label('Seating System')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Assigned Seating' : 'Free Standing'),
                                
                                TextEntry::make('wristband_exchange_start')
                                    ->label('Exchange Start')
                                    ->dateTime('d M Y, H:i')
                                    ->placeholder('Not set'),

                                TextEntry::make('wristband_exchange_end')
                                    ->label('Exchange End')
                                    ->dateTime('d M Y, H:i')
                                    ->placeholder('Not set'),
                            ]),
                    ]),
                
                Section::make('Gallery')
                    ->schema([
                        ImageEntry::make('additional_images')
                            ->hiddenLabel()
                            ->circular(false)
                            ->stacked()
                            ->limit(4)
                            ->width(200)
                            ->height(200)
                            ->extraImgAttributes([
                        'class' => 'object-cover rounded-lg m-2',
                    ]),
                    ])
                    ->visible(fn ($record) => !empty($record->additional_images)),
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
                
                Tables\Columns\TextColumn::make('eventCategory.name')
                    ->label('Category')
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
                
                Tables\Filters\SelectFilter::make('eventCategory')
                    ->relationship('eventCategory', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'view' => Pages\ViewEvent::route('/{record}'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
