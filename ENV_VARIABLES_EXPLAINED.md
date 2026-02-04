# Guide des Variables d'Environnement (.env)

Ce document explique l'impact de chaque variable que vous devez configurer dans le fichier `.env`.

---

## 🔵 SECTION 1 : CONFIGURATION DE L'APPLICATION

### `APP_NAME`
**Valeur actuelle :** `Laravel`  
**Impact :** 
- Nom de l'application affiché dans les emails, notifications, et l'interface
- Utilisé dans les en-têtes d'emails et les métadonnées
- **À modifier :** `SAAR Assurances` ou `SAAR Sinistre`

### `APP_ENV`
**Valeur actuelle :** `local`  
**Impact :**
- `local` : Mode développement (erreurs détaillées, pas de cache)
- `production` : Mode production (cache activé, erreurs masquées)
- **À modifier :** Gardez `local` pour le développement, `production` pour la mise en production

### `APP_KEY`
**Valeur actuelle :** `base64:3zzl4AIl/qTekQdzZmi6rj2sBg8iX4FufULx95xea+M=`  
**Impact :**
- Clé de chiffrement pour les sessions, cookies, et données sensibles
- **CRITIQUE** : Chaque installation doit avoir une clé unique
- **À générer :** `php artisan key:generate` (déjà fait)

### `APP_DEBUG`
**Valeur actuelle :** `true`  
**Impact :**
- `true` : Affiche les erreurs détaillées (développement)
- `false` : Masque les erreurs (production)
- **À modifier :** `false` en production pour la sécurité

### `APP_URL`
**Valeur actuelle :** `http://localhost`  
**Impact :**
- URL de base de l'application
- Utilisé pour générer les liens dans les emails
- **À modifier :** Votre URL de production (ex: `https://votre-domaine.com`)

---

## 🗄️ SECTION 2 : BASE DE DONNÉES

### `DB_CONNECTION`
**Valeur actuelle :** `mysql`  
**Impact :**
- Type de base de données (mysql, sqlite, pgsql)
- **À modifier :** Généralement `mysql` (déjà correct)

### `DB_HOST`
**Valeur actuelle :** `127.0.0.1`  
**Impact :**
- Adresse du serveur de base de données
- `127.0.0.1` = localhost (votre machine)
- **À modifier :** Si votre BDD est sur un autre serveur

### `DB_PORT`
**Valeur actuelle :** `3306`  
**Impact :**
- Port MySQL (3306 par défaut)
- **À modifier :** Seulement si votre MySQL utilise un autre port

### `DB_DATABASE`
**Valeur actuelle :** `saarsinistre_db`  
**Impact :**
- Nom de la base de données
- **À modifier :** Le nom de votre base de données MySQL

### `DB_USERNAME`
**Valeur actuelle :** `root`  
**Impact :**
- Nom d'utilisateur MySQL
- **À modifier :** Votre utilisateur MySQL (généralement `root` en local)

### `DB_PASSWORD`
**Valeur actuelle :** (vide)  
**Impact :**
- Mot de passe MySQL
- **À modifier :** Votre mot de passe MySQL (vide si pas de mot de passe)

---

## 📧 SECTION 3 : CONFIGURATION EMAIL (SMTP)

### `MAIL_MAILER`
**Valeur actuelle :** `smtp`  
**Impact :**
- `smtp` : Envoi réel via SMTP
- `log` : Écrit dans les logs (développement)
- `array` : Ne fait rien (tests)
- **À modifier :** `smtp` pour l'envoi réel

### `MAIL_HOST`
**Valeur actuelle :** `smtp.gmail.com`  
**Impact :**
- Serveur SMTP
- **Options :**
  - Gmail : `smtp.gmail.com`
  - Outlook : `smtp.office365.com`
  - Autre : Votre serveur SMTP
- **À modifier :** Selon votre fournisseur email

### `MAIL_PORT`
**Valeur actuelle :** `587`  
**Impact :**
- Port SMTP
- `587` : TLS (recommandé)
- `465` : SSL
- `25` : Non sécurisé (déconseillé)
- **À modifier :** Généralement `587` (déjà correct)

### `MAIL_USERNAME`
**Valeur actuelle :** `komimissiamenou97@gmail.com`  
**Impact :**
- Email utilisé pour envoyer les messages
- **À modifier :** Votre adresse email d'envoi

### `MAIL_PASSWORD`
**Valeur actuelle :** `"punc bhaq jxyp zwpl"`  
**Impact :**
- Mot de passe de l'email (ou mot de passe d'application pour Gmail)
- **À modifier :** Votre mot de passe ou mot de passe d'application

### `MAIL_FROM_ADDRESS`
**Valeur actuelle :** `"noreply@saar-assurances.com"`  
**Impact :**
- Adresse email affichée comme expéditeur
- **À modifier :** L'adresse email que vous voulez afficher

### `MAIL_FROM_NAME`
**Valeur actuelle :** `"SAAR Assurances"`  
**Impact :**
- Nom affiché comme expéditeur dans les emails
- **À modifier :** Le nom de votre entreprise

### `MAIL_ENCRYPTION`
**Valeur actuelle :** `tls`  
**Impact :**
- `tls` : Chiffrement TLS (port 587)
- `ssl` : Chiffrement SSL (port 465)
- **À modifier :** Généralement `tls` (déjà correct)

---

## 📱 SECTION 4 : CONFIGURATION ORANGE SMS

### `ORANGE_CLIENT_ID`
**Valeur actuelle :** `QZCioGhRkY5etOS8ofL0RQC5PSJrRfiV`  
**Impact :**
- Identifiant client pour l'authentification OAuth Orange
- **CRITIQUE** : Sans cela, aucun SMS ne peut être envoyé
- **À modifier :** Votre Client ID Orange (obtenu depuis le portail Orange)

