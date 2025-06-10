<?php

namespace App\Entity;

use App\Repository\MachineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\EtatEquipement;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MachineRepository::class)]
class Machine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The name cannot be empty.')]
    #[Assert\Length(
        max: 20,
        maxMessage: 'The name cannot be longer than {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Z][a-zA-Z]*$/',
        message: 'The name should start with an uppercase letter and contain only alphabetic characters.'
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The type cannot be empty.')]
    #[Assert\Length(
        max: 20,
        maxMessage: 'The type cannot be longer than {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Z][a-zA-Z]*$/',
        message: 'The type should start with an uppercase letter and contain only alphabetic characters.'
    )]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'The purchase date cannot be empty.')]
    #[Assert\LessThanOrEqual('today', message: 'The purchase date cannot be in the future.')]
    private ?\DateTimeInterface $date_achat = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 25,
        maxMessage: 'The previous condition cannot be longer than {{ limit }} characters.'
    )]
    private ?string $etat_pred = null;

    /**
     * @var Collection<int, Maintenance>
     */
    #[ORM\OneToMany(targetEntity: Maintenance::class, mappedBy: 'idMachine', cascade: ['remove'])]
    private Collection $Maintenance;

    #[ORM\Column(enumType: EtatEquipement::class)]
    #[Assert\NotBlank(message: 'The condition cannot be empty.')]
    private ?EtatEquipement $etat = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'machines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;


    public function __construct()
    {
        $this->Maintenance = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->date_achat;
    }

    public function setDateAchat(\DateTimeInterface $date_achat): static
    {
        $this->date_achat = $date_achat;

        return $this;
    }

    public function getEtatPred(): ?string
    {
        return $this->etat_pred;
    }

    public function setEtatPred(?string $etat_pred): static
    {
        $this->etat_pred = $etat_pred;

        return $this;
    }

    /**
     * @return Collection<int, Maintenance>
     */
    public function getMaintenance(): Collection
    {
        return $this->Maintenance;
    }

    public function addMaintenance(Maintenance $maintenance): static
    {
        if (!$this->Maintenance->contains($maintenance)) {
            $this->Maintenance->add($maintenance);
            $maintenance->setIdMachine($this);
        }

        return $this;
    }

    public function removeMaintenance(Maintenance $maintenance): static
    {
        if ($this->Maintenance->removeElement($maintenance)) {
            // set the owning side to null (unless already changed)
            if ($maintenance->getIdMachine() === $this) {
                $maintenance->setIdMachine(null);
            }
        }

        return $this;
    }

    public function getEtat(): ?EtatEquipement
    {
        return $this->etat;
    }

    public function setEtat(EtatEquipement $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
//Represent database tables as PHP classes
//Doctrine ORM: Manages database interactions.

//EntityManager: Handles persistence (e.g., persist (preparesthe entity ), flush(writes the changes to the database.), remove).

//Repository: Provides methods for querying the database (e.g., findAll, find).
//twig est un moteur de template pour PHP
//repository provide methed for quering(eg findAll,find)