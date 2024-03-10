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

        // Calculate total distances covered by the nearest and farthest routes
        $totalDistanceForUser = $this->calculateTotalDistanceForUser($user);

        // Calculate total kilos saved
        $totalKilosSaved = $totalDistanceForUser['farthest'] - $totalDistanceForUser['nearest'];

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

            Stat::make('Total Distance (Without Our System)', $totalDistanceForUser['farthest'])
                ->icon('heroicon-m-map')
                ->color('danger')
                ->description('The total distance that would have been traveled without using our system. This reflects the distance covered by following the farthest available route.'),

            Stat::make('Total Distance (With Our System)', $totalKilosSaved)
                ->icon('heroicon-m-map')
                ->color('success')
                ->description('The total distance that would have been traveled with using our system. This reflects the distance covered by following the nearest available route.'),

            Stat::make('Total Distance Saved', $totalDistanceForUser['nearest'])
                ->icon('heroicon-m-map')
                ->color('success')
                ->description('The overall reduction in distance achieved by using our system. This indicates the distance saved by following the optimized routes provided by our system.'),

        ];
    }


    protected function calculateTotalDistanceForUser($user)
    {
        $totalDistance = [
            'nearest'  => 0,
            'farthest' => 0,
        ];

        foreach ($user->journeyAttempts as $journeyAttempt) {
            $totalDistance['nearest']  += $journeyAttempt->nearest_route ? $this->calculateRouteDistance($journeyAttempt->nearest_route) : 0;
            $totalDistance['farthest'] += $journeyAttempt->farthest_route ? $this->calculateRouteDistance($journeyAttempt->farthest_route) : 0;
        }

        return $totalDistance;
    }

    protected function calculateRouteDistance($route)
    {
        // Initialize total distance
        $totalDistance = 0;

        // Loop through each waypoint in the route
        for ($i = 0; $i < count($route) - 1; $i++) {
            // Retrieve distance between consecutive waypoints from the database
            $distance = WaypointDistance::where('origin_id', $route[ $i ])
                                        ->where('destination_id', $route[ $i + 1 ])
                                        ->value('distance');

            // Add distance to the total distance
            $totalDistance += $distance;
        }

        return $totalDistance;
    }
}
