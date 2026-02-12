<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche d'Expertise</title>
    <style>
        @page {
            margin: 20mm 15mm;
            size: A4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }

        .header img {
            max-width: 180px;
            height: auto;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            color: #c41e3a;
            margin-top: 10px;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .info-item {
            display: table-cell;
            width: 33.33%;
            padding: 5px 10px;
            vertical-align: top;
        }

        .info-item strong {
            display: block;
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 9pt;
            color: #555;
        }

        .info-item span {
            display: block;
            font-size: 10pt;
            color: #000;
        }

        .operations-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
            page-break-inside: auto;
            border: 2px solid #333;
        }

        .operations-table thead {
            background-color: #f0f0f0;
        }

        .operations-table th {
            border: 1px solid #333;
            padding: 8px 5px;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
            background-color: #e8e8e8;
        }

        .operations-table td {
            border: 1px solid #333;
            padding: 4px 6px;
            font-size: 9pt;
        }

        .operations-table td.libelle {
            text-align: left;
            width: 60%;
        }

        .operations-table td.checkbox {
            text-align: center;
            width: 10%;
            font-size: 10pt;
            font-family: 'Courier New', monospace;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            font-size: 8pt;
            text-align: center;
            color: #666;
        }

        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            padding: 10px;
            text-align: center;
        }

        .signature-box strong {
            display: block;
            margin-bottom: 40px;
            font-size: 10pt;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 60%;
            margin: 0 auto;
            padding-top: 5px;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists(public_path('logo.png')))
            <img src="{{ public_path('logo.png') }}" alt="Logo SAAR">
        @endif
        <h1>FICHE D'EXPERTISE</h1>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-item">
                <strong>Date d'expertise :</strong>
                <span>{{ $expertise->date_expertise->format('d/m/Y') }}</span>
            </div>
            <div class="info-item">
                <strong>Sinistre N° :</strong>
                <span>{{ $expertise->sinistre->numero_sinistre ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <strong>Mandant :</strong>
                <span>SAAR Assurances</span>
            </div>
        </div>

        <div class="info-row">
            <div class="info-item">
                <strong>Nom du client :</strong>
                <span>{{ $expertise->client_nom }}</span>
            </div>
            <div class="info-item">
                <strong>Téléphone client :</strong>
                <span>{{ $expertise->contact_client }}</span>
            </div>
            <div class="info-item">
                <strong>Véhicule expertisé :</strong>
                <span>{{ $expertise->vehicule_expertise }}</span>
            </div>
        </div>

        <div class="info-row">
            <div class="info-item">
                <strong>Lieu d'expertise :</strong>
                <span>{{ $expertise->lieu_expertise }}</span>
            </div>
            <div class="info-item">
                <strong>Expert :</strong>
                <span>{{ $expertise->collaborateur_nom }}</span>
            </div>
            <div class="info-item">
                <strong>Contact expert :</strong>
                <span>{{ $expertise->collaborateur_telephone }}</span>
            </div>
        </div>

        <div class="info-row">
            <div class="info-item">
                <strong>E-mail :</strong>
                <span>{{ $expertise->collaborateur_email }}</span>
            </div>
        </div>
    </div>

    <table class="operations-table">
        <thead>
            <tr>
                <th>LIBELLÉ</th>
                <th>ECH</th>
                <th>REP</th>
                <th>CTL</th>
                <th>P</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expertise->operations ?? [] as $operation)
                <tr>
                    <td class="libelle">{{ $operation['libelle'] ?? '' }}</td>
                    <td class="checkbox">{{ ($operation['echange'] ?? false) ? '[X]' : '[ ]' }}</td>
                    <td class="checkbox">{{ ($operation['reparation'] ?? false) ? '[X]' : '[ ]' }}</td>
                    <td class="checkbox">{{ ($operation['controle'] ?? false) ? '[X]' : '[ ]' }}</td>
                    <td class="checkbox">{{ ($operation['peinture'] ?? false) ? '[X]' : '[ ]' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #999;">
                        Aucune opération enregistrée
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <strong>Propriétaire du Représentant :</strong>
            <div class="signature-line">Signature</div>
        </div>
        <div class="signature-box">
            <strong>Prestataire :</strong>
            <div class="signature-line">Signature</div>
        </div>
    </div>

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} - SAAR Assurances CI
    </div>
</body>
</html>
