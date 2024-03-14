<?php

namespace App\Filament\Widgets;

use App\Models\WaypointDistance;
use App\Services\JourneyStatsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAdminOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        // Calculate journey statistics using the injected service
        $stats = ( new JourneyStatsService() )->getStats($user);

        $result = [];

        foreach ($stats as $stat) {
            $result[] = Stat::make($stat['label'], $stat['value'])
                            ->icon($stat['icon'])
                            ->description($stat['description']);
        }

        return $result;
    }
}
