<?php

namespace App\Filament\Resources\JourneyAttemptResource\Pages;

use App\Filament\Resources\JourneyAttemptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJourneyAttempts extends ListRecords
{
    protected static string $resource = JourneyAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
