# Implémentation du Suivi de Sinistre - Documentation

## Vue d'ensemble

Fonctionnalité ajoutée : Suivi de sinistre public directement sur la page d'accueil.

## Fichiers créés/modifiés

### 1. Contrôleur Backend
**Fichier**: `app/Http/Controllers/SuiviSinistreController.php`

- Méthode `rechercher()` pour l'API de recherche
- Recherche par `numero_sinistre` OU `numero_police`
- Validation des données d'entrée
- Retourne JSON avec informations publiques uniquement
- Gestion complète des erreurs

### 2. Routes
**Fichier**: `routes/web.php`

- Route POST `/api/suivi-sinistre` ajoutée
- Accès public (pas d'authentification requise)
- Nom de route : `suivi.rechercher`

### 3. Interface Frontend
**Fichier**: `resources/views/welcome.blade.php`

Modifications majeures :
- Système d'onglets avec Alpine.js
- Onglet 1 : "Déclarer un sinistre" (contenu existant conservé)
- Onglet 2 : "Suivre mon sinistre" (nouveau)
- Formulaire de recherche interactif
- Affichage dynamique des résultats
- Gestion des erreurs avec messages clairs
- JavaScript intégré pour l'appel AJAX

## Fonctionnalités

### Recherche
- Par numéro de sinistre (ex: APP-00001-2026)
- Par numéro d'attestation/police (ex: FNIHY558)
- Validation côté client et serveur

### Affichage des résultats
Informations publiques affichées :
- ✅ Numéro de sinistre
- ✅ Statut (avec badge coloré selon l'état)
- ✅ Date de déclaration
- ✅ Date du sinistre
- ✅ Lieu du sinistre
- ✅ Gestionnaire assigné (ou "En attente d'affectation")
- ✅ Nombre de jours en cours
- ✅ Indicateur de retard si applicable

Informations privées (NON affichées) :
- ❌ Montants
- ❌ Documents
- ❌ Messages/chat

### États du statut
Couleurs des badges selon le statut :
- Jaune : En attente
- Bleu : En cours
- Violet : Expertise requise
- Orange : En attente de documents
- Indigo : Prêt pour règlement
- Vert : Réglé
- Rouge : Refusé
- Gris : Clos

## Sécurité

- Protection CSRF activée
- Validation des entrées
- Seules les données publiques sont exposées
- Utilisation d'Eloquent (protection contre SQL injection)
- Gestion propre des erreurs (pas d'exposition de détails système)

## Tests effectués

1. ✅ Route correctement enregistrée (`php artisan route:list`)
2. ✅ Base de données testée (3 sinistres trouvés)
3. ✅ Recherche par numéro de sinistre fonctionnelle
4. ✅ Recherche par numéro de police fonctionnelle
5. ✅ Aucune erreur de linting
6. ✅ Code validé syntaxiquement

## Utilisation

### Pour les utilisateurs
1. Aller sur la page d'accueil du site
2. Cliquer sur l'onglet "Suivre mon sinistre"
3. Entrer soit :
   - Le numéro de sinistre reçu par email/SMS
   - Le numéro d'attestation de leur police
4. Cliquer sur "Rechercher"
5. Consulter l'état d'avancement

### Pour le développement
Exemple d'appel API :

```bash
curl -X POST http://localhost/api/suivi-sinistre \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"numero": "APP-00001-2026"}'
```

Réponse attendue :
```json
{
  "success": true,
  "sinistre": {
    "numero_sinistre": "APP-00001-2026",
    "statut": "en_attente",
    "statut_libelle": "En attente",
    "statut_couleur": "yellow",
    "date_declaration": "11/02/2026",
    "date_sinistre": "10/02/2026",
    "lieu_sinistre": "Abidjan, Cocody",
    "gestionnaire": "Jean Dupont",
    "jours_en_cours": 1,
    "en_retard": false,
    "expertise_requise": false
  }
}
```

## Design

- Style cohérent avec le reste de l'application SAAR
- Utilisation des couleurs de la marque (orange, vert, bleu)
- Responsive (mobile-first)
- Animations douces pour une meilleure UX
- Icônes SVG pour chaque information
- Badges colorés pour les statuts

## Notes techniques

- Alpine.js utilisé pour la réactivité (déjà présent dans le projet)
- Tailwind CSS pour le styling
- Fetch API pour les appels AJAX
- CSRF token inclus dans les requêtes
- Gestion du loading state pendant la recherche

## Améliorations futures possibles

- Rate limiting sur l'API pour éviter les abus
- Historique des recherches (local storage)
- Partage du lien de suivi par email
- QR code pour accès rapide
- Notifications push pour les mises à jour de statut
- Timeline visuelle plus détaillée
- Export PDF du statut
