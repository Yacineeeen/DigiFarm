<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Etude;
use App\Form\EtudeType;
use App\Repository\EtudeRepository;
use App\Form\EtudeSearchType;
use App\Enum\Climat;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Enum\Dispo;
use App\Service\PdfGeneratorService; 
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
#[Route('/etude/{context}', requirements: ['context' => 'front|back'])]
final class EtudeController extends AbstractController
{
    
    #[Route('/', name: 'app_etude_index', methods: ['GET', 'POST'])]
    public function index(string $context, EtudeRepository $etudeRepository, SessionInterface $session, Request $request): Response
    {
        $form = $this->createForm(EtudeSearchType::class);
        $form->handleRequest($request);
    
        $etudes = [];
    
        $userId = $session->get('user_id');
        if ($context === 'front') {
            if (!$userId) {
                return $this->redirectToRoute('login');
            }
            $etudes = $etudeRepository->findBy(['id_user' => $userId]);
        } else {
            $etudes = $etudeRepository->findAll();
        }
    
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $climat = $data['climat'] ? Climat::from($data['climat']) : null;
            $etudes = $etudeRepository->searchEtudes(
                $climat,
                $data['expert'] ?? null,
                $userId 
            );
        }
    
        return $this->render("$context" . "OfficeEtude/etude/index.html.twig", [
            'etudes' => $etudes,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/etude/{id}/download-pdf', name: 'app_etude_download_pdf')]
    public function downloadPdf(Etude $etude, PdfGeneratorService $pdfGenerator): Response
    {
        $htmlContent = $this->renderView('FrontOfficeEtude/etude/pdf_template.html.twig', [
            'etude' => $etude,
        ]);
        

        $pdfContent = $pdfGenerator->generatePdf($htmlContent);

        return new Response(
            $pdfContent,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="etude_'.$etude->getId().'.pdf"',
            ]
        );
    }
    #[Route('/new', name: 'app_etude_new', methods: ['GET', 'POST'])]
    public function new(string $context, Request $request, EntityManagerInterface $entityManager, SessionInterface $session, MailerInterface $mailer): Response
    {
        if ($context === 'front') {
            $userId = $session->get('user_id');
            if (!$userId) {
                return $this->redirectToRoute('login');
            }
        }

        $etude = new Etude();
        $form = $this->createForm(EtudeType::class, $etude);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($context === 'front') {
                $user = $entityManager->getRepository(User::class)->find($session->get('user_id'));
                if (!$user) {
                    throw $this->createNotFoundException('User not found');
                }
                $etude->setIdUser($user);
            }

            $expert = $etude->getExpert();
            if ($expert) {
                $expert->setDispo(Dispo::NON_DISPONIBLE);
                $entityManager->flush();
                $email = (new Email())
                    ->from('yassineabidi431@gmail.com')
                    ->to($expert->getEmail())
                    ->subject('Nouvelle étude affectée')
                    ->text('Une nouvelle étude vous a été affectée. Vous êtes maintenant marqué comme non disponible.');

                $mailer->send($email);
            }

            $entityManager->persist($etude);
            $entityManager->flush();

            return $this->redirectToRoute('app_etude_index', ['context' => $context], Response::HTTP_SEE_OTHER);
        }

        return $this->render("$context" . "OfficeEtude/etude/new.html.twig", [
            'etude' => $etude,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etude_show', methods: ['GET'])]
    public function show(string $context, Etude $etude, SessionInterface $session): Response
    {
        if ($context === 'front') {
            $userId = $session->get('user_id');
            if (!$userId || $etude->getIdUser()->getId() !== $userId) {
                return $this->redirectToRoute('login');
            }
        }

        return $this->render("$context" . "OfficeEtude/etude/show.html.twig", [
            'etude' => $etude,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etude_edit', methods: ['GET', 'POST'])]
    public function edit(string $context, Request $request, Etude $etude, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        if ($context === 'front') {
            $userId = $session->get('user_id');
            if (!$userId || $etude->getIdUser()->getId() !== $userId) {
                return $this->redirectToRoute('login');
            }
        }

        $form = $this->createForm(EtudeType::class, $etude);
        $form->handleRequest($request);

        $originalExpert = $etude->getExpert();

        if ($form->isSubmitted() && $form->isValid()) {
            $newExpert = $etude->getExpert();
            if ($originalExpert !== $newExpert) {
                $originalExpert->setDispo(Dispo::DISPONIBLE);
                $entityManager->flush();
                if ($newExpert) {
                    $newExpert->setDispo(Dispo::NON_DISPONIBLE);
                    $entityManager->flush();
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_etude_index', ['context' => $context], Response::HTTP_SEE_OTHER);
        }

        return $this->render("$context" . "OfficeEtude/etude/edit.html.twig", [
            'etude' => $etude,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etude_delete', methods: ['POST'])]
    public function delete(string $context, Request $request, Etude $etude, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        if ($context === 'front') {
            $userId = $session->get('user_id');
            if (!$userId || $etude->getIdUser()->getId() !== $userId) {
                return $this->redirectToRoute('login');
            }
        }

        if ($this->isCsrfTokenValid('delete' . $etude->getId(), $request->get('_token'))) {
            $expert = $etude->getExpert();
            if ($expert) {
                $expert->setDispo(Dispo::DISPONIBLE);
                $entityManager->flush();
            }
            $entityManager->remove($etude);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etude_index', ['context' => $context], Response::HTTP_SEE_OTHER);
    }
}
