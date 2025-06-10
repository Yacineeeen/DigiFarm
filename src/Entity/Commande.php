<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]  
    #[Assert\NotBlank(message: "Le statut de la commande est requis.")]
    #[Assert\Choice(choices: ['en_cours', 'validée', 'livrée', 'annulée'], message: "Statut invalide.")]
    private ?string $statut = 'En cours';

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le montant total est obligatoire.")]
    #[Assert\Positive(message: "Le montant total doit être un nombre positif.")]
    private ?float $montantTotal = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date de commande est requise.")]
    #[Assert\LessThanOrEqual('today', message: "La date de commande ne peut pas être dans le futur.")]
    private ?\DateTimeInterface $dateCommande = null;


    /**
     * @var Collection<int, CommandeDetail>
     */
    #[ORM\OneToMany(targetEntity: CommandeDetail::class, mappedBy: 'commande')]
    private Collection $commandeDetails;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: Paiement::class, orphanRemoval: true)]
    private Collection $paiements;


    public function __construct()
    {
        $this->commandeProduits = new ArrayCollection();
        $this->commandeDetails = new ArrayCollection();
        $this->paiements = new ArrayCollection();
    }


    


    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): static
    {
        $this->dateCommande = $dateCommande;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    /**
     * @return Collection<int, CommandeProduit>
     */
    public function getCommandeProduits(): Collection
    {
        return $this->commandeProduits;
    }

    public function addCommandeProduit(CommandeProduit $commandeProduit): static
    {
        if (!$this->commandeProduits->contains($commandeProduit)) {
            $this->commandeProduits[] = $commandeProduit;
            $commandeProduit->setCommande($this);
        }

        return $this;
    }

    public function removeCommandeProduit(CommandeProduit $commandeProduit): static
    {
        if ($this->commandeProduits->removeElement($commandeProduit)) {
            if ($commandeProduit->getCommande() === $this) {
                $commandeProduit->setCommande(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CommandeDetail>
     */
    public function getCommandeDetails(): Collection
    {
        return $this->commandeDetails;
    }

    public function getPaiements(): Collection
    {
    return $this->paiements;
    }

    public function addCommandeDetail(CommandeDetail $commandeDetail): static
    {
        if (!$this->commandeDetails->contains($commandeDetail)) {
            $this->commandeDetails->add($commandeDetail);
            $commandeDetail->setCommande($this);
        }

        return $this;
    }

    public function removeCommandeDetail(CommandeDetail $commandeDetail): static
    {
        if ($this->commandeDetails->removeElement($commandeDetail)) {
            // set the owning side to null (unless already changed)
            if ($commandeDetail->getCommande() === $this) {
                $commandeDetail->setCommande(null);
            }
        }

        return $this;
    }
}
