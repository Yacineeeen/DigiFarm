<?php

namespace App\Controller;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Repository\CommandeDetailRepository;
use App\Repository\ProduitRepository;

class PaymentController extends AbstractController
{
    private string $stripeSK;

    // Inject parameters from services.yaml or .env
    public function __construct(ParameterBagInterface $params)
    {
        $this->stripeSK = $params->get('STRIPE_SECRET_KEY');
    }

    #[Route('/payment', name: 'app_payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }

    #[Route('/checkout/{id}', name: 'checkout')]
    public function checkout(int $id, CommandeRepository $commandeRepository,
                             CommandeDetailRepository $commandeDetailRepository,
                             ProduitRepository $produitRepository): Response
    {
        Stripe::setApiKey($this->stripeSK);

        $commande = $commandeRepository->find($id);
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        $commandeDetails = $commandeDetailRepository->findBy(['commande' => $commande]);

        $lineItems = [];
        foreach ($commandeDetails as $detail) {
            $produit = $detail->getProduit();
            $productName = $produit ? $produit->getNom() : "Produit non disponible";
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'usd',
                    'product_data' => [
                        'name' => $productName,
                    ],
                    'unit_amount'  => $detail->getPrixUnitaire() * 100, // Convertir en cents
                ],
                'quantity'   => $detail->getQuantite(),
            ];
        }

        // Ajouter les frais de livraison
        $shippingAmount = 300; // 3 USD en cents
        $lineItems[] = [
            'price_data' => [
                'currency'     => 'usd',
                'product_data' => [
                    'name' => 'Frais de livraison',
                ],
                'unit_amount'  => $shippingAmount,
            ],
            'quantity'   => 1,
        ];

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => $lineItems,
            'mode'                 => 'payment',
            'success_url'          => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url'           => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('/success-url', name: 'success_url')]
    public function successUrl(): Response
    {
        return $this->render('payment/success.html.twig');
    }

    #[Route('/cancel-url', name: 'cancel_url')]
    public function cancelUrl(): Response
    {
        return $this->render('payment/cancel.html.twig');
    }
}
