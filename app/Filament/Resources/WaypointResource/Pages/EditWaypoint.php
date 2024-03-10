<?php

namespace App\Filament\Resources\WaypointResource\Pages;

use App\Filament\Resources\WaypointResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWaypoint extends EditRecord
{
    protected static string $resource = WaypointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array {
        $location = $data['location'];

        $data['latitude'] = $location['lat'];
        $data['longitude'] = $location['lng'];
        return $data;
    }
}
