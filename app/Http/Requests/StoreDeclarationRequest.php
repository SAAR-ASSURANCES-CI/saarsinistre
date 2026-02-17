<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeclarationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Détermine si la requête provient d'un appareil mobile
     */
    protected function isMobileDevice(): bool
    {
        $userAgent = request()->header('User-Agent', '');
        return preg_match('/Mobile|Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i', $userAgent);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Définir les limites de taille selon le type d'appareil
        $isMobile = $this->isMobileDevice();
        $maxFileSize = $isMobile ? 10240 : 5120; // 10MB mobile, 5MB desktop
        $maxPhotoSize = $isMobile ? 8192 : 1280; // 8MB mobile, 1.28MB desktop
        $supportedMimes = $isMobile ? 'pdf,jpg,jpeg,png,heic,webp' : 'pdf,jpg,jpeg,png';
        
        return [
            'nom_assure' => 'required|string|max:255',
            'email_assure' => 'nullable|email|max:255',
            'telephone_assure' => 'required|string|max:20',
            'numero_police' => 'required|string|max:50',
            'implique_tiers' => 'boolean',
            'nombre_tiers' => 'nullable|required_if:implique_tiers,true|string|in:1,2,3,4,5,6,7,8,9,10+',
            'details_tiers' => 'nullable|string|max:2000',

'marque' => 'required|string|max:100',
'modele' => 'nullable|string|max:100',
'immatriculation' => 'required|string|max:20',
'annee' => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
'couleur' => 'nullable|string|max:50',
'numero_chassis' => 'required|string|min:5|max:25',
'type' => 'nullable|string|in:voiture,moto,camion,utilitaire,autre',
            
            'tiers' => 'nullable|array',
            'tiers.*.nom_conducteur' => 'nullable|string|max:255',
            'tiers.*.prenom_conducteur' => 'nullable|string|max:255',
            'tiers.*.telephone' => 'nullable|string|max:20',
            'tiers.*.email' => 'nullable|email|max:255',
            'tiers.*.adresse' => 'nullable|string|max:500',
            'tiers.*.marque_vehicule' => 'nullable|string|max:100',
            'tiers.*.modele_vehicule' => 'nullable|string|max:100',
            'tiers.*.immatriculation' => 'nullable|string|max:20',
            'tiers.*.compagnie_assurance' => 'nullable|string|max:255',
            'tiers.*.numero_police_assurance' => 'nullable|string|max:50',
            'tiers.*.details_supplementaires' => 'nullable|string|max:1000',

            'date_sinistre' => 'required|date|before_or_equal:today',
            'heure_sinistre' => 'nullable|date_format:H:i',
            'lieu_sinistre' => 'required|string|max:500',
            'circonstances' => 'required|string|max:2000',
            'conducteur_nom' => 'required|string|max:255',

            'constat_autorite' => 'boolean',
            'officier_nom' => 'nullable|required_if:constat_autorite,true|string|max:255',
            'commissariat' => 'nullable|required_if:constat_autorite,true|string|max:255',
            'dommages_releves' => 'nullable|string|max:1000',

            'carte_grise_recto' => "nullable|file|mimes:{$supportedMimes}|max:{$maxFileSize}",
            'carte_grise_verso' => "nullable|file|mimes:{$supportedMimes}|max:{$maxFileSize}",
            'visite_technique_recto' => "nullable|file|mimes:{$supportedMimes}|max:{$maxPhotoSize}",
            'visite_technique_verso' => "nullable|file|mimes:{$supportedMimes}|max:{$maxPhotoSize}",
            'attestation_assurance' => "nullable|file|mimes:{$supportedMimes}|max:{$maxPhotoSize}",
            'permis_conduire' => "nullable|file|mimes:{$supportedMimes}|max:{$maxPhotoSize}",
            
            'uploaded_files' => 'required|string|json',
            
            'tiers_photos_*' => 'nullable|array',
            'tiers_photos_*.*' => "nullable|file|mimes:{$supportedMimes}|max:{$maxFileSize}",
            'tiers_attestation_*' => "nullable|file|mimes:{$supportedMimes}|max:{$maxFileSize}",

            'photos_vehicule' => 'nullable|array|max:100',
            'photos_vehicule.*' => "file|mimes:jpg,jpeg,png,heic,webp|max:{$maxPhotoSize}",
        ];
    }

    public function messages(): array
    {
        return [
            'nom_assure.required' => 'Le nom complet est obligatoire.',
            'nom_assure.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'email_assure.required' => 'L\'adresse email est obligatoire.',
            'email_assure.email' => 'L\'adresse email n\'est pas valide.',
            'email_assure.max' => 'L\'email ne doit pas dépasser 255 caractères.',
            'telephone_assure.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone_assure.max' => 'Le téléphone ne doit pas dépasser 20 caractères.',
            'numero_police.required' => 'Le numéro de police est obligatoire.',
            'numero_police.max' => 'Le numéro de police ne doit pas dépasser 50 caractères.',

'marque.required' => 'La marque du véhicule est obligatoire.',
'marque.max' => 'La marque ne doit pas dépasser 100 caractères.',
'immatriculation.required' => 'L\'immatriculation est obligatoire.',
'numero_chassis.required' => 'Le numéro de châssis est obligatoire.',
'annee.min' => 'L\'année doit être supérieure à 1950.',
'annee.max' => 'L\'année ne peut pas être dans le futur.',

            'date_sinistre.required' => 'La date du sinistre est obligatoire.',
            'date_sinistre.before_or_equal' => 'La date du sinistre ne peut pas être dans le futur.',
            'lieu_sinistre.required' => 'Le lieu du sinistre est obligatoire.',
            'lieu_sinistre.max' => 'Le lieu ne doit pas dépasser 500 caractères.',
            'circonstances.required' => 'La description des circonstances est obligatoire.',
            'circonstances.max' => 'La description ne doit pas dépasser 2000 caractères.',
            'conducteur_nom.required' => 'Le nom du conducteur est obligatoire.',
            'conducteur_nom.max' => 'Le nom du conducteur ne doit pas dépasser 255 caractères.',

            'officier_nom.required_if' => 'Le nom de l\'officier est requis si un constat a été établi.',
            'officier_nom.max' => 'Le nom de l\'officier ne doit pas dépasser 255 caractères.',
            'commissariat.required_if' => 'Le commissariat/brigade est requis si un constat a été établi.',
            'commissariat.max' => 'Le nom du commissariat ne doit pas dépasser 255 caractères.',
            'dommages_releves.max' => 'La description des dommages ne doit pas dépasser 1000 caractères.',

            'carte_grise_recto.required' => 'La carte grise (recto) est obligatoire.',
            'carte_grise_verso.required' => 'La carte grise (verso) est obligatoire.',
            'visite_technique_recto.required' => 'La visite technique (recto) est obligatoire.',
            'visite_technique_verso.required' => 'La visite technique (verso) est obligatoire.',
            'attestation_assurance.required' => 'L\'attestation d\'assurance est obligatoire.',
            'permis_conduire.required' => 'Le permis de conduire est obligatoire.',
            '*.mimes' => 'Format de fichier non autorisé. Utilisez PDF, JPG, JPEG, PNG' . ($this->isMobileDevice() ? ', HEIC ou WEBP.' : '.'),
            '*.max' => 'La taille du fichier ne doit pas dépasser ' . ($this->isMobileDevice() ? '10MB.' : '5MB.'),
            'photos_vehicule.max' => 'Vous ne pouvez télécharger que 100 photos maximum.',
        ];
    }

}
