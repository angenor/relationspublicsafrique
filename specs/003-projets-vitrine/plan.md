# Implementation Plan: Section Projets (vitrine)

**Branch**: `003-projets-vitrine` | **Date**: 2026-06-26 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `specs/003-projets-vitrine/spec.md`

## Summary

Nouvelle section publique **`/projets`** : une vitrine institutionnelle des projets portés par Relations Publiques Afrique (RPA). Listing en **cards** (nom, visuel, brève description, thématique, zone géographique, badge de statut **Actif / Réalisé / En développement**, bouton « Découvrir ») avec **filtres réactifs** (thématique, zone, statut), et **page détaillée** par projet (contexte/problématique, objectifs, description détaillée, activités réalisées, **chiffres clés** d'impact, **partenaires**, **galerie** photos/vidéos, **témoignages** optionnels, bouton « Nous contacter »). Back-office Filament complet : CRUD projets, gestion des statuts, état de publication, upload des visuels et médias de galerie, partenaires, chiffres clés et témoignages.

**Approche technique** (détaillée dans [research.md](./research.md)) : modèle **`Projet` autonome** + tables liées, en **réutilisant les patrons et helpers déjà éprouvés** par les features 001 (annuaire) et 002 (média) — trait `App\Helper\Sluggable` (slug), `App\Services\Annuaire\TextNormalizer` (titre normalisé pour la recherche), patron de service `App\Services\Media\MediaSearchService` → `ProjetSearchService`, patron Livewire `App\Livewire\Media\GrilleMedias` → `GrilleProjets`, helper `App\Helper\VideoEmbed` (vidéos de galerie YouTube/Vimeo en embed), patron `MediaResource` (Filament + RelationManagers). Réutilisation des **référentiels existants** : `categories` avec `type='projet'` (thématiques) et `pays` (zone géographique). Le bouton « Nous contacter » réutilise la route `/contact` existante (référence du projet en paramètre). Aucune nouvelle dépendance npm/composer : la galerie utilise un lightbox léger et l'embed vidéo existant ; le layout public réutilise `layouts/front.blade.php`.

## Technical Context

**Language/Version**: PHP `^8.1`, Laravel 10
**Primary Dependencies**: Filament 3 (admin), Livewire 3 (grille/filtres), Vite + Bootstrap 5 (front, cohérent avec annuaire/média), `App\Helper\VideoEmbed` (existant, embed YouTube/Vimeo), `intervention/image` (existant, miniatures). **Aucune nouvelle dépendance** (pas de Plyr/FFmpeg : la galerie vidéo est en embed ou `<video>` natif).
**Storage**: MySQL 8 (prod) / SQLite in-memory (tests). Fichiers sur disque `public` : `projets/cards`, `projets/principal`, `projets/galerie`, `projets/partenaires`, `projets/temoignages`. Aucun nouveau disque.
**Testing**: PHPUnit (`phpunit.xml`), `tests/Feature/Projets/*` & `tests/Unit/Projets/*` (miroir de `tests/*/Media` et `tests/*/Annuaire`). `vendor/bin/pint` pour le style.
**Target Platform**: serveur Linux (web), navigateurs modernes ; responsive mobile/tablette/desktop.
**Project Type**: web — monolithe Laravel (front Blade/Livewire + admin Filament partageant les modèles Eloquent).
**Performance Goals**: listing/recherche < 1 s perçue (SC-001) ; pagination/charger-plus pour ≥ 50 projets (SC-005). Eager-loading systématique (anti N+1) sur thématiques, zone, visuels.
**Constraints**: **non-régression** des sections existantes (`/`, `/actualites`, `/media`, annuaire) — aucune route/vue/modèle existants modifiés hormis des points d'extension additifs. Les FK vers `categories`/`pays`/`users` sont des **colonnes `integer` sans contrainte DB** (tables legacy à `id` `int` sans AUTO_INCREMENT) — même contournement que `002-media-newsroom`.
**Scale/Scope**: contenu institutionnel (dizaines à quelques centaines de projets), trafic public modéré ; 7 tables nouvelles, ~2 ressources Filament (+ 4 RelationManagers), 1 composant Livewire, réutilisation du layout `front`.

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

La constitution (`.specify/memory/constitution.md`) est encore le **modèle vierge** (placeholders non renseignés) : aucun principe ratifié n'impose de gate spécifique. Gates par défaut appliqués (bonnes pratiques + cohérence projet) :

| Gate (par défaut) | Statut | Justification |
|---|---|---|
| Simplicité / réutilisation (YAGNI) | ✅ PASS | Réutilise `Sluggable`, `TextNormalizer`, `VideoEmbed`, patrons `MediaSearchService`/`GrilleMedias`/`MediaResource`, référentiels `categories`/`pays`, layout `front`. **Aucune** nouvelle dépendance. |
| Non-régression de l'existant | ✅ PASS | Section autonome `/projets` ; seules modifications de fichiers existants = points d'extension additifs (`routes/web.php`, `vite.config.js`, nav). |
| Test-first / couverture | ✅ PASS (intention) | Tests Feature/Unit prévus en miroir de 001/002, écrits avec chaque lot. |
| Pas de dépendance injustifiée | ✅ PASS | Zéro ajout npm/composer. |

> Recommandation (hors périmètre) : renseigner la constitution via `/speckit-constitution`. Aucune violation à tracker (*Complexity Tracking* vide).

**Re-check post-design (Phase 1)** : la conception (data-model + contrats) reste additive (7 tables, patrons réutilisés, points d'extension), sans violation nouvelle. ✅ PASS.

## Project Structure

### Documentation (this feature)

```text
specs/003-projets-vitrine/
├── plan.md              # Ce fichier
├── spec.md              # Spécification (déjà créée)
├── research.md          # Phase 0 — décisions techniques
├── data-model.md        # Phase 1 — schéma de données
├── quickstart.md        # Phase 1 — amorçage & vérification
├── contracts/           # Phase 1 — contrats d'interface
│   ├── public-routes.md
│   ├── livewire-grille-projets.md
│   └── admin-filament.md
├── checklists/
│   └── requirements.md  # Checklist qualité de la spec (16/16)
└── tasks.md             # Phase 2 (généré par /speckit-tasks — PAS ici)
```

### Source Code (repository root)

```text
app/
├── Models/
│   ├── Projet.php                      # + traits Sluggable, SoftDeletes ; casts/scopes/accessors/relations
│   ├── ProjetResultat.php              # chiffre clé (libellé/valeur/unité/position)
│   ├── ProjetMedia.php                 # média de galerie (image|video, chemin/url, légende, position)
│   ├── ProjetTemoignage.php            # témoignage (auteur, fonction, organisation, contenu, photo)
│   └── Partenaire.php                  # partenaire réutilisable (nom, logo, url) — pivot partenaire_projet
├── Observers/
│   └── ProjetObserver.php              # slug, titre_normalise (TextNormalizer)
├── Services/
│   └── Projets/
│       └── ProjetSearchService.php     # calqué sur Services/Media/MediaSearchService.php
├── Livewire/
│   └── Projets/
│       └── GrilleProjets.php           # calqué sur Livewire/Media/GrilleMedias.php
├── Http/Controllers/
│   └── ProjetController.php            # index (listing) + show (détail)
└── Filament/Resources/
    ├── ProjetResource.php              # (+ Pages/, RelationManagers/{Resultats,GalerieMedias,Temoignages,Partenaires})
    └── PartenaireResource.php          # (+ Pages/) — CRUD master des partenaires réutilisables

database/
├── migrations/   # 7 migrations additives : projets, category_projet (pivot thématiques),
│                 #  partenaires, partenaire_projet (pivot), projet_resultats,
│                 #  projet_medias (galerie), projet_temoignages + index/backfill titre_normalise
├── factories/    # ProjetFactory, ProjetResultatFactory, ProjetMediaFactory,
│                 #  ProjetTemoignageFactory, PartenaireFactory
└── seeders/      # ProjetSeeder (idempotent ; ≥1 projet par statut + galerie/partenaires/chiffres/témoignages)
                  #  + CategorySeeder enrichi (catégories type='projet')

resources/
├── views/
│   ├── projets/
│   │   ├── index.blade.php             # listing (utilise layouts/front + Livewire GrilleProjets)
│   │   └── show.blade.php              # page détaillée (sections conditionnelles + galerie + Nous contacter)
│   ├── components/
│   │   └── projet-card.blade.php       # card (calquée sur media-card)
│   └── livewire/projets/grille-projets.blade.php
├── js/projets.js                       # init lightbox galerie + embed vidéo (NOUVELLE entrée Vite)
└── css/projets.css                     # styles section projets (NOUVELLE entrée Vite)

config/projets.php                      # per_page, per_page_step, featured/à la une
routes/web.php                          # + routes projets.index / projets.show (aucune route existante touchée)
vite.config.js                          # + 2 entrées (css/projets.css, js/projets.js)
resources/views/layouts/front.blade.php # + 1 entrée nav « Projets » (seule modif d'un fichier existant non additif)

tests/
├── Feature/Projets/*                   # listing/filtres, show, statuts/publication, admin Filament, non-régression
└── Unit/Projets/*                      # ProjetSearchService, slug, normalisation, scopes, relations
```

**Structure Decision** : monolithe Laravel existant (Option « web »). Le code projets est **isolé** sous des sous-dossiers `Projets/` dédiés (Livewire, Services, tests) en miroir de l'organisation `Media/` (002) et `Annuaire/` (001). Le listing et le détail réutilisent le **layout public `front.blade.php`** (pas de nouveau layout : une vitrine institutionnelle standard, contrairement au design sidebar dédié de `/media`). Les seules modifications de fichiers existants sont des **points d'extension** (`routes/web.php`, `vite.config.js`) plus l'ajout d'une entrée de navigation « Projets ».

## Phasing (lots d'implémentation, par dépendances)

> Le détail tâche-par-tâche sera généré par `/speckit-tasks`. Découpage cible (chaque lot livrable/testable) :

- **Lot 1 — Données & socle** *(dépend de rien)* : 7 migrations additives (+ index + backfill `titre_normalise`), modèles `Projet` / `ProjetResultat` / `ProjetMedia` / `ProjetTemoignage` / `Partenaire` (traits, casts, relations, scopes `published`/`byStatut`, accessors visuels), `ProjetObserver` (slug + titre normalisé), `config/projets.php`, factories + `ProjetSeeder` + catégories `type='projet'`. Tests Unit (slug, normalisation, scopes, relations).
- **Lot 2 — Admin Filament** *(dépend L1)* : `ProjetResource` (sections : identité/card, contenu détaillé, statut & publication, zone & thématiques, SEO optionnel ; uploads visuel card/principal) + RelationManagers `Resultats`, `GalerieMedias` (image/embed), `Temoignages`, `Partenaires` ; `PartenaireResource` (master). Permet de produire du contenu. Tests Feature (CRUD, publication, statut).
- **Lot 3 — Backend public** *(dépend L1)* : `ProjetController` (index + show), `ProjetSearchService` (filtres thématique/zone/statut + tri), routes `projets.*`, lien « Nous contacter » vers `/contact?projet=…`. Tests Feature (statuts/publication, show, 404 brouillon, **non-régression** des sections existantes).
- **Lot 4 — Frontend listing & détail** *(dépend L3)* : vue `projets/index` + Livewire `GrilleProjets` + `projet-card` + vue `grille-projets` (filtres réactifs, badge statut, charger-plus/pagination), vue `projets/show` (sections conditionnelles, chiffres clés mis en valeur, partenaires, galerie photos/vidéos avec lightbox + embed, témoignages, bouton Nous contacter), entrées Vite `projets.css|js`, entrée nav « Projets ». Tests Feature (filtres/URL, masquage des sections optionnelles).
- **Lot 5 — Polish & non-régression** *(dépend L1-L4)* : état vide explicite (listing & filtres sans résultat), visuel placeholder, accessibilité (clavier/aria sur galerie & filtres), revue perf (index/eager-load), `pint`, complétion de la couverture de tests (incluant SC-002/SC-006/SC-007).

## Complexity Tracking

> Aucune violation de gate à justifier (constitution vierge, conception alignée sur les patrons existants). Section laissée vide intentionnellement.

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| — | — | — |
