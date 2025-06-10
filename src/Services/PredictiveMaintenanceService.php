<?php
// src/Service/PredictiveMaintenanceService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PredictiveMaintenanceService
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function predictFailure(array $data): string
    {
        $response = $this->httpClient->request('POST', 'http://127.0.0.1:5000/predict', [
            'json' => $data,
        ]);

        return $response->toArray()['prediction'];
    }
}