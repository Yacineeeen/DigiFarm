<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type est obligatoire.")]
    private ?string $type = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "L'âge est obligatoire.")]
    #[Assert\Positive(message: "L'âge doit être un nombre positif.")]
    private ?int $age = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le poids est obligatoire.")]
    #[Assert\Positive(message: "Le poids doit être un nombre positif.")]
    private ?float $poids = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 50,
        maxMessage: "La race ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $race = null;

    #[ORM\OneToOne(mappedBy: "animal", targetEntity: Suivi::class, cascade: ["persist", "remove"])]
    private ?Suivi $suivi = null;

    #[ORM\ManyToOne(inversedBy: 'animals')]
    private ?User $id_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;
        return $this;
    }

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(float $poids): static
    {
        $this->poids = $poids;
        return $this;
    }

    public function getRace(): ?string
    {
        return $this->race;
    }

    public function setRace(?string $race): static
    {
        $this->race = $race;
        return $this;
    }

    public function getSuivi(): ?Suivi
    {
        return $this->suivi;
    }

    public function setSuivi(?Suivi $suivi): static
    {
        $this->suivi = $suivi;
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
