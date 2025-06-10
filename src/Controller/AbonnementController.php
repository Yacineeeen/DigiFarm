<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Form\AbonnementType;
use App\Repository\AbonnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class AbonnementController extends AbstractController
{
    #[Route('/abonnement/', name: 'app_abonnement_index', methods: ['GET'])]
    public function index(AbonnementRepository $abonnementRepository): Response
    {
        return $this->render('frontOfficeAbonement/abonnement/index.html.twig', [
            'abonnements' => $abonnementRepository->findAll(),
        ]);
    }

    #[Route('/abonnement/new', name: 'app_abonnement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $abonnement = new Abonnement();
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ Calcul automatique du prix avant d'enregistrer
            $abonnement->calculerPrix();

            $entityManager->persist($abonnement);
            $entityManager->flush();

            // ✅ Message de confirmation
            $this->addFlash('success', 'Abonnement créé avec succès !');

            return $this->redirectToRoute('app_abonnement_index');
        }

        return $this->render('frontOfficeAbonement/abonnement/new.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form,
        ]);
    }

    #[Route('/abonnement/{id}', name: 'app_abonnement_show', methods: ['GET'])]
    public function show(Abonnement $abonnement): Response
    {
        return $this->render('frontOfficeAbonement/abonnement/show.html.twig', [
            'abonnement' => $abonnement,
        ]);
    }

    #[Route('/abonnement/{id}/edit', name: 'app_abonnement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ Recalcul du prix si l'utilisateur change l'abonnement
            $abonnement->calculerPrix();
            $entityManager->flush();

            $this->addFlash('success', 'Abonnement mis à jour avec succès !');

            return $this->redirectToRoute('app_abonnement_show', ['id' => $abonnement->getId()]);

        }

        return $this->render('frontOfficeAbonement/abonnement/edit.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form,
        ]);
    }

    #[Route('/abonnement/{id}', name: 'app_abonnement_delete', methods: ['POST'])]
    public function delete(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $abonnement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($abonnement);
            $entityManager->flush();
            $this->addFlash('success', 'Abonnement supprimé avec succès.');
        }

        return $this->redirectToRoute('app_abonnement_index');
    }
    #[Route('/abonnement2/', name: 'app_abonnement2_index', methods: ['GET'])]
    public function index2(AbonnementRepository $abonnementRepository): Response
    {
        return $this->render('backOfficeAbonement/abonnement2/index.html.twig', [
            'abonnements' => $abonnementRepository->findAll(),
        ]);
    }

    #[Route('/abonnement2/new', name: 'app_abonnement2_new', methods: ['GET', 'POST'])]
    public function new2(Request $request, EntityManagerInterface $entityManager): Response
    {
        $abonnement = new Abonnement();
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ Calcul automatique du prix avant d'enregistrer
            $abonnement->calculerPrix();

            $entityManager->persist($abonnement);
            $entityManager->flush();

            // ✅ Message de confirmation
            $this->addFlash('success', 'Abonnement créé avec succès !');

            return $this->redirectToRoute('app_abonnement2_index');
        }

        return $this->render('backOfficeAbonement/abonnement2/new.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form,
        ]);
    }

    #[Route('/abonnement2/{id}', name: 'app_abonnement2_show', methods: ['GET'])]
    public function show2(Abonnement $abonnement): Response
    {
        return $this->render('backOfficeAbonement/abonnement2/show.html.twig', [
            'abonnement' => $abonnement,
        ]);
    }

    #[Route('/abonnement2/{id}/edit', name: 'app_abonnement2_edit', methods: ['GET', 'POST'])]
    public function edit2(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ Recalcul du prix si l'utilisateur change l'abonnement
            $abonnement->calculerPrix();
            $entityManager->flush();

            $this->addFlash('success', 'Abonnement mis à jour avec succès !');

            return $this->redirectToRoute('app_abonnement2_index');
        }

        return $this->render('backOfficeAbonement/abonnement2/edit.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form,
        ]);
    }

    #[Route('/abonnement2/{id}', name: 'app_abonnement2_delete', methods: ['POST'])]
    public function delete2(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $abonnement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($abonnement);
            $entityManager->flush();
            $this->addFlash('success', 'Abonnement supprimé avec succès.');
        }

        return $this->redirectToRoute('app_abonnement2_index');
    }
}


