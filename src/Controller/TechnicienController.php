<?php

namespace App\Controller;

use App\Entity\Technicien;
use App\Form\TechnicienType;
use App\Services\GeolocationService;
use App\Repository\TechnicienRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TechnicienController extends AbstractController
{
   
    #[Route('/technicien', name: 'app_technicien_index', methods: ['GET'])]
    public function index(TechnicienRepository $technicienRepository): Response
    {
        return $this->render('technicien/index.html.twig', [
            'techniciens' => $technicienRepository->findAll(),
        ]);
    }

    
   #[Route('/technicien/new', name: 'app_technicien_new', methods: ['GET', 'POST'])]
   public function new(Request $request, EntityManagerInterface $entityManager, GeolocationService $geolocationService): Response
   {
       $technicien = new Technicien();
       $form = $this->createForm(TechnicienType::class, $technicien);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
           // Get the localisation (address) from the form
           $localisation = $technicien->getLocalisation();

           // Convert the address to coordinates using OpenStreetMap Nominatim
           $coordinates = $geolocationService->getCoordinatesFromAddress($localisation);

           if ($coordinates) {
               // Set the latitude and longitude in the Technicien entity
               $technicien->setLatitude($coordinates['latitude']);
               $technicien->setLongitude($coordinates['longitude']);
           } else {
               // Handle the case where the address is invalid
               $this->addFlash('error', 'Invalid address. Please enter a valid location.');
               return $this->redirectToRoute('app_technicien_new');
           }

           // Persist the Technicien entity to the database
           $entityManager->persist($technicien);
           $entityManager->flush();

           return $this->redirectToRoute('app_technicien_index', [], Response::HTTP_SEE_OTHER);
       }

       return $this->render('technicien/new.html.twig', [
           'technicien' => $technicien,
           'form' => $form,
       ]);
   }
    #[Route('/technicien/{id}', name: 'app_technicien_show', methods: ['GET'])]
    public function show(Technicien $technicien): Response
    {
        return $this->render('technicien/show.html.twig', [
            'technicien' => $technicien,
        ]);
    }

    #[Route('/technicien/{id}/edit', name: 'app_technicien_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Technicien $technicien, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TechnicienType::class, $technicien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_technicien_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('technicien/edit.html.twig', [
            'technicien' => $technicien,
            'form' => $form,
        ]);
    }

    #[Route('/technicien/{id}', name: 'app_technicien_delete', methods: ['POST'])]
    public function delete(Request $request, Technicien $technicien, EntityManagerInterface $entityManager): Response
    {
        
            $entityManager->remove($technicien);
            $entityManager->flush();

        return $this->redirectToRoute('app_technicien_index', [], Response::HTTP_SEE_OTHER);
    }
   
    // 🔹 Technicien 2 (avec noms de routes corrigés)

    #[Route('/technicien2', name: 'app_technicien2_index', methods: ['GET'])]
    public function indexTechnicien2(TechnicienRepository $technicienRepository): Response
    {
        return $this->render('technicien2/index.html.twig', [
            'techniciens' => $technicienRepository->findAll(),
        ]);
    }

    #[Route('/technicien2/new', name: 'app_technicien2_new', methods: ['GET', 'POST'])]
    public function newTechnicien2(Request $request, EntityManagerInterface $entityManager): Response
    {
        $technicien = new Technicien();
        $form = $this->createForm(TechnicienType::class, $technicien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($technicien);
            $entityManager->flush();

            return $this->redirectToRoute('app_technicien2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('technicien2/new.html.twig', [
            'technicien' => $technicien,
            'form' => $form,
        ]);
    }

    #[Route('/technicien2/{id}', name: 'app_technicien2_show', methods: ['GET'])]
    public function showTechnicien2(Technicien $technicien): Response
    {
        return $this->render('technicien2/show.html.twig', [
            'technicien' => $technicien,
        ]);
    }

    #[Route('/technicien2/{id}/edit', name: 'app_technicien2_edit', methods: ['GET', 'POST'])]
    public function editTechnicien2(Request $request, Technicien $technicien, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TechnicienType::class, $technicien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_technicien2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('technicien2/edit.html.twig', [
            'technicien' => $technicien,
            'form' => $form,
        ]);
    }

    #[Route('/technicien2/{id}', name: 'app_technicien2_delete', methods: ['POST'])]
    public function deleteTechnicien2(Request $request, Technicien $technicien, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $technicien->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($technicien);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_technicien2_delete', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/find-nearest-technicien', name: 'find_nearest_technicien', methods: ['POST'])]
    public function findNearestTechnicien(Request $request, TechnicienRepository $technicienRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];

        // Find nearest technician
        $nearestTechnicien = $technicienRepository->findNearestTechnicien($latitude, $longitude);

        if (!$nearestTechnicien) {
            return new JsonResponse(['error' => 'No technician found.'], 404);
        }

        // Calculate distance
        $distance = $this->calculateDistance($latitude, $longitude, $nearestTechnicien->getLatitude(), $nearestTechnicien->getLongitude());

        return new JsonResponse([
            'technicien' => [
                'name' => $nearestTechnicien->getName(),
                'latitude' => $nearestTechnicien->getLatitude(),
                'longitude' => $nearestTechnicien->getLongitude(),
            ],
            'distance' => $distance,
        ]);
    }

    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Earth radius in kilometers
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
   

