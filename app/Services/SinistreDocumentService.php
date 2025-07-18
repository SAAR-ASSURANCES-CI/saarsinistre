<?php

namespace App\Services;

use App\Models\Sinistre;
use Illuminate\Http\Request;
use App\Models\DocumentSinistre;

class SinistreDocumentService
{
    public function handleDocuments(Request $request, Sinistre $sinistre): void
    {
        $this->processStandardDocuments($request, $sinistre);
        $this->processVehiclePhotos($request, $sinistre);
    }

    protected function processStandardDocuments(Request $request, Sinistre $sinistre): void
    {
        $typesDocuments = [
            'carte_grise_recto' => 'Carte grise (Recto)',
            'carte_grise_verso' => 'Carte grise (Verso)',
            'visite_technique_recto' => 'Visite technique (Recto)',
            'visite_technique_verso' => 'Visite technique (Verso)',
            'attestation_assurance' => 'Attestation d\'assurance',
            'permis_conduire' => 'Permis de conduire'
        ];

        foreach ($typesDocuments as $typeInput => $libelle) {
            if ($request->hasFile($typeInput)) {
                $this->storeDocument($request->file($typeInput), $sinistre, $typeInput, $libelle);
            }
        }
    }

    protected function processVehiclePhotos(Request $request, Sinistre $sinistre): void
    {
        if ($request->hasFile('photos_vehicule')) {
            foreach ($request->file('photos_vehicule') as $index => $photo) {
                $libelle = "Photo véhicule " . ($index + 1);
                $this->storeDocument($photo, $sinistre, 'photo_vehicule', $libelle);
            }
        }
    }

    protected function storeDocument($file, Sinistre $sinistre, string $type, string $libelle): void
    {
        $extension = $file->getClientOriginalExtension();
        $nomFichier = $type . '_' . time() . '_' . uniqid() . '.' . $extension;

        $chemin = $file->storeAs("sinistres/{$sinistre->id}", $nomFichier, 'public');

        DocumentSinistre::create([
            'sinistre_id' => $sinistre->id,
            'type_document' => $type,
            'libelle_document' => $libelle,
            'nom_fichier' => $file->getClientOriginalName(),
            'nom_fichier_stocke' => $nomFichier,
            'chemin_fichier' => $chemin,
            'type_mime' => $file->getMimeType(),
            'taille_fichier' => $file->getSize(),
        ]);
    }
}
