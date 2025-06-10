<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'paiements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(message: "Le montant du paiement est obligatoire.")]
    #[Assert\Positive(message: "Le montant du paiement doit être positif.")]
    private ?float $montant = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: "La méthode de paiement est obligatoire.")]
    #[Assert\Choice(choices: ['stripe', 'paypal', 'carte bancaire'], message: "Méthode de paiement invalide.")]
    private ?string $methodePaiement = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank(message: "L'identifiant de transaction est requis.")]
    private ?string $transactionId = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(choices: ['en_attente', 'payé', 'échoué'], message: "Statut de paiement invalide.")]
    private string $statut = 'en_attente';

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: "La date de paiement est requise.")]
    private ?\DateTimeInterface $datePaiement = null;

    // Getters et setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;
        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;
        return $this;
    }

    public function getMethodePaiement(): ?string
    {
        return $this->methodePaiement;
    }

    public function setMethodePaiement(string $methodePaiement): self
    {
        $this->methodePaiement = $methodePaiement;
        return $this;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function setTransactionId(string $transactionId): self
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(\DateTimeInterface $datePaiement): self
    {
        $this->datePaiement = $datePaiement;
        return $this;
    }
}
