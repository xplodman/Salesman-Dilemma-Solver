<?php

namespace App\Filament\Resources\JourneyAttemptResource\Pages;

use App\Filament\Resources\JourneyAttemptResource;
use App\Models\JourneyAttempt;
use App\Services\JourneyRouteCalculatorService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJourneyAttempt extends EditRecord
{
    protected static string $resource = JourneyAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('calculateShortestPath')
                          ->label(function (JourneyAttempt $journeyAttempt) {
                            if ($journeyAttempt->calculated) {
                                return 'Recalculate';
                            }

                              return 'Calculate';
                          })
                          ->color(function (JourneyAttempt $journeyAttempt) {
                            if ($journeyAttempt->calculated) {
                                return 'info';
                            }

                              return 'success';
                          })
                          ->icon('heroicon-s-arrow-path')
                          ->requiresConfirmation()
                          ->action(function (JourneyAttempt $journeyAttempt) {
                              $this->calculateShortestPath($journeyAttempt);

                              // Redirect page to any URL you want
                              redirect(static::getUrl([
                                  'name'   => 'edit', // Assuming 'name' is the correct key for the page/route name, adjust as necessary
                                  'record' => $journeyAttempt->id, // The actual parameters expected by the route
                              ]));
                          }),
        ];
    }

    public function calculateShortestPath(JourneyAttempt $journeyAttempt): void
    {
        $journeyRouteCalculator = new JourneyRouteCalculatorService();
        $journeyRouteCalculator->calculateJourneyRoutes($journeyAttempt);
    }
}
