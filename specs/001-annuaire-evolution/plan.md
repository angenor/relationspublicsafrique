# Implementation Plan : Évolution de l'annuaire

**Branche** : `001-annuaire-evolution` | **Date** : 2026-05-10 | **Spec** : [spec.md](./spec.md)
**Entrée** : Spec fonctionnelle dans `specs/001-annuaire-evolution/spec.md`

## Summary

Évolution de la rubrique Annuaire : enrichir le modèle `Profil` existant (champs nationalité, biographies, type_profil, état_publication, consentement, etc.), introduire les entités associées (`DomaineExpertise`, `Tag`, `LienExterne`, `HistoriqueProfil`, `DemandeModeration`, `ConsentementProfil`), implémenter recherche multicritères insensible aux accents, filtres combinables et tri sur la page publique (grille de cartes par défaut + bascule liste), et fournir un back-office Filament 3 complet avec workflow de modération, import/export CSV (UTF-8 BOM + `;`) et XLSX, et historique des modifications. La migration est **additive** sur la table `profils` existante (pas de table parallèle).

## Technical Context

**Langage/Version** : PHP 8.1+ (back), JavaScript ES2022 (front)
**Framework principal** : Laravel 10, Filament 3, Livewire 3, Vue 3 + Vite, Tailwind/Bootstrap
**Stockage** : MySQL 8 (prod), SQLite (dev/tests)
**Tests** : PHPUnit (`vendor/bin/phpunit`) — Feature pour API/back-office, Unit pour services, Browser via Dusk si pertinent
**Plateforme cible** : Web (desktop + mobile responsive)
**Type de projet** : Web application (monolithe Laravel + îlots Vue/Livewire)
**Performance** : recherche < 1 s pour 5 000 profils ; combinaison recherche + 3 filtres + tri < 1,5 s
**Contraintes** :
- Migrations additives uniquement sur `profils` (aucune perte de données).
- CSV export : UTF-8 avec BOM + délimiteur `;`.
- Coordonnées masquées jamais transmises côté public (filtrage serveur, pas CSS).
- Recherche insensible à la casse ET aux accents.
**Échelle** : ~5 000 profils à 12 mois, ~50 administrateurs/éditeurs.

**Dépendances tierces à introduire** :
- `maatwebsite/excel` (^3.1) — export/import CSV et XLSX (gère délimiteur et BOM, mature).
- Pas d'index de recherche externe à ce stade ; recherche SQL avec colonnes normalisées + `LOWER()`.

**Dépendances déjà présentes réutilisées** : `intervention/image` (recadrage/validation photo), Filament 3, infrastructure d'authentification existante.

**Mécanique des rôles** : réutilisation du champ existant `users.type` (string). Valeurs : `admin`, `editeur`. Pas d'installation de `spatie/laravel-permission`. Détails et alternatives dans `research.md` §10.

## Constitution Check

*La constitution du projet (`.specify/memory/constitution.md`) est encore au template par défaut. Aucun garde-fou formel n'est défini. Pour cette feature, garde-fous implicites dérivés de `CLAUDE.md` et des règles globales utilisateur :*

| Garde-fou | Statut |
|-----------|--------|
| Préférer l'extension Filament aux contrôleurs `Admin/` legacy | ✅ — toutes les nouvelles actions back-office passent par `app/Filament/Resources/ProfilResource.php`. |
| Aucune fuite de coordonnées masquées (sécurité critique) | ✅ — voir *Stratégie de masquage* dans `data-model.md` et test SC-005. |
| Migrations additives (pas de drop) | ✅ — toutes les migrations ajoutent colonnes/tables sans suppression. |
| TDD : tests Feature avant implémentation | ✅ — phase 2 produira `tasks.md` avec tests en amont. |
| Pas de hardcoding de secrets | ✅ — jetons signés via `APP_KEY` Laravel. |
| Confidentialité RGPD | ✅ — voir FR-025 à FR-029 et entité `ConsentementProfil`. |
| Immutabilité (règle globale) | ✅ — services renvoient des DTO/Collections plutôt que de muter les arguments. Objets Eloquent restent persistants mais le code applicatif reste pur autour. |
| Couverture de tests ≥ 80 % (règle globale) | ✅ — visée explicite dans `tasks.md` ; mesure via `phpunit --coverage-text`. |

**Résultat du gate** : ✅ Pass (aucune violation à justifier).

## Project Structure

### Documentation (cette feature)

```text
specs/001-annuaire-evolution/
├── plan.md              # Ce fichier
├── research.md          # Phase 0 — décisions techniques
├── data-model.md        # Phase 1 — schéma logique et migrations
├── quickstart.md        # Phase 1 — comment lancer/tester la feature
├── contracts/           # Phase 1
│   ├── api-annuaire.md
│   └── csv-schema.md
├── checklists/
│   └── requirements.md
└── tasks.md             # Phase 2 (généré par /speckit-tasks)
```

### Source Code (racine du dépôt)

