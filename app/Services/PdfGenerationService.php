<?php

namespace App\Services;

use App\Models\Sinistre;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfGenerationService
{
    public function generateSinistreReceipt($sinistreId)
    {
        $sinistre = Sinistre::with('documents')->findOrFail($sinistreId);

        $data = [
            'sinistre' => $sinistre,
            'date_generation' => now()->format('d/m/Y H:i'),
            'company' => [
                'name' => 'SAAR ASSURANCES',
                'phone' => '+225 20 30 30 30',
                'email' => 'contact@saar-assurance.ci',
                'address' => 'Abidjan, Côte d\'Ivoire'
            ]
        ];

        $pdf = PDF::loadView('declaration.recu-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $nomFichier = 'Attestation_Declaration_' . $sinistre->numero_sinistre . '.pdf';

        return $pdf->download($nomFichier);
    }

    public function generateSinistreFiche(int $sinistreId)
    {
        $sinistre = Sinistre::with(['documents', 'gestionnaire'])->findOrFail($sinistreId);

        $data = [
            'sinistre' => $sinistre,
            'date_generation' => now()->format('d/m/Y H:i'),
            'company' => [
                'name' => 'SAAR ASSURANCES',
                'phone' => '+225 20 30 30 30',
                'email' => 'contact@saar-assurance.ci',
                'address' => 'Abidjan, Côte d\'Ivoire'
            ]
        ];

        $pdf = PDF::loadView('admin.sinistre-fiche-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $nomFichier = 'Fiche_Sinistre_' . $sinistre->numero_sinistre . '.pdf';

        return $pdf->download($nomFichier);
    }
}
