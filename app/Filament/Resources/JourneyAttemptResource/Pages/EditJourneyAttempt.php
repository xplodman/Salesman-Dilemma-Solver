<?php

namespace App\Filament\Resources\JourneyAttemptResource\Pages;

use App\Filament\Resources\JourneyAttemptResource;
use App\Models\JourneyAttempt;
use App\Services\JourneyRouteCalculatorService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJourneyAttempt extends EditRecord {
    protected static string $resource = JourneyAttemptResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make( 'calculateNearestRoute' )
                          ->label( function ( JourneyAttempt $journeyAttempt ) {
                              if ( $journeyAttempt->calculated ) {
                                  return 'Recalculate';
                              }

                              return 'Calculate';
                          } )
                          ->color( function ( JourneyAttempt $journeyAttempt ) {
                              if ( $journeyAttempt->calculated ) {
                                  return 'info';
                              }

                              return 'success';
                          } )
                          ->icon( 'heroicon-s-arrow-path' )
                          ->action( 'calculateNearestRoute' ),
        ];
    }

    public function calculateNearestRoute(): void {
        $journeyRouteCalculator = new JourneyRouteCalculatorService();
        $journeyRouteCalculator->calculate( $this->record );
    }
}
