# Guide de déploiement - Gestion du cache

## Problème résolu

L'application avait des problèmes de cache où les utilisateurs devaient vider manuellement le cache de leur navigateur après chaque déploiement. Ce problème était causé par :

1. **Service Worker avec cache agressif** - Version hardcodée qui ne changeait jamais
2. **Assets statiques non versionnés** - Fichiers JS/CSS sans cache busting
3. **Pas de build Vite en production** - Assets non optimisés

## Solutions implémentées

### 1. Service Worker avec versioning dynamique

- Le fichier `public/sw.template.js` contient le template du service worker
- Le script `scripts/update-sw-version.mjs` génère automatiquement une nouvelle version à chaque build
- La version est basée sur un timestamp pour garantir l'unicité

### 2. Assets versionnés

Tous les fichiers JavaScript et CSS statiques incluent maintenant un paramètre de version :
```php
<script src="{{ asset('js/Dashboard/utils.js') }}?v={{ config('app.asset_version') }}"></script>
```

### 3. Configuration Vite optimisée

Le fichier `vite.config.js` est configuré pour :
- Générer des fichiers avec hash pour le cache busting automatique
- Minifier et optimiser le code pour la production
- Supprimer les console.log en production

### 4. Headers HTTP de cache

Le fichier `public/.htaccess` configure les headers de cache appropriés :
- **Service Worker** : `no-cache` (toujours vérifier la nouvelle version)
- **Assets Vite** : cache 1 an (immutable car avec hash)
- **JS/CSS versionnés** : cache 1 heure avec revalidation
- **Images/Fonts** : cache 1 mois
- **Compression gzip** : activée pour tous les assets textuels

## Processus de déploiement

### En développement

```bash
npm run dev
```

Le mode développement fonctionne normalement avec Vite HMR.

### En production

#### 1. Build de l'application

```bash
npm run build
```

Ce script va automatiquement :
1. Exécuter `prebuild` : Mettre à jour la version du service worker
2. Exécuter `build` : Compiler les assets avec Vite
3. Exécuter `postbuild` : Afficher un message de confirmation

#### 2. Mettre à jour le cache Laravel

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

#### 3. Déployer les fichiers

Assurez-vous de déployer :
- `public/sw.js` (généré depuis le template)
- `public/build/` (généré par Vite)
- Tous les fichiers modifiés

### Mise à jour manuelle de la version

Si vous voulez forcer une nouvelle version sans rebuilder :

```bash
npm run update-cache-version
```

## Configuration

### Fichier .env

Le script de build met automatiquement à jour `.env` avec :

```env
ASSET_VERSION=20241107123045-1699357845000
```

Cette valeur est utilisée pour versionner les assets statiques.

### Version de l'application

Dans `config/app.php` :

```php
'version' => env('APP_VERSION', '1.0.0'),
'asset_version' => env('ASSET_VERSION', time()),
```

## Vérification

### 1. Vérifier la version du Service Worker

Ouvrez la console du navigateur :
```javascript
navigator.serviceWorker.ready.then(registration => {
    registration.active.postMessage({type: 'GET_VERSION'});
});
```

### 2. Vérifier les headers de cache

```bash
curl -I https://votre-site.com/sw.js
curl -I https://votre-site.com/js/Dashboard/utils.js
```

### 3. Tester le cache busting

1. Déployez une nouvelle version
2. Ouvrez l'application
3. Le service worker devrait détecter la mise à jour automatiquement
4. Les nouveaux assets devraient se charger sans vider le cache

## Dépannage

### Le Service Worker ne se met pas à jour

1. Vérifiez que `sw.js` contient bien une nouvelle version (pas `__SW_VERSION__`)
2. Vérifiez les headers HTTP de cache pour `sw.js`
3. Forcez le rechargement : DevTools > Application > Service Workers > Unregister

### Les assets ne se mettent pas à jour

1. Vérifiez que `ASSET_VERSION` est bien défini dans `.env`
2. Exécutez `php artisan config:clear`
3. Vérifiez que les URLs incluent bien `?v=...`

### Le build échoue

1. Vérifiez que le dossier `scripts/` existe
2. Vérifiez les permissions d'écriture sur `public/sw.js` et `.env`
3. Vérifiez que Node.js est installé (version 16+)

## Maintenance

### À chaque déploiement

```bash
# 1. Mettre à jour le code
git pull

# 2. Installer les dépendances si nécessaire
composer install --no-dev --optimize-autoloader
npm install

# 3. Build avec versioning automatique
npm run build

# 4. Nettoyer le cache Laravel
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize

# 5. Redémarrer les services si nécessaire
php artisan queue:restart
```

### Monitoring

Surveillez les métriques de cache :
- Taux de hit du cache
- Temps de chargement des assets
- Erreurs du Service Worker (dans les logs du navigateur)

## Notes importantes

1. **Ne modifiez pas directement `public/sw.js`** - Modifiez `public/sw.template.js` à la place
2. **Le script de build doit s'exécuter avant chaque déploiement**
3. **Les utilisateurs verront la mise à jour au prochain rechargement de la page**
4. **Le Service Worker met à jour les assets en arrière-plan**

## Architecture du cache

```
┌─────────────────────────────────────┐
│         Navigateur                  │
│                                     │
│  ┌──────────────────────────────┐  │
│  │   Service Worker              │  │
│  │   Version: timestamp          │  │
│  │                               │  │
│  │   Stratégies:                 │  │
│  │   - SW: no-cache             │  │
│  │   - /build/: cacheFirst      │  │
│  │   - /js/: staleWhileRevalidate│  │
│  │   - API: networkFirst        │  │
│  └──────────────────────────────┘  │
│                                     │
│  ┌──────────────────────────────┐  │
│  │   Assets                      │  │
│  │   - app.js?v=timestamp        │  │
│  │   - app.css?v=timestamp       │  │
│  │   - dashboard.js?v=timestamp  │  │
│  └──────────────────────────────┘  │
└─────────────────────────────────────┘
```

## Support

En cas de problème persistant :
1. Consultez les logs Laravel (`storage/logs/laravel.log`)
2. Vérifiez la console du navigateur (DevTools)
3. Testez en navigation privée pour isoler les problèmes de cache

