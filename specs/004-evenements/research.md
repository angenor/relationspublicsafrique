# Phase 0 — Research : Section Événements (vitrine)

Contexte : **le domaine Événement existe déjà** (modèle `Event`, `EventController`, routes `events.*`, `EventResource` Filament, `EventRegistration`/`EventRegistrationController`, accessors/scopes temporels). Cette feature est une **évolution** ; chaque décision ci-dessous vise la cohérence avec l'existant et avec les patrons 001/002/003, en minimisant la régression.

Format : **Décision / Rationale / Alternatives considérées**.

---

## R1 — Sémantique du champ `online` et source de visibilité publique *(dette à résoudre)*

**Décision** : Recentrer `online` sur le **format de l'événement** (`true` = en ligne, `false` = présentiel) — sens déjà suggéré par le label Filament « En ligne ». La **visibilité publique** repose désormais **uniquement** sur `status='published'` (+ scope `published()`). Le gate `->online()` est **retiré** du listing/détail public (`EventController@index/show`) et n'est **plus** une condition de `can_register`. Les actions Filament « Activer/Désactiver » (qui basculaient `online`) deviennent « **Publier/Dépublier** » (basculent `status` `published`↔`draft`).

**Rationale** : la spec demande explicitement un « format présentiel / en ligne » (FR-009) et une visibilité pilotée par la publication (FR-006/FR-018). Aujourd'hui `online` cumule deux rôles contradictoires (visibilité **et** format), ce qui rend impossible d'avoir un événement présentiel publié visible. Une seule source de vérité pour la visibilité (`status`) supprime le double-gate `online && published`.

**Impact non-régression** : des événements aujourd'hui `published` mais `online=false` (présentiels) **deviendront visibles** — comportement **attendu/corrigé**. Couvert par tests : un événement publié présentiel est listé ; le badge « En ligne » de Filament reflète bien le format. Le `EventSeeder` est ajusté en conséquence.

**Alternatives considérées** :
- *Ajouter une colonne `format`/`is_online` distincte et garder `online` comme visibilité* — rejeté : deux notions « en ligne » coexisteraient (confusion durable) et la visibilité resterait dédoublée avec `status`.
- *Garder le double-gate* — rejeté : contredit FR-006/FR-009, empêche les présentiels d'être affichés.

---

## R2 — Colonne `pays_id` manquante *(dette à résoudre)*

**Décision** : Ajouter une migration **additive** créant `events.pays_id` (`integer`, nullable, indexée, **sans contrainte DB** — référentiel `pays` legacy à `id` `int` sans AUTO_INCREMENT). La relation `pays()` et l'usage Filament existants deviennent fonctionnels ; le filtre public « par pays » (FR-015) s'appuie dessus.

**Rationale** : `EventResource` (form/table/filtre) et `Event::pays()` référencent `pays_id`, **absent de toute migration** (`create_events_table` + `add_view_column` seulement) → erreurs SQL latentes. Correctif minimal et conforme au contournement FK des features 002/003.

**Alternatives considérées** :
- *Réutiliser une autre colonne* — aucune n'existe. *Contrainte FK réelle* — rejeté : impossible vers une table sans AUTO_INCREMENT (cohérence projet).

---

## R3 — Statut temporel : dérivé des dates, jamais stocké

