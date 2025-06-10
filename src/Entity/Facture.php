<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

#[ORM\Entity]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $datef = null;

    #[ORM\Column(type: "float")]
    #[Assert\NotBlank(message: "Le prix total ne peut pas être vide.")]
    #[Assert\Positive(message: "Le prix total doit être un nombre positif.")]
    private ?float $prixt = null;

    #[ORM\ManyToOne(targetEntity: Abonnement::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Abonnement $abonnement = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Le CIN ne peut pas être vide.")]
    #[Assert\Regex(pattern: "/^\d{8}$/", message: "Le CIN doit contenir exactement 8 chiffres.")]
    private ?int $cin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'email ne peut pas être vide.")]
    #[Assert\Email(message: "Veuillez entrer un email valide.")]
    private ?string $email = null;

    public function __construct()
    {
        $this->datef = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatef(): ?\DateTimeInterface
    {
        return $this->datef;
    }

    public function setDatef(\DateTimeInterface $datef): self
    {
        $this->datef = $datef;
        return $this;
    }

    public function getPrixt(): ?float
    {
        return $this->prixt;
    }

    public function setPrixt(float $prixt): self
    {
        $this->prixt = $prixt;
        return $this;
    }

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(int $cin): self
    {
        $this->cin = $cin;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getAbonnement(): ?Abonnement
    {
        return $this->abonnement;
    }

    public function setAbonnement(Abonnement $abonnement): self
    {
        $this->abonnement = $abonnement;
        $this->prixt = $abonnement->getPrix();
        return $this;
    }
}
