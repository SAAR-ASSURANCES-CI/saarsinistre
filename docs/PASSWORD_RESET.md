# Réinitialisation de mot de passe pour les assurés

## Vue d'ensemble

Cette fonctionnalité permet aux assurés de réinitialiser leur mot de passe en cas d'oubli, en utilisant leur numéro de téléphone et un code de vérification envoyé par SMS.

## Processus de réinitialisation

### 1. Demande de réinitialisation
- L'assuré clique sur "Mot de passe oublié ?" sur la page de connexion
- Il entre son numéro de téléphone utilisé lors de la déclaration de sinistre
- Le système vérifie que ce numéro est associé à un sinistre existant
- Un code de vérification à 6 chiffres est généré et envoyé par SMS

### 2. Vérification du code
- L'assuré reçoit le code par SMS
- Il entre le code dans le formulaire de vérification
- Le code expire après 10 minutes
- Une fois vérifié, le code est marqué comme utilisé

### 3. Nouveau mot de passe
- L'assuré définit son nouveau mot de passe
- Le mot de passe doit contenir au moins 8 caractères
- Une confirmation est requise
- L'ancien mot de passe est remplacé

## Sécurité

- Les codes expirent automatiquement après 10 minutes
- Chaque code ne peut être utilisé qu'une seule fois
- Les anciens codes sont automatiquement invalidés lors de la création d'un nouveau
- La session est nettoyée après la réinitialisation

## Configuration requise

### Variables d'environnement
```env
ORANGE_CLIENT_ID=your_client_id
ORANGE_CLIENT_SECRET=your_client_secret
ORANGE_SMS_API_TOKEN_URL=your_token_url
ORANGE_SMS_API_SEND_URL=your_send_url
ORANGE_SMS_SENDER_NUMBER=your_sender_number
```

### Base de données
La migration `create_password_reset_codes_table` doit être exécutée :
```bash
php artisan migrate
```

## Maintenance

### Nettoyage automatique
Une commande Artisan est disponible pour nettoyer les codes expirés :
```bash
php artisan reset-codes:clean
```

### Planification
Il est recommandé d'ajouter cette commande au planificateur de tâches :
```php
// Dans app/Console/Kernel.php
$schedule->command('reset-codes:clean')->daily();
```

## Tests

Des tests automatisés sont disponibles dans `tests/Feature/PasswordResetTest.php` :
```bash
php artisan test --filter=PasswordResetTest
```

## Routes

- `GET /password/reset` - Formulaire de demande
- `POST /password/reset` - Envoi du code
- `GET /password/reset/verify` - Vérification du code
- `POST /password/reset/verify` - Traitement de la vérification
- `GET /password/reset/new` - Nouveau mot de passe
- `POST /password/reset/new` - Mise à jour du mot de passe

## Modèles

### PasswordResetCode
- `telephone` : Numéro de téléphone de l'assuré
- `code` : Code de vérification à 6 chiffres
- `expires_at` : Date d'expiration du code
- `used` : Indique si le code a été utilisé

## Contrôleurs

### PasswordResetController
Gère tout le processus de réinitialisation :
- Validation des numéros de téléphone
- Génération et envoi des codes
- Vérification des codes
- Mise à jour des mots de passe

## Utilisation

1. L'assuré accède à la page de connexion
2. Il clique sur "Mot de passe oublié ?"
3. Il entre son numéro de téléphone
4. Il reçoit un SMS avec le code
5. Il entre le code reçu
6. Il définit son nouveau mot de passe
7. Il peut se connecter avec le nouveau mot de passe

## Support

En cas de problème :
- Vérifier la configuration Orange SMS API
- Consulter les logs Laravel
- Vérifier que la base de données est accessible
- S'assurer que les migrations ont été exécutées
