<?php

namespace App\Entity;
use App\Entity\User;
use App\Enum\BensoinsEngrais;
use App\Repository\CultureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: CultureRepository::class)]
class Culture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'ne peut pas être vide')]
    #[Assert\Length(max: 255, maxMessage: 'ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'ne peut pas être vide')]
    #[Assert\Positive(message: ' doit être un nombre positif')]
    private ?float $surface = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: ' ne peut pas être vide')]
    private ?\DateTimeInterface $date_plantation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: ' ne peut pas être vide')]
    private ?\DateTimeInterface $date_recolte = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: ' ne peut pas être vide')]
    private ?string $region = null;

    #[ORM\Column(length: 25)]
    #[Assert\NotBlank(message: 'ne peut pas être vide')]
    private ?string $type_culture = null;

    /**
     * @var Collection<int, Etude>
     */
    #[ORM\OneToMany(targetEntity: Etude::class, mappedBy: 'culture', cascade: ['remove'])]
    private Collection $etudes;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'ne peut pas être vide')]
    #[Assert\Positive(message: ' doit être un nombre positif')]
    private ?float $densite_plantation = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'ne peut pas être vide')]
    #[Assert\Positive(message: ' doit être un nombre positif')]
    private ?float $besoins_eau = null;

    #[ORM\Column(enumType: BensoinsEngrais::class)]
    #[Assert\NotBlank(message: 'ne peut pas être vide')]
    private ?BensoinsEngrais $besoins_engrais = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: ' ne peut pas être vide')]
    #[Assert\Positive(message: ' doit être un nombre positif')]
    private ?float $rendement_moyen = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'ne peut pas être vide')]
    #[Assert\Positive(message: ' doit être un nombre positif')]
    private ?float $cout_moyen = null;

    #[ORM\ManyToOne(inversedBy: 'cultures')]
    private ?user $id_user = null;

    

    public function __construct()
    {
        $this->etudes = new ArrayCollection();
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

    public function getSurface(): ?float
    {
        return $this->surface;
    }

    public function setSurface(float $surface): static
    {
        $this->surface = $surface;

        return $this;
    }

    public function getDatePlantation(): ?\DateTimeInterface
    {
        return $this->date_plantation;
    }

    public function setDatePlantation(\DateTimeInterface $date_plantation): static
    {
        $this->date_plantation = $date_plantation;

        return $this;
    }

    public function getDateRecolte(): ?\DateTimeInterface
    {
        return $this->date_recolte;
    }

    public function setDateRecolte(\DateTimeInterface $date_recolte): static
    {
        $this->date_recolte = $date_recolte;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getTypeCulture(): ?string
    {
        return $this->type_culture;
    }

    public function setTypeCulture(string $type_culture): static
    {
        $this->type_culture = $type_culture;

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
            $etude->setCulture($this);
        }

        return $this;
    }

    public function removeEtude(Etude $etude): static
    {
        if ($this->etudes->removeElement($etude)) {
            // set the owning side to null (unless already changed)
            if ($etude->getCulture() === $this) {
                $etude->setCulture(null);
            }
        }

        return $this;
    }

    public function getDensitePlantation(): ?float
    {
        return $this->densite_plantation;
    }

    public function setDensitePlantation(float $densite_plantation): static
    {
        $this->densite_plantation = $densite_plantation;

        return $this;
    }

    public function getBesoinsEau(): ?float
    {
        return $this->besoins_eau;
    }

    public function setBesoinsEau(float $besoins_eau): static
    {
        $this->besoins_eau = $besoins_eau;

        return $this;
    }

    public function getBesoinsEngrais(): ?BensoinsEngrais
    {
        return $this->besoins_engrais;
    }

    public function setBesoinsEngrais(BensoinsEngrais $besoins_engrais): static
    {
        $this->besoins_engrais = $besoins_engrais;

        return $this;
    }

    public function getRendementMoyen(): ?float
    {
        return $this->rendement_moyen;
    }

    public function setRendementMoyen(float $rendement_moyen): static
    {
        $this->rendement_moyen = $rendement_moyen;

        return $this;
    }

    public function getCoutMoyen(): ?float
    {
        return $this->cout_moyen;
    }

    public function setCoutMoyen(float $cout_moyen): static
    {
        $this->cout_moyen = $cout_moyen;

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
