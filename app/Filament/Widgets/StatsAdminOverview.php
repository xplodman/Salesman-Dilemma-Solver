<?php

namespace App\Filament\Widgets;

use App\Models\WaypointDistance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAdminOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        // Calculate total distances covered by the shortest and longest routes
        $totalDistanceForUser = $this->calculateTotalDistanceForUser($user);

        // Calculate total kilos saved
        $totalKilosSaved = $totalDistanceForUser['longest'] - $totalDistanceForUser['shortest'];

        return [
            Stat::make('Total Journey Attempts', $user->journeyAttempts->count())
                ->icon('heroicon-m-map')
                ->description('The total number of journey attempts entered into the system.'),

            Stat::make('Total Waypoints', $user->waypoints->count())
                ->icon('heroicon-m-map-pin')
                ->description('The total number of waypoints entered into the system.'),

            Stat::make('Calculated Journey Attempts', $user->journeyAttempts->where('calculated', true)->count())
                ->icon('heroicon-m-map')
                ->color('success')
                ->description('The number of journey attempts that have been successfully calculated.'),

            Stat::make('Total Distance (Without Our System)', $totalDistanceForUser['longest'])
                ->icon('heroicon-m-map')
                ->color('danger')
                ->description('The total distance that would have been traveled without using our system. This reflects the distance covered by following the longest available route.'),

            Stat::make('Total Distance (With Our System)', $totalDistanceForUser['shortest'])
                ->icon('heroicon-m-map')
                ->color('success')
                ->description('The total distance that would have been traveled with using our system. This reflects the distance covered by following the shortest available route.'),

            Stat::make('Total Distance Saved', $totalKilosSaved)
                ->icon('heroicon-m-map')
                ->color('success')
                ->description('The overall reduction in distance achieved by using our system. This indicates the distance saved by following the optimized routes provided by our system.'),

        ];
    }


    protected function calculateTotalDistanceForUser($user)
    {
        $totalDistance = [
            'shortest' => 0,
            'longest'  => 0,
        ];

        foreach ($user->journeyAttempts as $journeyAttempt) {
            $totalDistance['shortest'] += $journeyAttempt->shortest_path_distance ?: 0;
            $totalDistance['longest']  += $journeyAttempt->longest_path_distance ?: 0;
        }

        return $totalDistance;
    }
}
