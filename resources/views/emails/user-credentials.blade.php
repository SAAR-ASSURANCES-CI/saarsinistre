<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos informations de connexion - {{ $company['name'] }}</title>
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
            background-color: #2c5aa0;
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
            background-color: #f8f9fa;
            border: 2px solid #2c5aa0;
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
            color: #2c5aa0;
        }
        .credential-value {
            font-family: monospace;
            background-color: #e9ecef;
            padding: 5px 10px;
            border-radius: 4px;
            margin-left: 10px;
            font-size: 18px;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            background-color: #2c5aa0;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
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
            <p>Bienvenue dans votre espace client</p>
        </div>

        <div class="content">
            <h2>Bonjour {{ $user->nom_complet }},</h2>
            
            <p>Votre compte {{ $company['name'] }} a été créé avec succès. Voici vos informations de connexion :</p>

            <div class="credentials-box">
                <div class="credential-item">
                    <span class="credential-label">Email :</span>
                    <span class="credential-value">{{ $user->email }}</span>
                </div>
                
                <div class="credential-item">
                    <span class="credential-label">Mot de passe temporaire :</span>
                    <span class="credential-value">{{ $motDePasseTemporaire }}</span>
                </div>
            </div>

            <div class="warning">
                <strong>Important :</strong> Ce mot de passe est temporaire et expire dans 48 heures. 
                Veuillez vous connecter rapidement et changer votre mot de passe lors de votre première connexion.
            </div>

            <div style="text-align: center;">
                <a href="{{ $urlConnexion }}" class="button">Se connecter maintenant</a>
            </div>

            <h3>Instructions de première connexion :</h3>
            <ol>
                <li>Cliquez sur le bouton "Se connecter maintenant" ci-dessus</li>
                <li>Utilisez votre email comme identifiant</li>
                <li>Saisissez le mot de passe temporaire fourni</li>
                <li>Vous serez invité à changer votre mot de passe</li>
            </ol>

            <p>Si vous rencontrez des difficultés pour vous connecter, n'hésitez pas à nous contacter.</p>
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
