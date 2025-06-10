<?php

namespace App\Controller;

use App\Entity\Suivi;
use App\Form\SuiviType;
use App\Repository\SuiviRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\VeterinaireRepository;
use GuzzleHttp\Client;
use Google\Cloud\AIPlatform\V1\GenerationConfig;
use Google\Cloud\AIPlatform\V1\GenerativeModel;
use Google\Cloud\AIPlatform\V1\SafetySetting;
use Google\Cloud\AIPlatform\V1\SafetySetting\HarmCategory;
use Google\Cloud\AIPlatform\V1\SafetySetting\HarmBlockThreshold;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

#[Route('/suivi')]
final class SuiviController extends AbstractController
{

    private $logger;

    // Inject the logger service via the constructor
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }



    #[Route(name: 'app_suivi_index', methods: ['GET'])]
    public function index(SuiviRepository $suiviRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $suiviRepository->createQueryBuilder('s')->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Get current page number, default is 1
            6 // Limit per page
        );

        return $this->render('suivi/index.html.twig', [
            'pagination' => $pagination,
            
        ]);
    }

    #[Route('/front', name: 'app_suivi_index2', methods: ['GET'])]
    public function index2(SuiviRepository $suiviRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $suiviRepository->createQueryBuilder('s')->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Get current page number, default is 1
            6 // Limit per page
        );

        return $this->render('suivi/index2.html.twig', [
            'pagination' => $pagination,
            'suivis' => $pagination->getItems(),
        ]);
    }

    #[Route('/new', name: 'app_suivi_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $suivi = new Suivi();
        $form = $this->createForm(SuiviType::class, $suivi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($suivi);
            $entityManager->flush();

            return $this->redirectToRoute('app_suivi_index2', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('suivi/new.html.twig', [
            'suivi' => $suivi,
            'form' => $form,
        ]);
    }
  
  
//     #[Route('/analyze/{id}', name: 'app_suivi_analyze', methods: ['GET','POST'],requirements: ['id' => '\d+'])]
// public function analyze(int $id, SuiviRepository $suiviRepository, EntityManagerInterface $entityManager): Response
// {
//     // Fetch the Suivi entity by ID
//     $suivi = $suiviRepository->find($id);
//     if (!$suivi) {
//         throw $this->createNotFoundException('Suivi not found');
//     }

//     // Prepare the data to send to Gemini AI
//     $data = [
//         'temperature' => $suivi->getTemperature(),
//         'rythme_cardiaque' => $suivi->getRythmeCardiaque(),
//         'etat' => $suivi->getEtat(),
//     ];

//     // Log the data being sent
//     $this->logger->info('Sending data to Gemini AI:', $data);

//     // Initialize Guzzle client
//     $client = new Client();

//     try {
//         // Send the data to Gemini AI API
//         $response = $client->post('http://123.45.67.89/analyze', [
//             'json' => $data,
//             'headers' => [
//             'Authorization' => 'Bearer ' . $_ENV['GEMINI_API_KEY'],
//             'Content-Type' => 'application/json',
//             ],
//         ]);

//         // Log the API response
//         $responseBody = $response->getBody()->getContents();
//         $this->logger->info('Gemini AI API response:', ['response' => $responseBody]);

//         // Get the analysis result
//         $analysis = json_decode($responseBody, true);

//         // Save the analysis result to the database
//         $suivi->setAnalysis($analysis['result']);
//         $entityManager->flush();

//         // Render the analysis result in a Twig template
//         return $this->render('suivi/analysis.html.twig', [
//             'suivi' => $suivi,
//             'analysis' => $analysis['result'], // Pass the analysis result to the template
//         ]);
//     } catch (\Exception $e) {
//         // Log the error
//         $this->logger->error('Error calling Gemini AI API:', ['error' => $e->getMessage()]);

//         // Handle any errors
//         return $this->render('suivi/error.html.twig', [
//             'error' => $e->getMessage(),
//         ]);
//     }
// }

// #[Route('/analyze/{id}', name: 'app_suivi_analyze', methods: ['GET','POST'], requirements: ['id' => '\d+'])]
// public function analyze(int $id, SuiviRepository $suiviRepository, EntityManagerInterface $entityManager): Response
// {
//     // Fetch the Suivi entity by ID
//     $suivi = $suiviRepository->find($id);
//     if (!$suivi) {
//         throw $this->createNotFoundException('Suivi not found');
//     }

//     // Prepare the data to send to OpenAI GPT
//     $data = [
//         'temperature' => $suivi->getTemperature(),
//         'rythme_cardiaque' => $suivi->getRythmeCardiaque(),
//         'etat' => $suivi->getEtat(),
//     ];

//     // Log the data being sent
//     $this->logger->info('Sending data to OpenAI GPT:', $data);

//     // Initialize Guzzle client
//     $client = new Client();

//     try {
//         // Send the data to OpenAI GPT API
//         $response = $client->post('https://api.openai.com/v1/chat/completions', [
//             'json' => [
//                 'model' => 'gpt-3.5-turbo',
//                 'messages' => [
//                     [
//                         'role' => 'user',
//                         'content' => "Analyze the following animal health data: Temperature = {$data['temperature']}°C, Heart Rate = {$data['rythme_cardiaque']} BPM, State = {$data['etat']}.",
//                     ],
//                 ],
//             ],
//             'headers' => [
//                 'Authorization' => 'Bearer ' . $_ENV['OPENAI_API_KEY'],
//                 'Content-Type' => 'application/json',
//             ],
//         ]);

//         // Log the API response
//         $responseBody = $response->getBody()->getContents();
//         $this->logger->info('OpenAI GPT API response:', ['response' => $responseBody]);

//         // Get the analysis result
//         $analysis = json_decode($responseBody, true);
//         $analysisResult = $analysis['choices'][0]['message']['content'];

//         // Save the analysis result to the database
//         $suivi->setAnalysis($analysisResult);
//         $entityManager->flush();

//         // Render the analysis result in a Twig template
//         return $this->render('suivi/analysis.html.twig', [
//             'suivi' => $suivi,
//             'analysis' => $analysisResult, // Pass the analysis result to the template
//         ]);
//     } catch (\Exception $e) {
//         // Log the error
//         $this->logger->error('Error calling OpenAI GPT API:', ['error' => $e->getMessage()]);

//         // Handle any errors
//         return $this->render('suivi/error.html.twig', [
//             'error' => $e->getMessage(),
//         ]);
//     }
// }
    

    #[Route('/{id}', name: 'app_suivi_show', methods: ['GET'],requirements: ['id' => '\d+'])]
    public function show(Suivi $suivi): Response
    {
        return $this->render('suivi/show.html.twig', [
            'suivi' => $suivi,
        ]);
    }

    #[Route('/{id}/back', name: 'app_suivi_show2', methods: ['GET'],requirements: ['id' => '\d+'])]
    public function show2(Suivi $suivi): Response
    {
        return $this->render('suivi/show2.html.twig', [
            'suivi' => $suivi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_suivi_edit', methods: ['GET', 'POST'],requirements: ['id' => '\d+'])]
    public function edit(Request $request, Suivi $suivi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SuiviType::class, $suivi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_suivi_index2', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('suivi/edit.html.twig', [
            'suivi' => $suivi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_suivi_delete', methods: ['POST'],requirements: ['id' => '\d+'])]
    public function delete(Request $request, Suivi $suivi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $suivi->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($suivi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_suivi_index2', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/export', name: 'app_suivi_export', methods: ['GET'])]
public function exportExcel(SuiviRepository $suiviRepository): Response
{
    $suivis = $suiviRepository->findAll();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Température (°C)');
    $sheet->setCellValue('C1', 'Rythme Cardiaque (BPM)');
    $sheet->setCellValue('D1', 'État');
    $sheet->setCellValue('E1', 'Vétérinaire');
    $sheet->setCellValue('F1', 'Client ID');

    $row = 2;
    foreach ($suivis as $suivi) {
        $sheet->setCellValue('A' . $row, $suivi->getId());
        $sheet->setCellValue('B' . $row, $suivi->getTemperature());
        $sheet->setCellValue('C' . $row, $suivi->getRythmeCardiaque());
        $sheet->setCellValue('D' . $row, $suivi->getEtat());
        $sheet->setCellValue('E' . $row, $suivi->getVeterinaire() ? $suivi->getVeterinaire()->getNom() : 'N/A');
        $sheet->setCellValue('F' . $row, $suivi->getIdClient());
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    $response = new StreamedResponse(function () use ($writer) {
        $writer->save('php://output');
    });

    $fileName = 'suivi_export.xlsx';
    $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);
    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->headers->set('Content-Disposition', $disposition);

    return $response;
}
#[Route('/send-email/{id}', name: 'app_send_email_veterinaire', methods: ['GET'],requirements: ['id' => '\d+'])]
public function sendEmailToVeterinaire(MailerInterface $mailer, int $id, VeterinaireRepository $veterinaireRepository, SuiviRepository $suiviRepository): Response
{
    // Récupérer le vétérinaire par ID
    $veterinaire = $veterinaireRepository->find($id);
    if (!$veterinaire) {
        throw $this->createNotFoundException('Vétérinaire non trouvé');
    }

    // Récupérer les suivis du vétérinaire
    $suivis = $suiviRepository->findBy(['veterinaire' => $veterinaire]);

    // Création du fichier Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Définir les entêtes de colonnes
    $sheet->setCellValue('A1', 'ID')
          ->setCellValue('B1', 'Température (°C)')
          ->setCellValue('C1', 'Rythme Cardiaque (BPM)')
          ->setCellValue('D1', 'État')
          ->setCellValue('E1', 'Vétérinaire')
          ->setCellValue('F1', 'Client ID');

    // Remplir les données
    $row = 2;
    foreach ($suivis as $suivi) {
        $sheet->setCellValue('A' . $row, $suivi->getId())
              ->setCellValue('B' . $row, $suivi->getTemperature())
              ->setCellValue('C' . $row, $suivi->getRythmeCardiaque())
              ->setCellValue('D' . $row, $suivi->getEtat())
              ->setCellValue('E' . $row, $veterinaire->getNom())
              ->setCellValue('F' . $row, $suivi->getIdClient());
        $row++;
    }

    // Créer un fichier Excel temporaire
    $writer = new Xlsx($spreadsheet);

    // Define a path within public/uploads folder
    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/';
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $filePath = $uploadDir . $veterinaire->getNom() . '_suivi_export.xlsx';
    $writer->save($filePath);

    // Création de l'email
    $email = (new Email())
        ->from('oumayma@example.com')
        ->to($veterinaire->getEmail())
        ->subject('Suivi Exporté - Vétérinaire: ' . $veterinaire->getNom())
        ->html('<p>Bonjour ' . $veterinaire->getNom() . ',</p><p>Veuillez trouver en pièce jointe le fichier Excel contenant les suivis des patients.</p>')
        ->attachFromPath($filePath, $veterinaire->getNom() . '_suivi_export.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    // Envoi de l'email
    $mailer->send($email);

    // Suppression du fichier temporaire après l'envoi
    unlink($filePath);

    // Retourner une réponse indiquant que l'email a été envoyé
    return new Response('Email envoyé avec succès à ' . $veterinaire->getNom());
}
    #[Route('/suivi/veterinaire/{id}', name: 'app_suivi_veterinaire')]
    public function showSuivi(int $id, VeterinaireRepository $veterinaireRepository, SuiviRepository $suiviRepository)
    {
        // Récupérer le vétérinaire par ID
        $veterinaire = $veterinaireRepository->find($id);
        if (!$veterinaire) {
            throw $this->createNotFoundException('Vétérinaire non trouvé');
        }

        // Récupérer les suivis du vétérinaire
        $suivis = $suiviRepository->findBy(['veterinaire' => $veterinaire]);

        return $this->render('suivi/veterinaire.html.twig', [
            'veterinaire' => $veterinaire,
            'suivis' => $suivis,
        ]);
    }

}

