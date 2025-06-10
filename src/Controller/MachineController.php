<?php

namespace App\Controller;

use App\Entity\Machine;
use App\Form\MachineType;
use App\Repository\MachineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

final class MachineController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    #[Route('/machine/front_machine', name: 'front_machine_page')]
    public function index2(): Response
    {
        return $this->render('frontMachine.html.twig');
    }

    #[Route('/contact_machine', name: 'contact_machine_page')]
    public function index3(): Response
    {
        return $this->render('contact.html.twig');
    }

    #[Route('/home', name: 'home')]
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    #[Route('/machine/', name: 'app_machine_index', methods: ['GET'])]
    public function index(MachineRepository $machineRepository): Response
    {
       

        return $this->render('machine/index.html.twig', [
            'machines' => $machineRepository->findAll(),
        ]);
    }

    #[Route('/machine/new', name: 'app_machine_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
      

        $machine = new Machine();
        $form = $this->createForm(MachineType::class, $machine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($machine);
            $entityManager->flush();

            return $this->redirectToRoute('app_machine_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('machine/new.html.twig', [
            'machine' => $machine,
            'form' => $form,
        ]);
    }

    #[Route('/machine/{id}', name: 'app_machine_show', methods: ['GET'])]
    public function show(Machine $machine): Response
    {
        

        return $this->render('machine/show.html.twig', [
            'machine' => $machine,
        ]);
    }

    #[Route('/machine/{id}/edit', name: 'app_machine_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Machine $machine, EntityManagerInterface $entityManager): Response
    {
       

        $form = $this->createForm(MachineType::class, $machine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_machine_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('machine/edit.html.twig', [
            'machine' => $machine,
            'form' => $form,
        ]);
    }

    #[Route('/machine/{id}', name: 'app_machine_delete', methods: ['POST'])]
    public function delete(Request $request, Machine $machine, EntityManagerInterface $entityManager): Response
    {
       

        $entityManager->remove($machine);
        $entityManager->flush();

        return $this->redirectToRoute('app_machine_index', [], Response::HTTP_SEE_OTHER);
    }

    // New routes for machine2 directory
    #[Route('/machine2', name: 'app_machine2_index', methods: ['GET'])]
    public function indexMachine2(MachineRepository $machineRepository): Response
    {
       

        return $this->render('machine2/index.html.twig', [
            'machines' => $machineRepository->findAll(),
        ]);
    }

    #[Route('/machine2/new', name: 'app_machine2_new', methods: ['GET', 'POST'])]
    public function newMachine2(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Check if the user is logged in and has the required role (ROLE_ADMIN)
        if ($this->isUserLoggedIn() || $this->isUserGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home'); // Redirect to home or an "access denied" page
        }

        $machine = new Machine();
        $form = $this->createForm(MachineType::class, $machine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($machine);
            $entityManager->flush();

            return $this->redirectToRoute('app_machine2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('machine2/new.html.twig', [
            'machine' => $machine,
            'form' => $form,
        ]);
    }

    #[Route('/machine2/{id}', name: 'app_machine2_show', methods: ['GET'])]
    public function showMachine2(Machine $machine): Response
    {
        // Check if the user is logged in and has the required role (ROLE_ADMIN)
        if ($this->isUserLoggedIn() || $this->isUserGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home'); // Redirect to home or an "access denied" page
        }

        return $this->render('machine2/show.html.twig', [
            'machine' => $machine,
        ]);
    }

    #[Route('/machine2/{id}/edit', name: 'app_machine2_edit', methods: ['GET', 'POST'])]
    public function editMachine2(Request $request, Machine $machine, EntityManagerInterface $entityManager): Response
    {
        // Check if the user is logged in and has the required role (ROLE_ADMIN)
        if ($this->isUserLoggedIn() || $this->isUserGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home'); // Redirect to home or an "access denied" page
        }

        $form = $this->createForm(MachineType::class, $machine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_machine2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('machine2/edit.html.twig', [
            'machine' => $machine,
            'form' => $form,
        ]);
    }

    #[Route('/machine2/{id}', name: 'app_machine2_delete', methods: ['POST'])]
    public function deleteMachine2(Request $request, Machine $machine, EntityManagerInterface $entityManager): Response
    {
        // Check if the user is logged in and has the required role (ROLE_ADMIN)
        if ($this->isUserLoggedIn() || $this->isUserGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home'); // Redirect to home or an "access denied" page
        }

        $entityManager->remove($machine);
        $entityManager->flush();

        return $this->redirectToRoute('app_machine2_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Helper method to check if the user is logged in.
     */
    private function isUserLoggedIn(): bool
    {
        return $this->getSession()->has('user_id'); // Check if the user ID is stored in the session
    }

    /**
     * Helper method to check if the user has a specific role.
     */
    private function isUserGranted(string $role): bool
    {
        // Retrieve the user's role from the session
        $userRole = $this->getSession()->get('user_role');

        // Check if the user's role matches the required role
        return $userRole === $role;
    }
}