**Décision** : Le statut temporel (**à venir / en cours / clos**) reste **calculé** à partir de `start_date`/`end_date`. On **réutilise** les accessors existants `is_upcoming`/`is_ongoing`/`is_completed` et les scopes `upcoming()`/`ongoing()`/`past()`, complétés par un accessor agrégé `temporal_status` (`upcoming|ongoing|past`) + `temporal_status_label` pour l'affichage du badge (card, détail, Filament). Le statut **éditorial** (`draft/published/cancelled/completed`) reste saisi et **distinct** (FR-019). Un événement `cancelled` prime à l'affichage (badge « Annulé », pas d'inscription).

**Rationale** : satisfait FR-018 (« automatique selon date ») sans champ redondant ni risque de désynchronisation ; réutilise du code déjà présent et testé.

**Alternatives considérées** :
- *Colonne `temporal_status` mise à jour par cron* — rejeté : redondance, fenêtres d'incohérence entre deux passages du cron (échouerait SC-002/SC-004).
- *Cron obligatoire* — rejeté : non nécessaire pour l'affichage. Voir R8 pour un cron **optionnel** d'hygiène éditoriale.

---

## R4 — « Type d'événement » : réutilisation des `categories` (`type='event'`)

**Décision** : Le filtre « par type d'événement » (FR-015) réutilise le référentiel **`categories`** avec **`type='event'`** (conférence, atelier, webinaire…), via le `category_id` **déjà présent** sur `events`. Le `CategorySeeder` est enrichi de catégories `type='event'`.

**Rationale** : cohérence stricte avec 002 (`type='media'`) et 003 (`type='projet'`) ; `events.category_id` existe déjà → zéro nouvelle table, réutilisation du picker Filament en place.

**Alternatives considérées** :
- *Nouvelle table/enum `event_types`* — rejeté : duplique le mécanisme `categories`, casse la cohérence inter-features.

---

## R5 — Intervenants (`event_speakers`)

**Décision** : Nouvelle table `event_speakers` en **`hasMany`** depuis `Event` (nom, rôle/qualité, photo, bio courte, position). Affichés sur la page détail (FR-009) ; gérés via un **RelationManager** Filament. Pas de table master réutilisable (un intervenant appartient au contexte d'un événement, contrairement aux partenaires de 003).

**Rationale** : besoin simple et local ; `hasMany` ordonné par `position`, masqué si vide (cohérent avec les sections optionnelles de 003).

**Alternatives considérées** :
- *Table master `speakers` + pivot many-to-many* — rejeté : YAGNI (pas d'exigence de réutilisation d'un même intervenant entre événements à ce stade ; extensible plus tard).
- *Champ texte libre* — rejeté : empêche photo/rôle structurés et l'ordre d'affichage.

---

## R6 — Médias post-événement (`event_medias`) : replay / photos / compte rendu

**Décision** : Nouvelle table `event_medias` en **`hasMany`** (type `replay`|`image`|`document`, `chemin` fichier nullable, `url_embed` nullable, `legende`, `position`). Replay vidéo via **`App\Helper\VideoEmbed`** (`url_embed` YouTube/Vimeo) ou `<video>` natif (`chemin`) ; photos = galerie images avec lightbox léger ; compte rendu = `document` téléchargeable et/ou champ riche `compte_rendu` sur `events` (texte). Le CTA « Voir replay / photos / compte rendu » s'active dès qu'au moins un média post-événement (ou `compte_rendu`) existe sur un événement **clos** (accessor `has_post_event_media`/`has_replay`).

**Rationale** : réutilise le patron galerie de 003 (`projet_medias` + `VideoEmbed`) et le helper d'embed existant → aucune dépendance (pas de Plyr/FFmpeg). Couvre les trois types demandés (FR-022, FR-010).

**Alternatives considérées** :
- *Réutiliser `projet_medias`* — rejeté : couplage de domaines distincts, FK ambiguë.
- *Tout en champs texte sur `events`* — rejeté : pas de galerie photos ordonnée.

---

## R7 — Listing réactif : Livewire `GrilleEvents` (patron `GrilleProjets`)

**Décision** : Remplacer le listing statique de `EventController@index` par une vue `events/index` intégrant un composant **`App\Livewire\Events\GrilleEvents`** calqué sur `GrilleProjets` : props `#[Url]` `statut` (temporel `upcoming|ongoing|past`), `type` (id catégorie), `pays`, `tri` (`proche|recent`), `q`, `perPage` ; `chargerPlus()` ; reset pagination au changement de filtre ; `EventSearchService` pour la requête. Le contrôleur fournit la page (layout `front`) + comptes par statut. Les **sections** « En cours / À venir / Clos » sont rendues soit comme groupes, soit pilotées par le filtre `statut` (défaut : à venir + en cours en tête, passés ensuite).

**Rationale** : filtres réactifs + URL partageable + « charger plus » déjà éprouvés en 002/003 ; cohérence UX et code. `start_date asc` pour à venir/en cours, `start_date desc` pour passés (tri « proche » vs « récent », SC tri).

**Alternatives considérées** :
- *Filtres via rechargement serveur (l'actuel)* — rejeté : UX inférieure, incohérent avec /projets et /media.
- *Vue.js island* — rejeté : Livewire est le patron retenu du projet pour ce type de grille.

---

## R8 — Compte à rebours & hygiène éditoriale (cron optionnel)

**Décision** : **Compte à rebours** (FR-017, optionnel) rendu **côté client** (JS dans `events.js`) à partir de `start_date` (attribut `data-*`), sur les cards/détail des événements **à venir** ; disparaît/au passage en « en cours ». Aucune logique serveur. En complément, commande **optionnelle** `events:mark-completed` (scheduler quotidien) qui bascule l'éditorial `published→completed` pour les événements dont `end_date` est passée — **pur confort back-office** ; la **source de vérité d'affichage reste le statut temporel dérivé** (R3).

**Rationale** : countdown purement présentationnel → client = simple et sans charge serveur. Le cron n'est pas nécessaire au respect des FR (R3 couvre déjà l'automatisme d'affichage) mais aide le suivi admin ; il est donc **isolé et optionnel** (Lot 5).

**Alternatives considérées** :
- *Countdown serveur (re-render Livewire périodique)* — rejeté : coût et complexité injustifiés.
- *Cron pilotant le statut d'affichage* — rejeté (R3) : fenêtres d'incohérence.

---

## R9 — Mode d'inscription interne vs externe

**Décision** : Ajouter `registration_mode` (enum `internal|external`, défaut `internal`) + `registration_url` (string nullable) sur `events`. CTA détail/card :
- `external` → lien `registration_url` (nouvel onglet) ;
- `internal` → parcours existant `EventRegistrationController` (réutilisé **tel quel**), conditionné par `can_register` (corrigé en R1 : publié, non annulé, à venir/en cours, deadline non dépassée, capacité non atteinte).
Si inscription indisponible → mention « Complet » / « Inscriptions closes » / « Annulé » (FR-011).

**Rationale** : couvre FR-013/FR-014 en réutilisant le dispositif d'inscription interne déjà en place ; l'ajout se limite à 2 colonnes + un branchement de CTA.

**Alternatives considérées** :
- *Toujours externe (lien obligatoire)* — rejeté : casse le parcours interne existant et `EventRegistration`.
- *Champ unique « lien » sans mode* — rejeté : ambigu pour distinguer interne/externe et piloter `can_register`.

---

## R10 — Assets front & non-régression de navigation

**Décision** : Nouvelles entrées Vite `resources/css/events.css` + `resources/js/events.js` (countdown, lightbox, init embed) ajoutées à `vite.config.js` (miroir projets). Ajout d'une **entrée de navigation « Événements »** → `route('events.index')` dans `layouts/front.blade.php`. La section **« Lomé COM'TOUR »** (qui pointe déjà sur `events.index`) et le CSS existant `front/assets/css/events-custom.css` sont **conservés inchangés**.

**Rationale** : isole les assets de la section (cohérence 002/003), sans toucher aux entrées existantes. Réutilise le layout `front` (vitrine institutionnelle, comme /projets).

**Alternatives considérées** :
- *Charger le JS countdown globalement* — rejeté : périmètre/poids inutiles hors `/evenements`.
- *Nouveau layout dédié* — rejeté : pas de design sidebar spécifique requis (contrairement à /media).

---

## Synthèse des décisions

| Réf | Sujet | Décision |
|---|---|---|
| R1 | `online` (dette) | = format en ligne ; visibilité = `status='published'` ; Activer/Désactiver → Publier/Dépublier ; `can_register` corrigé |
| R2 | `pays_id` (dette) | migration additive corrective (integer, nullable, sans contrainte DB) |
| R3 | Statut temporel | dérivé des dates (accessors/scopes existants + `temporal_status`), distinct de l'éditorial |
| R4 | Type d'événement | `categories` `type='event'` via `category_id` existant |
| R5 | Intervenants | table `event_speakers` (hasMany, RelationManager) |
| R6 | Médias post-événement | table `event_medias` (replay/image/document) + `VideoEmbed` ; `compte_rendu` texte |
| R7 | Listing | Livewire `GrilleEvents` (patron `GrilleProjets`) + `EventSearchService` |
| R8 | Countdown / cron | countdown JS client (optionnel) ; cron `events:mark-completed` optionnel (éditorial only) |
| R9 | Inscription | `registration_mode` + `registration_url` ; interne réutilise l'existant |
| R10 | Assets / nav | entrées Vite `events.css|js` + nav « Événements » ; Lomé COM'TOUR conservé |

Tous les points « NEEDS CLARIFICATION » potentiels sont résolus par défaut raisonnable + cohérence avec l'existant. Aucun blocage pour la Phase 1.
