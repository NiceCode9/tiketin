<?php

namespace App\Filament\Resources\VenueResource\Pages;

use App\Filament\Resources\VenueResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVenue extends CreateRecord
{
    protected static string $resource = VenueResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure client_id is set for non-super admins
        if (!auth()->user()->hasRole('super_admin')) {
            $data['client_id'] = auth()->user()->client_id;
        }
        
        return $data;
    }
}
