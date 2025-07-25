<?php

namespace App\Http\Controllers;

use App\Models\Sinistre;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewMessageNotification;

class ChatController extends Controller
{
    
    public function index($sinistre_id)
    {
        $sinistre = Sinistre::with(['messages.sender', 'messages.receiver'])->findOrFail($sinistre_id);
        $user = Auth::user();

        // Sécurité : seul l'assuré ou le gestionnaire peut accéder
        if ($user->id !== $sinistre->assure_id && $user->id !== $sinistre->gestionnaire_id) {
            abort(403);
        }
        $messages = $sinistre->messages()->with(['sender', 'receiver'])->orderBy('created_at')->get();
        return view('chat.index', compact('sinistre', 'messages'));
    }

  
    public function fetch($sinistre_id)
    {
        $sinistre = Sinistre::findOrFail($sinistre_id);
        $user = Auth::user();
        if ($user->id !== $sinistre->assure_id && $user->id !== $sinistre->gestionnaire_id) {
            abort(403);
        }
        $messages = $sinistre->messages()->with(['sender', 'receiver'])->orderBy('created_at')->get();
        return response()->json($messages);
    }

    public function store(Request $request, $sinistre_id)
    {
        $request->validate([
            'contenu' => 'required|string|max:2000',
        ]);
        $sinistre = Sinistre::findOrFail($sinistre_id);
        $user = Auth::user();
        if ($user->id !== $sinistre->assure_id && $user->id !== $sinistre->gestionnaire_id) {
            abort(403);
        }
        // Déterminer le destinataire
        $receiver_id = ($user->id === $sinistre->assure_id) ? $sinistre->gestionnaire_id : $sinistre->assure_id;
        if (!$receiver_id) {
            return response()->json(['error' => 'Aucun destinataire.'], 422);
        }
        $message = Message::create([
            'sinistre_id' => $sinistre->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiver_id,
            'contenu' => $request->contenu,
            'lu' => false,
        ]);
        // Notification push (Reverb via event/notification)
        Notification::sendNow($message->receiver, new NewMessageNotification($message));
        return response()->json($message->load(['sender', 'receiver']));
    }
} 