### `ORANGE_CLIENT_SECRET`
**Valeur actuelle :** `sc2fIwi6tDTBGh8zeswyjlChjDlZTkHpewLiebl63FMw`  
**Impact :**
- Secret client pour l'authentification OAuth Orange
- **CRITIQUE** : Secret, ne jamais partager
- **À modifier :** Votre Client Secret Orange

### `ORANGE_SMS_API_TOKEN_URL`
**Valeur actuelle :** `https://api.orange.com/oauth/v3/token`  
**Impact :**
- URL pour obtenir le token d'accès Orange
- **À modifier :** Généralement ne change pas (déjà correct)

### `ORANGE_SMS_API_SEND_URL`
**Valeur actuelle :** `https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B2250000/requests`  
**Impact :**
- URL pour envoyer les SMS
- Contient le numéro d'expéditeur (2250000)
- **À modifier :** Si votre numéro d'expéditeur change

### `ORANGE_SMS_API_STATUS_URL`
**Valeur actuelle :** `https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B2250000/requests`  
**Impact :**
- URL pour vérifier le statut d'envoi des SMS
- **À modifier :** Généralement identique à SEND_URL

### `ORANGE_SMS_SENDER_ADDRESS`
**Valeur actuelle :** `2250000`  
**Impact :**
- Adresse de l'expéditeur SMS
- **À modifier :** Votre numéro d'expéditeur Orange

### `ORANGE_SMS_SENDER_NUMBER`
**Valeur actuelle :** `2250000`  
**Impact :**
- Numéro de téléphone de l'expéditeur
- **À modifier :** Votre numéro d'expéditeur Orange

---

## ⚙️ SECTION 5 : CONFIGURATION AVANCÉE

### `QUEUE_CONNECTION`
**Valeur actuelle :** `database`  
**Impact :**
- `database` : Jobs en queue dans la base de données (nécessite `php artisan queue:work`)
- `sync` : Jobs exécutés immédiatement (pas de queue)
- **À modifier :** `database` pour la production, `sync` pour les tests rapides

### `SESSION_DRIVER`
**Valeur actuelle :** `database`  
**Impact :**
- Où sont stockées les sessions utilisateur
- `database` : Dans la BDD (recommandé)
- `file` : Dans des fichiers
- **À modifier :** Généralement `database` (déjà correct)

### `SESSION_LIFETIME`
**Valeur actuelle :** `120`  
**Impact :**
- Durée de vie des sessions en minutes (120 = 2 heures)
- **À modifier :** Selon vos besoins de sécurité

### `CACHE_STORE`
**Valeur actuelle :** `database`  
**Impact :**
- Où est stocké le cache
- `database` : Dans la BDD
- `file` : Dans des fichiers
- `redis` : Dans Redis (plus rapide)
- **À modifier :** `database` pour commencer, `redis` pour la performance

### `LOG_LEVEL`
**Valeur actuelle :** `debug`  
**Impact :**
- Niveau de détail des logs
- `debug` : Tout (développement)
- `info` : Informations importantes
- `error` : Seulement les erreurs (production)
- **À modifier :** `debug` en développement, `error` en production

---

## 🔧 SECTION 6 : CONFIGURATION OPTIONNELLE

### `N8N_ENABLED`
**Valeur actuelle :** `true`  
**Impact :**
- Active/désactive l'intégration N8N (automatisation)
- **À modifier :** `false` si vous n'utilisez pas N8N

### `N8N_WEBHOOK_URL`
**Valeur actuelle :** (vide)  
**Impact :**
- URL du webhook N8N pour l'automatisation
- **À modifier :** Si vous utilisez N8N

### Variables AWS (S3)
**Valeurs actuelles :** (vides)  
**Impact :**
- Pour stocker les fichiers sur AWS S3 au lieu du serveur local
- **À modifier :** Seulement si vous utilisez AWS S3

---

## 📋 CHECKLIST DE CONFIGURATION

### ✅ Variables OBLIGATOIRES à modifier :
- [ ] `APP_NAME` → Nom de votre application
- [ ] `APP_KEY` → Générer avec `php artisan key:generate`
- [ ] `DB_DATABASE` → Nom de votre base de données
- [ ] `DB_USERNAME` → Votre utilisateur MySQL
- [ ] `DB_PASSWORD` → Votre mot de passe MySQL
- [ ] `MAIL_USERNAME` → Votre email d'envoi
- [ ] `MAIL_PASSWORD` → Votre mot de passe email
- [ ] `ORANGE_CLIENT_ID` → Votre Client ID Orange
- [ ] `ORANGE_CLIENT_SECRET` → Votre Client Secret Orange

### ⚠️ Variables RECOMMANDÉES à modifier :
- [ ] `APP_URL` → URL de votre application
- [ ] `MAIL_FROM_ADDRESS` → Email expéditeur
- [ ] `MAIL_FROM_NAME` → Nom expéditeur
- [ ] `ORANGE_SMS_SENDER_NUMBER` → Votre numéro Orange

### 🔒 Variables PRODUCTION à modifier :
- [ ] `APP_ENV` → `production`
- [ ] `APP_DEBUG` → `false`
- [ ] `LOG_LEVEL` → `error`

---

## 🚨 IMPORTANT

1. **Ne jamais commiter le fichier `.env`** dans Git (déjà dans `.gitignore`)
2. **Chaque environnement doit avoir son propre `.env`**
3. **Après modification du `.env`, exécutez :** `php artisan config:clear`
4. **Les variables sensibles** (mots de passe, secrets) ne doivent jamais être partagées
