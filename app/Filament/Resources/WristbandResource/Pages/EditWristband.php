<?php

namespace App\Filament\Resources\WristbandResource\Pages;

use App\Filament\Resources\WristbandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWristband extends EditRecord
{
    protected static string $resource = WristbandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
