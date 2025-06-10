<?php

namespace App\Controller;
use TCPDF;
use App\Entity\Commande;
use App\Entity\Produit;
use App\Entity\CommandeDetail;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\StatistiquesVentesType;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\PdfGeneratorService;



#[Route('/commande')]
final class CommandeController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }
    
    #[Route('/list', name: 'app_commande_gestion', methods: ['GET'])]
    public function indexBack(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/List.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }
   
    #[Route('/statistiques', name: 'statistiques')]
   public function statistiques(Request $request, CommandeRepository $commandeRepository, PaginatorInterface $paginator): Response
   {
    // Création et gestion du formulaire
    $form = $this->createForm(StatistiquesVentesType::class);
    $form->handleRequest($request);

    // Récupération des dates depuis le formulaire ou la requête GET
    $startDate = $this->getDateFromRequestOrForm($request, $form, 'start_date');
    $endDate = $this->getDateFromRequestOrForm($request, $form, 'end_date');

    // Initialisation des variables
    $pagination = null;
    $ventesParJour = [];

    if ($startDate && $endDate) {
        // Création de la requête avec QueryBuilder
        $query = $commandeRepository->createQueryBuilder('c')
            ->where('c.dateCommande BETWEEN :start AND :end')
            ->setParameter('start', $startDate->format('Y-m-d'))
            ->setParameter('end', $endDate->format('Y-m-d'))
            ->orderBy('c.dateCommande', 'ASC')
            ->getQuery();

        // Pagination des résultats
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10,
            ['pageParameterName' => 'page', 'defaultSortFieldName' => 'c.dateCommande', 'defaultSortDirection' => 'asc']
        );

        // Calcul des ventes par jour
        foreach ($pagination->getItems() as $commande) {
            $day = $commande->getDateCommande()->format('Y-m-d');
            $ventesParJour[$day] = ($ventesParJour[$day] ?? 0) + $commande->getMontantTotal();
        }
    }
    dump(array_keys($ventesParJour), array_values($ventesParJour));

    // Affichage de la vue avec les données
    return $this->render('commande/statistiques.html.twig', [
        'form' => $form->createView(),
        'pagination' => $pagination,
        'dates' => array_keys($ventesParJour),
        'montants' => array_values($ventesParJour),
        'start_date' => $startDate ? $startDate->format('Y-m-d') : null,
        'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
    ]);
    }

  /**
  * Récupère une date depuis le formulaire ou la requête GET.
  */
  private function getDateFromRequestOrForm(Request $request, $form, string $fieldName): ?\DateTime
  {
    return $form->get($fieldName)->getData()
        ?? ($request->query->get($fieldName) ? new \DateTime($request->query->get($fieldName)) : null);
  }

    #[Route('/export/pdf', name: 'export_sales_pdf')]
    public function exportSalesPdf(Request $request, CommandeRepository $commandeRepository,PdfGeneratorService $pdfGeneratorService): Response
    {
        {
            $startDate = new \DateTime($request->query->get('start_date'));
            $endDate = new \DateTime($request->query->get('end_date'));
        
            $commandes = $commandeRepository->findByPeriod($startDate, $endDate);
        
            // Utilisation du service pour générer le PDF
            $pdf = $pdfGeneratorService->generateSalesReport($commandes, $startDate, $endDate);
        
            // Génération du PDF et sortie
            $pdf->Output('sales_report.pdf', 'I');
            
            return new Response('PDF généré avec succès');
        }
    }

    #[Route(name: 'commande_page', methods: ['GET', 'POST'])]
    public function commander(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produitIds = $request->request->all('produits');
        
        if (empty($produitIds)) {
            return $this->redirectToRoute('app_produit_index');
        }
        
        $produits = $entityManager->getRepository(Produit::class)->findBy(['id' => $produitIds]);
        
        return $this->render('commande/commande.html.twig', [
            'produits' => $produits,
            // 'commande' => new Commande(),
        ]);
    }

    #[Route('/commande/finaliser', name: 'finaliser_commande', methods: ['POST'])]
    public function finaliserCommande(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produitIds = $request->request->all('produits');
        $quantites = $request->request->all('quantites');
    
        if (empty($produitIds)) {
            return $this->redirectToRoute('app_produit_index');
        }

        $produits = $entityManager->getRepository(Produit::class)->findBy(['id' => $produitIds]);
        $commande = new Commande();
        $commande->setDateCommande(new \DateTime());
        $commande->setMontantTotal(0);
        $entityManager->persist($commande);
        $entityManager->flush();
    
        $total = 0;
    
        foreach ($produits as $produit) {
            if (!$produit) {
                throw new \Exception("Produit non trouvé !");
            }
    
            $quantity = isset($quantites[$produit->getId()]) ? (int)$quantites[$produit->getId()] : 1;
            $subtotal = $produit->getPrix() * $quantity;
    
            $commandeDetail = new CommandeDetail();
            $commandeDetail->setCommande($commande);
            $commandeDetail->setProduit($produit);
            $commandeDetail->setQuantite($quantity);
            $commandeDetail->setPrixUnitaire($produit->getPrix());
            $commandeDetail->setMontantTotal($subtotal);
    
            $entityManager->persist($commandeDetail);
            $total += $subtotal;
        }
    
        $commande->setMontantTotal($total);
        $entityManager->flush();
    
        return $this->redirectToRoute('checkout',['id' => $commande->getId()]);
        // return $this->render('commande/commande.html.twig', [
        //     'commande' => $commande,  // Passez la commande avec son ID
        // ]);
    }

    #[Route('/commande/{id}/payer', name: 'commande_payer')]
     public function payer(Commande $commande, PaiementController $paiementController): Response
    {
        if (!$commande) {
             throw $this->createNotFoundException('Commande non trouvée');
        }
       
        return $paiementController->checkout($commande);
    }

}
