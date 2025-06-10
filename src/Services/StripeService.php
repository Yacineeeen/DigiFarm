<?php
namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeService
{
    private $stripeSecretKey;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripeSecretKey = $stripeSecretKey;
        Stripe::setApiKey($this->stripeSecretKey);
    }

    public function createCheckoutSession($commande): string
    {
        $session = Session::create([
            'paiement_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Commande #' . $commande->getId(),
                    ],
                    'unit_amount' => $commande->getMontantTotal() * 100, // Convertir en centimes
                ],
                'quantity' => 1,
            ]],
            'mode' => 'paiement',
            'success_url' => 'http://127.0.0.1:8000/paiement/success',
            'cancel_url' => 'http://127.0.0.1:8000/paiement/cancel',
        ]);

        return $session->url;
    }
}
