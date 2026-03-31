<?php

namespace App\Filament\Resources\WristbandResource\Pages;

use App\Filament\Resources\WristbandResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWristband extends ViewRecord
{
    protected static string $resource = WristbandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
