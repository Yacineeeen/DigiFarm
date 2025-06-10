<?php

namespace App\Entity;
use Doctrine\DBAL\Types\Types;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\UserRole;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le nom est obligatoire.")]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le prénom est obligatoire.")]
    private ?string $Prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"L'adresse e-mail est obligatoire.")]
    #[Assert\Email(message: "Veuillez entrer une adresse e-mail valide.")]
    private ?string $AdresseMail = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le mot de passe est obligatoire.")]
    #[Assert\Length(min: 6, minMessage: "Le mot de passe doit contenir au moins 6 caractères.")]
    private ?string $Password = null;

    #[ORM\Column(type: 'string', enumType: UserRole::class)]
    #[Assert\NotBlank(message:"Le rôle est obligatoire.")]
    private UserRole $Role;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $resetToken = null;
    
    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }
    
    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }

    /**
     * @var Collection<int, Culture>
     */
    #[ORM\OneToMany(targetEntity: Culture::class, mappedBy: 'id_user')]
    private Collection $cultures;

    /**
     * @var Collection<int, Etude>
     */
    #[ORM\OneToMany(targetEntity: Etude::class, mappedBy: 'id_user')]
    private Collection $etudes;

    /**
     * @var Collection<int, Animal>
     */
    #[ORM\OneToMany(targetEntity: Animal::class, mappedBy: 'id_user')]
    private Collection $animals;

    /**
     * @var Collection<int, Suivi>
     */
    #[ORM\OneToMany(targetEntity: Suivi::class, mappedBy: 'id_user')]
    private Collection $suivis;

   

    /**
     * @var Collection<int, Machine>
     */
    #[ORM\OneToMany(targetEntity: Machine::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $machines;
/**
     * @var Collection<int, produit>
     */
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'id_user')]
    private Collection $produits;
    

    

    public function __construct()
    {
        $this->cultures = new ArrayCollection();
        $this->etudes = new ArrayCollection();
        $this->suivis = new ArrayCollection();
        $this->animals = new ArrayCollection();
        $this->machines = new ArrayCollection();
        $this->produits = new ArrayCollection();
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): static
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function getAdresseMail(): ?string
    {
        return $this->AdresseMail;
    }

    public function setAdresseMail(string $AdresseMail): static
    {
        $this->AdresseMail = $AdresseMail;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->Password;
    }

    public function setPassword(string $Password): static
    {
        $this->Password = $Password;

        return $this;
    }

    public function getRole(): UserRole
    {
        return $this->Role;
    }

    public function setRole(UserRole $Role): self
    {
        $this->Role = $Role;
        return $this;
    }

    /**
     * @return Collection<int, Culture>
     */
    public function getCultures(): Collection
    {
        return $this->cultures;
    }

    public function addCulture(Culture $culture): static
    {
        if (!$this->cultures->contains($culture)) {
            $this->cultures->add($culture);
            $culture->setIdUser($this);
        }

        return $this;
    }

    public function removeCulture(Culture $culture): static
    {
        if ($this->cultures->removeElement($culture)) {
            // set the owning side to null (unless already changed)
            if ($culture->getIdUser() === $this) {
                $culture->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Etude>
     */
    public function getEtudes(): Collection
    {
        return $this->etudes;
    }

    public function addEtude(Etude $etude): static
    {
        if (!$this->etudes->contains($etude)) {
            $this->etudes->add($etude);
            $etude->setIdUser($this);
        }

        return $this;
    }

    public function removeEtude(Etude $etude): static
    {
        if ($this->etudes->removeElement($etude)) {
            // set the owning side to null (unless already changed)
            if ($etude->getIdUser() === $this) {
                $etude->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Animal>
     */
    public function getAnimals(): Collection
    {
        return $this->animals;
    }

    public function addAnimal(Animal $animal): static
    {
        if (!$this->animals->contains($animal)) {
            $this->animals->add($animal);
            $animal->setIdUser($this);
        }

        return $this;
    }

    public function removeAnimal(Animal $animal): static
    {
        if ($this->animals->removeElement($animal)) {
            // set the owning side to null (unless already changed)
            if ($animal->getIdUser() === $this) {
                $animal->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Suivi>
     */
    public function getSuivis(): Collection
    {
        return $this->suivis;
    }

    public function addSuivi(Suivi $suivi): static
    {
        if (!$this->suivis->contains($suivi)) {
            $this->suivis->add($suivi);
            $suivi->setIdUser($this);
        }

        return $this;
    }

    public function removeSuivi(Suivi $suivi): static
    {
        if ($this->suivis->removeElement($suivi)) {
            // set the owning side to null (unless already changed)
            if ($suivi->getIdUser() === $this) {
                $suivi->setIdUser(null);
            }
        }

        return $this;
    }
     /**
     * @return Collection<int, Machine>
     */
    public function getMachines(): Collection
    {
        return $this->machines;
    }

    public function addMachine(Machine $machine): static
    {
        if (!$this->machines->contains($machine)) {
            $this->machines->add($machine);
            $machine->setOwner($this);
        }

        return $this;
    }

    public function removeMachine(Machine $machine): static
    {
        if ($this->machines->removeElement($machine)) {
            // set the owning side to null (unless already changed)
            if ($machine->getOwner() === $this) {
                $machine->setOwner(null);
            }
        }

        return $this;
    }
   
    
    
}




