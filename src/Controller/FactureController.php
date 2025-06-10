<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use App\Repository\AbonnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class FactureController extends AbstractController
{
    #[Route('/facture/', name: 'app_facture_index', methods: ['GET'])]
    public function index(FactureRepository $factureRepository, AbonnementRepository $abonnementRepository): Response
    {
        return $this->render('frontOfficeAbonement/facture/index.html.twig', [
            'factures' => $factureRepository->findAll(),
            'abonnements' => $abonnementRepository->findAll(),
        ]);
    }

    #[Route('/facture/new/{abonnementId}', name: 'app_facture_new', methods: ['GET', 'POST'])]
    public function new(int $abonnementId, Request $request, AbonnementRepository $abonnementRepository, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'abonnementId est valide
        $abonnement = $abonnementRepository->find($abonnementId);
        if (!$abonnement) {
            throw $this->createNotFoundException('Abonnement non trouvé.');
        }

        // Créer la facture et assigner le prix de l'abonnement
        $facture = new Facture();
        $facture->setAbonnement($abonnement);
        $facture->setPrixt($abonnement->getPrix()); 

        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($facture);
            $entityManager->flush();

            return $this->redirectToRoute('app_facture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontOfficeAbonement/facture/new.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    #[Route('/facture/{id}', name: 'app_facture_show', methods: ['GET'])]
    public function show(Facture $facture): Response
    {
        return $this->render('frontOfficeAbonement/facture/show.html.twig', [
            'facture' => $facture,
        ]);
    }

    #[Route('/facture/{id}/edit', name: 'app_facture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_facture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontOfficeAbonement/facture/edit.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    #[Route('/facture/{id}', name: 'app_facture_delete', methods: ['POST'])]
    public function delete(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $facture->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($facture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_facture_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/facture2/', name: 'app_facture2_index', methods: ['GET'])]
    public function index2(FactureRepository $factureRepository, AbonnementRepository $abonnementRepository): Response
    {
        return $this->render('backOfficeAbonement/facture2/index.html.twig', [
            'factures' => $factureRepository->findAll(),
            'abonnements' => $abonnementRepository->findAll(),
        ]);
    }

    #[Route('/facture2/new/{abonnementId}', name: 'app_facture2_new', methods: ['GET', 'POST'])]
    public function new2(int $abonnementId, Request $request, AbonnementRepository $abonnementRepository, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'abonnementId est valide
        $abonnement = $abonnementRepository->find($abonnementId);
        if (!$abonnement) {
            throw $this->createNotFoundException('Abonnement non trouvé.');
        }

        // Créer la facture et assigner le prix de l'abonnement
        $facture = new Facture();
        $facture->setAbonnement($abonnement);
        $facture->setPrixt($abonnement->getPrix()); 

        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($facture);
            $entityManager->flush();

            return $this->redirectToRoute('app_facture2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backOfficeAbonement/facture2/new.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    #[Route('/facture2/{id}', name: 'app_facture2_show', methods: ['GET'])]
    public function show2(Facture $facture): Response
    {
        return $this->render('backOfficeAbonement/facture2/show.html.twig', [
            'facture' => $facture,
        ]);
    }

    #[Route('/facture2/{id}/edit', name: 'app_facture2_edit', methods: ['GET', 'POST'])]
    public function edit2(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_facture2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backOfficeAbonement/facture2/edit.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    #[Route('/facture2/{id}', name: 'app_facture2_delete', methods: ['POST'])]
    public function delete2(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $facture->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($facture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_facture2_index', [], Response::HTTP_SEE_OTHER);
    }
}



