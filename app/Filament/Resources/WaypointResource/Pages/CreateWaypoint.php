<?php

namespace App\Filament\Resources\WaypointResource\Pages;

use App\Filament\Resources\WaypointResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWaypoint extends CreateRecord
{
    protected static string $resource = WaypointResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        $location = $data['location'];

        $data['latitude'] = $location['lat'];
        $data['longitude'] = $location['lng'];
        return $data;
    }
}
