<?php

namespace App\Controller;

use App\Entity\Maintenance;
use App\Form\MaintenanceType;
use App\Repository\MaintenanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Attribute\IsGranted;
// src/Controller/MaintenanceController.php
use Psr\Log\LoggerInterface;


final class MaintenanceController extends AbstractController
{
    #[Route('/maintenance/',name: 'app_maintenance_index', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')] 
    public function index(MaintenanceRepository $maintenanceRepository): Response
    {
        return $this->render('maintenance/index.html.twig', [
            'maintenances' => $maintenanceRepository->findAll(),
        ]);
    }

    #[Route('/maintenance/new', name: 'app_maintenance_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_AGRICULTEUR')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $maintenance = new Maintenance();
        $form = $this->createForm(MaintenanceType::class, $maintenance);
        $form->handleRequest($request);
    
     // src/Controller/MaintenanceController.php
if ($form->isSubmitted() && $form->isValid()) {
    // Get form data for prediction
    $data = [
        'cout' => $maintenance->getCout(),
        'temperature' => $maintenance->getTemperature(),
        'humidite' => $maintenance->getHumidite(),
        'consoCarburant' => $maintenance->getConsoCarburant(),
        'consoEnergie' => $maintenance->getConsoEnergie(),
    ];

    // Call Flask API to get prediction
    $httpClient = HttpClient::create();
    $response = $httpClient->request('POST', 'http://127.0.0.1:5000/predict', [
        'json' => $data,
    ]);

    // Get the prediction result
    $prediction = $response->toArray()['prediction'];

    // Save the prediction to the new etatPred field
    $maintenance->setEtatPred($prediction); // Save prediction in etatPred

    // Persist and flush the maintenance entity
    $entityManager->persist($maintenance);
    $entityManager->flush();

    // Redirect to the result page with the prediction
    return $this->redirectToRoute('app_maintenance_result', [
        'id' => $maintenance->getId(),
        'prediction' => $prediction,
    ]);
}

        return $this->render('maintenance/new.html.twig', [
            'maintenance' => $maintenance,
            'form' => $form,
        ]);
    }
    
    #[Route('/maintenance/result/{id}/{prediction}', name: 'app_maintenance_result', methods: ['GET'])]
    public function result(Maintenance $maintenance, string $prediction): Response
    {
        return $this->render('maintenance/result.html.twig', [
            'maintenance' => $maintenance,
            'prediction' => $prediction,
        ]);
    }

    // src/Controller/MaintenanceController.php
#[Route('/maintenance/send-email/{id}', name: 'app_maintenance_send_email', methods: ['GET'])]
public function sendEmail(Maintenance $maintenance, MailerInterface $mailer, LoggerInterface $logger): Response
{
    // Get the technician associated with the maintenance
    $technicien = $maintenance->getIdTechnicien();

    // Ensure the technician exists and has an email
    if (!$technicien || !$technicien->getEmail()) {
        $this->addFlash('error', 'Technician email not found.');
        return $this->redirectToRoute('app_maintenance_result', [
            'id' => $maintenance->getId(),
            'prediction' => $maintenance->getEtatPred(),
        ]);
    }

    // Create the email
    $email = (new Email())
        ->from('chahedchacha84@gmail.com') // Replace with your email
        ->to($technicien->getEmail()) // Send to the technician's email
        ->subject('Maintenance Prediction Status')
        ->text(sprintf(
            'The predicted status for maintenance "%s" is: %s',
            $maintenance->getIdMachine()->getNom(), // Assuming the machine has a name field
            $maintenance->getEtatPred() // Use etatPred for the prediction
        ));

    // Log the email content for debugging
    $logger->info('Sending email to: ' . $technicien->getEmail());
    $logger->info('Email content: ' . $email->getTextBody());

    // Send the email
    try {
        $mailer->send($email);
        $this->addFlash('success', 'Email sent successfully to ' . $technicien->getEmail() . '!');
    } catch (\Exception $e) {
        $logger->error('Failed to send email: ' . $e->getMessage());
        $this->addFlash('error', 'Failed to send email. Please try again.');
    }

    // Redirect back to the result page
    return $this->redirectToRoute('app_maintenance_result', [
        'id' => $maintenance->getId(),
        'prediction' => $maintenance->getEtatPred(),
    ]);
}

    #[Route('maintenance/{id}', name: 'app_maintenance_show', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function show(Maintenance $maintenance): Response
    {
        return $this->render('maintenance/show.html.twig', [
            'maintenance' => $maintenance,
        ]);
    }

    #[Route('/maintenance/{id}/edit', name: 'app_maintenance_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_AGRICULTEUR')]
    public function edit(Request $request, Maintenance $maintenance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MaintenanceType::class, $maintenance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_maintenance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('maintenance/edit.html.twig', [
            'maintenance' => $maintenance,
            'form' => $form,
        ]);
    }

    #[Route('/maintenance/{id}', name: 'app_maintenance_delete', methods: ['POST'])]
    #[IsGranted('ROLE_AGRICULTEUR')]
    public function delete(Request $request, Maintenance $maintenance, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$maintenance->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($maintenance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_maintenance_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/maintenance2', name: 'app_maintenance2_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function indexMaintenance2(MaintenanceRepository $maintenanceRepository): Response
    {
        return $this->render('maintenance2/index.html.twig', [
            'maintenances' => $maintenanceRepository->findAll(),
        ]);
    }

    #[Route('/maintenance2/new', name: 'app_maintenance2_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newMaintenance2(Request $request, EntityManagerInterface $entityManager): Response
    {
        $maintenance = new Maintenance();
        $form = $this->createForm(MaintenanceType::class, $maintenance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($maintenance);
            $entityManager->flush();

            return $this->redirectToRoute('app_maintenance2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('maintenance2/new.html.twig', [
            'maintenance' => $maintenance,
            'form' => $form,
        ]);
    }

    #[Route('/maintenance2/{id}', name: 'app_maintenance2_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showMaintenance2(Maintenance $maintenance): Response
    {
        return $this->render('maintenance2/show.html.twig', [
            'maintenance' => $maintenance,
        ]);
    }

    #[Route('/maintenance2/{id}/edit', name: 'app_maintenance2_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editMaintenance2(Request $request, Maintenance $maintenance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MaintenanceType::class, $maintenance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_maintenance2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('maintenance2/edit.html.twig', [
            'maintenance' => $maintenance,
            'form' => $form,
        ]);
    }

    #[Route('/maintenance2/{id}', name: 'app_maintenance2_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteMaintenance2(Request $request, Maintenance $maintenance, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$maintenance->getId(), $request->request->get('_token'))) {
            $entityManager->remove($maintenance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_maintenance2_index', [], Response::HTTP_SEE_OTHER);
    }
   
}
