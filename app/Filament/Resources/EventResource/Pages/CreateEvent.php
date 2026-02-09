<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-assign client_id for client users
        if (auth()->user()->hasRole('client')) {
            $data['client_id'] = auth()->user()->client_id;
        }

        return $data;
    }
}
