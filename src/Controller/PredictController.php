<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\PredictiveMaintenanceService;

class PredictController
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/predict', methods: ['POST'])]
    public function predict(): JsonResponse
    {
        $data = [
            'cout' => 100,
            'temperature' => 25,
            'humidite' => 50,
            'consoCarburant' => 10,
            'consoEnergie' => 500,
        ];

        $prediction = $this->predictiveMaintenanceService->predictFailure($data);
        return $this->json(['prediction' => $prediction]);
    }
    
}
