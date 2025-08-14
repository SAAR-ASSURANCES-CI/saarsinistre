<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\Sinistre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index($sinistre_id)
    {
        $sinistre = Sinistre::with(['messages' => function ($query) {
            $query->with([
                'sender' => function ($q) {
                    $q->select('id', 'nom_complet');
                },
                'attachments'
            ])->orderBy('created_at', 'asc');
        }])->findOrFail($sinistre_id);

        $user = Auth::user();

        if ($user->id !== $sinistre->assure_id && $user->id !== $sinistre->gestionnaire_id) {
            abort(403);
        }

        // Marquer les messages comme lus
        Message::where('sinistre_id', $sinistre_id)
            ->where('receiver_id', $user->id)
            ->where('lu', false)
            ->update(['lu' => true]);

        return view('chat.index', [
            'sinistre' => $sinistre,
            'messages' => $sinistre->messages
        ]);
    }

    public function fetch($sinistre_id)
    {
        $sinistre = Sinistre::findOrFail($sinistre_id);
        $user = Auth::user();

        // Vérifier les permissions
        if ($user->id !== $sinistre->assure_id && $user->id !== $sinistre->gestionnaire_id) {
            abort(403);
        }

        $lastMessageId = request()->query('last_id', 0);

        $query = Message::where('sinistre_id', $sinistre_id)
            ->with([
                'sender' => function ($q) {
                    $q->select('id', 'nom_complet');
                },
                'attachments'
            ])
            ->orderBy('created_at', 'asc');

        // Si last_id est fourni, récupérer seulement les messages plus récents
        if ($lastMessageId > 0) {
            $query->where('id', '>', $lastMessageId);
        }

        $messages = $query->get();

        // Marquer les messages comme lus pour l'utilisateur actuel
        Message::where('sinistre_id', $sinistre_id)
            ->where('receiver_id', $user->id)
            ->where('lu', false)
            ->update(['lu' => true]);

        return response()->json($messages);
    }

    public function store(Request $request, $sinistre_id)
    {
        $request->validate([
            'contenu' => 'nullable|string|max:2000',
            'fichiers' => 'nullable',
            'fichiers.*' => 'file|max:10240',
        ]);

        $sinistre = Sinistre::findOrFail($sinistre_id);
        $user = Auth::user();

        if ($user->id !== $sinistre->assure_id && $user->id !== $sinistre->gestionnaire_id) {
            abort(403);
        }

        $receiver_id = ($user->id === $sinistre->assure_id)
            ? $sinistre->gestionnaire_id
            : $sinistre->assure_id;

        if (!$request->filled('contenu') && !$request->hasFile('fichiers')) {
            return response()->json(['message' => 'Le message ne peut pas être vide'], 422);
        }

        $message = Message::create([
            'sinistre_id' => $sinistre->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiver_id,
            'contenu' => $request->input('contenu', ''),
            'lu' => false,
        ]);

        // Gérer les pièces jointes
        if ($request->hasFile('fichiers')) {
            $files = $request->file('fichiers');
            foreach ((array)$files as $file) {
                $storedPath = $file->storeAs(
                    "sinistres/{$sinistre->id}/chat",
                    time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension(),
                    'public'
                );
                MessageAttachment::create([
                    'message_id' => $message->id,
                    'nom_fichier' => $file->getClientOriginalName(),
                    'chemin_fichier' => $storedPath,
                    'type_mime' => $file->getMimeType(),
                    'taille' => $file->getSize(),
                ]);
            }
        }

        $message->load(['sender' => function ($q) {
            $q->select('id', 'nom_complet');
        }, 'attachments']);

        // Déclencher l'événement de diffusion
        broadcast(new MessageSent($message));

        return response()->json($message, 201);
    }
}
