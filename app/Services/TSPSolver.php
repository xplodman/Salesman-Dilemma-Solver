<?php

namespace App\Services;

class TSPSolver {
    public function solveNearestRoute( $distanceMatrix, $startWaypoint ) {
        // Initialize the path with the start waypoint
        $path = [ $startWaypoint->id ];

        // Get the number of waypoints
        $numWaypoints = count( $distanceMatrix );

        // Create an array to keep track of visited cities
        $visited = array_fill_keys( array_keys( $distanceMatrix ), false );

        // Mark the start waypoint as visited
        $visited[ $startWaypoint->id ] = true;

        // Loop until all waypoints are visited
        while ( count( $path ) < $numWaypoints ) {
            // Initialize variables to track the nearest city and its distance
            $nearestCity     = null;
            $nearestDistance = PHP_INT_MAX;

            // Get the ID of the current city
            $currentCity = end( $path );

            // Iterate over the distances from the current city to other cities
            foreach ( $distanceMatrix[ $currentCity ] as $cityId => $distance ) {
                // Check if the city is unvisited and closer than the current nearest city
                if ( ! $visited[ $cityId ] && $distance < $nearestDistance ) {
                    $nearestCity     = $cityId;
                    $nearestDistance = $distance;
                }
            }

            // If a nearest unvisited city is found, add it to the path
            if ( $nearestCity !== null ) {
                $path[]                  = $nearestCity;
                $visited[ $nearestCity ] = true;
            } else {
                // If no unvisited city is reachable from the current city,
                // find the nearest unvisited city from any visited city
                $nearestDistance = PHP_INT_MAX;

                // Iterate over the visited cities in the path
                foreach ( $path as $visitedCityId ) {
                    foreach ( $distanceMatrix[ $visitedCityId ] as $cityId => $distance ) {
                        // Check if the city is unvisited and closer than the current nearest city
                        if ( ! $visited[ $cityId ] && $distance < $nearestDistance ) {
                            $nearestCity     = $cityId;
                            $nearestDistance = $distance;
                        }
                    }
                }

                // If a nearest unvisited city is found, add it to the path
                if ( $nearestCity !== null ) {
                    $path[]                  = $nearestCity;
                    $visited[ $nearestCity ] = true;
                } else {
                    // If no unvisited city is reachable from any visited city, exit the loop
                    break;
                }
            }
        }

        return $path;
    }

    public function solveFarthestRoute($distanceMatrix, $startWaypoint)
    {
        // Initialize the path with the start waypoint
        $path = [$startWaypoint->id];

        // Get the number of waypoints
        $numWaypoints = count($distanceMatrix);

        // Create an array to keep track of visited cities
        $visited = array_fill_keys(array_keys($distanceMatrix), false);

        // Mark the start waypoint as visited
        $visited[$startWaypoint->id] = true;

        // Loop until all waypoints are visited
        while (count($path) < $numWaypoints) {
            // Initialize variables to track the farthest city and its distance
            $farthestCity = null;
            $farthestDistance = 0;

            // Get the ID of the current city
            $currentCity = end($path);

            // Iterate over the distances from the current city to other cities
            foreach ($distanceMatrix[$currentCity] as $cityId => $distance) {
                // Check if the city is unvisited and farther than the current farthest city
                if (!$visited[$cityId] && $distance > $farthestDistance) {
                    $farthestCity = $cityId;
                    $farthestDistance = $distance;
                }
            }

            // If a farthest unvisited city is found, add it to the path
            if ($farthestCity !== null) {
                $path[] = $farthestCity;
                $visited[$farthestCity] = true;
            } else {
                // If no unvisited city is reachable from the current city,
                // find the farthest unvisited city from any visited city
                $farthestDistance = 0;

                // Iterate over the visited cities in the path
                foreach ($path as $visitedCityId) {
                    foreach ($distanceMatrix[$visitedCityId] as $cityId => $distance) {
                        // Check if the city is unvisited and farther than the current farthest city
                        if (!$visited[$cityId] && $distance > $farthestDistance) {
                            $farthestCity = $cityId;
                            $farthestDistance = $distance;
                        }
                    }
                }

                // If a farthest unvisited city is found, add it to the path
                if ($farthestCity !== null) {
                    $path[] = $farthestCity;
                    $visited[$farthestCity] = true;
                } else {
                    // If no unvisited city is reachable from any visited city, exit the loop
                    break;
                }
            }
        }

        return $path;
    }


}
