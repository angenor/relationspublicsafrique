# Quickstart — Section Média (002-media-newsroom)

Guide d'amorçage pour développer et vérifier la feature. Stack : Laravel 10 / PHP 8.1+, Filament 3, Livewire 3, Vite + Bootstrap 5, MySQL (prod) / SQLite (tests).

## 1. Pré-requis & branche
```bash
git checkout 002-media-newsroom          # branche déjà créée
composer install
npm install
```

## 2. Nouvelle dépendance front (lecteur Plyr)
```bash
npm install plyr
```
- Ajouter une entrée Vite dédiée dans `vite.config.js` :
  ```js
  input: [
    "resources/sass/app.scss",
    "resources/css/app.css",
    "resources/css2/app.css",
    "resources/js/app.js",
    "resources/js2/app.js",
    "resources/css/media.css",   // + nouveau
    "resources/js/media.js",     // + nouveau (import 'plyr', init des lecteurs)
  ],
  ```
- Le layout `resources/views/layouts/media.blade.php` charge `@vite(['resources/css/media.css','resources/js/media.js'])` + `theme-variables.css` + `@livewireStyles/@livewireScripts`.

## 3. Configuration
- Créer `config/media.php` (miroir de `config/annuaire.php`) :
  ```php
  return [
      'per_page'          => (int) env('MEDIA_PER_PAGE', 12),
      'per_page_step'     => (int) env('MEDIA_PER_PAGE_STEP', 12),
      'a_la_une'          => (int) env('MEDIA_A_LA_UNE', 3),
      'rate_limit_public' => (int) env('MEDIA_RATE_LIMIT_PUBLIC', 30),
      'popularity_half_life_days' => (int) env('MEDIA_POP_HALFLIFE', 14),
  ];
  ```

## 4. Base de données
```bash
php artisan migrate                # crée media, media_series, category_media, media_tag, media_authors, media_comments, newsletter_subscribers
php artisan db:seed --class=MediaSeeder
```
- Migrations additives (aucune table existante modifiée). `titre_normalise` backfillé dans la migration (pattern `pays.name_normalise`).

## 5. Lancer
```bash
# Terminal 1 (PHP) — ou via Docker: docker compose up -d
php artisan serve
# Terminal 2 (front, sur l'hôte)
npm run dev
```
- Public : http://localhost:8000/media
- Admin Filament : http://localhost:8000/admin → groupe de navigation « Média »
- Emails (local) : MailHog http://localhost:8025

## 6. Publication programmée & tendances (scheduler)
```bash
# bascule scheduled→published à l'échéance + déclenche la newsletter
php artisan media:publish-scheduled
# recalcule popularity_score
php artisan media:recompute-popularity
# en conditions réelles : le cron exécute « php artisan schedule:run » chaque minute
```

## 7. File d'attente (newsletter)
- **Local** : `QUEUE_CONNECTION=sync` (défaut) → envoi immédiat, rien à lancer.
- **Prod** : `QUEUE_CONNECTION=database` →
  ```bash
  php artisan queue:table && php artisan migrate
  php artisan queue:work
  ```

## 8. Tests
```bash
vendor/bin/phpunit --filter=Media
vendor/bin/phpunit tests/Feature/Media tests/Unit/Media
vendor/bin/pint           # style
```
Couverture cible : parcours public (home/show/grille), recherche/filtres/tri, visibilité par statut, séries, commentaires+modération, newsletter (opt-in signé), **non-régression `/` et `/actualites`**.

## 9. Checklist de vérification manuelle (alignée Success Criteria)
- [x] `/media` affiche À la une (1-3), Dernières publications, Tendances, sélection éditoriale (SC-001).
- [x] Recherche + filtres (type/thématique/pays/auteur) + tri (récent/populaire/recommandé) ; URL partageable (SC-002, SC-010).
- [x] Article : chapô, corps, durée de lecture, bloc auteur(s), partage (LinkedIn/X/Facebook/email), similaires, préc/suiv.
- [x] Podcast : lecteur Plyr, durée, sous-titres, téléchargement (si activé) (SC-009).
- [x] Vidéo : embed YouTube/Vimeo responsive, durée (SC-009).
- [x] Série : épisodes ordonnés (saison/épisode), navigation.
- [x] Média `scheduled` invisible avant échéance, visible après `media:publish-scheduled` (SC-005).
- [x] Newsletter : opt-in confirmé, alerte à la publication, désabonnement (SC-006).
- [x] Commentaire soumis = masqué ; visible après approbation admin (SC-006).
- [x] Responsive mobile/tablette/desktop, sidebar repliable (SC-003).
- [x] `/` et `/actualites` strictement inchangés (SC-008).
