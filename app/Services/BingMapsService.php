<?php


namespace App\Services;
use GuzzleHttp\Client;

class BingMapsService {
    protected $client;
    protected $apiKey;

    public function __construct() {
        $this->client = new Client( [ 'base_uri' => 'http://dev.virtualearth.net/REST/v1/' ] );
        $this->apiKey = env('BING_MAPS_API_KEY');
    }

    public function getDistance( $origin, $destination ): float|int|string {
        $url      = "http://dev.virtualearth.net/REST/V1/Routes/Driving";
        $response = $this->client->request( 'GET', $url, [
            'query' => [
                'wp.0'  => implode( ',', $origin ),
                'wp.1'  => implode( ',', $destination ),
                'avoid' => 'minimizeTolls',
                'key'   => $this->apiKey,
            ],
        ] );

        $data     = json_decode( $response->getBody(), true );
        $distance = array_get( $data, 'resourceSets.0.resources.0.travelDistance', - 1 );

        // Check if the distance is numeric before returning
        if ( is_numeric( $distance ) ) {
            return $distance;
        }

        return - 1;
    }

}
