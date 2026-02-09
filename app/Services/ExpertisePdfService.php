<?php

namespace App\Services;

use App\Models\Expertise;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ExpertisePdfService
{
    public function generateExpertiseWord(Expertise $expertise): string
    {
        $templatePath = storage_path('templates/expertise_template.docx');
        
        if (!file_exists($templatePath)) {
            throw new \Exception("Le template Word n'existe pas : " . $templatePath);
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        $templateProcessor->setValue('date_expertise', $expertise->date_expertise->format('d/m/Y'));
        $templateProcessor->setValue('client_nom', $expertise->client_nom ?? '');
        $templateProcessor->setValue('mandant_saar', 'SAAR Assurances');
        
        $templateProcessor->setValue('collaborateur_nom', $expertise->collaborateur_nom ?? '');
        $templateProcessor->setValue('collaborateur_telephone', $expertise->collaborateur_telephone ?? '');
        $templateProcessor->setValue('collaborateur_email', $expertise->collaborateur_email ?? '');
        
        $templateProcessor->setValue('lieu_expertise', $expertise->lieu_expertise ?? '');
        $templateProcessor->setValue('contact_client', $expertise->contact_client ?? '');
        $templateProcessor->setValue('vehicule_expertise', $expertise->vehicule_expertise ?? '');

        $operations = $expertise->operations ?? [];
        
        if (count($operations) > 0) {
            $templateProcessor->cloneRow('libelle', count($operations));
            
            foreach ($operations as $index => $operation) {
                $rowIndex = $index + 1;
                
                $templateProcessor->setValue('libelle#' . $rowIndex, $operation['libelle'] ?? '');
                
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

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        $outputPath = $tempDir . '/expertise_' . $expertise->id . '_' . time() . '.docx';
        $templateProcessor->saveAs($outputPath);

        return $outputPath;
    }

    private function getLibreOfficePath(): ?string
    {
        $possiblePaths = [
            'C:\Program Files\LibreOffice\program\soffice.exe',
            'C:\Program Files (x86)\LibreOffice\program\soffice.exe',
            '/usr/bin/soffice',
            '/usr/bin/libreoffice',
            '/Applications/LibreOffice.app/Contents/MacOS/soffice',
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        $command = PHP_OS_FAMILY === 'Windows' ? 'where soffice' : 'which soffice';
        exec($command . ' 2>&1', $output, $returnCode);
        
        if ($returnCode === 0 && !empty($output[0])) {
            return trim($output[0]);
        }

        return null;
    }


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

        $pdfPath = $outputDir . '/' . basename($wordPath, '.docx') . '.pdf';

        if (!file_exists($pdfPath)) {
            throw new \Exception('Le fichier PDF n\'a pas été généré par LibreOffice.');
        }

        return $pdfPath;
    }

    private function convertWordToPdfWithDomPDF(string $wordPath): \Barryvdh\DomPDF\PDF
    {
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($wordPath);
        
        $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
        
        $tempDir = storage_path('app/temp');
        $htmlPath = $tempDir . '/' . basename($wordPath, '.docx') . '.html';
        
        $htmlWriter->save($htmlPath);
        
        $htmlContent = file_get_contents($htmlPath);
        
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
        
        $pdf = Pdf::loadHTML($styledHtml);
        $pdf->setPaper('A4', 'portrait');
        
        if (file_exists($htmlPath)) {
            unlink($htmlPath);
        }
        
        return $pdf;
    }


    public function generateExpertisePdf(Expertise $expertise)
    {
        $wordPath = $this->generateExpertiseWord($expertise);
        
        try {
            $pdfPath = $this->convertWordToPdfWithLibreOffice($wordPath);
            
            $pdfContent = file_get_contents($pdfPath);
            
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf');
                
        } catch (\Exception $e) {
            \Log::warning('LibreOffice non disponible, utilisation de DomPDF: ' . $e->getMessage());
            
            $pdf = $this->convertWordToPdfWithDomPDF($wordPath);
            
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            
            return $pdf;
        }
    }


    public function downloadExpertisePdf(Expertise $expertise)
    {
        $wordPath = $this->generateExpertiseWord($expertise);
        $nomFichier = 'Fiche_Expertise_' . $expertise->sinistre->numero_sinistre . '.pdf';
        
        try {
            $pdfPath = $this->convertWordToPdfWithLibreOffice($wordPath);
            
            $response = response()->download($pdfPath, $nomFichier)->deleteFileAfterSend(true);
            
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            
            return $response;
            
        } catch (\Exception $e) {
            $pdf = $this->convertWordToPdfWithDomPDF($wordPath);
            
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            
            return $pdf->download($nomFichier);
        }
    }


    public function previewExpertisePdf(Expertise $expertise)
    {
        $wordPath = $this->generateExpertiseWord($expertise);
        $nomFichier = 'Fiche_Expertise_' . $expertise->sinistre->numero_sinistre . '.pdf';
        
        try {
            $pdfPath = $this->convertWordToPdfWithLibreOffice($wordPath);
            
            $pdfContent = file_get_contents($pdfPath);
            
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $nomFichier . '"');
            
        } catch (\Exception $e) {
            $pdf = $this->convertWordToPdfWithDomPDF($wordPath);
            
            if (file_exists($wordPath)) {
                unlink($wordPath);
            }
            
            return $pdf->stream($nomFichier);
        }
    }

 
    public function downloadExpertiseWord(Expertise $expertise)
    {
        $wordPath = $this->generateExpertiseWord($expertise);
        $nomFichier = 'Fiche_Expertise_' . $expertise->sinistre->numero_sinistre . '.docx';
        
        return response()->download($wordPath, $nomFichier)->deleteFileAfterSend(true);
    }
}
