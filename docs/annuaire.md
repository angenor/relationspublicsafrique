# Annuaire — Documentation

Documentation des routes, endpoints et conventions techniques de la rubrique **Annuaire**
livrée par la feature `001-annuaire-evolution`.

Référence spec : [`specs/001-annuaire-evolution/spec.md`](../specs/001-annuaire-evolution/spec.md)
Référence plan : [`specs/001-annuaire-evolution/plan.md`](../specs/001-annuaire-evolution/plan.md)
Contracts : [`specs/001-annuaire-evolution/contracts/api-annuaire.md`](../specs/001-annuaire-evolution/contracts/api-annuaire.md)

## Sommaire

1. [Routes publiques (web)](#routes-publiques-web)
2. [API JSON publique](#api-json-publique)
3. [Back-office Filament](#back-office-filament)
4. [Commandes Artisan](#commandes-artisan)
5. [Configuration](#configuration)
6. [Tests](#tests)
7. [Benchmarks performance](#benchmarks-performance)

---

## Routes publiques (web)

| Méthode | URI | Action contrôleur | Nom de route | Auth |
|---------|-----|-------------------|--------------|------|
| GET | `/annuaire` | `AnnuaireController@index` | `annuaire.index` | publique |
| GET | `/annuaire/consulter` | `AnnuaireController@index` (alias) | `annuaire.consulter` | publique |
| GET | `/annuaire/rejoindre` | `AnnuaireController@rejoindre` | `annuaire.rejoindre` | publique |
| GET | `/annuaire/suggerer` | `AnnuaireController@suggerer` | `annuaire.suggerer` | publique |
| GET | `/annuaire/{slug}` | `AnnuaireController@show` | `annuaire.show` | publique |
| GET | `/annuaire/retrait/{token}` | `ProfilController@retraitForm` | `annuaire.retrait.form` | URL signée |
| POST | `/annuaire/retrait/{token}/confirmer` | `ProfilController@retraitConfirmer` | `annuaire.retrait.confirmer` | URL signée |

### Paramètres URL `/annuaire`

Synchronisés avec l'URL pour permettre partage et SEO (Livewire `#[Url]`) :

| Paramètre | Type | Description |
|-----------|------|-------------|
| `q` | string | Recherche libre (insensible accents/casse) |
| `pays` | slug | Filtre pays |
| `type` | enum | `expert`, `etudiant`, `alumni`, `partenaire`, `autre` |
| `domaines[]` | slug[] | Domaines d'expertise (multi) |
| `tags[]` | slug[] | Tags (multi) |
| `tri` | enum | `alpha`, `recent`, `pertinence` (défaut : `alpha` ou `pertinence` si `q`) |
| `mode` | enum | `grille` (défaut) ou `liste` |
| `page` | int | Pagination |
| `perPage` | int | 12 / 24 / 48 / 96 |

### Lien signé de retrait RGPD (FR-027/FR-028)

- Généré par `ProfilConsentementService::regenererJeton()`.
- Construit via `URL::signedRoute('annuaire.retrait.form', ['token' => $jeton])` avec
  expiration glissante de 90 jours (`ANNUAIRE_CONSENTEMENT_EXPIRY_DAYS`).
- Vérifie la signature Laravel **et** la cohérence du jeton stocké en BDD.

## API JSON publique

Préfixe : `/api`. Middleware : `throttle:60,1` (60 req/min/IP).

| Méthode | URI | Action contrôleur | Nom de route |
|---------|-----|-------------------|--------------|
| GET | `/api/annuaire` | `AnnuaireController@apiIndex` | `api.annuaire.index` |
| GET | `/api/annuaire/{slug}` | `AnnuaireController@apiShow` | `api.annuaire.show` |

### Enveloppe de réponse

```json
{
  "success": true,
  "data": [ /* ProfilPublicResource */ ],
  "meta": {
    "total": 247,
    "per_page": 24,
    "current_page": 1,
    "last_page": 11
  }
}
```

### Règles de sérialisation (SC-005)

- `email` omis si `masquer_email = true`.
- `tel` omis si `masquer_tel = true`.
- Jamais sérialisés publiquement : `etat_publication`, `legacy_sans_consentement`, jetons.
- `bio_longue` uniquement dans la réponse détail (`/api/annuaire/{slug}`).

## Back-office Filament

Mounted sous `/admin/profils`. Sécurisé par `ProfilPolicy`.

| Route | Action |
|-------|--------|
| `/admin/profils` | Liste des profils (admin = tous ; éditeur = ses profils) |
| `/admin/profils/create` | Création |
| `/admin/profils/{id}` | Vue (prévisualisation publique, FR-021) |
| `/admin/profils/{id}/edit` | Édition |
| `/admin/annuaire-consentements` | Page d'audit RGPD (FR-029) |

### Actions personnalisées

- **Approuver / Rejeter** (modal motif) — admin uniquement.
- **Archiver / Republier** — admin.
- **Renvoyer email consentement / Régénérer jeton retrait** — admin.
- **Exporter CSV / XLSX / Importer** — admin.
- **Restaurer version** depuis l'historique — admin.

### RelationManagers

- `LiensExternesRelationManager` — édition inline des liens (LinkedIn, site, etc.).
- `DemandesModerationRelationManager` — lecture seule, historique des décisions.
- `HistoriqueRelationManager` — lecture seule, audit complet + restauration version.

## Commandes Artisan

| Commande | Description |
|----------|-------------|
| `php artisan annuaire:renormaliser` | Recalcule toutes les colonnes `*_normalise`. À lancer après changement de `TextNormalizer`. |
| `php artisan annuaire:envoyer-rappels-consentement` | Renvoie l'email de consentement aux profils dont `jeton_expire_le` est < 7 jours. Planifié quotidiennement via `Kernel::schedule()`. |
| `php artisan db:seed --class=AnnuaireMigrationSeeder` | Migration des profils legacy (idempotent). |
| `php artisan db:seed --class=DomainesExpertiseSeeder` | Seed des domaines courants. |
| `php artisan db:seed --class=ProfilsStressSeeder` | Génère 5 000 profils factices pour les benchmarks. |

## Configuration

Toutes les options vivent dans `config/annuaire.php`, alimenté par `.env` :

```php
return [
    'per_page' => env('ANNUAIRE_PER_PAGE', 24),
    'consentement_expiry_days' => env('ANNUAIRE_CONSENTEMENT_EXPIRY_DAYS', 90),
    'rate_limit_public' => env('ANNUAIRE_RATE_LIMIT_PUBLIC', 60),
];
```

## Tests

```bash
# Suite annuaire complète (Feature + Unit)
vendor/bin/phpunit --filter=Annuaire

# Couverture annuaire
vendor/bin/phpunit --coverage-text --filter=Annuaire
```

Couverture cible : ≥ 80 % sur `app/Services/Annuaire/`,
`app/Http/Controllers/AnnuaireController.php`, `app/Http/Controllers/ProfilController.php`,
`app/Filament/Resources/ProfilResource*`, `app/Policies/ProfilPolicy.php`.

## Benchmarks performance

Hors suite par défaut (répertoire `tests/Performance/` non référencé dans `phpunit.xml`).

```bash
# SC-002 + SC-008 (recherche, recherche+filtres+tri)
vendor/bin/phpunit tests/Performance/AnnuaireBench.php

# SC-004 (import 500 profils < 120 s)
vendor/bin/phpunit tests/Performance/AnnuaireImportBench.php
```

Critères :
- SC-002 : 95 % des recherches < 1 s sur 5 000 profils.
- SC-008 : recherche libre + 3 filtres + tri < 1,5 s.
- SC-004 : import CSV 500 profils < 120 s.

## Schémas et contrats

- Données : [`data-model.md`](../specs/001-annuaire-evolution/data-model.md)
- API : [`contracts/api-annuaire.md`](../specs/001-annuaire-evolution/contracts/api-annuaire.md)
- CSV : [`contracts/csv-schema.md`](../specs/001-annuaire-evolution/contracts/csv-schema.md)
- Quickstart : [`quickstart.md`](../specs/001-annuaire-evolution/quickstart.md)
