<?php

namespace App\Controller;

use App\Entity\Expert;
use App\Form\ExpertType;
use App\Repository\ExpertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
final class ExpertController extends AbstractController
{
   

    #[Route('/front', name: 'front_page')]
    public function index2(): Response
    {
return $this->render('frontOfficeEtude/frontetude.html.twig');
    }
    #[Route('/front/home', name: 'home_page')]
    public function home(): Response
    {
return $this->render('frontOfficeEtude/home.html.twig');
    }
    #[Route('/front/contact', name: 'contact_page')]
    public function contact(): Response
    {
       return $this->render('frontOfficeEtude/contact.html.twig');
    }

    #[Route('/front/aboutus', name: 'aboutus_page')]
    public function aboutus(): Response
    {
       return $this->render('frontOfficeEtude/about.html.twig');
    }


    #[Route('/front/allservices', name: 'allservices_page')]
    public function allservices(): Response
    {
       return $this->render('frontOfficeEtude/allservices.html.twig');
    }

    #[Route('/expert/{context}', requirements: ['context' => 'front|back'], name: 'app_expert_index', methods: ['GET'])]
    public function index(string $context, ExpertRepository $expertRepository): Response
    {
        return $this->render("$context" . "OfficeEtude/expert/index.html.twig", [
            'experts' => $expertRepository->findAll(),
        ]);
    }

    #[Route('/expert/{context}/new', name: 'app_expert_new', methods: ['GET', 'POST'])]
    public function new(string $context, Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $expert = new Expert();
        $form = $this->createForm(ExpertType::class, $expert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($expert);
            $entityManager->flush();

            $email = (new Email())
                ->from('yassineabidi431@gmail.com')
                ->to($expert->getEmail())
                ->subject('Nouvelle expert ajoutée')
                ->text('Bienvenue dans notre team agricox');

            $mailer->send($email);

            return $this->redirectToRoute('app_expert_index', ['context' => $context], Response::HTTP_SEE_OTHER);
        }

        return $this->render("$context" . "OfficeEtude/expert/new.html.twig", [
            'expert' => $expert,
            'form' => $form,
        ]);
    }

    #[Route('/expert/{context}/{id}', name: 'app_expert_show', methods: ['GET'])]
    public function show(string $context, Expert $expert): Response
    {
        return $this->render("$context" . "OfficeEtude/expert/show.html.twig", [
            'expert' => $expert,
        ]);
    }
    #[Route('/expert/{context}/{id}/edit', name: 'app_expert_edit', methods: ['GET', 'POST'])]
    public function edit(string $context, Request $request, Expert $expert, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExpertType::class, $expert);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            return $this->redirectToRoute('app_expert_index', ['context' => $context], Response::HTTP_SEE_OTHER);
        }
    
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }
    
        return $this->render("$context" . "OfficeEtude/expert/edit.html.twig", [
            'expert' => $expert,
            'form' => $form,
            'errors' => $errors,  
        ]);
    }
    

    #[Route('/expert/{context}/{id}', name: 'app_expert_delete', methods: ['POST'])]
    public function delete(string $context, Request $request, Expert $expert, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $expert->getId(), $request->get('_token'))) {
            $entityManager->remove($expert);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_expert_index', ['context' => $context], Response::HTTP_SEE_OTHER);
    }
}
