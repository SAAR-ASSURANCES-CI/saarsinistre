<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Fiche d'Expertise - {{ $expertise->sinistre->numero_sinistre }}</title>
    <style>
        @page {
            margin: 15mm 20mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header {
            border: 3px solid #e53e3e;
            padding: 8px 12px;
            margin-bottom: 15px;
            position: relative;
            min-height: 60px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .logo-section {
            flex: 1;
        }

        .logo-image {
            max-width: 100px;
            height: auto;
            margin-bottom: 5px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #e53e3e;
            margin-bottom: 5px;
        }

        .insurance-types {
            font-size: 8px;
            line-height: 1.4;
        }

        .insurance-types .category {
            margin-bottom: 2px;
        }

        .insurance-types .category strong {
            font-size: 9px;
            font-weight: bold;
        }

        .document-title {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }

        .section {
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #000;
        }

        .info-row {
            margin-bottom: 4px;
            font-size: 10px;
            line-height: 1.5;
        }

        .info-label {
            font-weight: bold;
            display: inline;
        }

        .info-value {
            display: inline;
            border-bottom: 1px solid #000;
            min-width: 200px;
            padding-left: 5px;
        }

        .info-line {
            border-bottom: 1px solid #000;
            min-height: 14px;
            padding-left: 3px;
        }

        .operations-intro {
            font-size: 10px;
            margin: 10px 0 5px 0;
            font-weight: normal;
        }

        .operations-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0 15px 0;
            font-size: 9px;
        }

        .operations-table th,
        .operations-table td {
            border: 2px solid #000;
            padding: 5px;
            text-align: left;
        }

        .operations-table th {
            background-color: #fff;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }

        .operations-table .libelle-col {
            width: 60%;
        }

        .operations-table .checkbox-col {
            width: 10%;
            text-align: center;
        }

        .operations-table .checkbox-cell {
            text-align: center;
            padding: 3px;
        }

        .checkbox-mark {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #000;
            background-color: #fff;
            vertical-align: middle;
        }

        .checkbox-mark.checked {
            background-color: #000;
            position: relative;
        }

        .checkbox-mark.checked::after {
            content: '✓';
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            display: block;
            text-align: center;
            line-height: 14px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            margin-bottom: 15px;
        }

        .signature-block {
            width: 45%;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 9px;
            text-align: center;
        }

        .footer {
            border: 2px solid #000;
            padding: 8px;
            margin-top: 15px;
            font-size: 8px;
            text-align: center;
            background-color: #f9f9f9;
        }

        .footer-line {
            margin: 2px 0;
            line-height: 1.4;
        }

        .footer-title {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 3px;
        }
    </style>
</head>

<body>
    <!-- En-tête avec logo et types d'assurance -->
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                <img src="{{ public_path('logo.png') }}" alt="SAAR" class="logo-image">
                <div class="insurance-types">
                    <div class="category">
                        <strong>VIE</strong> : Epargne ; Prévoyance ; Capitalisation
                    </div>
                    <div class="category">
                        <strong>NON VIE</strong> : Automobile ; Transport ; Risque Divers ; Responsabilité Civile ; Caution ; santé
                    </div>
                </div>
            </div>
            <div class="document-title">Fiche d'Expertise</div>
        </div>
    </div>

    <!-- Informations générales -->
    <div class="section">
        <div class="info-row">
            <span class="info-label">Date :</span> {{ $expertise->date_expertise->format('d/m/Y') }}
            <span style="margin-left: 100px;"><span class="info-label">client :</span> <span class="info-line" style="display:inline-block; min-width:200px;">{{ $expertise->client_nom }}</span></span>
            <span style="margin-left: 30px;"><span class="info-label">mandant SAAR :</span></span>
        </div>
    </div>

    <!-- Informations collaborateur -->
    <div class="section">
        <div class="section-title">Notre Collaborateur</div>
        <div class="info-row">
            <span class="info-label">M. {{ $expertise->collaborateur_nom }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">TEL :</span> {{ $expertise->collaborateur_telephone }}
        </div>
        <div class="info-row">
            <span class="info-label">E-mail:</span> {{ $expertise->collaborateur_email }}
        </div>
    </div>

    <!-- Informations expertise -->
    <div class="section">
        <div class="info-row">
            <span class="info-label">A procédé à l'expertise de votre véhicule</span> <span class="info-line" style="display:inline-block; min-width:300px;"></span>
        </div>
        <div class="info-row">
            <span class="info-label">Dans la commune de :</span> <span class="info-line" style="display:inline-block; min-width:250px;">{{ $expertise->lieu_expertise }}</span>
            <span style="margin-left: 30px;"><span class="info-label">contact client :</span> <span class="info-line" style="display:inline-block; min-width:150px;">{{ $expertise->contact_client ?? '' }}</span></span>
        </div>
    </div>

    <!-- Tableau des opérations -->
    <div class="section">
        <div class="operations-intro">
            Dont la remise en état nécessite les opérations suivantes :
        </div>
        <table class="operations-table">
            <thead>
                <tr>
                    <th class="libelle-col">LIBELLE</th>
                    <th class="checkbox-col">ECH</th>
                    <th class="checkbox-col">REP</th>
                    <th class="checkbox-col">CTL</th>
                    <th class="checkbox-col">P</th>
                </tr>
            </thead>
            <tbody>
                @if($expertise->operations && count($expertise->operations) > 0)
                    @foreach($expertise->operations as $operation)
                        <tr>
                            <td>{{ $operation['libelle'] ?? '' }}</td>
                            <td class="checkbox-cell">
                                <span class="checkbox-mark {{ ($operation['echange'] ?? false) ? 'checked' : '' }}"></span>
                            </td>
                            <td class="checkbox-cell">
                                <span class="checkbox-mark {{ ($operation['reparation'] ?? false) ? 'checked' : '' }}"></span>
                            </td>
                            <td class="checkbox-cell">
                                <span class="checkbox-mark {{ ($operation['controle'] ?? false) ? 'checked' : '' }}"></span>
                            </td>
                            <td class="checkbox-cell">
                                <span class="checkbox-mark {{ ($operation['peinture'] ?? false) ? 'checked' : '' }}"></span>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Zones de signature -->
    <div class="signatures">
        <div class="signature-block">
            <div class="signature-line">
                Propriétaire ou Représentant (e)
            </div>
        </div>
        <div class="signature-block">
            <div class="signature-line">
                Prestataire
            </div>
        </div>
    </div>

    <!-- Pied de page avec informations SAAR -->
    <div class="footer">
        <div class="footer-title">SAAR Assurances</div>
        <div class="footer-line">Deux plateaux Aghien -01 - BP 12201 Abidjan 01 -Côte d'Ivoire</div>
        <div class="footer-line">Téléphone : (225) 27 22 50 81 50 / Fax : (225) 27 22 50 25 12</div>
        <div class="footer-line">E-mail: infos@saar-assurances.com – WWW.saar-assurances.com</div>
    </div>
</body>

</html>
