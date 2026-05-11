# Contracts : API et routes web de l'annuaire

## Routes publiques (Blade + Livewire)

### `GET /annuaire`

Page liste publique de l'annuaire. Composant Livewire `ProfilGrid`.

**Query parameters** (synchronisés avec l'URL pour partage et SEO) :

| Paramètre | Type | Description |
|---|---|---|
| `q` | string | Terme de recherche libre (nom, prénom, organisation, ville, domaine, tag). |
| `pays` | string (code ISO ou slug) | Filtre pays. |
| `type` | string (`expert`, `étudiant`, `alumni`, `partenaire`, `autre`) | Filtre type_profil. |
| `domaine` | string (slug) ou tableau | Filtre domaine(s) d'expertise. Multi-valeurs via `domaine[]=`. |
| `tag` | string (slug) ou tableau | Filtre tag(s). |
| `tri` | enum (`alpha`, `recent`, `pertinence`) | Tri (défaut : `alpha` si `q` vide, sinon `pertinence`). |
| `mode` | enum (`grille`, `liste`) | Mode d'affichage (défaut : `grille`). |
| `page` | int | Pagination (défaut : 1). |
| `per_page` | int | 12, 24, 48, 96 (défaut : 24). |

**Réponse** : HTML rendu par Livewire avec :
- Barre de recherche + filtres + tri + bascule grille/liste.
- Cards/lignes des profils filtrés (champs **publics** uniquement : voir `ProfilPublicResource`).
- Pagination.

**Codes** : 200. Pas d'authentification requise.

### `GET /annuaire/{slug}`

Fiche détail publique d'un profil. Slug stable (canonical URL).

**Réponse** : HTML Blade.
- Si `etat_publication != publié` → 404 (sauf si utilisateur connecté admin/éditeur).
- Champs affichés : nom, prénom, nationalité, photo, bio_courte, bio_longue, fonction, organisation, ville/pays, type_profil, domaines, tags, liens_externes.
- `email` et `tel` affichés UNIQUEMENT si `masquer_email = false` (resp. `masquer_tel = false`).

### `GET /annuaire/retrait/{token}`

Lien signé envoyé par email à la personne référencée.

- Si signature valide et `jeton_expire_le > now()` :
  - Affiche un récapitulatif du profil (données stockées).
  - Propose trois actions : « Tout est correct », « Demander rectification » (formulaire libre envoyé aux admins), « Demander retrait définitif ».
- Si lien expiré ou invalide : message d'erreur + lien pour redemander un email.

### `POST /annuaire/retrait/{token}/confirmer`

Action « Demander retrait définitif ». Met `etat_publication = archivé`, journalise dans `historique_profils` (action `retrait_demandé`), notifie tous les admins, envoie un email de confirmation à la personne.

## API JSON publique (`routes/api.php`)

### `GET /api/annuaire`

Endpoint Sanctum-friendly mais utilisable sans auth (lecture publique).

**Query parameters** : identiques à `/annuaire` (sans `mode`).

**Réponse 200** (enveloppe API standard du projet) :

```json
{
  "success": true,
  "data": [
    {
      "id": 12,
      "slug": "marie-diop",
      "nom": "Diop",
      "prenom": "Marie",
      "nom_complet": "Marie Diop",
      "nationalite": "Sénégalaise",
      "fonction": "Consultante en relations publiques",
      "organisation": "Cabinet RPA",
      "ville": "Dakar",
      "pays": {"code": "SN", "libelle": "Sénégal"},
      "type_profil": "expert",
      "domaines_expertise": [
        {"slug": "communication", "libelle": "Communication"},
        {"slug": "lobbying", "libelle": "Lobbying"}
      ],
      "tags": ["francophone", "afrique-ouest"],
      "bio_courte": "Spécialiste de la communication d'influence...",
      "photo_url": "https://.../profils/12.webp",
      "liens_externes": [
        {"type": "linkedin", "url": "https://linkedin.com/in/..."},
        {"type": "site_web", "url": "https://..."}
      ]
    }
  ],
  "meta": {
    "total": 247,
    "per_page": 24,
    "current_page": 1,
    "last_page": 11
  }
}
```

**Règles de sérialisation** :
- `email` inclus seulement si `masquer_email = false`.
- `tel` inclus seulement si `masquer_tel = false`.
- `bio_longue` inclus uniquement sur `/api/annuaire/{slug}` (fiche détail), pas dans la liste.
- Aucun champ admin (`etat_publication`, `legacy_sans_consentement`, jetons…) n'est jamais sérialisé publiquement.

**Codes** :
- 200 : succès.
- 422 : paramètres invalides (`tri` hors enum, etc.) — message d'erreur localisé.

### `GET /api/annuaire/{slug}`

Détail JSON d'un profil publié.

**Réponse 200** : objet profil enrichi (avec `bio_longue`).
**Réponse 404** : profil introuvable ou non publié.

## Back-office (Filament)

Routes auto-générées par Filament sous `/admin/profils` :
- `/admin/profils` (List)
- `/admin/profils/create` (Create)
- `/admin/profils/{id}/edit` (Edit)
- `/admin/profils/{id}` (View)
- `/admin/profils/{id}/historique` (custom page, RelationManager)
- `/admin/profils/import` (custom action)
- `/admin/profils/export` (custom action — respecte filtres actifs)

**Actions Filament personnalisées** :
- `Approuver` (admin uniquement, visible si `etat_publication = en_attente`).
- `Rejeter` (admin, ouvre une modale demandant le motif).
- `Archiver` / `Republier` (admin).
- `Supprimer` (soft delete via `SoftDeletes`).
- `Restaurer version` (admin, depuis l'historique).
- `Renvoyer email consentement` (admin).
- `Régénérer jeton retrait` (admin).

## Sécurité

- Toutes les routes back-office sont protégées par le middleware Filament + `ProfilPolicy`.
- Les routes `/api/annuaire*` sont publiques en lecture mais soumises à un rate-limit (60 req/min/IP via `RouteServiceProvider`).
- Les URL signées pour le retrait utilisent `Illuminate\Routing\URL::signedRoute()` avec une expiration explicite.
- Les exports/imports respectent la policy `admin`.
