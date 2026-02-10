# Fonctionnalité Expertise - Documentation Complète

## Vue d'ensemble

Cette fonctionnalité permet aux gestionnaires de créer et générer des fiches d'expertise professionnelles en PDF à partir d'un template Word. Le système utilise PHPWord et LibreOffice pour garantir une fidélité parfaite au design du template.

---

## Table des matières

1. [Architecture](#architecture)
2. [Installation &amp; Configuration](#installation--configuration)
3. [Utilisation](#utilisation)
4. [Structure des fichiers](#structure-des-fichiers)
5. [Tests](#tests)
6. [Dépannage](#dépannage)

---

## Architecture

### Composants principaux

```
┌─────────────────┐
│  Interface UI   │  (Blade + JavaScript)
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Contrôleur     │  ExpertiseController
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Service PDF    │  ExpertisePdfService
└────────┬────────┘
         │
         ├──► PHPWord (manipulation du template)
         ├──► LibreOffice (conversion DOCX → PDF)
         └──► DomPDF (fallback si LibreOffice indisponible)
```

### Modèles et relations

```
User (expert)
  │
  │ hasMany
  ▼
Expertise ◄──hasOne── Sinistre
  │
  │ belongsTo
  ▼
Sinistre

⚠️ CONTRAINTE IMPORTANTE : Un sinistre ne peut avoir qu'UNE SEULE expertise
   - L'expertise peut être créée/modifiée par n'importe quel expert ou admin
   - La dernière sauvegarde écrase les données précédentes
  
Champs de l'expertise :
  - date_expertise
  - client_nom
  - lieu_expertise
  - vehicule_expertise
  - operations (JSON)
  - collaborateur_*
```

---

## Installation & Configuration

### Prérequis

- PHP 8.2+
- Laravel 12.0+
- Composer
- MySQL
- LibreOffice (optionnel mais recommandé)

### Étape 1 : Migration

```bash
php artisan migrate
```

Cette commande exécute la migration `add_vehicule_expertise_to_expertises_table` qui ajoute le champ `vehicule_expertise` à la table `expertises`.

### Étape 2 : Template Word

1. Le template est situé dans : `storage/templates/expertise_template.docx`
2. Le template doit contenir les placeholders suivants :

#### Placeholders obligatoires

| Placeholder                    | Description         | Exemple                     |
| ------------------------------ | ------------------- | --------------------------- |
| `${date_expertise}`          | Date de l'expertise | 06/02/2026                  |
| `${client_nom}`              | Nom du client       | Jean Dupont                 |
| `${mandant_saar}`            | Mandant (statique)  | SAAR Assurances             |
| `${collaborateur_nom}`       | Nom de l'expert     | Expert SAAR                 |
| `${collaborateur_telephone}` | Téléphone         | 0747707127                  |
| `${collaborateur_email}`     | Email               | expert@saar.com             |
| `${lieu_expertise}`          | Lieu/Commune        | Abidjan                     |
| `${contact_client}`          | Téléphone client  | 0225123456                  |
| `${vehicule_expertise}`      | Véhicule           | Toyota Corolla - AB 1234 CD |

#### Tableau des opérations

Le template doit contenir un tableau avec **une seule ligne de données** :

| LIBELLÉ                    | ECH                      | REP      | CTL | P |
| --------------------------- | ------------------------ | -------- | --- | - |
| `${libelle}` | `${ech}` | `${rep}` | `${ctl}` | `${p}` |     |   |

PHPWord clonera automatiquement cette ligne pour chaque opération.

### Étape 3 : LibreOffice (recommandé)

LibreOffice permet une conversion DOCX → PDF pixel-perfect.

**Installation Windows :**

```bash
# Télécharger depuis https://www.libreoffice.org/
# Installer en mode silencieux si nécessaire
```

**Installation Linux :**

```bash
sudo apt-get install libreoffice
```

**Installation Mac :**

```bash
brew install --cask libreoffice
```

Si LibreOffice n'est pas installé, le système utilisera automatiquement DomPDF comme fallback.

---

## Utilisation

### Interface utilisateur

1. **Accéder à un sinistre** depuis le dashboard
2. **Ouvrir la modale d'expertise**
3. **Remplir les champs** :

   - **Champs automatiques** (grisés, lecture seule) :
     - Date d'expertise
     - Nom du client
     - Informations du collaborateur
     - Contact client
   - **Champs à remplir par l'expert** :
     - Lieu d'expertise (obligatoire)
     - Véhicule expertisé (obligatoire)
     - Opérations (au moins 1 obligatoire)
4. **Ajouter des opérations** :

   - Cliquez sur "Ajouter une ligne"
   - Saisissez le libellé
   - Cochez au moins une case (ECH, REP, CTL, P)
5. **Actions disponibles** :

   - **Prévisualiser** : Ouvre le PDF dans un nouvel onglet
   - **Télécharger** : Enregistre et télécharge le PDF

### Workflow

```
1. Gestionnaire remplit le formulaire
   ↓
2. Clic sur "Prévisualiser" ou "Télécharger"
   ↓
3. Validation des données
   ↓
4. Sauvegarde dans la base de données
   ↓
5. Génération du Word depuis template
   ↓
6. Conversion en PDF (LibreOffice ou DomPDF)
   ↓
7. Affichage/Téléchargement du PDF
```

### API REST

#### Sauvegarder une expertise

```http
POST /gestionnaires/dashboard/sinistres/{sinistre_id}/expertise

{
  "lieu_expertise": "Abidjan",
  "vehicule_expertise": "Toyota Corolla - AB 1234 CD",
  "operations": [
    {
      "libelle": "Remplacement pare-brise",
      "echange": true,
      "reparation": false,
      "controle": false,
      "peinture": false
    }
  ]
}
```

#### Prévisualiser le PDF

```http
GET /gestionnaires/dashboard/sinistres/{sinistre_id}/expertise/preview
```

#### Télécharger le PDF

```http
GET /gestionnaires/dashboard/sinistres/{sinistre_id}/expertise/pdf
```

---

## Structure des fichiers

### Code source

```
app/
├── Models/
│   └── Expertise.php                    # Modèle Eloquent
├── Services/
│   └── ExpertisePdfService.php          # Service de génération PDF
├── Http/
│   └── Controllers/
│       └── ExpertiseController.php      # Contrôleur API
│
database/
├── migrations/
│   └── *_add_vehicule_expertise_to_expertises_table.php
└── factories/
    └── ExpertiseFactory.php             # Factory pour les tests
│
resources/
└── views/
    └── admin/
        └── modals/
            └── expertise.blade.php      # Modale du formulaire
│
public/
└── js/
    └── Dashboard/
        ├── modals.js                    # Logique du formulaire
        └── api.js                       # Appels API
│
routes/
└── gestionnaires.php                    # Routes pour gestionnaires
```

### Template et stockage

```
storage/
├── templates/
│   └── expertise_template.docx          # Template Word
└── app/
    └── temp/                            # Fichiers temporaires (auto-nettoyés)
```

### Tests

```
tests/
├── Unit/
│   ├── ExpertiseTest.php                # Tests du modèle (8 tests)
│   └── ExpertisePdfServiceTest.php      # Tests du service (5 tests)
└── Feature/
    └── ExpertiseFeatureTest.php         # Tests d'intégration (10 tests)
```

---

## Tests

### Vue d'ensemble

- **23 tests créés**
- **22 tests passent** ✅
- **1 test skippé** (normal si template existe)
- **52 assertions validées**

### Tests unitaires

#### ExpertiseTest.php (8 tests)

- Création d'une expertise
- Vérification des champs fillable
- Relations avec Sinistre et Expert
- Stockage des opérations en JSON
- Cast de la date
- Mise à jour d'une expertise
- Champ vehicule_expertise

#### ExpertisePdfServiceTest.php (5 tests)

- Génération du document Word depuis le template
- Gestion du template manquant
- Remplacement des placeholders
- Gestion de plusieurs opérations
- Création automatique du répertoire temp

### Tests de feature

#### ExpertiseFeatureTest.php (10 tests)

- Sauvegarde via API
- Validation des champs obligatoires
- Validation du nombre minimum d'opérations
- Validation de la structure des opérations
- Mise à jour d'une expertise existante
- Prévisualisation du PDF
- Téléchargement du PDF
- Authentification requise
- Stockage du champ vehicule_expertise
- Gestion de 5 opérations simultanées

### Exécution des tests

```bash
# Tous les tests d'expertise
php artisan test --filter=Expertise

# Tests unitaires uniquement
php artisan test tests/Unit/ExpertiseTest.php
php artisan test tests/Unit/ExpertisePdfServiceTest.php

# Tests de feature uniquement
php artisan test tests/Feature/ExpertiseFeatureTest.php

# Un test spécifique
php artisan test --filter=it_can_create_an_expertise

# Tous les tests du projet
php artisan test
```

### Configuration des tests

Le fichier `phpunit.xml` est configuré pour utiliser MySQL :

```xml
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="saarsinistre_test"/>
```

---

## Historique du Développement et Solutions

Cette section retrace les différentes approches testées et les difficultés rencontrées durant le développement.

### Génération de PDF : Parcours des Solutions Tentées

#### Tentative 1 : Blade Template + DomPDF

**Ce qui a été essayé** : Créer une vue Blade avec HTML/CSS personnalisé et utiliser DomPDF.

**Pourquoi ça n'a pas marché** :

- Impossible de reproduire pixel-perfect le design du modèle Word fourni
- Ajustements CSS très fastidieux et approximatifs
- Chaque modification du design nécessite de modifier le code
- Tableaux complexes difficiles à gérer en CSS

**Décision** : Abandonné après implémentation partielle

#### Tentative 2 : PHPWord + Conversion HTML + DomPDF

**Ce qui a été essayé** : Utiliser PHPWord pour manipuler le template, convertir en HTML, puis en PDF avec DomPDF.

**Pourquoi ça n'a pas marché** :

- PHPWord génère du HTML qui perd énormément de formatage
- DomPDF ne supporte pas toutes les propriétés CSS générées
- Tableaux complètement déformés
- Polices et espacements incorrects
- Le rendu final était très éloigné du modèle Word original

**Décision** : Abandonné après tests

#### ✅ Solution Finale : PHPWord + LibreOffice

**Ce qui a été adopté** : PHPWord pour manipuler le template + LibreOffice pour conversion DOCX → PDF.

**Pourquoi ça marche** :

- Conversion pixel-perfect du document Word
- Design original parfaitement préservé
- Maintenance simple (modifier uniquement le fichier .docx)
- Solution fiable et professionnelle
- Fallback vers DomPDF si LibreOffice indisponible

**Installation de LibreOffice** :

```bash
# Windows
Télécharger et installer depuis https://www.libreoffice.org/

# Linux
sudo apt-get install libreoffice

# Mac
brew install --cask libreoffice
```

---

### Autres Difficultés Techniques Résolues

#### Problème : Alignement des informations (Date, Client, Mandant)

**Ce qui s'est passé** : Les éléments n'étaient pas sur la même ligne horizontale.

**Cause** : Utilisation de texte libre avec espaces/tabulations causant des retours à la ligne.

**Solution** : Utiliser un tableau invisible (sans bordures) pour garantir l'alignement.

#### Problème : Conflit de dépendances Composer

**Ce qui s'est passé** : `maatwebsite/excel` nécessitait PHP 8.3 incompatible avec le projet.

**Solution** :

```bash
composer require maennchen/zipstream-php:^2.4
composer require phpoffice/phpword:^1.4
```

---

### Dépannage Rapide

#### Tests : "Class ExpertiseFactory not found"

```bash
composer dump-autoload
```

#### Tests : "Table expertises doesn't exist"

```bash
php artisan migrate:fresh --env=testing
```

#### Checkboxes n'apparaissent pas

Utiliser une police qui supporte Unicode (Arial, Calibri) ou remplacer ☐/☑ par [ ]/[X] dans le code.

---

## Changelog

### Ajouté

- Modèle Expertise avec support des opérations JSON
- Service ExpertisePdfService avec support PHPWord
- Génération PDF depuis template Word
- Support LibreOffice pour conversion pixel-perfect
- Fallback DomPDF si LibreOffice indisponible
- Interface modale avec distinction champs auto/manuels
- Tableau dynamique pour les opérations
- Boutons Prévisualiser et Télécharger
- 23 tests unitaires et de feature
- Architecture
- Template Word avec placeholders
- Clonage automatique des lignes de tableau
- Checkboxes automatiques (☐/☑)
- Validation complète des données

---

## Support

Pour toute question ou problème :

1. Consultez la section [Dépannage](#dépannage)
2. Vérifiez les logs : `storage/logs/laravel.log`
3. Exécutez les tests pour identifier le problème
4. Vérifiez que le template Word est correctement configuré

---

**Laravel** : 12.0
**PHP** : 8.2.12
**PHPWord** : 1.4.0
