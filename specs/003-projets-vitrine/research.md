# Phase 0 — Research : Section Projets (vitrine)

Objet : lever les inconnues techniques et figer les décisions structurantes avant la conception (Phase 1). Aucune inconnue `NEEDS CLARIFICATION` ne subsiste après cette phase. Les choix réutilisent au maximum les patrons des features 001 (annuaire) et 002 (média).

---

## R1 — Modèle autonome `Projet` vs réutilisation de `Post`/`Media`

- **Décision** : créer un modèle **`Projet` autonome** (table `projets`) avec ses tables liées.
- **Rationale** : un projet n'est ni un article de blog (`Post`) ni un contenu éditorial (`Media`) ; il porte des champs propres (contexte, objectifs, activités, chiffres clés d'impact, partenaires, statut métier Actif/Réalisé/En développement). Surcharger `Post`/`Media` mélangerait les domaines et fragiliserait l'existant. Le CLAUDE.md insiste déjà sur la séparation `Media`/`Post`.
- **Alternatives rejetées** : (a) étendre `Post` avec un `type='projet'` → pollue le blog, champs incompatibles ; (b) étendre `Media` → couplage à la newsroom (séries, lecteurs audio) hors sujet.

## R2 — Thématiques : réutilisation de `categories` (`type='projet'`)

- **Décision** : réutiliser la table `categories` existante avec une nouvelle valeur `type='projet'`, en relation **many-to-many** via un pivot `category_projet`.
- **Rationale** : `categories` porte déjà un discriminant `type` (utilisé par `002` avec `type='media'`). Un projet peut relever de plusieurs thématiques (communication, formation, média…). Réutiliser le référentiel évite un doublon et un nouvel écran d'admin (la `CategoryResource` existante gère déjà les catégories par type).
- **Contrainte** : `categories.id` est un `int` legacy **sans AUTO_INCREMENT** → la FK côté pivot est une **colonne `integer` sans contrainte DB** et le seeder calcule l'id manuellement (même contournement que `MediaSeeder`).
- **Alternatives rejetées** : table `thematiques` dédiée → redondante avec `categories`.

## R3 — Zone géographique : réutilisation de `pays` + portée régionale

