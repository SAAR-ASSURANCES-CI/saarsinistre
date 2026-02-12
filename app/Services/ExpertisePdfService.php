<?php

namespace App\Services;

use App\Models\Expertise;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpertisePdfService
{
    /**
     * Génère le PDF d'expertise 
     */
    private function generatePdf(Expertise $expertise): \Barryvdh\DomPDF\PDF
    {
        $expertise->load('sinistre');
        
        $pdf = Pdf::loadView('pdf.expertise', [
            'expertise' => $expertise
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf;
    }

    /**
     * Prévisualise le PDF 
     */
    public function previewExpertisePdf(Expertise $expertise)
    {
        $pdf = $this->generatePdf($expertise);
        $nomFichier = 'Fiche_Expertise_' . $expertise->sinistre->numero_sinistre . '.pdf';
        
        return $pdf->stream($nomFichier);
    }

    /**
     * Télécharge le PDF
     */
    public function downloadExpertisePdf(Expertise $expertise)
    {
        $pdf = $this->generatePdf($expertise);
        $nomFichier = 'Fiche_Expertise_' . $expertise->sinistre->numero_sinistre . '.pdf';
        
        return $pdf->download($nomFichier);
    }

    /**
     * Génère et retourne le PDF 
     */
    public function generateExpertisePdf(Expertise $expertise)
    {
        return $this->previewExpertisePdf($expertise);
    }
}
