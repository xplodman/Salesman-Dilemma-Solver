<?php

namespace App\Services;

use App\Models\User;

class JourneyStatsService {
    protected $stats = [
        'totalJourneyAttempts'           => [
            'label'       => 'Total Journey Attempts',
            'icon'        => 'heroicon-m-map',
            'description' => 'The total number of journey attempts entered into the system.',
        ],
        'totalWaypoints'                 => [
            'label'       => 'Total Waypoints',
            'icon'        => 'heroicon-m-map-pin',
            'description' => 'The total number of waypoints entered into the system.',
        ],
        'totalCalculatedJourneyAttempts' => [
            'label'       => 'Calculated Journey Attempts',
            'icon'        => 'heroicon-m-map',
            'description' => 'The number of journey attempts that have been successfully calculated.',
        ],
        'totalShortestDistance'          => [
            'label'       => 'Total Distance (With Our System)',
            'icon'        => 'heroicon-m-map',
            'description' => 'The total distance that would have been traveled without using our system. This reflects the distance covered by following the longest available route.',
        ],
        'totalLongestDistance'           => [
            'label'       => 'Total Distance (Without Our System)',
            'icon'        => 'heroicon-m-map',
            'description' => 'The total distance that would have been traveled with using our system. This reflects the distance covered by following the shortest available route.',
        ],
        'totalDistanceSaved'             => [
            'label'       => 'Total Distance Saved',
            'icon'        => 'heroicon-m-map',
            'description' => 'The overall reduction in distance achieved by using our system. This indicates the distance saved by following the optimized routes provided by our system.',
        ],
    ];

    public function getStats( User $user ): array {
        $stats = [];
        foreach ( $this->stats as $key => $stat ) {
            $stats[ $key ] = [
                'description' => $stat['description'],
                'icon'        => $stat['icon'],
                'label'       => $stat['label'],
                'value'       => $this->calculateStatValue( $user, $key ),
            ];
        }

        return $stats;
    }

    protected function calculateTotalDistanceForUser( User $user ): array {
        $totalDistance = [
            'shortest' => 0,
            'longest'  => 0,
        ];

        foreach ( $user->journeyAttempts as $journeyAttempt ) {
            $totalDistance['shortest'] += $journeyAttempt->shortest_path_distance ?: 0;
            $totalDistance['longest']  += $journeyAttempt->longest_path_distance ?: 0;
        }

        return $totalDistance;
    }

    protected function calculateStatValue( User $user, string $key ) {
        return match ( $key ) {
            'totalJourneyAttempts' => $user->journeyAttempts->count(),
            'totalWaypoints' => $user->waypoints->count(),
            'totalCalculatedJourneyAttempts' => $user->journeyAttempts->where( 'calculated', true )->count(),
            'totalShortestDistance' => $this->calculateTotalDistanceForUser( $user )['shortest'],
            'totalLongestDistance' => $this->calculateTotalDistanceForUser( $user )['longest'],
            'totalDistanceSaved' => $this->calculateTotalDistanceForUser( $user )['longest'] - $this->calculateTotalDistanceForUser( $user )['shortest'],
            default => 0,
        };

    }
}

