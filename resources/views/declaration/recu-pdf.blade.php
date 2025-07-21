<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Attestation de Déclaration - {{ $sinistre->numero_sinistre }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #e53e3e;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #e53e3e;
            margin-bottom: 5px;
        }

        .document-title {
            font-size: 18px;
            font-weight: bold;
            color: #2d3748;
            margin-top: 15px;
        }

        .sinistre-number {
            background: #ebf8ff;
            border: 2px solid #3182ce;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }

        .sinistre-number .label {
            font-size: 10px;
            color: #3182ce;
            font-weight: bold;
            text-transform: uppercase;
        }

        .sinistre-number .number {
            font-size: 20px;
            font-weight: bold;
            color: #1a365d;
            margin-top: 5px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2d3748;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 40%;
            padding: 3px 10px 3px 0;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            padding: 3px 0;
            vertical-align: top;
        }

        .status-badge {
            background: #fef5e7;
            color: #744210;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }

        .documents-list {
            background: #f7fafc;
            padding: 15px;
            border-radius: 5px;
        }

        .document-item {
            padding: 5px 0;
            border-bottom: 1px dotted #cbd5e0;
        }

        .document-item:last-child {
            border-bottom: none;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 10px;
            color: #718096;
        }

        .footer-contact {
            text-align: center;
            margin-bottom: 10px;
        }

        .footer-note {
            text-align: center;
            font-style: italic;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(229, 62, 62, 0.1);
            z-index: -1;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="watermark">SAAR ASSURANCES</div>

    <!-- En-tête -->
    <div class="header">
        <div class="company-name">{{ $company['name'] }}</div>
        <div>{{ $company['address'] }}</div>
        <div>Tél: {{ $company['phone'] }} | Email: {{ $company['email'] }}</div>
        <div class="document-title">ATTESTATION DE DÉCLARATION DE SINISTRE</div>
    </div>

    <!-- Numéro de sinistre -->
    <div class="sinistre-number">
        <div class="label">Numéro de sinistre</div>
        <div class="number">{{ $sinistre->numero_sinistre }}</div>
    </div>

    <!-- Informations de l'assuré -->
    <div class="section">
        <div class="section-title"><i class="fas fa-user"></i> INFORMATIONS DE L'ASSURÉ</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nom complet :</div>
                <div class="info-value">{{ $sinistre->nom_assure }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email :</div>
                <div class="info-value">{{ $sinistre->email_assure }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone :</div>
                <div class="info-value">{{ $sinistre->telephone_assure }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">N° Police :</div>
                <div class="info-value">{{ $sinistre->numero_police }}</div>
            </div>
        </div>
    </div>

    <!-- Détails du sinistre -->
    <div class="section">
        <div class="section-title">DÉTAILS DU SINISTRE</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Date du sinistre :</div>
                <div class="info-value">{{ $sinistre->date_sinistre->format('d/m/Y') }}</div>
            </div>
            @if ($sinistre->heure_sinistre)
                <div class="info-row">
                    <div class="info-label">Heure du sinistre :</div>
                    <div class="info-value">{{ $sinistre->heure_sinistre->format('H:i') }}</div>
                </div>
            @endif
            <div class="info-row">
                <div class="info-label">Lieu du sinistre :</div>
                <div class="info-value">{{ $sinistre->lieu_sinistre }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Conducteur :</div>
                <div class="info-value">{{ $sinistre->conducteur_nom }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Constat d'autorité :</div>
                <div class="info-value">{{ $sinistre->constat_autorite ? 'Oui' : 'Non' }}</div>
            </div>
            @if ($sinistre->constat_autorite && $sinistre->officier_nom)
                <div class="info-row">
                    <div class="info-label">Officier :</div>
                    <div class="info-value">{{ $sinistre->officier_nom }}</div>
                </div>
            @endif
            @if ($sinistre->constat_autorite && $sinistre->commissariat)
                <div class="info-row">
                    <div class="info-label">Commissariat :</div>
                    <div class="info-value">{{ $sinistre->commissariat }}</div>
                </div>
            @endif
            <div class="info-row">
                <div class="info-label">Statut :</div>
                <div class="info-value">
                    <span class="status-badge">{{ ucfirst(str_replace('_', ' ', $sinistre->statut)) }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Impliqué un tiers :</div>
                <div class="info-value">{{ $sinistre->implique_tiers ? 'Oui' : 'Non' }}</div>
            </div>
            @if ($sinistre->implique_tiers && $sinistre->details_tiers)
                <div class="info-row">
                    <div class="info-label">Détails tiers :</div>
                    <div class="info-value">{{ $sinistre->details_tiers }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Circonstances -->
    <div class="section">
        <div class="section-title"> CIRCONSTANCES</div>
        <div style="background: #f7fafc; padding: 10px; border-radius: 5px; font-style: italic;">
            {{ $sinistre->circonstances }}
        </div>
    </div>

    <!-- Prochaines étapes -->
    <div class="section">
        <div class="section-title"> PROCHAINES ÉTAPES</div>
        <div style="background: #ebf8ff; padding: 15px; border-radius: 5px;">
            <div style="margin-bottom: 8px;"><strong>1.</strong> Un gestionnaire sera assigné sous 24h ouvrées</div>
            <div style="margin-bottom: 8px;"><strong>2.</strong> Notre équipe étudiera votre dossier et vous contactera si nécessaire</div>
        </div>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        <div class="footer-note">
            Document généré le {{ $date_generation }} | Conservez ce reçu comme preuve de votre déclaration
        </div>
    </div>
</body>

</html>
