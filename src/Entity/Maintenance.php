<?php

namespace App\Entity;

use App\Enum\StatutMaintenance;
use App\Repository\MaintenanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MaintenanceRepository::class)]
class Maintenance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'The purchase date cannot be empty.')]
    #[Assert\LessThanOrEqual('today', message: 'The maintenance date cannot be in the future.')]
    private ?\DateTimeInterface $dateEntretien = null;


    #[ORM\Column]
    #[Assert\NotBlank(message: 'The cost cannot be empty.')]
    #[Assert\Positive(message: 'The cost must be a positive number.')]
    #[Assert\Range(
        max: 1000000,
        maxMessage: 'The cost cannot exceed {{ max }}.'
    )]
    private ?float $cout = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(
        min: -50,
        max: 50,
        notInRangeMessage: 'The temperature must be between {{ min }}°C and {{ max }}°C.'
    )]
    private ?int $temperature = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(
    min: 0,
    max: 100,
    notInRangeMessage: 'The humidity must be between {{ min }}% and {{ max }}%.'
    )]
    private ?int $humidite = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: 'The fuel consumption must be zero or a positive number.')]
    #[Assert\Range(
    max: 1000,
    maxMessage: 'The fuel consumption cannot exceed {{ max }} liters.'
    )]
    private ?float $consoCarburant = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: 'The energy consumption must be zero or a positive number.')]
    #[Assert\Range(
    max: 10000,
    maxMessage: 'The energy consumption cannot exceed {{ max }} kWh.'
    )]
    private ?float $consoEnergie = null;

    #[ORM\Column(enumType: StatutMaintenance::class)]
    #[Assert\NotBlank(message: 'The status cannot be empty.')]
    #[Assert\Type(type: StatutMaintenance::class, message: 'The status must be a valid maintenance status.')]
    private ?StatutMaintenance $Status = null;

    #[ORM\ManyToOne(inversedBy: 'Maintenance')]
    #[ORM\JoinColumn(nullable: false)] 
    private ?Machine $idMachine = null;

    // src/Entity/Maintenance.php
#[ORM\ManyToOne(targetEntity: Technicien::class, inversedBy: 'maintenances')]
#[ORM\JoinColumn(name: 'id_technicien_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
private ?Technicien $idTechnicien = null;
// src/Entity/Maintenance.php
#[ORM\Column(type: Types::STRING, nullable: true)]
private ?string $etatPred = null; // Add this line
   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getDateEntretien(): ?\DateTimeInterface
    {
        return $this->dateEntretien;
    }

    public function setDateEntretien(\DateTimeInterface $dateEntretien): static
    {
        $this->dateEntretien = $dateEntretien;

        return $this;
    }

    public function getCout(): ?float
    {
        return $this->cout;
    }

    public function setCout(float $cout): static
    {
        $this->cout = $cout;

        return $this;
    }

    public function getTemperature(): ?int
    {
        return $this->temperature;
    }

    public function setTemperature(?int $temperature): static
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getHumidite(): ?int
    {
        return $this->humidite;
    }

    public function setHumidite(?int $humidite): static
    {
        $this->humidite = $humidite;

        return $this;
    }

    public function getConsoCarburant(): ?float
    {
        return $this->consoCarburant;
    }

    public function setConsoCarburant(?float $consoCarburant): static
    {
        $this->consoCarburant = $consoCarburant;

        return $this;
    }

    public function getConsoEnergie(): ?float
    {
        return $this->consoEnergie;
    }

    public function setConsoEnergie(?float $consoEnergie): static
    {
        $this->consoEnergie = $consoEnergie;

        return $this;
    }

    public function getStatus(): ?StatutMaintenance
    {
        return $this->Status;
    }

    public function setStatus(StatutMaintenance $Status): static
    {
        $this->Status = $Status;

        return $this;
    }

    public function getIdMachine(): ?Machine
    {
        return $this->idMachine;
    }

    public function setIdMachine(?Machine $idMachine): static
    {
        $this->idMachine = $idMachine;

        return $this;
    }

    public function getIdTechnicien(): ?Technicien
    {
        return $this->idTechnicien;
    }

    public function setIdTechnicien(?Technicien $idTechnicien): static
    {
        $this->idTechnicien = $idTechnicien;

        return $this;
    }
    // src/Entity/Maintenance.php
public function getEtatPred(): ?string
{
    return $this->etatPred;
}

public function setEtatPred(?string $etatPred): static
{
    $this->etatPred = $etatPred;

    return $this;
}
}
