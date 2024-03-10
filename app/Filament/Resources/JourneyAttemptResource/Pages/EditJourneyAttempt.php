<?php

namespace App\Filament\Resources\JourneyAttemptResource\Pages;

use App\Filament\Resources\JourneyAttemptResource;
use App\Services\JourneyRouteCalculatorService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJourneyAttempt extends EditRecord {
    protected static string $resource = JourneyAttemptResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make( 'calculateNearestRoute' )
                          ->label( 'Calculate' )
                          ->color( 'success' )
                          ->icon( 'heroicon-s-arrow-path' )
                          ->action( 'calculateNearestRoute' ),
        ];
    }

    public function calculateNearestRoute(): void {
        $journeyRouteCalculator = new JourneyRouteCalculatorService();
        $journeyRouteCalculator->calculate($this->record);
    }
}
