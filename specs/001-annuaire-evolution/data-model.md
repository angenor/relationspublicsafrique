# Phase 1 — Data Model : Évolution de l'annuaire

## Vue d'ensemble

Toutes les modifications sont **additives** sur la table `profils` existante. Aucune colonne existante n'est supprimée. De nouvelles tables couvrent les relations N-N, l'historique, la modération et le consentement.

## Diagramme logique

```text
                ┌────────────────────────────┐
                │           profils          │ (étendue)
                └─────────────┬──────────────┘
                              │
   ┌──────────────────────────┼──────────────────────────────────┐
   │                          │                                  │
   ▼                          ▼                                  ▼
profil_domaine_expertise   profil_tag                    liens_externes
   │                          │                                  
   ▼                          ▼                                  
domaines_expertise         tags

profils ──── 1-N ──── historique_profils
profils ──── 1-N ──── demandes_moderation
profils ──── 1-1 ──── consentements_profils
profils ──── N-1 ──── pays (existant)
profils ──── N-1 ──── users (créateur — existant)
```

## Tables

### `profils` (extension via migration additive)

Colonnes existantes conservées : `id, name, slug, nom, prenom, title, fonction, domaine, facebook, twitter, youtube, linkding, site, contact, adresse, tel, email, bio, image, online, aprouve, pays_id, user_id, created_at, updated_at, cover`.

**Nouvelles colonnes ajoutées** :

