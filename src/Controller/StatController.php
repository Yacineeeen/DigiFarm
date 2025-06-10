<?php
// src/Controller/StatController.php

namespace App\Controller;

use App\Repository\AnimalRepository;
use App\Repository\SuiviRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class StatController extends AbstractController
{
    #[Route('/api/animal/stats', name: 'api_animal_stats', methods: ['GET'])]
    public function animalStats(AnimalRepository $animalRepository): JsonResponse
    {
        $animals = $animalRepository->findAll();
        $stats = [];

        // Build the animal type statistics
        foreach ($animals as $animal) {
            $type = $animal->getType();
            if (!isset($stats[$type])) {
                $stats[$type] = 0;
            }
            $stats[$type]++;
        }

        // Return animal stats as a JSON response
        return new JsonResponse($stats);
    }

    #[Route('/api/suivi/stats', name: 'api_suivi_stats', methods: ['GET'])]
    public function suiviStats(SuiviRepository $suiviRepository): JsonResponse
    {
        $suivis = $suiviRepository->findAll();
        $stats = [
            'Bon' => 0,
            'Moyen' => 0,
            'Critique' => 0,
        ];

        // Build the suivi state statistics
        foreach ($suivis as $suivi) {
            $etat = $suivi->getEtat();
            if (array_key_exists($etat, $stats)) {
                $stats[$etat]++;
            }
        }

        // Return suivi stats as a JSON response
        return new JsonResponse($stats);
    }
}
