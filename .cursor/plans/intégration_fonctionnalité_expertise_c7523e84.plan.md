---
name: Intégration fonctionnalité Expertise
overview: "Ajouter une fonctionnalité complète d'expertise pour les utilisateurs avec le rôle \"expert\" : nouveau bouton visible uniquement pour les experts, formulaire modale basé sur le template \"Fiche d'Expertise\", stockage des données, prévisualisation PDF et téléchargement."
todos:
  - id: migration
    content: Créer migration pour table expertises avec tous les champs nécessaires
    status: completed
  - id: model
    content: Créer modèle Expertise avec relations et casts appropriés
    status: completed
    dependencies:
      - migration
  - id: controller
    content: Créer ExpertiseController avec méthodes show, store, preview, downloadPdf
    status: completed
    dependencies:
      - model
  - id: request
    content: Créer StoreExpertiseRequest avec validation complète
    status: completed
  - id: routes
    content: Ajouter routes pour expertise dans routes/gestionnaires.php
    status: completed
    dependencies:
      - controller
  - id: pdf_service
    content: Créer ExpertisePdfService pour génération PDF
    status: completed
    dependencies:
      - model
  - id: pdf_template
    content: Créer template Blade pour PDF (resources/views/expertise/pdf.blade.php)
    status: completed
  - id: modal_view
    content: Créer vue modale expertise.blade.php avec formulaire et tableau dynamique
    status: completed
  - id: js_modals
    content: Ajouter méthodes dans modals.js pour gérer formulaire expertise et tableau opérations
    status: completed
    dependencies:
      - modal_view
  - id: js_api
    content: Ajouter méthodes API dans api.js pour expertise
    status: completed
  - id: js_sinistres
    content: Ajouter bouton Expertise dans sinistres.js (visible uniquement pour experts)
    status: completed
    dependencies:
      - js_modals
      - js_api
  - id: auth_expert
    content: Modifier AuthController pour autoriser connexion des experts
    status: completed
  - id: sinistre_relation
    content: Ajouter relation hasOne(Expertise) dans modèle Sinistre
    status: completed
    dependencies:
      - model
  - id: dashboard_role
    content: Ajouter variable JavaScript userRole dans dashboard.blade.php
    status: completed
---

# Plan d'intégration : Fonctionnalité Expertise

## Analyse détaillée du template

### Champs et comportement

**Pré-remplis automatiquement :**

- Date : Date du jour
- Client : `nom_assure` du sinistre
- Collaborateur nom : `nom_complet` de l'expert connecté
- Collaborateur email : `email` de l'expert connecté
- Collaborateur téléphone : Fixe `0747707127/0711236714` (hardcodé)
- Contact client : `telephone_assure` du sinistre

**Saisie manuelle par l'expert :**

- Lieu d'expertise (commune) : Champ texte libre

**Éléments statiques (template) :**

- mandant SAAR : Texte fixe, pas de champ
- Zones de signature : Affichage texte uniquement (signatures manuelles sur PDF)

### Tableau d'opérations

- **État initial** : Vide (aucune ligne)
- **Ajout** : Bouton "+" pour ajouter une ligne
- **Suppression** : Bouton de suppression sur chaque ligne
- **Structure par ligne** :
  - LIBELLE : Input texte (saisie libre)
  - ECH, REP, CTL, P : Cases à cocher (checkboxes)
- **Validation** : Au moins une opération avec libellé requis

### Comportement après sauvegarde

1. **Prévisualisation** : PDF affiché dans iframe/modale
2. **Modification** : Fermer PDF → Réouvrir formulaire avec données existantes
3. **Téléchargement** : Bouton pour télécharger le PDF

## Structure de données

### Table `expertises`

```php
- id, sinistre_id, expert_id
- date_expertise (date)
- client_nom (string)
- collaborateur_nom, collaborateur_telephone, collaborateur_email
- lieu_expertise (string)
- contact_client (string)
- operations (JSON) - Format: [{libelle, echange, reparation, controle, peinture}, ...]
- created_at, updated_at
```

## Fichiers à créer/modifier

### Backend

1. **Migration** : `database/migrations/XXXX_XX_XX_create_expertises_table.php`

   - Table `expertises` avec structure définie

