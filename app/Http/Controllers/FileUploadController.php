<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileUploadController extends Controller
{
    /**
     * Upload progressif d'un fichier pour la déclaration
     */
    public function uploadFile(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:pdf,jpg,jpeg,png,heic,webp|max:10240', // 10MB max
                'type' => 'required|string|in:carte_grise_recto,carte_grise_verso,visite_technique_recto,visite_technique_verso,attestation_assurance,permis_conduire,photo_vehicule,tiers_photo,tiers_attestation',
                'session_id' => 'required|string'
            ]);

            $file = $request->file('file');
            $type = $request->input('type');
            $sessionId = $request->input('session_id');

            // Créer un nom de fichier unique
            $extension = $file->getClientOriginalExtension();
            $filename = $type . '_' . time() . '_' . uniqid() . '.' . $extension;
            
            // Stocker temporairement dans un dossier de session
            $tempPath = "temp_uploads/{$sessionId}";
            $filePath = $file->storeAs($tempPath, $filename, 'public');

            // Retourner les informations du fichier uploadé
            return response()->json([
                'success' => true,
                'message' => 'Fichier uploadé avec succès',
                'file_info' => [
                    'original_name' => $file->getClientOriginalName(),
                    'stored_path' => $filePath,
                    'filename' => $filename,
                    'type' => $type,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur upload fichier: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload du fichier'
            ], 500);
        }
    }

    /**
     * Supprimer un fichier temporaire
     */
    public function deleteFile(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'file_path' => 'required|string',
                'session_id' => 'required|string'
            ]);

            $filePath = $request->input('file_path');
            $sessionId = $request->input('session_id');

            // Vérifier que le fichier appartient à cette session
            if (!str_contains($filePath, "temp_uploads/{$sessionId}")) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier non autorisé'
                ], 403);
            }

            // Supprimer le fichier
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return response()->json([
                'success' => true,
                'message' => 'Fichier supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur suppression fichier: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }

    /**
     * Nettoyer les fichiers temporaires d'une session
     */
    public function cleanupSession(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'session_id' => 'required|string'
            ]);

            $sessionId = $request->input('session_id');
            $tempPath = "temp_uploads/{$sessionId}";

            // Supprimer tout le dossier de session
            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->deleteDirectory($tempPath);
            }

            return response()->json([
                'success' => true,
                'message' => 'Session nettoyée avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur nettoyage session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du nettoyage'
            ], 500);
        }
    }
}
