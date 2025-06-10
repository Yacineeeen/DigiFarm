<?php

namespace App\Controller;

use App\Entity\Suivi;
use App\Repository\SuiviRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

#[Route('/suivi')]
final class aiController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    #[Route('/analyze/{id}', name: 'app_suivi_analyze', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
public function analyze(int $id, SuiviRepository $suiviRepository, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
{
    $suivi = $suiviRepository->find($id);
    if (!$suivi) {
        throw $this->createNotFoundException('Suivi not found');
    }

    $data = [
        'temperature' => $suivi->getTemperature(),
        'rythme_cardiaque' => $suivi->getRythmeCardiaque(),
        'etat' => $suivi->getEtat(),
    ];

    $logger->info('Sending data to Open Source AI:', $data);

    try {
        // Replace 'Bon' with 'Hel' in the 'etat' field
        if ($data['etat'] === 'Bon') {
            $data['etat'] = 'Hel';
        }

        $client = new Client();
        $url = 'https://api-inference.huggingface.co/models/gpt2'; // Use a different model if needed

        $payload = [
            'inputs' => "Analyze the following medical data: " . json_encode($data),
            'parameters' => [
                'max_length' => 100,
                'temperature' => 0.7,
            ],
            'options' => [
                'wait_for_model' => true, // Wait for the model to load
            ],
        ];

        $response = $client->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $_ENV['HUGGING_FACE_API_KEY'], // Use the correct environment variable
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        $responseBody = json_decode($response->getBody()->getContents(), true);

        // Check for errors in the API response
        if (isset($responseBody['error'])) {
            throw new \Exception('Hugging Face API Error: ' . $responseBody['error']);
        }

        // Extract the generated text
        $generatedText = $responseBody[0]['generated_text'];

        $logger->info('Open Source AI API response:', ['response' => $generatedText]);

        $suivi->setAnalysis($generatedText);
        $entityManager->flush();

        return $this->render('suivi/analysis.html.twig', [
            'suivi' => $suivi,
            'analysis' => $generatedText,
        ]);
    } catch (\Exception $e) {
        $logger->error('Error calling Open Source AI API: ' . $e->getMessage());
        return $this->render('suivi/error.html.twig', [
            'error' => 'An error occurred: ' . $e->getMessage(),
        ]);
    }



    
}
}
