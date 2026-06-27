# Contrat — Back-office Filament (`EventResource` étendu)

**Évolution** de la ressource **existante** `App\Filament\Resources\EventResource` (groupe « Actualités », icône `heroicon-o-calendar`). On **enrichit** le formulaire/table et on **corrige** les actions de visibilité ; on **ajoute** 2 RelationManagers. Aucune nouvelle ressource racine.

## Formulaire — sections (form schema)

Réorganisation en sections (Card/Section), en conservant les champs existants :

1. **Identité & card** : `title` (live → `slug`), `slug`, `resume` (Textarea, courte description card), `image` (FileUpload `events`).
2. **Contenu détaillé** *(nouveau)* : `description` (RichEditor, existant), `objectifs` (RichEditor/Textarea), `programme` (RichEditor), `public_cible` (Textarea).
3. **Dates** : `start_date`, `end_date` (`after('start_date')`), `registration_deadline` (`before('start_date')`) — existants.
4. **Format & lieu** *(clarifié — R1)* : `online` (Toggle « En ligne (format) »), `location` (« Lieu / Ville », masqué/optionnel si `online`).
5. **Inscription** *(nouveau — R9)* : `registration_mode` (Select `Event::$registrationModes`, défaut `internal`), `registration_url` (`url`, **requis si** `registration_mode='external'`, `visible` si externe), `max_participants`, `current_participants` (disabled), `price`.
6. **Publication & classement** : `status` (Select `Event::$statuses`), `is_featured` (Toggle), `category_id` (**type d'événement**, catégories `type='event'`), `pays_id` (Select `pays`), `user_id` (organisateur).
7. **Compte rendu** *(nouveau, pertinent post-événement)* : `compte_rendu` (RichEditor).

> **Badge statut temporel** (lecture seule) : un `Placeholder` affichant `temporal_status_label` (« À venir / En cours / Clos ») calculé depuis les dates, pour aider l'admin (FR-023). Distinct du `status` éditorial.

### Validation (form)
- `title`, `start_date`, `end_date` requis ; `end_date ≥ start_date` ; `registration_deadline ≤ start_date`.
- `registration_url` requis + URL valide **si** `registration_mode='external'`.
- `status ∈ $statuses` ; `registration_mode ∈ $registrationModes`.
- `category_id` restreint aux catégories `type='event'`.

## Table

Colonnes existantes conservées (image, title, start_date, location, pays.name, price, status badge, online icon, is_featured icon, participants). **Ajouts** :
- `BadgeColumn`/`TextColumn` **statut temporel** (À venir/En cours/Clos) calculé.
- `registration_mode` (badge interne/externe).
- Comptes `speakers`/`medias` (optionnel).

Filtres existants conservés (`status`, `pays_id`, En ligne, Mis en avant, À venir, En cours) + ajout filtre **type** (`category_id` parmi `type='event'`) et **Clos** (`past()`).

## Actions *(correction R1)*

- **Remplacer** les actions « Activer/Désactiver » (qui basculaient `online`) par **« Publier »/« Dépublier »** agissant sur `status` (`published`↔`draft`). Bulk équivalents.
- Conserver View/Edit/Delete + bulk delete.
- (Optionnel) action « Marquer terminé » (`status='completed'`) pour un événement clos.

> ⚠️ La méthode modèle `activate()/deactivate()` (bascule `online`) n'est **plus** utilisée pour la visibilité ; soit la conserver pour un usage « format », soit la déprécier — à trancher en implémentation, sans impact public (R1).

## RelationManagers

1. **`RegistrationsRelationManager`** *(existant — conservé)* : inscriptions internes.
2. **`SpeakersRelationManager`** *(nouveau — R5)* : `event_speakers` — champs `nom` (requis), `role`, `organisation`, `photo` (FileUpload `events/speakers`), `bio`, `position` (reorderable). Table ordonnée par `position`.
3. **`MediasRelationManager`** *(nouveau — R6)* : `event_medias` — `type` (Select `replay|image|document`), `chemin` (FileUpload `events/medias`, visible si fichier), `url_embed` (visible si `type=replay`), `legende`, `position` (reorderable). Permet l'**upload de médias post-événement** (FR-022).

## Pages

Inchangées : `ListEvents`, `CreateEvent`, `EditEvent` (`EventResource/Pages/`).

## Tests de contrat (Feature)

- Création d'un événement complet (tous champs + intervenants + médias) → visible côté public dans le bon groupe temporel (SC-005).
- Action **Publier** → `status='published'` → événement listé publiquement ; **Dépublier** → retiré du public (FR-006/FR-020).
- `registration_mode='external'` sans `registration_url` → validation échoue ; avec URL → CTA public externe (FR-013).
- Ajout d'un média `replay` sur un événement clos → CTA public « Voir replay / photos / compte rendu » (SC-006).
- `category_id` limité aux catégories `type='event'`.
- **Non-régression** : `RegistrationsRelationManager` et les pages existantes fonctionnent ; la table liste les événements (publiés et brouillons) côté admin.
