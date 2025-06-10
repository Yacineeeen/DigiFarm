<?php
// src/Service/PdfGeneratorService.php
namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGeneratorService
{
    private Dompdf $dompdf;

    public function __construct()
    {
        // Configuration des options DomPDF (par exemple, activer le mode de débogage, gérer les images, etc.)
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        // Instanciation de Dompdf
        $this->dompdf = new Dompdf($options);
    }

    public function generatePdf(string $htmlContent): string
    {
        // Charger le contenu HTML dans DomPDF
        $this->dompdf->loadHtml($htmlContent);

        // (Optionnel) Définir la taille du papier
        $this->dompdf->setPaper('A4', 'portrait');

        // Rendre le PDF
        $this->dompdf->render();

        // Retourner le fichier PDF sous forme de string
        return $this->dompdf->output();
    }
}
