<?php

namespace App\Entity;

use App\Repository\TechnicienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TechnicienRepository::class)]
class Technicien
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The name cannot be empty.')]
    #[Assert\Length(
        max: 50,
        maxMessage: 'The name cannot be longer than {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Z][a-zA-Z]*$/',
        message: 'The name should start with an uppercase letter and contain only alphabetic characters.'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The first name cannot be empty.')]
    #[Assert\Length(
        max: 50,
        maxMessage: 'The first name cannot be longer than {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Z][a-zA-Z]*$/',
        message: 'The first name should start with an uppercase letter and contain only alphabetic characters.'
    )]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The specialty cannot be empty.')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'The specialty cannot be longer than {{ limit }} characters.'
    )]
    private ?string $specialite = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The email cannot be empty.')]
    #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'The email cannot be longer than {{ limit }} characters.'
    )]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'The telephone number cannot be empty.')]
    #[Assert\Positive(message: 'The telephone number must be a positive number.')]
    #[Assert\Length(
        min: 8,
        max: 8,
        exactMessage: 'The telephone number must be exactly {{ limit }} digits long.'
    )]
    #[Assert\Regex(
        pattern: '/^\d{8}$/',
        message: 'The telephone number must contain exactly 8 digits.'
    )]
    private ?int $telephone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Please enter a location.')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'The location cannot be longer than {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s\',.-]*$/',
        message: 'The location can only contain letters, numbers, spaces, commas, periods, or hyphens.'
    )]
    private ?string $localisation = null;

    #[ORM\Column(type: 'float', nullable: true)]
    
    private ?float $latitude = null;
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;

    /**
     * @var Collection<int, Maintenance>
     */
    #[ORM\OneToMany(targetEntity: Maintenance::class, mappedBy: 'idTechnicien', cascade: ['remove'])]
    private Collection $Maintenance;

    public function __construct()
    {
        $this->Maintenance = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): static
    {
        $this->specialite = $specialite;

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

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

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
            $maintenance->setIdTechnicien($this);
        }

        return $this;
    }

    public function removeMaintenance(Maintenance $maintenance): static
    {
        if ($this->Maintenance->removeElement($maintenance)) {
            // set the owning side to null (unless already changed)
            if ($maintenance->getIdTechnicien() === $this) {
                $maintenance->setIdTechnicien(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name . ' ' . $this->prenom;
    }
}
