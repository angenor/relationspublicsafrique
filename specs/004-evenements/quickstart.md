# Quickstart — Section Événements (vitrine)

Amorçage et vérification de la feature `004-evenements`. Suppose l'environnement projet en place (cf. `CLAUDE.md`). **Rappel** : le domaine Événement préexiste — cette feature l'**étend**.

## 1. Pré-requis & dépendances

- Aucune nouvelle dépendance composer/npm.
- Front lancé sur l'hôte (`npm run dev`), stack PHP via `php artisan serve` ou Docker.

## 2. Migrations & données

```bash
# Migrations additives de la feature (events colonnes + pays_id + event_speakers + event_medias)
php artisan migrate

# Seed : catégories type='event' + événements de démo (1 par statut temporel,
# intervenants, médias post-événement, modes inscription interne & externe)
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=EventSeeder
```

> ⚠️ La migration `add_pays_id_to_events` **corrige** une colonne manquante référencée par l'existant (R2). Sur une base déjà migrée, seules les nouvelles migrations s'appliquent.

## 3. Assets

```bash
npm run dev      # ou: npm run build
```
Vérifier les entrées `resources/css/events.css` et `resources/js/events.js` dans `vite.config.js`.

## 4. Vérification fonctionnelle (public)

1. **Listing** `/evenements` :
   - Bannière + cards groupées/filtrables **En cours / À venir / Clos**.
   - Filtres réactifs **statut / type / pays** + **tri** (proche/récent) → URL mise à jour, pagination réinitialisée.
   - Card : visuel, titre, date(s), lieu (ville ou « En ligne »), **badge statut**, courte description, **CTA contextuel** ; **compte à rebours** sur les à venir.
   - Un événement **présentiel publié** apparaît (validation R1) ; un **brouillon** n'apparaît pas.
2. **Détail** `/evenements/{slug}-{id}` :
   - Bannière + visuel, date/heure, lieu/format, description, **objectifs**, **programme**, **intervenants**, **public cible**.
   - **CTA dynamique** : « S'inscrire » (interne → modal/parcours ; externe → lien) ou « Voir replay / photos / compte rendu » (clos avec médias).
   - Sections vides masquées ; brouillon/slug inconnu → **404**.
3. **Inscription interne** : utilisateur connecté éligible → inscription enregistrée ; doublon refusé ; capacité/deadline respectées.

## 5. Vérification back-office (Filament)

1. Créer un événement complet (contenu détaillé + intervenants + médias) → **Publier**.
2. Vérifier le **badge statut temporel** (lecture seule) cohérent avec les dates.
3. `registration_mode='external'` → `registration_url` requis ; CTA public pointe sur le lien.
4. Ajouter un média **replay** sur un événement clos → CTA public bascule sur « Voir replay… ».
5. Le **type** (catégorie `type='event'`) et le **pays** sont sélectionnables et filtrables.

## 6. Tests

```bash
vendor/bin/phpunit --filter=Events           # suites Feature/Unit de la feature
vendor/bin/phpunit                            # suite complète (non-régression)
vendor/bin/pint                               # style
```

Points de vérification clés (mappés aux Success Criteria) :
- **SC-002/SC-004** : statut temporel exact selon les dates (à venir/en cours/clos), sans cron.
- **SC-006** : CTA « Voir replay… » présent dès qu'un média post-événement existe (card + détail).
- **SC-007** : filtres statut/type/pays ne renvoient que les événements correspondants.
- **Non-régression** : `events.index/show/category/calendar/search` → 200 ; **Lomé COM'TOUR** intact ; inscription interne inchangée.

## 7. Automatisation optionnelle (Lot 5)

```bash
php artisan events:mark-completed     # bascule éditoriale published→completed des événements clos
```
Ajouter au scheduler (quotidien). **Optionnel** : l'affichage du statut temporel reste dérivé des dates (R3).

## 8. Points d'attention (dettes résolues)

- **`online` = format** (en ligne/présentiel), **plus** un gate de visibilité ; visibilité = `status='published'` (R1). Vérifier qu'aucun événement publié légitime ne disparaît/n'apparaît à tort.
- **`pays_id`** désormais en base (R2) : filtre pays opérationnel, `EventResource` cohérent.
- **Visibilité (`scopePublished`)** : couvre `status ∈ {published, completed}` afin que les événements clos basculés par `events:mark-completed` (R8) restent visibles (replay/photos — SC-006). `draft` et `cancelled` restent exclus (FR-006).
