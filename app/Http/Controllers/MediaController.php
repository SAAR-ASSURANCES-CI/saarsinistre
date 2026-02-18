<?php

namespace App\Http\Controllers;

use App\Models\DocumentSinistre;
use App\Models\Sinistre;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $sinistres = Sinistre::whereHas('documents')
            ->with(['documents', 'vehicule'])
            ->paginate(36);

        $viewMode = session('media_view_mode', 'gallery');
        if (!in_array($viewMode, ['gallery', 'list'])) {
            $viewMode = 'gallery';
        }

        if ($request->ajax() && $request->has('view')) {
            $viewMode = in_array($request->view, ['gallery', 'list']) ? $request->view : 'gallery';
            session(['media_view_mode' => $viewMode]);
            return response()->json([
                'html' => view('media.partials.' . $viewMode . '-view', compact('sinistres'))->render(),
                'view' => $viewMode,
            ]);
        }

        return view('media.index', compact('sinistres', 'viewMode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sinistre_id' => 'required|exists:sinistres,id',
            'file'        => 'required|file|max:10240',
        ]);

        $sinistre = Sinistre::findOrFail($request->sinistre_id);
        $file     = $request->file('file');
        $type     = $request->input('type_document', 'autre');
        $libelle  = $request->input('libelle_document', $file->getClientOriginalName());

        $nomFichier = $type . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $chemin     = $file->storeAs("sinistres/{$sinistre->id}", $nomFichier, 'public');

        DocumentSinistre::create([
            'sinistre_id'       => $sinistre->id,
            'type_document'     => $type,
            'libelle_document'  => $libelle,
            'nom_fichier'       => $file->getClientOriginalName(),
            'nom_fichier_stocke'=> $nomFichier,
            'chemin_fichier'    => $chemin,
            'type_mime'         => $file->getMimeType(),
            'taille_fichier'    => $file->getSize(),
        ]);

        return redirect()->route('media.index')->with('success', 'Fichier ajouté avec succès.');
    }

    public function destroy(DocumentSinistre $document)
    {
        $document->delete();
        return redirect()->route('media.index')->with('success', 'Fichier supprimé avec succès.');
    }

    public function search(Request $request)
    {
        $sinistres = Sinistre::whereHas('documents')
            ->where('numero_sinistre', 'like', '%' . $request->input('q') . '%')
            ->with(['documents', 'vehicule'])
            ->get();

        return response()->json($sinistres);
    }

    public function download(Sinistre $sinistre)
    {
        $documents = $sinistre->documents;

        if ($documents->isEmpty()) {
            return back()->with('error', 'Aucun document à télécharger.');
        }

        $zipName = 'sinistre_' . $sinistre->numero_sinistre . '.zip';
        $zipPath = storage_path('app/temp/' . $zipName);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($documents as $index => $doc) {
            $filePath = storage_path('app/public/' . $doc->chemin_fichier);
            if (file_exists($filePath)) {
                $extension  = pathinfo($doc->chemin_fichier, PATHINFO_EXTENSION);
                $nomDansZip = ($doc->libelle_document ?? 'document') . '.' . $extension;
                $zip->addFile($filePath, $nomDansZip);
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }
}