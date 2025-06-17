<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Sinistre Déclaré</title>
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
            background-color: #ca0505;
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
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .sinistre-info {
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

        .priority-high {
            color: #dc3545;
            font-weight: bold;
        }

        .status-badge {
            background-color: #ffc107;
            color: #212529;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🚨 Nouveau Sinistre Déclaré</h1>
            <div class="company-name">{{ $company['name'] }}</div>
        </div>

        <div class="content">
            <div class="alert">
                ⚠️ Un nouveau sinistre vient d'être déclaré et nécessite votre attention.
            </div>

            <div class="sinistre-info">
                <h3 style="margin-top: 0; color: #333;">Informations du Sinistre</h3>

                <div class="info-row">
                    <div class="info-label">N° Sinistre :</div>
                    <div class="info-value"><strong>{{ $sinistre->numero_sinistre }}</strong></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Statut :</div>
                    <div class="info-value">
                        <span class="status-badge">{{ ucfirst(str_replace('_', ' ', $sinistre->statut)) }}</span>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Date du sinistre :</div>
                    <div class="info-value">{{ $sinistre->date_sinistre->format('d/m/Y') }}</div>
                </div>

                @if ($sinistre->heure_sinistre)
                    <div class="info-row">
                        <div class="info-label">Heure :</div>
                        <div class="info-value">{{ $sinistre->heure_sinistre }}</div>
                    </div>
                @endif

                <div class="info-row">
                    <div class="info-label">Lieu :</div>
                    <div class="info-value">{{ $sinistre->lieu_sinistre }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Assuré :</div>
                    <div class="info-value">{{ $sinistre->nom_assure }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Téléphone :</div>
                    <div class="info-value">{{ $sinistre->telephone_assure }}</div>
                </div>

                @if ($sinistre->email_assure)
                    <div class="info-row">
                        <div class="info-label">Email :</div>
                        <div class="info-value">{{ $sinistre->email_assure }}</div>
                    </div>
                @endif

                <div class="info-row">
                    <div class="info-label">N° Police :</div>
                    <div class="info-value">{{ $sinistre->numero_police }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Conducteur :</div>
                    <div class="info-value">{{ $sinistre->conducteur_nom }}</div>
                </div>

                @if ($sinistre->constat_autorite)
                    <div class="info-row">
                        <div class="info-label">Constat d'autorité :</div>
                        <div class="info-value priority-high">✅ Oui</div>
                    </div>

                    @if ($sinistre->officier_nom)
                        <div class="info-row">
                            <div class="info-label">Officier :</div>
                            <div class="info-value">{{ $sinistre->officier_nom }}</div>
                        </div>
                    @endif

                    @if ($sinistre->commissariat)
                        <div class="info-row">
                            <div class="info-label">Commissariat :</div>
                            <div class="info-value">{{ $sinistre->commissariat }}</div>
                        </div>
                    @endif
                @endif

                <div class="info-row">
                    <div class="info-label">Date de déclaration :</div>
                    <div class="info-value">{{ $sinistre->created_at->format('d/m/Y à H:i') }}</div>
                </div>
            </div>

            @if ($sinistre->circonstances)
                <div style="margin: 20px 0;">
                    <h4 style="color: #333;">Circonstances du sinistre :</h4>
                    <div
                        style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #17a2b8;">
                        {{ $sinistre->circonstances }}
                    </div>
                </div>
            @endif

            @if ($sinistre->dommages_releves)
                <div style="margin: 20px 0;">
                    <h4 style="color: #333;">Dommages relevés :</h4>
                    <div
                        style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;">
                        {{ $sinistre->dommages_releves }}
                    </div>
                </div>
            @endif

            {{-- <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $url_sinistre }}" class="action-button">
                    📋 Consulter le Dossier Complet
                </a>
            </div> --}}

            <div style="background-color: #e9ecef; padding: 15px; border-radius: 5px; margin-top: 20px;">
                <p style="margin: 0; font-size: 14px; color: #666;">
                    <strong>Action requise :</strong> Ce sinistre nécessite une prise en charge dans les plus brefs
                    délais.
                    Veuillez consulter le dossier complet et assigner un gestionnaire si nécessaire.
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
                Cet email a été généré automatiquement par le système de gestion des sinistres.
            </div>
        </div>
    </div>
</body>

</html>
