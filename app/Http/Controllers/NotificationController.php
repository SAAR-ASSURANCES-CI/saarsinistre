<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

class NotificationController extends Controller
{
    // Retourne la liste des messages non lus reçus par l'utilisateur connecté
    public function unreadMessages()
    {
        $user = Auth::user();
        $messages = Message::where('receiver_id', $user->id)
            ->where('lu', false)
            ->with(['sender', 'sinistre'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function($msg) {
                return [
                    'id' => $msg->id,
                    'sinistre_id' => $msg->sinistre_id,
                    'sender_nom' => $msg->sender->nom_complet ?? 'Utilisateur',
                    'contenu' => $msg->contenu,
                    'created_at' => $msg->created_at->format('d/m/Y H:i'),
                ];
            });
        return response()->json($messages);
    }

    // Retourne le nombre de messages non lus reçus par l'utilisateur connecté
    public function unreadMessagesCount()
    {
        $user = Auth::user();
        $count = Message::where('receiver_id', $user->id)
            ->where('lu', false)
            ->count();
        return response()->json(['count' => $count]);
    }
} 