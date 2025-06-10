<?php

namespace App\Entity;
use App\Enum\Climat;
use App\Enum\TypeSol;
use App\Repository\EtudeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: EtudeRepository::class)]
class Etude
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

  

    

    #[ORM\Column(type: Types::DATE_MUTABLE )]
    #[Assert\NotBlank(message:"peut pas etre vide")]

    private ?\DateTimeInterface $date_r = null;



    #[ORM\ManyToOne(inversedBy: 'etudes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'La culture est obligatoire.')]
    private ?Culture $culture = null;

    #[ORM\ManyToOne(inversedBy: 'etudes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'L\'expert est obligatoire.')]
    private ?Expert $expert = null;

    #[ORM\Column(enumType: Climat::class)]
    #[Assert\NotBlank(message: 'ne peut pas être vide')]
    private ?Climat $climat = null;

    #[ORM\Column(enumType: TypeSol::class)]
    #[Assert\NotBlank(message: 'ne peut pas être vide')]
    private ?TypeSol $type_sol = null;

    #[ORM\Column]
    private ?bool $irrigation = false;

    #[ORM\Column]
    private ?bool $fertilisation = false;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'obligatoire')]
    #[Assert\Type("numeric", message: 'doit être un nombre valide')]
    #[Assert\Positive(message: 'Le prix doit être un nombre positif')]
    private ?float $prix = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: ' est obligatoire')]
    #[Assert\Type("numeric", message: 'doit être un nombre valide')]
    #[Assert\Positive(message: ' doit être un nombre positif')]
    private ?float $rendement = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'obligatoires')]
    #[Assert\Type("numeric", message: ' doivent être un nombre valide')]
    #[Assert\Positive(message: ' doit être un nombre positif')]
    private ?float $precipitations = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: ' obligatoire')]
    #[Assert\Type("numeric", message: ' doit être un nombre valide')]
    #[Assert\Positive(message: ' doit être un nombre positif')]
    private ?float $main_oeuvre = null;

    #[ORM\ManyToOne(inversedBy: 'etudes')]
    private ?User $id_user = null;


    public function getId(): ?int
    {
        return $this->id;
    }


  

    public function getDateR(): ?\DateTimeInterface
    {
        return $this->date_r;
    }

    public function setDateR(\DateTimeInterface $date_r): self
    {
        $this->date_r = $date_r;

        return $this;
    }

    public function getCulture(): ?Culture
    {
        return $this->culture;
    }

    public function setCulture(?Culture $culture): static
    {
        $this->culture = $culture;

        return $this;
    }

    public function getExpert(): ?Expert
    {
        return $this->expert;
    }

    public function setExpert(?Expert $expert): static
    {
        $this->expert = $expert;

        return $this;
    }

    public function getClimat(): ?Climat
    {
        return $this->climat;
    }

    public function setClimat(Climat $climat): static
    {
        $this->climat = $climat;

        return $this;
    }

    public function getTypeSol(): ?TypeSol
    {
        return $this->type_sol;
    }

    public function setTypeSol(TypeSol $type_sol): static
    {
        $this->type_sol = $type_sol;

        return $this;
    }

    public function isIrrigation(): ?bool
    {
        return $this->irrigation;
    }

    public function setIrrigation(bool $irrigation): static
    {
        $this->irrigation = $irrigation;

        return $this;
    }

    public function isFertilisation(): ?bool
    {
        return $this->fertilisation;
    }

    public function setFertilisation(bool $fertilisation): static
    {
        $this->fertilisation = $fertilisation;

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

    public function getRendement(): ?float
    {
        return $this->rendement;
    }

    public function setRendement(float $rendement): static
    {
        $this->rendement = $rendement;

        return $this;
    }

    public function getPrecipitations(): ?float
    {
        return $this->precipitations;
    }

    public function setPrecipitations(float $precipitations): static
    {
        $this->precipitations = $precipitations;

        return $this;
    }

    public function getMainOeuvre(): ?float
    {
        return $this->main_oeuvre;
    }

    public function setMainOeuvre(float $main_oeuvre): static
    {
        $this->main_oeuvre = $main_oeuvre;

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
