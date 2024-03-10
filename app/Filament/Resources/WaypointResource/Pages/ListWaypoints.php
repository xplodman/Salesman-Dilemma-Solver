<?php

namespace App\Filament\Resources\WaypointResource\Pages;

use App\Filament\Resources\WaypointResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWaypoints extends ListRecords
{
    protected static string $resource = WaypointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
