<?php

namespace App\Filament\Resources\JourneyAttemptResource\Pages;

use App\Filament\Resources\JourneyAttemptResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJourneyAttempt extends CreateRecord
{
    protected static string $resource = JourneyAttemptResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
