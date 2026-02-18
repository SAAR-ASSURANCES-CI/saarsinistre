<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Fiche Sinistre - {{ $sinistre->numero_sinistre }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #1a202c;
            margin: 0;
            padding: 24px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e53e3e;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }

        .brand {
            color: #e53e3e;
            font-weight: bold;
            font-size: 20px;
        }

        .meta {
            text-align: right;
            font-size: 10px;
            color: #4a5568;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
            margin: 12px 0 16px;
        }

        .section {
            margin-bottom: 16px;
        }

        .section h3 {
            margin: 0 0 8px;
            font-size: 13px;
            color: #2d3748;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        .grid {
            display: table;
            width: 100%;
        }

        .row {
            display: table-row;
        }

        .cell-label {
            display: table-cell;
            width: 38%;
            font-weight: bold;
            padding: 3px 10px 3px 0;
            vertical-align: top;
            color: #2d3748;
        }

        .cell-value {
            display: table-cell;
            padding: 3px 0;
            vertical-align: top;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            background: #edf2f7;
            color: #2d3748;
        }

        .list {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
        }

        .list .item {
            padding: 6px 0;
            border-bottom: 1px dotted #cbd5e0;
            display: flex;
            justify-content: space-between;
        }

        .list .item:last-child {
            border-bottom: none;
        }

        .muted {
            color: #718096;
            font-size: 11px;
        }

        .footer {
            margin-top: 22px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #718096;
        }
    </style>
</head>

<body>
    <div class="header">
        <div>
            <div class="brand">{{ $company['name'] }}</div>
            <div class="muted">{{ $company['address'] }}</div>
            <div class="muted">Tél: {{ $company['phone'] }} | Email: {{ $company['email'] }}</div>
        </div>
        <div class="meta">
            <div>Fiche sinistre</div>
            <div>Généré le {{ $date_generation }}</div>
            <div>N°: <strong>{{ $sinistre->numero_sinistre }}</strong></div>
        </div>
    </div>

    <div class="section">
        <div class="title">Informations de l'assuré</div>
        <div class="grid">
            <div class="row">
                <div class="cell-label">Nom complet</div>
                <div class="cell-value">{{ $sinistre->nom_assure }}</div>
            </div>
            <div class="row">
                <div class="cell-label">Téléphone</div>
                <div class="cell-value">{{ $sinistre->telephone_assure }}</div>
            </div>
            <div class="row">
                <div class="cell-label">Email</div>
                <div class="cell-value">{{ $sinistre->email_assure }}</div>
            </div>
            <div class="row">
                <div class="cell-label">N° de police</div>
                <div class="cell-value">{{ $sinistre->numero_police }}</div>
            </div>
        </div>
    </div>
@if($sinistre->vehicule)
<div class="section">
    <div class="title">Informations du véhicule</div>
    <div class="grid">
        <div class="row">
            <div class="cell-label">Marque</div>
            <div class="cell-value">{{ $sinistre->vehicule->marque }}</div>
        </div>
        @if($sinistre->vehicule->modele)
        <div class="row">
            <div class="cell-label">Modèle</div>
            <div class="cell-value">{{ $sinistre->vehicule->modele }}</div>
        </div>
        @endif
        <div class="row">
            <div class="cell-label">Immatriculation</div>
            <div class="cell-value">{{ $sinistre->vehicule->immatriculation }}</div>
        </div>
    </div>
</div>
@endif

    <div class="section">
        <div class="title">Détails du sinistre</div>
        <div class="grid">
            <div class="row">
                <div class="cell-label">Date</div>
                <div class="cell-value">{{ $sinistre->date_sinistre }}</div>
            </div>
            @if ($sinistre->heure_sinistre)
                <div class="row">
                    <div class="cell-label">Heure</div>
                    <div class="cell-value">{{ $sinistre->heure_sinistre }}</div>
                </div>
            @endif
            <div class="row">
                <div class="cell-label">Lieu</div>
                <div class="cell-value">{{ $sinistre->lieu_sinistre }}</div>
            </div>
            <div class="row">
                <div class="cell-label">Conducteur</div>
                <div class="cell-value">{{ $sinistre->conducteur_nom }}</div>
            </div>
            <div class="row">
                <div class="cell-label">Statut</div>
                <div class="cell-value"><span
                        class="badge">{{ ucfirst(str_replace('_', ' ', $sinistre->statut)) }}</span></div>
            </div>
            <div class="row">
                <div class="cell-label">Gestionnaire</div>
                <div class="cell-value">{{ optional($sinistre->gestionnaire)->nom_complet ?? 'Non assigné' }}</div>
            </div>
        </div>
    </div>

    @if ($sinistre->circonstances)
        <div class="section">
            <div class="title">Circonstances</div>
            <div class="list" style="font-style: italic;">{{ $sinistre->circonstances }}</div>
        </div>
    @endif

    <div class="section">
        <div class="title">Documents fournis</div>
        <div class="list">
            @forelse ($sinistre->documents as $document)
                <div class="item">
                    <div>
                        <strong>{{ $document->libelle_document ?? ucfirst(str_replace('_', ' ', $document->type_document)) }}</strong>
                        <div class="muted">{{ $document->nom_fichier }} • {{ $document->type_mime }}</div>
                    </div>
                    <div class="muted">{{ $document->taille_formatee }}</div>
                </div>
            @empty
                <div class="muted">Aucun document fourni.</div>
            @endforelse
        </div>
    </div>

    @if ($sinistre->implique_tiers)
        <div class="section">
            <div class="title">Informations sur le tiers</div>
            <div class="grid">
                <div class="row">
                    <div class="cell-label">Impliqué un tiers</div>
                    <div class="cell-value">{{ $sinistre->implique_tiers ? 'Oui' : 'Non' }}</div>
                </div>
                @if ($sinistre->details_tiers)
                    <div class="row">
                        <div class="cell-label">Détails</div>
                        <div class="cell-value">{{ $sinistre->details_tiers }}</div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="footer">Document généré automatiquement. {{ $company['name'] }} — {{ $company['address'] }}</div>
</body>

</html>
