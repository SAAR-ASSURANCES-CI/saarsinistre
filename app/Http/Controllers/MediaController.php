<?php

namespace App\Http\Controllers;

use App\Models\DocumentSinistre;
use App\Models\Sinistre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        $sinistres = Sinistre::whereHas('documents')
           ->with('documents')
           ->paginate(25);
        return view('media.index', compact('sinistres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sinistre_id' => 'required|exists:sinistres,id',
            'file' => 'required|file|max:10240', 
        ]);

        $sinistre = Sinistre::findOrFail($request->sinistre_id);
        $file = $request->file('file');
        $type = $request->input('type_document', 'autre');
        $libelle = $request->input('libelle_document', $file->getClientOriginalName());

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

        return redirect()->route('media.index')->with('success', 'Fichier ajouté avec succès.');
    }

    public function destroy(DocumentSinistre $document)
    {
        $document->delete();
        return redirect()->route('media.index')->with('success', 'Fichier supprimé avec succès.');
    }
    
    public function search(Request $request)
{
    $query = $request->input('q');
    
    $sinistres = Sinistre::whereHas('documents')
        ->where('numero_sinistre', 'like', '%' . $query . '%')
        ->with('documents')
        ->get();
    
    return response()->json($sinistres);
}
} 