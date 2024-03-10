<?php

namespace App\Services;

use App\Models\JourneyAttempt;
use App\Models\Waypoint;
use App\Models\WaypointDistance;

class JourneyRouteCalculatorService
{
    protected $bingMapsService;
    protected $tspSolver;

    public function __construct()
    {
        $this->bingMapsService = new BingMapsService();
        $this->tspSolver       = new TSPSolver();
    }

    public function calculate(JourneyAttempt $journeyAttempt): void
    {
        // Retrieve the start waypoint associated with the journey attempt
        $startWaypoint = $journeyAttempt->startWaypoint;

        // Retrieve waypoints associated with the journey attempt excluding the start waypoint
        $waypoints = $journeyAttempt->waypoints()->where('id', '!=', $startWaypoint->id)->get();

        // Calculate the distance matrix
        $distanceMatrix = $this->calculateDistanceMatrix($waypoints, $startWaypoint);

        // Solve the TSP using the best path
        $nearestRoute  = $this->tspSolver->solveNearestRoute($distanceMatrix, $startWaypoint);
        $farthestRoute = $this->tspSolver->solveFarthestRoute($distanceMatrix, $startWaypoint);

        // Update the journey attempt with the calculated route
        $journeyAttempt->update([ 'calculated' => true, 'nearest_route' => $nearestRoute, 'farthest_route' => $farthestRoute ]);
    }

    protected function calculateDistanceMatrix($waypoints, Waypoint $startWaypoint): array
    {
        $distanceMatrix = [];

        // Include the start waypoint in the list of waypoints
        $waypoints = $waypoints->prepend($startWaypoint);

        // Loop through all pairs of waypoints
        foreach ($waypoints as $i => $origin) {
            foreach ($waypoints as $j => $destination) {
                // Calculate distance from origin to destination
                $distance = $this->getDistance($origin, $destination);

                // Store the distance in the database if it's not already stored
                if (! isset($distanceMatrix[ $origin->id ][ $destination->id ])) {
                    $this->storeDistance($origin->id, $destination->id, $distance);
                }

                // Populate the distance matrix symmetrically
                $distanceMatrix[ $origin->id ][ $destination->id ] = $distance;
            }
        }

        return $distanceMatrix;
    }

    protected function storeDistance($originId, $destinationId, $distance): void
    {
        WaypointDistance::create([
            'origin_id'      => $originId,
            'destination_id' => $destinationId,
            'distance'       => $distance,
        ]);
    }

    public function getDistance(Waypoint $origin, Waypoint $destination): float|int|string
    {
        if ($origin === $destination) {
            return 0;
        }

        try {
            // Check if the distance is already stored in the database
            $distance = $this->getStoredDistance($origin->id, $destination->id);

            if ($distance !== null) {
                return $distance;
            }

            // If the distance is not stored, fetch it from the API
            $distance = $this->fetchDistanceFromAPI($origin, $destination);

            // Store the fetched distance in the database
            $this->storeDistance($origin->id, $destination->id, $distance);

            return $distance;
        } catch (\Exception $e) {
            // Handle any exceptions that occur during distance retrieval
            return - 1;
        }
    }

    protected function getStoredDistance($originId, $destinationId): ?float
    {
        $distanceRecord = WaypointDistance::where('origin_id', $originId)
                                          ->where('destination_id', $destinationId)
                                          ->first();

        return $distanceRecord ? $distanceRecord->distance : null;
    }

    protected function fetchDistanceFromAPI(Waypoint $origin, Waypoint $destination): float|int|string
    {
        // Use Bing Maps API to fetch distance
        return $this->bingMapsService->getDistance(
            [ $origin->latitude, $origin->longitude ],
            [ $destination->latitude, $destination->longitude ]
        );
    }

    public function generateGoogleMapsLink(array $waypointIds): array
    {
        if (empty($waypointIds)) {
            return [ 'text' => '', 'link' => '' ];
        }

        // Construct the Google Maps URL
        $baseURL   = "https://www.google.com/maps/dir/";
        $waypoints = Waypoint::whereIn('id', $waypointIds)
                             ->orderByRaw('FIELD(id, ' . implode(',', $waypointIds) . ')')
                             ->get();

        // Build the Google Maps URL
        $googleMapsURL = $baseURL;
        foreach ($waypoints as $waypoint) {
            $googleMapsURL .= $waypoint->latitude . ',' . $waypoint->longitude . '/';
        }

        // Remove the trailing slash
        $googleMapsURL = rtrim($googleMapsURL, '/');

        // Concatenate sorted waypoint names into a string
        $waypointNames = $waypoints->pluck('name')->implode(', ');

        // Create the HTML link
        $link = "<a style='--c-300:var(--primary-300);--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);' class='fi-link relative inline-flex items-center justify-center font-semibold outline-none transition duration-75 hover:underline focus-visible:underline fi-size-sm fi-link-size-sm gap-1 text-sm fi-color-custom text-custom-600 dark:text-custom-400 fi-ac-link-action' href='{$googleMapsURL}' target='_blank'>Google Map</a>";

        return [ 'text' => $waypointNames, 'link' => $link ];
    }
}
