<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Culture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\CultureType;
use Symfony\Component\Process\Process ;
use App\Repository\CultureRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

final class CultureController extends AbstractController
{
    #[Route('/culture/statistiques/rendement', name: 'statistiques_rendement')]
    public function statistiquesRendement(): Response
    {
        $scriptPath = $this->getParameter('kernel.project_dir') . '/public/analyse_rendement.py'; 
    
        if (!file_exists($scriptPath)) {
            throw new \Exception("Erreur : Le fichier analyse_rendement.py est introuvable à : " . $scriptPath);
        }
    
        $publicPath = $this->getParameter('kernel.project_dir') . '/public';
        $imagePath = $publicPath . '/rendement.png';
    
        if (!is_dir($publicPath)) {
            mkdir($publicPath, 0777, true); 
        }
    
      
        $process = new Process(['python', $scriptPath]);
        $process->setEnv(['PYTHONIOENCODING' => 'utf-8']); 
        $process->run();
    
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    
        // ✅ Vérifier si l'image est bien générée
        if (!file_exists($imagePath)) {
            throw new \Exception("Erreur : L'image de rendement n'a pas été générée.");
        }
    
        return $this->render('statistiques/rendement.html.twig', [
            'image' => 'rendement.png',
        ]);
    }
    
    
    private function estimerRendement(Culture $culture): ?float
    {
        $scriptPath = $this->getParameter('kernel.project_dir') . '/public/analyse_rendement.py';
    
        $process = new Process([
            'python', $scriptPath,
            '--densite', (string) $culture->getDensitePlantation(),
            '--eau', (string) $culture->getBesoinsEau(),
            '--cout', (string) $culture->getCoutMoyen()
        ]);
    
        $process->run();
    
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    
        $output = trim($process->getOutput());
    
        return is_numeric($output) ? (float) $output : null;
    }
    #[Route('/culture/predire/{id}', name: 'app_culture_predire', methods: ['GET'])]
public function predireRendement(CultureRepository $cultureRepository, int $id): JsonResponse
{
    $culture = $cultureRepository->find($id);

    if (!$culture) {
        return new JsonResponse(['error' => 'Culture non trouvée'], Response::HTTP_NOT_FOUND);
    }

    $rendement_estime = $this->estimerRendement($culture);

    return new JsonResponse(['rendement_estime' => $rendement_estime]);
}

    
    
    #[Route('/calendar', name: 'app_calendar', methods: ['GET'])]
    public function showCalendar(CultureRepository $cultureRepository, SessionInterface $session): Response
    {
        $userId = $session->get('user_id');
        if (!$userId) {
            return $this->redirectToRoute('login');
        }
    
        $cultures = $cultureRepository->findBy(['id_user' => $userId]);
    
        $events = [];
    
        foreach ($cultures as $culture) {
            if ($culture->getDatePlantation()) {
                $events[] = [
                    'title' => '🌱 Plantation - ' . $culture->getNom(),
                    'start' => $culture->getDatePlantation()->format('Y-m-d'),
                    'color' => '#28a745',
                ];
            }
    
            if ($culture->getDateRecolte()) {
                $events[] = [
                    'title' => '🌾 Récolte - ' . $culture->getNom(),
                    'start' => $culture->getDateRecolte()->format('Y-m-d'),
                    'color' => '#dc3545',
                ];
            }
        }
    
        return $this->render('frontOfficeEtude/culture/calendar.html.twig', [
            'events' => json_encode($events),
        ]);
    }
    

    #[Route('/culture/{context}', requirements: ['context' => 'front|back'], name: 'app_culture_index', methods: ['GET'])]
    public function index(string $context, CultureRepository $cultureRepository, SessionInterface $session): Response
    {
        if ($context === 'front') {
            $userId = $session->get('user_id');
            if (!$userId) {
                return $this->redirectToRoute('login');
            }

            $cultures = $cultureRepository->findBy(['id_user' => $userId]);
        } else {
            $cultures = $cultureRepository->findAll();
        }

        return $this->render("$context" . "OfficeEtude/culture/index.html.twig", [
            'cultures' => $cultures,
        ]);
    }

    
    

    #[Route('/culture/{context}/new', name: 'app_culture_new', methods: ['GET', 'POST'])]
    public function new(string $context, Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        if ($context === 'front') {
            $userId = $session->get('user_id');
            if (!$userId) {
                return $this->redirectToRoute('login');
            }
        }

        $culture = new Culture();
        $form = $this->createForm(CultureType::class, $culture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($context === 'front') {
                $user = $entityManager->getRepository(User::class)->find($session->get('user_id'));
                if (!$user) {
                    throw $this->createNotFoundException('User not found');
                }
                $culture->setIdUser($user);
            }

            $entityManager->persist($culture);
            $entityManager->flush();

            return $this->redirectToRoute('app_culture_index', ['context' => $context], Response::HTTP_SEE_OTHER);
        }

        return $this->render("$context" . "OfficeEtude/culture/new.html.twig", [
            'culture' => $culture,
            'form' => $form,
        ]);
    }

    #[Route('/culture/{context}/{id}', name: 'app_culture_show', methods: ['GET'])]
    public function show(string $context, Culture $culture, SessionInterface $session): Response
    {
        if ($context === 'front') {
            $userId = $session->get('user_id');
            if (!$userId || $culture->getIdUser()->getId() !== $userId) {
                return $this->redirectToRoute('login');
            }
        }

        return $this->render("$context" . "OfficeEtude/culture/show.html.twig", [
            'culture' => $culture,
        ]);
    }
    #[Route('/culture/{context}/{id}/edit', name: 'app_culture_edit', methods: ['GET', 'POST'])]
public function edit(string $context, int $id, Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
{
    $culture = $entityManager->getRepository(Culture::class)->find($id);

    if (!$culture) {
        throw $this->createNotFoundException("❌ Culture with ID $id not found.");
    }

    if ($context === 'front') {
        $userId = $session->get('user_id');
        if (!$userId || $culture->getIdUser()->getId() !== $userId) {
            return $this->redirectToRoute('login');
        }
    }

    $form = $this->createForm(CultureType::class, $culture);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('app_culture_index', ['context' => $context], Response::HTTP_SEE_OTHER);
    }

    return $this->render("$context" . "OfficeEtude/culture/edit.html.twig", [
        'culture' => $culture,
        'form' => $form,
    ]);
}

#[Route('/culture/{context}/{id}', name: 'app_culture_delete', methods: ['POST'])]
public function delete(string $context, Request $request, Culture $culture, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        if ($context === 'front') {
            $userId = $session->get('user_id');
            if (!$userId || $culture->getIdUser()->getId() !== $userId) {
                return $this->redirectToRoute('login');
            }
        }

        if ($this->isCsrfTokenValid('delete' . $culture->getId(), $request->get('_token'))) {
            $entityManager->remove($culture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_culture_index', ['context' => $context], Response::HTTP_SEE_OTHER);
    }
}
