<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Form\Animal1Type;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/animal')]
class AnimalController extends AbstractController
{
    // Index page with pagination
    #[Route('/animal/front_animal', name: 'front_animal_page')]
    public function index3(): Response
    {
        return $this->render('frontOfficeAnimal/frontAnimal.html.twig');
    }

    #[Route(name: 'app_animal_index', methods: ['GET'])]
    public function index(AnimalRepository $animalRepository, Request $request, PaginatorInterface $paginator, SessionInterface $session): Response
    {
        $userId = $session->get('user_id');
    if (!$userId) {
        return $this->redirectToRoute('login');
    }

    $animals = $animalRepository->findBy(['id_user' => $userId]);

    $pagination = $paginator->paginate(
        $animals, // Results array
        $request->query->getInt('page', 1),
        6
    );

        

        return $this->render('animal/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    // Admin/Backoffice page to view all animals
    #[Route('/back', name: 'app_animal_index2', methods: ['GET'])]
    public function index2(AnimalRepository $animalRepository): Response
    {
        return $this->render('animal/index2.html.twig', [
            'animals' => $animalRepository->findAll(),
        ]);
    }

    // Form to create a new animal
    #[Route('/new', name: 'app_animal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $animal = new Animal();
        $form = $this->createForm(Animal1Type::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($animal);
            $entityManager->flush();

            return $this->redirectToRoute('app_animal_index2', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('animal/new.html.twig', [
            'animal' => $animal,
            'form' => $form,
        ]);
    }

    // Show details of a single animal
    #[Route('/{id}', name: 'app_animal_show', methods: ['GET'],requirements: ['id' => '\d+'])]
    public function show(Animal $animal): Response
    {
        return $this->render('animal/show2.html.twig', [
            'animal' => $animal,
        ]);
    }

    // Edit an existing animal
    #[Route('/{id}/edit', name: 'app_animal_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Animal $animal, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Animal1Type::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_animal_index2', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('animal/edit.html.twig', [
            'animal' => $animal,
            'form' => $form,
        ]);
    }

    // Delete an animal
    #[Route('/{id}', name: 'app_animal_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Animal $animal, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $animal->getId(), $request->get('_token'))) {
            $entityManager->remove($animal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_animal_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/export', name: 'app_animal_export', methods: ['GET'])]
    public function exportExcel(AnimalRepository $animalRepository): Response
    {
        $animals = $animalRepository->findAll();
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Type');
        $sheet->setCellValue('D1', 'Âge');
        $sheet->setCellValue('E1', 'Poids');
        $sheet->setCellValue('F1', 'Race');
    
        $row = 2;
        foreach ($animals as $animal) {
            $sheet->setCellValue('A' . $row, $animal->getId());
            $sheet->setCellValue('B' . $row, $animal->getNom());
            $sheet->setCellValue('C' . $row, $animal->getType());
            $sheet->setCellValue('D' . $row, $animal->getAge());
            $sheet->setCellValue('E' . $row, $animal->getPoids());
            $sheet->setCellValue('F' . $row, $animal->getRace());
            $row++;
        }
    
        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });
    
        $fileName = 'animals.xlsx';
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $disposition);
    
        return $response;
    }
    
}

   


