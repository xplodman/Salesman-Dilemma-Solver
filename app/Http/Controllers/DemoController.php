<?php

namespace App\Http\Controllers;

use App\Models\Waypoint;
use App\Models\WaypointDistance;
use App\Services\JourneyRouteCalculatorService;

class DemoController extends Controller {
    public function createDemo() {
        // Check if the user already has the demo journey
        $user        = auth()->user();
        $demoJourney = $user->journeyAttempts()->where( 'name', 'Demo USA Cities Journey' )->first();

        if ( ! $demoJourney ) {
            // Create the demo journey
            $journey = $user->journeyAttempts()->create( [
                'name' => 'Demo USA Cities Journey',
            ] );

            // Seed the USA cities
            $this->seedCities( $journey );

            // Add a start waypoint
            $startWaypoint = $journey->waypoints()->create( [
                'name'      => 'New York',
                'latitude'  => 40.6943,
                'longitude' => - 73.9249,
                'user_id'   => auth()->user()->id,
            ] );

            // Update the demo journey with the start waypoint
            $journey->update( [ 'start_waypoint_id' => $startWaypoint->id ] );

            return response()->json( [ 'message' => 'USA cities seeded successfully' ] );
        }

        return response()->json( [ 'message' => 'Demo USA Cities Journey already exists' ] );
    }

    public function calculateDemo() {
        // Check if the user already has the demo journey
        $user        = auth()->user();
        $demoJourney = $user->journeyAttempts()->where( 'name', 'Demo USA Cities Journey' )->first();

        if ( ! $demoJourney ) {
            return response()->json( [ 'message' => 'Demo journey not found' ], 404 );
        }

        // Calculate journey routes for the demo journey
        $journeyRouteCalculator = new JourneyRouteCalculatorService();
        $journeyRouteCalculator->calculateJourneyRoutes( $demoJourney );

        return response()->json( [ 'message' => 'Demo journey routes calculated successfully' ] );
    }

    public function removeDemo()
    {
        $user = auth()->user();
        $demoJourney = $user->journeyAttempts()->where('name', 'Demo USA Cities Journey')->first();

        if ($demoJourney) {
            // Delete all related waypoints
            $demoJourney->waypoints->each(function ($waypoint) {
                // Delete distances related to this waypoint
                WaypointDistance::where('origin_id', $waypoint->id)->orWhere('destination_id', $waypoint->id)->forceDelete();
                $waypoint->forceDelete();
            });

            // Finally, delete the demo journey
            $demoJourney->forceDelete();

            return response()->json(['message' => 'Demo removed successfully']);
        }

        return response()->json(['message' => 'Demo does not exist']);
    }

    private function seedCities( $journey ) {
        $cities = [
            [ 'name' => 'Los Angeles', 'latitude' => 34.1141, 'longitude' => - 118.4068 ],
            [ 'name' => 'Chicago', 'latitude' => 41.8375, 'longitude' => - 87.6866 ],
            [ 'name' => 'Miami', 'latitude' => 25.784, 'longitude' => - 80.2101 ],
            [ 'name' => 'Houston', 'latitude' => 29.786, 'longitude' => - 95.3885 ],
            [ 'name' => 'Dallas', 'latitude' => 32.7935, 'longitude' => - 96.7667 ],
        ];

        // Insert each city into the waypoints table
        foreach ( $cities as $city ) {
            $waypoint = new Waypoint( [
                'name'      => $city['name'],
                'latitude'  => $city['latitude'],
                'longitude' => $city['longitude'],
                'user_id'   => auth()->user()->id,
            ] );

            $journey->waypoints()->save( $waypoint );
        }
    }
}