| Colonne | Type | Null | Défaut | Description |
|---|---|---|---|---|
| `nationalite` | string(100) | oui | null | Nationalité de la personne (peut différer du pays de localisation). |
| `bio_courte` | string(280) | oui | null | Biographie courte affichée sur la carte. |
| `bio_longue` | text | oui | null | Biographie longue ; remplace progressivement `bio` (legacy laissé en place). |
| `ville` | string(150) | oui | null | Ville de localisation. |
| `organisation` | string(200) | oui | null | Organisation / structure d'appartenance. |
| `type_profil` | enum(`expert`,`étudiant`,`alumni`,`partenaire`,`autre`) | non | `autre` | Catégorie métier filtrable. |
| `etat_publication` | enum(`en_attente`,`publié`,`archivé`) | non | `en_attente` | Workflow modération. |
| `masquer_email` | boolean | non | `true` | Si vrai, l'email n'est pas exposé publiquement. |
| `masquer_tel` | boolean | non | `true` | Idem pour le téléphone. |
| `nom_normalise` | string(150) | non | `''` | Indexé. Maintenu via mutateur. |
| `prenom_normalise` | string(150) | non | `''` | Indexé. |
| `organisation_normalisee` | string(200) | non | `''` | Indexé. |
| `ville_normalisee` | string(150) | non | `''` | Indexé. |
| `legacy_sans_consentement` | boolean | non | `false` | Marque les profils migrés sans consentement attesté. |
| `published_at` | timestamp | oui | null | Date de première publication (renseignée à l'approbation). |
| `deleted_at` | timestamp | oui | null | SoftDeletes — suppression logique. |

**Index** :
- `idx_profils_etat_type (etat_publication, type_profil)`
- `idx_profils_nom_norm (nom_normalise)`
- `idx_profils_prenom_norm (prenom_normalise)`
- `idx_profils_orga_norm (organisation_normalisee)`
- `idx_profils_ville_norm (ville_normalisee)`
- `idx_profils_pays (pays_id)` (probablement déjà présent)

**Règles de validation** (à enforcer via `ProfilStoreRequest`/`ProfilUpdateRequest`) :
- `nom`, `prenom` : requis, max 100.
- `email` : facultatif, format email, max 200.
- `bio_courte` : max 280.
- `type_profil` : in enum.
- `image` : facultative à la création (avatar par défaut sinon), validée selon FR-004.
- Liens externes : URL valide pour chaque ligne associée.

### `domaines_expertise`

| Colonne | Type | Null | Défaut |
|---|---|---|---|
| `id` | bigint PK | non | auto |
| `libelle` | string(150) | non | — |
| `slug` | string(150) UNIQUE | non | — |
| `created_at`/`updated_at` | timestamps | oui | null |

### `profil_domaine_expertise` (pivot)

| Colonne | Type |
|---|---|
| `profil_id` | bigint FK → profils.id (cascade delete) |
| `domaine_expertise_id` | bigint FK → domaines_expertise.id (cascade delete) |
| PK composite `(profil_id, domaine_expertise_id)` |

### `tags`

| Colonne | Type |
|---|---|
| `id` | bigint PK |
| `libelle` | string(80) |
| `slug` | string(80) UNIQUE |
| `created_at`/`updated_at` | timestamps |

### `profil_tag` (pivot)

| Colonne | Type |
|---|---|
| `profil_id` | bigint FK → profils.id (cascade) |
| `tag_id` | bigint FK → tags.id (cascade) |
| PK `(profil_id, tag_id)` |

### `liens_externes`

| Colonne | Type | Null | Défaut |
|---|---|---|---|
| `id` | bigint PK | non | — |
| `profil_id` | bigint FK → profils.id (cascade) | non | — |
| `type` | enum(`linkedin`,`site_web`,`portfolio`,`facebook`,`twitter`,`youtube`,`autre`) | non | — |
| `url` | string(500) | non | — |
| `libelle` | string(150) | oui | null | Libellé optionnel affiché à la place de l'URL. |
| `created_at`/`updated_at` | timestamps | oui | null |

Index : `idx_liens_profil (profil_id)`.

### `historique_profils`

| Colonne | Type | Description |
|---|---|---|
| `id` | bigint PK | |
| `profil_id` | bigint FK → profils.id (cascade) | |
| `user_id` | bigint FK → users.id nullable (système) | Auteur. |
| `action` | enum(`créé`,`modifié`,`statut_changé`,`approuvé`,`rejeté`,`supprimé`,`restauré`,`retrait_demandé`) | |
| `diff` | json | `{champ: {avant, apres}, …}` |
| `motif` | text nullable | Pour les rejets ou demandes de retrait. |
| `ip` | string(45) nullable | IPv4/IPv6. |
| `user_agent` | string(255) nullable | |
| `created_at` | timestamp | |

Index : `idx_hist_profil_date (profil_id, created_at DESC)`.

### `demandes_moderation`

| Colonne | Type | Description |
|---|---|---|
| `id` | bigint PK | |
| `profil_id` | bigint FK → profils.id (cascade) | |
| `soumis_par` | bigint FK → users.id | Éditeur qui a soumis. |
| `moderateur_id` | bigint FK → users.id nullable | Admin qui a tranché. |
| `decision` | enum(`en_attente`,`approuvée`,`rejetée`) | défaut `en_attente`. |
| `motif` | text nullable | Justificatif en cas de rejet. |
| `created_at`/`updated_at` | timestamps | |

Index : `idx_modera_profil_decision (profil_id, decision)`.

### `consentements_profils`

| Colonne | Type | Description |
|---|---|---|
| `id` | bigint PK | |
| `profil_id` | bigint FK → profils.id (cascade), UNIQUE | 1-1 |
| `atteste_par` | bigint FK → users.id | Éditeur ayant coché la case. |
| `atteste_le` | timestamp non null | Date du consentement. |
| `email_notification_envoye_a` | string(200) nullable | Adresse cible (gel au moment de l'envoi). |
| `email_notification_envoye_le` | timestamp nullable | |
| `jeton_retrait` | string(64) nullable, UNIQUE | Pour le lien signé. |
| `jeton_expire_le` | timestamp nullable | |
| `retrait_demande_le` | timestamp nullable | |
| `created_at`/`updated_at` | timestamps | |

Index : `idx_consent_jeton (jeton_retrait)`.

## Stratégie de masquage des coordonnées

**Critique** : ne jamais sérialiser un champ masqué dans une réponse publique.

- `App\Http\Resources\ProfilPublicResource` (utilisée par l'API publique et par Livewire pour la page publique) :
  - omet `email` si `masquer_email = true`,
  - omet `tel` si `masquer_tel = true`,
  - omet en toutes circonstances les champs administratifs (`legacy_sans_consentement`, `etat_publication`, jetons, etc.).
- Les vues Blade publiques utilisent EXCLUSIVEMENT les attributs résolus via `ProfilPublicResource`.
- Test automatisé `ProfilDetailTest::test_coordonnees_masquees_jamais_dans_reponse_publique()` parcourt le HTML rendu et le JSON API pour vérifier qu'aucun email/téléphone masqué n'apparaît.

## Transitions d'état (modération)

```text
[création par éditeur] ─► en_attente
                          │
                          ├─► (approve admin)   ─► publié      (publication: published_at = now())
                          └─► (reject  admin)   ─► en_attente  + motif (notification éditeur)

[publié] ──► (archive admin)              ─► archivé
[publié] ──► (demande_retrait personne)   ─► archivé  + notification admins

[archivé] ──► (republier admin)           ─► publié
[*] ──► (supprimer admin)                 ─► SoftDeletes deleted_at
[supprimé] ──► (restaurer admin)          ─► retour à l'état précédent (lu via historique)
```

## Rôles et politique d'accès (`ProfilPolicy`)

| Action | Visiteur | Éditeur | Admin |
|---|---|---|---|
| Voir liste publique (état_publication=publié) | ✅ | ✅ | ✅ |
| Voir fiche publique (état_publication=publié) | ✅ | ✅ | ✅ |
| Voir back-office (tous états) | ❌ | ✅ (limité aux profils qu'il a créés ou qui lui sont assignés) | ✅ (tous) |
| Créer profil | ❌ | ✅ | ✅ |
| Modifier profil | ❌ | ✅ (ses profils) | ✅ |
| Approuver / Rejeter | ❌ | ❌ | ✅ |
| Archiver / Republier | ❌ | ❌ | ✅ |
| Supprimer (soft) | ❌ | ❌ | ✅ |
| Suppression définitive (force delete) | ❌ | ❌ | ✅ |
| Restaurer une version (US6) | ❌ | ❌ | ✅ |
| Importer / Exporter | ❌ | ❌ | ✅ |

## Migration des données existantes

Mapping des colonnes legacy → nouvelles :

| Champ legacy | Cible nouvelle | Règle |
|---|---|---|
| `online=1` | `etat_publication=publié` | si `aprouve=1` |
| `online=0` ou `aprouve=0` | `etat_publication=en_attente` ou `archivé` | voir `research.md §9` |
| `domaine` (string) | lignes `domaines_expertise` + pivot | split sur `,` et `;` |
| `facebook/twitter/youtube/linkding/site` | lignes `liens_externes` | type associé |
| (aucun) | `type_profil=autre` | par défaut |
| (aucun) | `legacy_sans_consentement=true` | tous les profils legacy |
| `bio` | `bio_longue` ← copié | `bio` laissé en place pour rétro-compat ; tronquer `bio_courte` au besoin |

Le seeder de migration journalise dans `historique_profils` une entrée `action=migration_initiale` par profil pour traçabilité.
