<?php

namespace App\Entity;

use App\Repository\VeterinaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VeterinaireRepository::class)]
class Veterinaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $num_tel = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse_cabine = null;

    #[ORM\OneToMany(targetEntity: Suivi::class, mappedBy: 'veterinaire')] // Change 'veterinaires' to 'veterinaire'
    private Collection $suivis;

    public function __construct()
    {
        $this->suivis = new ArrayCollection();
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

    public function getNumTel(): ?int
    {
        return $this->num_tel;
    }

    public function setNumTel(int $num_tel): static
    {
        $this->num_tel = $num_tel;

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

    public function getAdresseCabine(): ?string
    {
        return $this->adresse_cabine;
    }

    public function setAdresseCabine(string $adresse_cabine): static
    {
        $this->adresse_cabine = $adresse_cabine;

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
            $suivi->setVeterinaires($this);
        }

        return $this;
    }

    public function removeSuivi(Suivi $suivi): static
    {
        if ($this->suivis->removeElement($suivi)) {
            // set the owning side to null (unless already changed)
            if ($suivi->getVeterinaires() === $this) {
                $suivi->setVeterinaires(null);
            }
        }

        return $this;
    }

    public function __toString(): string
{
    return $this->nom ?? 'Veterinaire'; // Adjust as needed
}
#[Route('/send-email/{id}', name: 'app_send_email_veterinaire', methods: ['GET'])]
    public function sendEmailToVeterinaire(MailerInterface $mailer, Veterinaire $veterinaire, SuiviRepository $suiviRepository): Response
    {
        // Récupération des suivis du vétérinaire
        $suivis = $suiviRepository->findBy(['veterinaire' => $veterinaire]);

        // Création du fichier Excel pour le vétérinaire
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Définir les entêtes de colonnes
        $sheet->setCellValue('A1', 'ID')
              ->setCellValue('B1', 'Température (°C)')
              ->setCellValue('C1', 'Rythme Cardiaque (BPM)')
              ->setCellValue('D1', 'État')
              ->setCellValue('E1', 'Vétérinaire')
              ->setCellValue('F1', 'Client ID');

        // Remplir les données
        $row = 2;
        foreach ($suivis as $suivi) {
            $sheet->setCellValue('A' . $row, $suivi->getId())
                  ->setCellValue('B' . $row, $suivi->getTemperature())
                  ->setCellValue('C' . $row, $suivi->getRythmeCardiaque())
                  ->setCellValue('D' . $row, $suivi->getEtat())
                  ->setCellValue('E' . $row, $veterinaire->getNom())
                  ->setCellValue('F' . $row, $suivi->getIdClient());
            $row++;
        }

        // Créer un fichier Excel temporaire
        $writer = new Xlsx($spreadsheet);
        $filePath = '/tmp/' . $veterinaire->getNom() . '_suivi_export.xlsx';
        $writer->save($filePath);

        // Création de l'email
        $email = (new Email())
            ->from('your-email@example.com')
            ->to($veterinaire->getEmail())
            ->subject('Suivi Exporté - Vétérinaire: ' . $veterinaire->getNom())
            ->html('<p>Bonjour ' . $veterinaire->getNom() . ',</p><p>Veuillez trouver en pièce jointe le fichier Excel contenant les suivis des patients.</p>')
            ->attachFromPath($filePath, $veterinaire->getNom() . '_suivi_export.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Envoi de l'email
        $mailer->send($email);

        // Suppression du fichier temporaire après l'envoi
        unlink($filePath);

        // Retourner une réponse indiquant que l'email a été envoyé
        return new Response('Email envoyé avec succès à ' . $veterinaire->getNom());
    }

}
