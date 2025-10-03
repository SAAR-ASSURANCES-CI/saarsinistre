<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    protected $chunkDirectory = 'temp_uploads';
    protected $maxChunkSize = 1024 * 1024; // 1MB
    protected $maxFileSize = 50 * 1024 * 1024; // 50MB

    /**
     * Uploader un chunk de fichier
     */
    public function uploadChunk(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'chunk' => 'required|file|max:' . $this->maxChunkSize,
                'fileId' => 'required|string|max:100',
                'chunkIndex' => 'required|integer|min:0',
                'totalChunks' => 'required|integer|min:1',
                'sessionId' => 'required|string|max:100'
            ]);

            $fileId = $request->input('fileId');
            $chunkIndex = $request->input('chunkIndex');
            $totalChunks = $request->input('totalChunks');
            $sessionId = $request->input('sessionId');
            $chunk = $request->file('chunk');

            $sessionPath = "{$this->chunkDirectory}/{$sessionId}";
            if (!Storage::disk('public')->exists($sessionPath)) {
                Storage::disk('public')->makeDirectory($sessionPath);
            }

            $chunkPath = "{$sessionPath}/{$fileId}_chunk_{$chunkIndex}";
            $chunk->storeAs('', $chunkPath, 'public');

            Log::info('Chunk uploadé', [
                'fileId' => $fileId,
                'chunkIndex' => $chunkIndex,
                'totalChunks' => $totalChunks,
                'sessionId' => $sessionId
            ]);

            return response()->json([
                'success' => true,
                'chunkIndex' => $chunkIndex,
                'message' => 'Chunk uploadé avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur upload chunk', [
                'error' => $e->getMessage(),
                'fileId' => $request->input('fileId'),
                'chunkIndex' => $request->input('chunkIndex')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload du chunk'
            ], 500);
        }
    }

    /**
     * Finaliser l'upload d'un fichier
     */
    public function finalizeUpload(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'fileId' => 'required|string|max:100',
                'sessionId' => 'required|string|max:100',
                'originalName' => 'required|string|max:255',
                'mimeType' => 'required|string|max:100',
                'size' => 'required|integer|min:1|max:' . $this->maxFileSize,
                'field' => 'required|string|max:50'
            ]);

            $fileId = $request->input('fileId');
            $sessionId = $request->input('sessionId');
            $originalName = $request->input('originalName');
            $mimeType = $request->input('mimeType');
            $size = $request->input('size');
            $field = $request->input('field');

            $sessionPath = "{$this->chunkDirectory}/{$sessionId}";
            $chunkPattern = "{$sessionPath}/{$fileId}_chunk_*";

            $chunkFiles = Storage::disk('public')->files($sessionPath);
            $chunks = array_filter($chunkFiles, function($file) use ($fileId) {
                return strpos($file, "{$fileId}_chunk_") !== false;
            });

            if (empty($chunks)) {
                throw new \Exception('Aucun chunk trouvé pour ce fichier');
            }

            sort($chunks);

            $expectedChunks = count($chunks);
            for ($i = 0; $i < $expectedChunks; $i++) {
                $expectedChunk = "{$sessionPath}/{$fileId}_chunk_{$i}";
                if (!in_array($expectedChunk, $chunks)) {
                    throw new \Exception("Chunk manquant: {$i}");
                }
            }

            $finalPath = $this->reconstructFile($chunks, $fileId, $sessionId, $originalName);

            $this->cleanupChunks($chunks);

            Log::info('Fichier finalisé', [
                'fileId' => $fileId,
                'originalName' => $originalName,
                'finalPath' => $finalPath,
                'size' => $size
            ]);

            return response()->json([
                'success' => true,
                'path' => $finalPath,
                'originalName' => $originalName,
                'size' => $size,
                'mimeType' => $mimeType,
                'field' => $field
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur finalisation upload', [
                'error' => $e->getMessage(),
                'fileId' => $request->input('fileId'),
                'sessionId' => $request->input('sessionId')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la finalisation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reconstituer le fichier à partir des chunks
     */
    protected function reconstructFile(array $chunks, string $fileId, string $sessionId, string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = $fileId . '.' . $extension;
        $finalPath = "{$this->chunkDirectory}/{$sessionId}/{$filename}";

        $finalFile = Storage::disk('public')->path($finalPath);
        $handle = fopen($finalFile, 'wb');

        if (!$handle) {
            throw new \Exception('Impossible de créer le fichier final');
        }

        try {
            foreach ($chunks as $chunkPath) {
                $chunkContent = Storage::disk('public')->get($chunkPath);
                fwrite($handle, $chunkContent);
            }
        } finally {
            fclose($handle);
        }

        return $finalPath;
    }

    /**
     * Nettoyer les chunks
     */
    protected function cleanupChunks(array $chunks): void
    {
        foreach ($chunks as $chunkPath) {
            Storage::disk('public')->delete($chunkPath);
        }
    }

    /**
     * Nettoyer une session complète
     */
    public function cleanupSession(Request $request): JsonResponse
    {
        try {
            $sessionId = $request->input('sessionId');
            
            if (!$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session ID requis'
                ], 400);
            }

            $sessionPath = "{$this->chunkDirectory}/{$sessionId}";
            
            if (Storage::disk('public')->exists($sessionPath)) {
                Storage::disk('public')->deleteDirectory($sessionPath);
            }

            return response()->json([
                'success' => true,
                'message' => 'Session nettoyée'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur nettoyage session', [
                'error' => $e->getMessage(),
                'sessionId' => $request->input('sessionId')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du nettoyage'
            ], 500);
        }
    }
}
