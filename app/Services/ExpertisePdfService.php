<?php

namespace App\Services;

use App\Models\Expertise;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ExpertisePdfService
{
    /**
     * Générer le document Word rempli à partir du template
     */
    public function generateExpertiseWord(Expertise $expertise): string
    {
        // Chemin vers le template
        $templatePath = storage_path('templates/expertise_template.docx');
        
        if (!file_exists($templatePath)) {
            throw new \Exception("Le template Word n'existe pas : " . $templatePath);
        }

        // Charger le template
        $templateProcessor = new TemplateProcessor($templatePath);

        // Remplacer les variables simples
        $templateProcessor->setValue('date_expertise', $expertise->date_expertise->format('d/m/Y'));
        $templateProcessor->setValue('client_nom', $expertise->client_nom ?? '');
        $templateProcessor->setValue('mandant_saar', 'SAAR Assurances');
        
        // Informations du collaborateur
        $templateProcessor->setValue('collaborateur_nom', $expertise->collaborateur_nom ?? '');
        $templateProcessor->setValue('collaborateur_telephone', $expertise->collaborateur_telephone ?? '');
        $templateProcessor->setValue('collaborateur_email', $expertise->collaborateur_email ?? '');
        
        // Informations de l'expertise
        $templateProcessor->setValue('lieu_expertise', $expertise->lieu_expertise ?? '');
        $templateProcessor->setValue('contact_client', $expertise->contact_client ?? '');
        $templateProcessor->setValue('vehicule_expertise', $expertise->vehicule_expertise ?? '');

        // Remplir le tableau des opérations
        $operations = $expertise->operations ?? [];
        
        if (count($operations) > 0) {
            // Cloner la ligne du tableau pour chaque opération
            $templateProcessor->cloneRow('libelle', count($operations));
            
            foreach ($operations as $index => $operation) {
                $rowIndex = $index + 1;
                
                // Libellé de l'opération
                $templateProcessor->setValue('libelle#' . $rowIndex, $operation['libelle'] ?? '');
                
                // Checkboxes (☐ = U+2610, ☑ = U+2611)
                $templateProcessor->setValue('ech#' . $rowIndex, 
                    ($operation['echange'] ?? false) ? '☑' : '☐'
                );
                $templateProcessor->setValue('rep#' . $rowIndex, 
                    ($operation['reparation'] ?? false) ? '☑' : '☐'
                );
                $templateProcessor->setValue('ctl#' . $rowIndex, 
                    ($operation['controle'] ?? false) ? '☑' : '☐'
                );
                $templateProcessor->setValue('p#' . $rowIndex, 
                    ($operation['peinture'] ?? false) ? '☑' : '☐'
                );
            }
        }

        // Sauvegarder le fichier Word temporaire
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        $outputPath = $tempDir . '/expertise_' . $expertise->id . '_' . time() . '.docx';
        $templateProcessor->saveAs($outputPath);

        return $outputPath;
    }

    /**
     * Détecter le chemin de LibreOffice sur le système
     */
    private function getLibreOfficePath(): ?string
    {
        $possiblePaths = [
            // Windows
            'C:\Program Files\LibreOffice\program\soffice.exe',
            'C:\Program Files (x86)\LibreOffice\program\soffice.exe',
            // Linux
            '/usr/bin/soffice',
            '/usr/bin/libreoffice',
            // Mac
            '/Applications/LibreOffice.app/Contents/MacOS/soffice',
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Essayer via la commande which/where
        $command = PHP_OS_FAMILY === 'Windows' ? 'where soffice' : 'which soffice';
        exec($command . ' 2>&1', $output, $returnCode);
        
        if ($returnCode === 0 && !empty($output[0])) {
            return trim($output[0]);
        }

        return null;
    }

    /**
     * Convertir un fichier Word en PDF via LibreOffice
     */
    private function convertWordToPdfWithLibreOffice(string $wordPath): string
    {
        $libreOfficePath = $this->getLibreOfficePath();
        
        if (!$libreOfficePath) {
            throw new \Exception(
                'LibreOffice n\'est pas installé. ' .
                'Veuillez installer LibreOffice depuis https://www.libreoffice.org/download/ ' .
                'pour générer des PDF avec le design exact du modèle Word.'
            );
        }

        $outputDir = storage_path('app/temp');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Commande LibreOffice pour convertir en PDF
        $command = sprintf(
            '"%s" --headless --convert-to pdf --outdir "%s" "%s" 2>&1',
            $libreOfficePath,
            $outputDir,
            $wordPath
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Erreur lors de la conversion LibreOffice: ' . implode("\n", $output));
        }

        // Le PDF généré a le même nom que le Word mais avec l'extension .pdf
        $pdfPath = $outputDir . '/' . basename($wordPath, '.docx') . '.pdf';

        if (!file_exists($pdfPath)) {
            throw new \Exception('Le fichier PDF n\'a pas été généré par LibreOffice.');
        }

        return $pdfPath;
    }

    /**
     * Convertir un fichier Word en HTML puis en PDF (fallback)
     * Utilise PHPWord pour lire le Word et DomPDF pour générer le PDF
     */
    private function convertWordToPdfWithDomPDF(string $wordPath): \Barryvdh\DomPDF\PDF
    {
        // Lire le document Word
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($wordPath);
        
        // Convertir en HTML
        $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
        
        $tempDir = storage_path('app/temp');
        $htmlPath = $tempDir . '/' . basename($wordPath, '.docx') . '.html';
        
        $htmlWriter->save($htmlPath);
        
        // Lire le HTML généré
        $htmlContent = file_get_contents($htmlPath);
        
        // Ajouter des styles CSS pour améliorer le rendu
        $styledHtml = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                @page { margin: 15mm 20mm; }
                body { font-family: Arial, sans-serif; font-size: 10px; }
                table { border-collapse: collapse; width: 100%; }
                table td, table th { border: 2px solid #000; padding: 5px; }
                table th { font-weight: bold; text-align: center; }
            </style>
        </head>
        <body>
        ' . $htmlContent . '
        </body>
        </html>';
        
        // Générer le PDF à partir du HTML
        $pdf = Pdf::loadHTML($styledHtml);
        $pdf->setPaper('A4', 'portrait');
        
        // Nettoyer les fichiers temporaires
        if (file_exists($htmlPath)) {
            unlink($htmlPath);
        }
        
        return $pdf;
    }

    /**
     * Générer le PDF de la fiche d'expertise
     */
    public function generateExpertisePdf(Expertise $expertise)
    {
        // Générer le Word d'abord
        $wordPath = $this->generateExpertiseWord($expertise);
        
        try {
            // Essayer d'utiliser LibreOffice pour une qualité optimale
            $pdfPath = $this->convertWordToPdfWithLibreOffice($wordPath);
            
            // Lire le PDF généré
            $pdfContent = file_get_contents($pdfPath);
            
            // Nettoyer les fichiers temporaires
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            
            // Retourner une réponse avec le contenu du PDF
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf');
                
        } catch (\Exception $e) {
            // Si LibreOffice n'est pas disponible, utiliser DomPDF (fallback)
            \Log::warning('LibreOffice non disponible, utilisation de DomPDF: ' . $e->getMessage());
            
            $pdf = $this->convertWordToPdfWithDomPDF($wordPath);
            
            // Nettoyer le fichier Word temporaire
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            
            return $pdf;
        }
    }

    /**
     * Télécharger le PDF de la fiche d'expertise
     */
    public function downloadExpertisePdf(Expertise $expertise)
    {
        // Générer le Word
        $wordPath = $this->generateExpertiseWord($expertise);
        $nomFichier = 'Fiche_Expertise_' . $expertise->sinistre->numero_sinistre . '.pdf';
        
        try {
            // Utiliser LibreOffice
            $pdfPath = $this->convertWordToPdfWithLibreOffice($wordPath);
            
            // Télécharger le PDF et nettoyer
            $response = response()->download($pdfPath, $nomFichier)->deleteFileAfterSend(true);
            
            // Nettoyer le Word
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            
            return $response;
            
        } catch (\Exception $e) {
            // Fallback DomPDF
            $pdf = $this->convertWordToPdfWithDomPDF($wordPath);
            
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            
            return $pdf->download($nomFichier);
        }
    }

    /**
     * Afficher le PDF de la fiche d'expertise (prévisualisation)
     */
    public function previewExpertisePdf(Expertise $expertise)
    {
        // Générer le Word
        $wordPath = $this->generateExpertiseWord($expertise);
        $nomFichier = 'Fiche_Expertise_' . $expertise->sinistre->numero_sinistre . '.pdf';
        
        try {
            // Utiliser LibreOffice
            $pdfPath = $this->convertWordToPdfWithLibreOffice($wordPath);
            
            // Lire le contenu
            $pdfContent = file_get_contents($pdfPath);
            
            // Nettoyer les fichiers
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            
            // Retourner pour affichage dans le navigateur
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $nomFichier . '"');
            
        } catch (\Exception $e) {
            // Fallback DomPDF
            $pdf = $this->convertWordToPdfWithDomPDF($wordPath);
            
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            
            return $pdf->stream($nomFichier);
        }
    }

    /**
     * Télécharger le fichier Word directement (optionnel)
     */
    public function downloadExpertiseWord(Expertise $expertise)
    {
        $wordPath = $this->generateExpertiseWord($expertise);
        $nomFichier = 'Fiche_Expertise_' . $expertise->sinistre->numero_sinistre . '.docx';
        
        return response()->download($wordPath, $nomFichier)->deleteFileAfterSend(true);
    }
}
