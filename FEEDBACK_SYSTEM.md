# Système de Feedback - Assurance

## Vue d'ensemble

Ce système de feedback permet aux assurés de donner leur avis sur les services de l'assurance. Il se déclenche automatiquement quand l'assuré se connecte et qu'il a des sinistres clos ou réglés nécessitant un feedback.

## Fonctionnalités

### 🚀 Côté Assuré
- **Popup automatique** : S'affiche à la connexion si des sinistres nécessitent un feedback
- **Note de service** : Système de notation de 1 à 5 étoiles
- **Humeur avec emoticons** : 5 niveaux de satisfaction (😊 😐 😠)
- **Commentaires libres** : Possibilité d'ajouter des suggestions
- **Indicateur visuel** : Badge "Feedback requis" dans la liste des sinistres

### 🎯 Côté Gestionnaire
- **Tableau de bord des feedbacks** : Vue d'ensemble avec statistiques
- **Filtres avancés** : Par note, humeur, dates
- **Détails complets** : Vue détaillée de chaque feedback
- **Export CSV** : Export des données pour analyse

## Comment ça fonctionne

### 1. Déclenchement automatique
- Quand un assuré se connecte, le système vérifie s'il a des sinistres clos/réglés
- Si oui, une popup s'affiche automatiquement après 1 seconde
- L'assuré peut choisir de donner son avis immédiatement ou plus tard

### 2. Interface intuitive
- La popup liste tous les sinistres nécessitant un feedback
- Chaque sinistre a un lien direct vers le formulaire de feedback
- Un badge orange indique les sinistres nécessitant un feedback dans la liste

### 3. Gestion des feedbacks
- Les gestionnaires peuvent consulter tous les avis
- Filtres par note, humeur, dates
- Export des données pour analyse externe

## Installation

### 1. Migration de la base de données
```bash
php artisan migrate
```

### 2. Seeder (optionnel - pour les tests)
```bash
php artisan db:seed --class=FeedbackSeeder
```

## Utilisation

### Côté Assuré
- Se connecter au dashboard
- La popup s'affiche automatiquement si des feedbacks sont nécessaires
- Cliquer sur "Commencer maintenant" ou "Plus tard"
- Accéder aux formulaires via les liens dans la popup ou les badges dans la liste

### Côté Gestionnaire
- Accéder à `/dashboard/feedback`
- Consulter les statistiques et la liste des feedbacks
- Utiliser les filtres pour analyser les données
- Exporter en CSV si nécessaire

## Routes disponibles

#### Côté Assuré
- `GET /sinistres/{sinistre}/feedback` - Formulaire de feedback
- `POST /sinistres/{sinistre}/feedback` - Enregistrement du feedback

#### Côté Gestionnaire
- `GET /dashboard/feedback` - Liste des feedbacks
- `GET /dashboard/feedback/{feedback}` - Détails d'un feedback
- `GET /dashboard/feedback/export/csv` - Export CSV

## Personnalisation

### Modifier le délai d'affichage de la popup
Dans `resources/views/assures/partials/feedback-popup.blade.php` :
```javascript
setTimeout(function() {
    document.getElementById('feedback-popup').style.display = 'block';
}, 1000); // Modifier cette valeur (en millisecondes)
```

### Modifier les emoticons
Dans `app/Models/Feedback.php` :
```php
protected function getHumeurLibelleAttribute(): string
{
    $humeurs = [
        '😊' => 'Très satisfait',
        '🙂' => 'Satisfait',
        // ... personnalisez ici
    ];
}
```

## Avantages de cette approche

1. **Non-intrusif** : L'assuré n'est pas bombardé d'emails
2. **Contextuel** : Le feedback est demandé au bon moment (connexion)
3. **Flexible** : L'assuré peut choisir de répondre immédiatement ou plus tard
4. **Visuel** : Indicateurs clairs dans l'interface
5. **Automatique** : Aucune action manuelle requise de la part des gestionnaires

## Maintenance

- Les feedbacks sont stockés en base de données
- Aucun job en queue ou notification email à gérer
- Le système est léger et performant
- Facile à déboguer et maintenir
