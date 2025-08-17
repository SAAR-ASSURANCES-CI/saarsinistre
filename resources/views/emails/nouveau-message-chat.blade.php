<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Message Reçu</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #007bff;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header .company-name {
            font-size: 18px;
            margin-top: 10px;
            opacity: 0.9;
        }

        .content {
            padding: 30px;
        }

        .alert {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .message-info {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin: 20px 0;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }

        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .info-label {
            font-weight: bold;
            color: #333;
            width: 150px;
            flex-shrink: 0;
        }

        .info-value {
            color: #666;
            flex: 1;
        }

        .message-content {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .message-bubble {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            border-radius: 0 8px 8px 0;
            margin: 10px 0;
        }

        .action-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
            transition: transform 0.2s;
        }

        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }

        .footer .company-contact {
            margin-top: 10px;
        }

        .sender-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .timestamp {
            font-size: 12px;
            color: #999;
            text-align: right;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>💬 Nouveau Message Reçu</h1>
            <div class="company-name">{{ $company['name'] }}</div>
        </div>

        <div class="content">
            <div class="alert">
                📩 Vous avez reçu un nouveau message dans le chat du sinistre.
            </div>

            <div class="sender-info">
                <strong>🗣️ Message de :</strong> {{ $assure->nom_complet }}
            </div>

            <div class="message-info">
                <h3 style="margin-top: 0; color: #333;">Informations du Sinistre</h3>

                <div class="info-row">
                    <div class="info-label">N° Sinistre :</div>
                    <div class="info-value"><strong>{{ $sinistre->numero_sinistre }}</strong></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Assuré :</div>
                    <div class="info-value">{{ $sinistre->nom_assure }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Date du sinistre :</div>
                    <div class="info-value">{{ $sinistre->date_sinistre->format('d/m/Y') }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Statut :</div>
                    <div class="info-value">{{ ucfirst(str_replace('_', ' ', $sinistre->statut)) }}</div>
                </div>
            </div>

            @if($chatMessage->contenu)
            <div class="message-content">
                <h4 style="margin-top: 0; color: #333;">Contenu du Message :</h4>
                
                <div class="message-bubble">
                    {{ $chatMessage->contenu }}
                </div>
                
                <div class="timestamp">
                    Envoyé le {{ $chatMessage->created_at->format('d/m/Y à H:i') }}
                </div>
            </div>
            @endif

            @if($chatMessage->attachments && $chatMessage->attachments->count() > 0)
            <div style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
                <h4 style="margin-top: 0; color: #333;">📎 Pièces jointes :</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($chatMessage->attachments as $attachment)
                    <li style="margin-bottom: 5px;">
                        📄 {{ $attachment->nom_fichier }} 
                        <span style="color: #666; font-size: 12px;">({{ number_format($attachment->taille / 1024, 1) }} KB)</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $url_chat }}" class="action-button">
                    💬 Répondre dans le Chat
                </a>
            </div>

            <div style="background-color: #e9ecef; padding: 15px; border-radius: 5px; margin-top: 20px;">
                <p style="margin: 0; font-size: 14px; color: #666;">
                    <strong>Action suggérée :</strong> L'assuré attend votre réponse. 
                    Cliquez sur le bouton ci-dessus pour accéder directement au chat et répondre à son message.
                </p>
            </div>
        </div>

        <div class="footer">
            <div>
                <strong>{{ $company['name'] }}</strong><br>
                {{ $company['address'] }}
            </div>
            <div class="company-contact">
                📞 {{ $company['phone'] }} | 📧 {{ $company['email'] }}
            </div>
            <div style="margin-top: 15px; font-size: 12px; color: #999;">
                Cet email a été généré automatiquement par le système de chat des sinistres.
            </div>
        </div>
    </div>
</body>

</html>
