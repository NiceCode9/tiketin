<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-assign client_id for client users creating scanner accounts
        if (auth()->user()->hasRole('client') && !isset($data['client_id'])) {
            $data['client_id'] = auth()->user()->client_id;
        }

        return $data;
    }
}
