<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe - {{ $company['name'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #dc2626;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .content {
            margin: 20px 0;
        }
        .credentials-box {
            background-color: #fef2f2;
            border: 2px solid #dc2626;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .credential-item {
            margin: 10px 0;
            font-size: 16px;
        }
        .credential-label {
            font-weight: bold;
            color: #dc2626;
        }
        .credential-value {
            font-family: monospace;
            background-color: #e5e7eb;
            padding: 5px 10px;
            border-radius: 4px;
            margin-left: 10px;
            font-size: 18px;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            background-color: #dc2626;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $company['name'] }}</h1>
            <p>Réinitialisation de mot de passe</p>
        </div>

        <div class="content">
            <h2>Bonjour {{ $user->nom_complet }},</h2>
            
            <p>Vous avez demandé la réinitialisation de votre mot de passe. Voici vos nouvelles informations de connexion :</p>

            <div class="credentials-box">
                <div class="credential-item">
                    <span class="credential-label">Email :</span>
                    <span class="credential-value">{{ $user->email }}</span>
                </div>
                
                <div class="credential-item">
                    <span class="credential-label">Nouveau mot de passe temporaire :</span>
                    <span class="credential-value">{{ $motDePasseTemporaire }}</span>
                </div>
            </div>

            <div class="warning">
                <strong>Important :</strong> Ce mot de passe est temporaire et expire dans 24 heures. 
                Vous devrez le changer lors de votre prochaine connexion.
            </div>

            <div style="text-align: center;">
                <a href="{{ $urlConnexion }}" class="button">Se connecter maintenant</a>
            </div>

            <h3>Instructions :</h3>
            <ol>
                <li>Cliquez sur le bouton "Se connecter maintenant" ci-dessus</li>
                <li>Utilisez votre email comme identifiant</li>
                <li>Saisissez le mot de passe temporaire fourni</li>
                <li>Vous serez automatiquement redirigé pour changer votre mot de passe</li>
            </ol>

            <p><strong>Si vous n'avez pas demandé cette réinitialisation,</strong> veuillez ignorer cet email et contacter immédiatement notre support.</p>
        </div>

        <div class="footer">
            <p><strong>{{ $company['name'] }}</strong></p>
            <p>{{ $company['address'] }}</p>
            <p>Téléphone : {{ $company['phone'] }}</p>
            <p>Email : {{ $company['email'] }}</p>
            <p><small>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</small></p>
        </div>
    </div>
</body>
</html>
