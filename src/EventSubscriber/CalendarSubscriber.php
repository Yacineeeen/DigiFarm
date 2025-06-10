<?php

namespace App\EventSubscriber;

use CalendarBundle\Entity\Event;
use CalendarBundle\Event\SetDataEvent;
use App\Repository\CultureRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CalendarSubscriber implements EventSubscriberInterface
{
    private CultureRepository $cultureRepository;
    private RequestStack $requestStack;

    public function __construct(CultureRepository $cultureRepository, RequestStack $requestStack)
    {
        $this->cultureRepository = $cultureRepository;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
        return [
            SetDataEvent::class => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(SetDataEvent $setDataEvent)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return;
        }

        $session = $request->hasSession() ? $request->getSession() : null;
        if (!$session || !$session->has('user_id')) {
            return;
        }
        
        $userId = $session->get('user_id');
        
        // Fetch user's cultures
        $cultures = $this->cultureRepository->findCulturesByUser($userId);

        if (!$cultures) {
            error_log("🚨 Aucun événement trouvé pour l'utilisateur ID: " . $userId);
            return;
        }
        foreach ($cultures as $culture) {
            error_log("🔎 Processing Culture: " . $culture['nom']);
        
            if (!empty($culture['datePlantation'])) {
                error_log("📅 Adding Plantation Event: " . $culture['datePlantation']);
        
                $setDataEvent->addEvent(new Event(
                    '🌱 Plantation - ' . $culture['nom'],
                    $culture['datePlantation'],
                    null,
                    [
                        'color' => '#28a745',
                        'description' => "Surface: {$culture['surface']} ha\nRégion: {$culture['region']}\nType: {$culture['type_culture']}"
                    ]
                ));
            } else {
                error_log("🚨 No Plantation Date for: " . $culture['nom']);
            }
            if (!empty($culture['dateRecolte'])) {
                $setDataEvent->addEvent(new Event(
                    '🌾 Récolte - ' . $culture['nom'],
                    $culture['dateRecolte'],
                    null,
                    [
                        'color' => '#dc3545',
                        'description' => "📍 Surface: {$culture['surface']} ha\n🏡 Région: {$culture['region']}\n🌱 Type: {$culture['type_culture']}\n🌊 Besoins Eau: {$culture['besoins_eau']} L\n🌾 Rendement: {$culture['rendement_moyen']} kg\n💰 Coût Moyen: {$culture['cout_moyen']} €"
                    ]
                ));
            }
            
        }
    }
}