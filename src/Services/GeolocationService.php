<?php
namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeolocationService
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getCoordinatesFromAddress(string $address): ?array
    {
        // Use OpenStreetMap Nominatim for geocoding
        $response = $this->httpClient->request(
            'GET',
            'https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                ],
            ]
        );

        $data = $response->toArray();

        if (!empty($data[0]['lat']) && !empty($data[0]['lon'])) {
            return [
                'latitude' => (float) $data[0]['lat'],
                'longitude' => (float) $data[0]['lon'],
            ];
        }

        return null;
    }
}