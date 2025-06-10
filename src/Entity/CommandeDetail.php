<?php

namespace App\Entity;

use App\Repository\CommandeDetailRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Commande;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;



#[ORM\Entity(repositoryClass: CommandeDetailRepository::class)]
class CommandeDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $prixUnitaire = null;

    #[ORM\Column]
    private ?float $montantTotal = null;

    #[ORM\Column(type: 'integer')]
    private int $quantite; 
    
    //***//

    #[ORM\ManyToOne(targetEntity: Commande::class,inversedBy: 'commandeDetail')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'commandeDetail')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    //***//

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getQuantite(): int
    {
        return $this->quantite;
    }
    public function getCommande(): ?commande
    {
        return $this->commande;
    }
    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }
    public function setCommande(?commande $commande): static
    {
        $this->commande = $commande;

        return $this;
    }

    public function getProduit(): ?produit
    {
        return $this->produit;
    }

   public function setProduit(?produit $produit): static
     {
        $this->produit = $produit;

        return $this;
    }


    public function getPrixUnitaire(): ?float
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(float $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    public function getMontantTotal(): ?float
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(float $montantTotal): static
    {
        $this->montantTotal = $montantTotal;

        return $this;
    }
}