2. **Modèle** : `app/Models/Expertise.php`

   - Relations : `belongsTo(Sinistre)`, `belongsTo(User, 'expert_id')`
   - Casts : `date_expertise => 'date'`, `operations => 'array'`

3. **Service PDF** : `app/Services/ExpertisePdfService.php`

   - Méthode `generateExpertisePdf(Expertise $expertise)`
   - Utilise DomPDF (déjà installé)

4. **Contrôleur** : `app/Http/Controllers/ExpertiseController.php`

   - `show()` : Récupérer expertise existante
   - `store()` : Sauvegarder expertise
   - `preview()` : Prévisualiser PDF
   - `downloadPdf()` : Télécharger PDF

5. **Request** : `app/Http/Requests/StoreExpertiseRequest.php`

   - Validation : tous champs obligatoires + au moins une opération

6. **Routes** : `routes/gestionnaires.php`

   - GET/POST `/gestionnaires/dashboard/sinistres/{sinistre}/expertise`
   - GET `/gestionnaires/dashboard/sinistres/{sinistre}/expertise/preview`
   - GET `/gestionnaires/dashboard/sinistres/{sinistre}/expertise/pdf`
   - Middleware : `auth`, `role:admin,gestionnaire,expert`

7. **Modèle Sinistre** : `app/Models/Sinistre.php`

   - Ajouter relation : `hasOne(Expertise::class)`

8. **AuthController** : `app/Http/Controllers/AuthController.php`

   - Ajouter 'expert' dans les rôles autorisés (ligne 52)

### Frontend

9. **Vue Modale** : `resources/views/admin/modals/expertise.blade.php`

   - Formulaire avec champs pré-remplis et saisie libre
   - Tableau d'opérations dynamique (bouton +, suppression)
   - Zones de signature (texte statique)

10. **Template PDF** : `resources/views/expertise/pdf.blade.php`

    - Reproduire fidèlement le template "Fiche d'Expertise"

11. **JavaScript Modals** : `public/js/Dashboard/modals.js`

    - `showExpertiseModal(sinistreId)` : Ouvrir modale, pré-remplir champs
    - `addOperationRow()` : Ajouter ligne au tableau
    - `removeOperationRow()` : Supprimer ligne
    - `confirmExpertise()` : Valider et soumettre
    - `previewExpertise()` : Prévisualiser PDF

12. **JavaScript API** : `public/js/Dashboard/api.js`

    - `getExpertise(sinistreId)`
    - `saveExpertise(sinistreId, data)`
    - `previewExpertisePdf(sinistreId)`
    - `downloadExpertisePdf(sinistreId)`

13. **JavaScript Sinistres** : `public/js/Dashboard/sinistres.js`

    - Dans `createSinistreRow()`, ajouter bouton "Expertise"
    - **Condition** : Afficher uniquement si `userRole === 'expert'`
    - Icône : clipboard-check ou document-text

14. **Dashboard** : `resources/views/admin/dashboard.blade.php`

    - Ajouter : `<script>window.userRole = '{{ Auth::user()->role }}';</script>`

## Flux utilisateur

1. Expert se connecte → Dashboard gestionnaires
2. Expert voit liste des sinistres
3. Expert clique sur bouton "Expertise" (visible uniquement pour experts)
4. Modale s'ouvre avec formulaire pré-rempli (date, client, collaborateur, contact)
5. Expert saisit lieu d'expertise
6. Expert ajoute opérations (bouton +) : libellé + cases à cocher ECH/REP/CTL/P
7. Expert peut supprimer des lignes
8. Expert enregistre → Validation → Sauvegarde
9. Prévisualisation PDF dans iframe/modale
10. Expert peut modifier (fermer PDF → réouvrir formulaire) ou télécharger PDF

## Points techniques importants

- **Accès** : Bouton visible uniquement pour `role === 'expert'`
- **Pré-remplissage** : Date (aujourd'hui), Client (sinistre), Collaborateur (expert connecté), Contact (sinistre)
- **Tableau dynamique** : JavaScript pour gestion ajout/suppression lignes
- **Validation** : Tous champs obligatoires + au moins une opération avec libellé
- **PDF** : DomPDF avec template Blade fidèle au template original
- **Signatures** : Zones vides (signatures manuelles sur PDF téléchargé)