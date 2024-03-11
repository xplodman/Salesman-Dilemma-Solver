<?php

namespace App\Services;

/**
 * Class TSPSolver
 * Solves the Traveling Salesman Problem (TSP) by finding the longest and shortest paths through all waypoints.
 */
class TSPSolver
{
    /**
     * @var array The distance matrix, mapping distances between waypoints.
     */
    private array $distanceMatrix;

    /**
     * @var int The ID of the starting waypoint.
     */
    private int $startWaypoint;

    /**
     * @var array The longest path found, including total distance and path.
     */
    private array $longestPath;

    /**
     * @var array The shortest path found, including total distance and path.
     */
    private array $shortestPath;

    /**
     * TSPSolver constructor.
     * Initializes the solver with a distance matrix and a starting waypoint.
     *
     * @param   array  $distanceMatrix  The distance matrix.
     * @param   int    $startWaypoint   The starting waypoint ID.
     */
    public function __construct(array $distanceMatrix, int $startWaypoint)
    {
        $this->distanceMatrix = $distanceMatrix;
        $this->startWaypoint  = $startWaypoint;
        $this->longestPath    = [ 'distance' => 0, 'path' => [] ];
        $this->shortestPath   = [ 'distance' => PHP_INT_MAX, 'path' => [] ];
    }

    /**
     * Recursively finds all paths starting from the given waypoint, updating the longest and shortest paths discovered.
     * This method employs a depth-first search (DFS) algorithm to traverse all possible paths.
     *
     * @param   int        $start          The current waypoint ID from which to continue exploring paths.
     * @param   array      $visited        Keeps track of waypoints that have been visited in the current path, to avoid cycles.
     * @param   array      $path           The current path of waypoints visited, including the current waypoint.
     * @param   float|int  $totalDistance  The total distance covered by the current path.
     */
    private function findAllPaths(int $start, array $visited = [], array $path = [], float|int $totalDistance = 0): void
    {
        // Mark the current waypoint as visited to prevent revisiting it in this path.
        $visited[ $start ] = true;

        // Add the current waypoint to the path.
        $path[] = $start;

        // Check if the current path includes all waypoints in the distance matrix.
        if (count($path) === count($this->distanceMatrix)) {
            // If this path's total distance is greater than the current longest path, update the longest path.
            if ($totalDistance > $this->longestPath['distance']) {
                $this->longestPath = [ 'distance' => $totalDistance, 'path' => $path ];
            }
            // If this path's total distance is less than the current shortest path, update the shortest path.
            if ($totalDistance < $this->shortestPath['distance']) {
                $this->shortestPath = [ 'distance' => $totalDistance, 'path' => $path ];
            }
        } else {
            // For each adjacent waypoint to the current waypoint,
            // if it has not been visited, recursively explore further paths from that waypoint.
            foreach ($this->distanceMatrix[ $start ] as $nextWaypoint => $distance) {
                if (! isset($visited[ $nextWaypoint ])) {
                    $this->findAllPaths($nextWaypoint, $visited, $path, $totalDistance + $distance);
                }
            }
        }

        // Upon returning from recursion (backtracking), unset the current waypoint from visited
        // to allow exploring different paths that include this waypoint later on.
        unset($visited[ $start ]);
    }

    /**
     * Solves the TSP by finding the longest and shortest paths from the start waypoint.
     */
    public function solve(): void
    {
        $this->findAllPaths($this->startWaypoint);
    }

    /**
     * Returns the longest path found.
     *
     * @return array An associative array containing the 'distance' and 'path' of the longest path.
     */
    public function getLongestPath(): array
    {
        return $this->longestPath;
    }

    /**
     * Returns the shortest path found.
     *
     * @return array An associative array containing the 'distance' and 'path' of the shortest path.
     */
    public function getShortestPath(): array
    {
        return $this->shortestPath;
    }
}
