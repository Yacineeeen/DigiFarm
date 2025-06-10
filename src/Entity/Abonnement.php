<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AbonnementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "L'identifiant ne peut pas être vide.")]
    private ?int $idc = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide.')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le prénom ne peut pas être vide.')]
    #[Assert\Length(max: 255, maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $prenom = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le numéro de téléphone ne peut pas être vide.')]
    #[Assert\Regex(pattern: "/^\d{8}$/", message: 'Le numéro de téléphone doit être composé de 8 chiffres.')]
    private ?int $numero = null;

    #[ORM\Column(length: 255)]
    private ?string $typeabb = null;

    #[ORM\Column]
    private ?int $dureeabb = null;

    #[ORM\Column]
    private ?float $prix = null;

    /**
     * @var Collection<int, Facture>
     */
    #[ORM\OneToMany(targetEntity: Facture::class, mappedBy: 'abonnement', cascade: ['remove'])]
    private Collection $factures;

    public function __construct()
    {
        $this->factures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdc(): ?int
    {
        return $this->idc;
    }

    public function setIdc(int $idc): static
    {
        $this->idc = $idc;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getTypeabb(): ?string
    {
        return $this->typeabb;
    }

    public function setTypeabb(string $typeabb): static
    {
        $this->typeabb = $typeabb;

        return $this;
    }

    public function getDureeabb(): ?int
    {
        return $this->dureeabb;
    }

    public function setDureeabb(int $dureeabb): static
    {
        $this->dureeabb = $dureeabb;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, Facture>
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    

    
    

    public function calculerPrix(): void
    {
        $prixParMois = match ($this->getTypeabb()) {
            'bronze' => 10.0,
            'silver' => 15.0,
            'gold' => 20.0,
            default => 10.0
        };

        $multiplicateur = match ($this->getDureeabb()) {
            1 => 1,
            6 => 6,
            12 => 12,
            default => 1
        };

        $this->prix = $prixParMois * $multiplicateur;
    }
    

}