- **Décision** : colonne `pays_id` (`integer` nullable, réutilise `pays`) pour un projet national **plus** un champ `portee` (enum `pays` | `regional` | `continental`) et un libellé libre `zone_libelle` pour les portées non nationales (ex. « Afrique de l'Ouest »).
- **Rationale** : la spec exige de représenter et filtrer aussi bien un pays précis qu'une portée régionale/continentale (FR-005, edge case « régional vs pays »). Réutiliser `pays` aligne le filtre sur l'annuaire.
- **Contrainte** : `pays.id` legacy `int` sans AUTO_INCREMENT → `pays_id` est une colonne `integer` sans contrainte DB.
- **Alternatives rejetées** : champ texte unique → empêche un filtre fiable par pays.

## R4 — Statut métier vs état de publication

- **Décision** : deux notions distinctes —
  - `statut` (enum métier : `actif`, `realise`, `en_developpement`) → affiché en badge, éditorial ;
  - `is_published` (booléen) + `published_at` (datetime nullable) → contrôle la visibilité publique.
- **Rationale** : la spec (Assumptions) sépare explicitement le statut éditorial de la publication. Un projet « En développement » peut être publié (visible) ou non. Mappe la nuance demandée par FR-003/FR-006/FR-020.
- **Alternatives rejetées** : un seul champ `status` mélangeant les deux → impossible d'avoir un projet « Réalisé » encore en brouillon.

## R5 — Recherche & filtres : patron `MediaSearchService` + Livewire `GrilleMedias`

- **Décision** : créer `App\Services\Projets\ProjetSearchService` calqué sur `App\Services\Media\MediaSearchService`, et un composant `App\Livewire\Projets\GrilleProjets` calqué sur `App\Livewire\Media\GrilleMedias`. Recherche plein-texte sur `titre_normalise` (normalisé par `App\Services\Annuaire\TextNormalizer`), filtres `thematique`/`zone`/`statut`, tri (récents/alpha), pagination « charger plus ».
- **Rationale** : ces patrons sont déjà éprouvés (001 puis 002), garantissent l'anti-N+1 (eager-loading), l'URL-state des filtres et la cohérence UX. Pas besoin de moteur externe (Scout/Meilisearch) au volume attendu.
- **Alternatives rejetées** : Laravel Scout → surdimensionné pour quelques centaines de projets ; requête ad hoc dans le contrôleur → duplique la logique de filtre.

## R6 — Galerie photos/vidéos : `<img>`/lightbox + embed `VideoEmbed`

- **Décision** : table `projet_medias` polymorphe-légère avec `type` (`image` | `video`). Les images sont des fichiers téléversés (disque `public`, miniatures via `intervention/image`) ; les vidéos sont soit un **embed** YouTube/Vimeo via `App\Helper\VideoEmbed` existant, soit un fichier `<video>` natif. Lightbox JS léger (`resources/js/projets.js`).
- **Rationale** : réutilise l'helper d'embed déjà présent (002) et évite l'hébergement vidéo lourd (pas de FFmpeg/HLS). Cohérent avec le choix « embed » de la newsroom.
- **Alternatives rejetées** : Plyr/lecteur complet → inutile pour une galerie vitrine ; hébergement vidéo interne → coût et complexité injustifiés.

## R7 — Partenaires : table master réutilisable + pivot

- **Décision** : table `partenaires` (nom, logo, url) **réutilisable** entre projets, reliée par un pivot `partenaire_projet` (many-to-many, `position`).
- **Rationale** : une institution comme RPA collabore avec des partenaires récurrents ; une table master évite de re-saisir et re-téléverser le logo à chaque projet, et permet une `PartenaireResource` d'admin dédiée. FR-022.
- **Alternatives rejetées** : lignes partenaires dupliquées par projet → ressaisie, logos en double ; liaison à l'annuaire `Profil` → les partenaires ne sont pas des membres, couplage injustifié (cf. spec Assumptions).

## R8 — Chiffres clés (résultats/impact) : table dédiée ordonnée

- **Décision** : table `projet_resultats` (libelle, valeur, unite, icone optionnelle, position) en `hasMany`.
- **Rationale** : permet la mise en valeur ordonnée (FR-013) et le masquage si vide (FR-012), tout en restant éditable ligne à ligne dans un RelationManager Filament. Plus souple qu'un JSON pour l'ordre et l'affichage.
- **Alternatives rejetées** : colonne JSON `resultats` → ordre et édition unitaire plus pénibles côté admin.

## R9 — Témoignages optionnels : table dédiée

- **Décision** : table `projet_temoignages` (auteur, fonction, organisation, contenu, photo optionnelle, position), `hasMany`. Section entièrement masquée si aucune ligne (FR-011/FR-012).
- **Rationale** : symétrique aux chiffres clés ; édition simple via RelationManager.
- **Alternatives rejetées** : réutiliser un système de commentaires (`MediaComment`) → modération/spam hors sujet, les témoignages sont curatés par l'admin.

## R10 — « Nous contacter » : réutilisation de `/contact`

- **Décision** : le bouton « Nous contacter » de la page détaillée pointe vers la route existante `/contact` (`contact`) en passant la **référence du projet** en paramètre (`?projet={slug}`), le formulaire de contact existant (`App\Livewire\Contactform`) pré-renseignant éventuellement l'objet.
- **Rationale** : la spec (Assumptions) retient la réutilisation du dispositif de contact existant plutôt qu'un nouveau canal. Évite une nouvelle table de demandes et un nouveau flux mail.
- **Alternatives rejetées** : formulaire d'inquiry dédié par projet + stockage → hors périmètre MVP (à rouvrir via `/speckit-clarify` si souhaité).

## R11 — Layout & assets : réutilisation de `layouts/front.blade.php`

- **Décision** : le listing et le détail utilisent le **layout public existant `front.blade.php`** (en-tête/nav/pied communs). Deux nouvelles entrées Vite `resources/css/projets.css` et `resources/js/projets.js` (lightbox galerie + init embed), ajoutées à `vite.config.js`.
- **Rationale** : une vitrine institutionnelle doit s'intégrer au site principal (contrairement au design sidebar autonome de `/media`). Cohérence visuelle et SEO, moindre coût. Le pattern « entrée Vite dédiée » est déjà établi par 002 (`media.css|js`).
- **Alternatives rejetées** : nouveau layout dédié → rupture visuelle et duplication inutiles.

## R12 — Slug & normalisation du titre

- **Décision** : trait `App\Helper\Sluggable` pour le slug (route-key `slug`), et `App\Services\Annuaire\TextNormalizer` pour remplir `titre_normalise` (recherche accent-insensible), via un `ProjetObserver`.
- **Rationale** : exactement le patron de `Media` (slug + `titre_normalise` rempli par observer). Garantit des URLs lisibles (FR-009) et une recherche robuste.
- **Alternatives rejetées** : slug à la main / recherche `LIKE` brute → fragile aux accents et doublons.

---

## Synthèse des décisions

| # | Sujet | Décision |
|---|---|---|
| R1 | Modèle | `Projet` autonome + tables liées |
| R2 | Thématiques | `categories` (`type='projet'`) + pivot `category_projet` |
| R3 | Zone | `pays_id` + `portee` (pays/regional/continental) + `zone_libelle` |
| R4 | Statut/publication | `statut` métier ≠ `is_published`/`published_at` |
| R5 | Recherche/filtres | `ProjetSearchService` + Livewire `GrilleProjets` |
| R6 | Galerie | `projet_medias` images (upload) + vidéos (embed `VideoEmbed`) + lightbox |
| R7 | Partenaires | table master `partenaires` + pivot `partenaire_projet` |
| R8 | Chiffres clés | table `projet_resultats` ordonnée |
| R9 | Témoignages | table `projet_temoignages` optionnelle |
| R10 | Nous contacter | réutilise `/contact?projet=slug` |
| R11 | Layout/assets | `layouts/front` + entrées Vite `projets.css|js` |
| R12 | Slug/normalisation | `Sluggable` + `TextNormalizer` via `ProjetObserver` |

Aucune inconnue résiduelle. Prêt pour la Phase 1 (data-model, contrats, quickstart).