```text
app/
├── Models/
│   ├── Profil.php                          # ÉTENDU : nouveaux champs, relations, scopes
│   ├── DomaineExpertise.php                # NOUVEAU
│   ├── Tag.php                             # NOUVEAU
│   ├── LienExterne.php                     # NOUVEAU
│   ├── HistoriqueProfil.php                # NOUVEAU
│   ├── DemandeModeration.php               # NOUVEAU
│   └── ConsentementProfil.php              # NOUVEAU
├── Filament/Resources/
│   └── ProfilResource.php                  # NOUVEAU (annuaire admin)
│       ├── Pages/{ListProfils,CreateProfil,EditProfil,ViewProfil}.php
│       └── RelationManagers/{DomainesExpertiseRM,TagsRM,LiensExternesRM,HistoriqueRM}.php
├── Http/
│   ├── Controllers/
│   │   ├── AnnuaireController.php          # ÉTENDU : liste + recherche + filtres + tri
│   │   └── ProfilController.php            # ÉTENDU : fiche détail publique + lien retrait
│   ├── Resources/
│   │   └── ProfilPublicResource.php        # API ressource publique (masquage)
│   └── Requests/
│       ├── ProfilStoreRequest.php
│       └── ProfilUpdateRequest.php
├── Observers/ProfilObserver.php            # alimente HistoriqueProfil
├── Services/Annuaire/
│   ├── ProfilSearchService.php             # recherche/filtres/tri
│   ├── ProfilModerationService.php         # workflow état_publication
│   ├── ProfilHistoriqueService.php         # journalisation diffs
│   ├── ProfilConsentementService.php       # jetons + emails
│   ├── ProfilImportService.php             # import CSV/XLSX
│   ├── ProfilExportService.php             # export CSV/XLSX (BOM + ;)
│   └── TextNormalizer.php                  # suppression accents/casse
├── Imports/ProfilsImport.php
├── Exports/ProfilsExport.php
├── Notifications/
│   ├── ProfilPubliePersonneNotification.php
│   ├── ProfilModerationDecisionNotification.php
│   └── DemandeRetraitConfirmeNotification.php
├── Livewire/Annuaire/
│   ├── ProfilGrid.php                      # page publique (grille + bascule + filtres + tri)
│   └── ProfilCarte.php
└── Policies/ProfilPolicy.php

database/migrations/
├── 2026_05_10_000001_extend_profils_for_annuaire.php
├── 2026_05_10_000002_create_domaines_expertise_table.php
├── 2026_05_10_000003_create_profil_domaine_expertise_pivot.php
├── 2026_05_10_000004_create_tags_table.php
├── 2026_05_10_000005_create_profil_tag_pivot.php
├── 2026_05_10_000006_create_liens_externes_table.php
├── 2026_05_10_000007_create_historique_profils_table.php
├── 2026_05_10_000008_create_demandes_moderation_table.php
└── 2026_05_10_000009_create_consentements_profils_table.php

routes/
├── web.php                                 # /annuaire, /annuaire/{slug}, /annuaire/retrait/{token}
└── api.php                                 # GET /api/annuaire?q=...&pays=...&type=...

resources/views/annuaire/
├── index.blade.php                         # liste publique (Livewire)
└── show.blade.php                          # fiche détaillée

tests/
├── Feature/Annuaire/
│   ├── AnnuaireListingTest.php             # US1
│   ├── AnnuaireSearchTest.php              # US1/US2 — recherche + filtres + tri
│   ├── ProfilDetailTest.php                # US1 — masquage coords (SC-005)
│   ├── BackofficeProfilCrudTest.php        # US3
│   ├── ModerationWorkflowTest.php          # US4
│   ├── ImportExportTest.php                # US5
│   ├── HistoriqueTest.php                  # US6
│   └── ConsentementRgpdTest.php            # FR-025 à FR-029, SC-009, SC-010
└── Unit/Annuaire/
    ├── ProfilSearchServiceTest.php
    ├── TextNormalizerTest.php
    └── ProfilExportServiceTest.php         # vérifie BOM + délimiteur ;
```

**Structure Decision** : Application web Laravel monolithique. Convention Filament déjà en place pour les ressources, services métier regroupés sous `app/Services/Annuaire/` pour testabilité.

## Complexity Tracking

Aucune violation de constitution → table non remplie.

## Phase 0 — Outline & Research

Voir [`research.md`](./research.md). Décisions clés :

1. **Recherche insensible aux accents** → colonnes pré-normalisées (`nom_normalise`, `prenom_normalise`, `organisation_normalisee`) maintenues par mutateurs Eloquent + `TextNormalizer`. Pas d'extension MySQL requise.
2. **Bibliothèque export/import** → `maatwebsite/excel` ^3.1.
3. **Jeton de retrait RGPD** → `Str::random(64)` + URL signée Laravel + expiration glissante 90 jours.
4. **Workflow modération** → enum simple + table `demandes_moderation` historisée.
5. **Historique** → table dédiée alimentée par `ProfilObserver` (pas de package tiers).
6. **Affichage public** → Livewire 3 pour grille + filtres (réactivité simple) ; Blade pour fiche détail.

## Phase 1 — Design & Contracts

Voir [`data-model.md`](./data-model.md), [`contracts/api-annuaire.md`](./contracts/api-annuaire.md), [`contracts/csv-schema.md`](./contracts/csv-schema.md), [`quickstart.md`](./quickstart.md).

### Mise à jour de l'agent context

Le bloc SPECKIT de `CLAUDE.md` est mis à jour pour référencer ce plan.

## Re-evaluation Constitution Check (post-design)

Aucun nouveau risque introduit. Services découplés, testables unitairement, séparation Filament/Livewire/Services respecte les surfaces de requête décrites dans `CLAUDE.md`. ✅ Pass.
