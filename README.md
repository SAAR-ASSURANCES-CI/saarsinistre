  
SAAR Sinistre est une application développée avec Laravel pour la gestion et le suivi des sinistres.  

## Fonctionnalités principales

- Gestion des dossiers de sinistre
- Suivi des étapes de traitement
- Notifications et alertes
- Tableau de bord interactif

## Prérequis

- PHP >= 8.1
- Composer
- MySQL ou autre base de données compatible

## Installation

```bash
git clone hhttps://github.com/SAAR-ASSURANCES-CI/saarsinistre.git
cd saarsinistre
composer install
cp .env.example .env
php artisan key:generate
```

Configurez le fichier `.env` puis lancez les migrations :

```bash
php artisan migrate
```

## Lancement du serveur

```bash
php artisan serve
```

