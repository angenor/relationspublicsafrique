# Quickstart — Section Projets (vitrine)

Amorçage et vérification de la feature `003-projets-vitrine` en local. Prérequis : stack installée (cf. `CLAUDE.md`).

## 1. Branche & dépendances

```bash
git switch 003-projets-vitrine
composer install
npm install            # aucune nouvelle dépendance ajoutée par cette feature
```

## 2. Base de données

```bash
php artisan migrate           # applique les 7 migrations additives (projets, pivots, résultats, médias, témoignages, partenaires)
php artisan db:seed --class=ProjetSeeder   # ≥1 projet par statut + thématiques type='projet' + galerie/partenaires/chiffres/témoignages
```

> Le `ProjetSeeder` est **idempotent** et crée/garantit des catégories `type='projet'`. Comme `categories.id`/`pays.id` sont des `int` legacy sans AUTO_INCREMENT, le seeder résout les ids manuellement (cf. `MediaSeeder`).

## 3. Assets front

```bash
npm run dev      # ou: npm run build
```

Les entrées Vite `resources/css/projets.css` et `resources/js/projets.js` (lightbox galerie + init embed) sont déclarées dans `vite.config.js`.

## 4. Lancer l'application

```bash
php artisan serve
```

- **Listing public** : http://localhost:8000/projets
- **Détail** : cliquer « Découvrir » sur une card, ou http://localhost:8000/projets/{slug}
- **Back-office** : panneau Filament → groupe « Projets » → ressources *Projets* et *Partenaires*.

## 5. Parcours de vérification (mappés aux user stories)

### US1 — Listing & découverte (P1)
- [ ] `/projets` affiche une card par projet publié (nom, visuel, résumé, thématique, zone, badge statut).
- [ ] Les filtres **thématique**, **zone**, **statut** réduisent correctement la liste (l'URL reflète l'état).
- [ ] « Découvrir » mène à la page détaillée.
- [ ] Avec un filtre sans résultat → message d'état vide explicite.

### US2 — Détail d'un projet (P1)
- [ ] La page détail affiche, dans l'ordre : titre + visuel principal, contexte, objectifs, description, activités, chiffres clés, partenaires, galerie.
- [ ] Un projet **sans témoignages** masque entièrement la section témoignages (aucun bloc vide).
- [ ] La galerie affiche images (lightbox) et vidéos (embed YouTube/Vimeo).
- [ ] « Nous contacter » mène à `/contact?projet={slug}`.
- [ ] Un slug inconnu ou un brouillon → page 404.

### US3 — Back-office (P1)
- [ ] Créer un projet complet (tous champs + galerie + chiffres + partenaires + témoignages), le publier → visible sur `/projets` et sa page détail.
- [ ] Modifier le statut / le contenu → reflété côté public.
- [ ] Dépublier / supprimer → disparaît du listing et `show` → 404.
- [ ] Attacher un partenaire existant (table master) à plusieurs projets.

## 6. Tests automatisés

```bash
vendor/bin/phpunit --filter Projets        # tests/Feature/Projets/* + tests/Unit/Projets/*
vendor/bin/pint                            # style
```

Couverture cible : listing/filtres (SC-004), publication vs brouillon (SC-002), sections optionnelles masquées (SC-006), bouton contact (SC-007), **non-régression** de `/`, `/actualites`, `/media`.

## 7. Non-régression

- [ ] `/`, `/actualites`, `/media` et l'annuaire répondent 200 et sont inchangés.
- [ ] Aucune route/vue/modèle existant n'a été modifié hormis les points d'extension additifs (`routes/web.php`, `vite.config.js`, entrée nav « Projets »).
