<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\GeolocationService;

class MapController extends AbstractController
{
    #[Route('/map', name: 'map')]
    public function map(): Response
    {
        return $this->render('map/index.html.twig');
    }
}