<?php

namespace App\Entity;

use App\Repository\SuiviRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SuiviRepository::class)]
class Suivi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Animal::class, inversedBy: "suivi", cascade: ["persist", "remove"])]
    #[ORM\JoinColumn(name: "id_animal", referencedColumnName: "id", onDelete: "CASCADE")]
    #[Assert\NotNull(message: "L'animal est obligatoire.")]
    private ?Animal $animal = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "La température ne peut pas être vide.")]
    #[Assert\Range(
        min: 30,
        max: 45,
        notInRangeMessage: "La température doit être comprise entre 30°C et 45°C."
    )]
    private ?float $temperature = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "Le rythme cardiaque ne peut pas être vide.")]
    #[Assert\Positive(message: "Le rythme cardiaque doit être un nombre positif.")]
    private ?float $rythme_cardiaque = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'état de l'animal est obligatoire.")]
    #[Assert\Choice(choices: ["Bon", "Moyen", "Critique"], message: "L'état doit être 'Bon', 'Moyen' ou 'Critique'.")]
    private ?string $etat = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "L'ID du client est obligatoire.")]
    #[Assert\Positive(message: "L'ID du client doit être un nombre positif.")]
    private ?int $id_client = null;

    #[ORM\ManyToOne(targetEntity: Veterinaire::class, inversedBy: 'suivis', cascade: ['persist'])]
    #[ORM\JoinColumn(name: "veterinaire_id", referencedColumnName: "id", nullable: false)]
    private ?Veterinaire $veterinaire = null; // Keep it singular
    // Getters and Setters


    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $analysis = null;

    #[ORM\ManyToOne(inversedBy: 'suivis')]
    private ?User $id_user = null;
    
    // Add getter and setter
    public function getAnalysis(): ?string
    {
        return $this->analysis;
    }
    
    public function setAnalysis(?string $analysis): self
    {
        $this->analysis = $analysis;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): self
    {
        $this->animal = $animal;
        return $this;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(?float $temperature): self
    {
        $this->temperature = $temperature;
        return $this;
    }

    public function getRythmeCardiaque(): ?float
    {
        return $this->rythme_cardiaque;
    }

    public function setRythmeCardiaque(?float $rythme_cardiaque): self
    {
        $this->rythme_cardiaque = $rythme_cardiaque;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

    public function getIdClient(): ?int
    {
        return $this->id_client;
    }

    public function setIdClient(int $id_client): self
    {
        $this->id_client = $id_client;
        return $this;
    }

    
    // Fix getter and setter names
    public function getVeterinaire(): ?Veterinaire
    {
        return $this->veterinaire;
    }
    
    public function setVeterinaire(?Veterinaire $veterinaire): self
    {
        $this->veterinaire = $veterinaire;
        return $this;
    }

    public function getIdUser(): ?user
    {
        return $this->id_user;
    }

    public function setIdUser(?user $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }
    
}
