<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Sinistre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index($sinistre_id)
    {
        $sinistre = Sinistre::with(['messages' => function($query) {
            $query->with(['sender' => function($q) {
                $q->select('id', 'nom_complet');
            }])->orderBy('created_at', 'asc');
        }])->findOrFail($sinistre_id);
    
        $user = Auth::user();
    
        if ($user->id !== $sinistre->assure_id && $user->id !== $sinistre->gestionnaire_id) {
            abort(403);
        }
    
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
        $lastMessageId = request()->query('last_id', 0);
    
        $messages = Message::where('sinistre_id', $sinistre_id)
            ->when($lastMessageId > 0, function($query) use ($lastMessageId) {
                $query->where('id', '>', $lastMessageId);
            })
            ->with(['sender' => function($q) {
                $q->select('id', 'nom_complet');
            }])
            ->orderBy('created_at', 'asc')
            ->get();
    
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

        $receiver_id = ($user->id === $sinistre->assure_id) 
            ? $sinistre->gestionnaire_id 
            : $sinistre->assure_id;

        $message = Message::create([
            'sinistre_id' => $sinistre->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiver_id,
            'contenu' => $request->contenu,
            'lu' => false,
        ]);

        $message->load(['sender' => function($q) {
            $q->select('id', 'nom_complet');
        }]);

        return response()->json($message, 201);
    }
}