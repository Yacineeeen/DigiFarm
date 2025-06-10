<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/produit')]
final class ProduitController extends AbstractController
{
    //  FRONT-OFFICE : Liste des produits (accessible aux clients)
    #[Route( name: 'app_produit_index', methods: ['GET'])]
    public function indexFront(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    //  FRONT-OFFICE : Détails d’un produit
    #[Route('/details/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function showFront(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    // BACK-OFFICE : Liste des produits avec actions de gestion
    #[Route('/gestion', name: 'app_produit_gestion', methods: ['GET'])]
    public function indexBack(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index2.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    // BACK-OFFICE : Création d’un produit
    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleImageUpload($form, $produit, $slugger);
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_gestion');
        }

        return $this->render('produit/new.html.twig', [
            'form' => $form,
        ]);
    }

    //  BACK-OFFICE :edit d’un produit
    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleImageUpload($form, $produit, $slugger);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_gestion');
        }

        return $this->render('produit/edit.html.twig', [
            'form' => $form,
            'produit' => $produit,
        ]);
    }

    //  BACK-OFFICE : Suppression d’un produit
    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_gestion');
    }

    // Fonction privée pour gérer l'upload des images
    private function handleImageUpload($form, Produit $produit, SluggerInterface $slugger)
    {
        $file = $form->get('imageFile')->getData();
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

            try {
                $file->move($this->getParameter('uploads_directory'), $newFilename);
                $produit->setImage($newFilename);
            } catch (FileException $e) {
                throw new \Exception("Erreur lors de l'upload de l'image");
            }
        }
    }
}
