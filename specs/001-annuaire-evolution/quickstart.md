# Quickstart : Évolution de l'annuaire

## Prérequis

- Stack du projet déjà installée (`composer install`, `npm install`).
- Conteneurs Docker démarrés (`docker compose up -d`) OU `php artisan serve` en local + MySQL/SQLite accessible.
- Compte administrateur Filament déjà créé.

## Installation des nouvelles dépendances

```bash
composer require maatwebsite/excel:^3.1
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
```

## Application des migrations

```bash
php artisan migrate
# OU (uniquement les migrations de l'annuaire en dry-run)
php artisan migrate --pretend
```

Les migrations étant additives, elles peuvent être appliquées en production sans interruption.

## Seeders et migration des données existantes

```bash
php artisan db:seed --class=AnnuaireMigrationSeeder
```

Ce seeder :
- copie `online`/`aprouve` vers `etat_publication`.
- copie `bio` vers `bio_longue`.
- éclate `domaine` (string) en relations `domaines_expertise`.
- crée les `liens_externes` à partir de `facebook/twitter/youtube/linkding/site`.
- marque `legacy_sans_consentement = true` pour tous les profils existants.
- journalise `action = migration_initiale` dans `historique_profils`.

## Lancer l'application

```bash
php artisan serve            # back PHP
npm run dev                  # Vite (assets front)
```

Front public : <http://localhost:8000/annuaire>
Back-office :  <http://localhost:8000/admin/profils>

## Vérifications manuelles (golden path)

1. **Liste publique** : ouvrir `/annuaire`. Vérifier la grille de cartes par défaut, l'absence des profils `en_attente`, la pagination.
2. **Recherche** : taper « Sénégal » dans la barre de recherche → seuls les profils correspondants restent.
3. **Filtres** : appliquer Pays = `CI`, Type = `expert`, vérifier le résultat.
4. **Tri** : passer `tri=recent` dans l'URL → tri par date décroissante.
5. **Bascule grille/liste** : cliquer la bascule en haut à droite.
6. **Fiche détail** : cliquer un profil → vérifier que l'email/téléphone masqués ne sont pas dans la page (View source).
7. **Back-office** : créer un profil sans cocher le consentement → impossible de passer en `publié` (FR-026).
8. **Modération** : en tant qu'éditeur, soumettre un profil → en tant qu'admin, l'approuver → vérifier l'email envoyé à la personne.
9. **Lien retrait** : depuis l'email, cliquer « Demander retrait » → profil archivé, admins notifiés.
10. **Export CSV** : depuis le back-office, exporter → ouvrir directement dans Excel français/Windows → vérifier les accents (Côte d'Ivoire).
11. **Import CSV** : modifier le fichier exporté, réimporter → rapport correct, mises à jour appliquées.

## Lancer les tests

```bash
vendor/bin/phpunit --testsuite=Feature --filter=Annuaire
vendor/bin/phpunit --testsuite=Unit --filter=Annuaire
vendor/bin/phpunit --coverage-text --filter=Annuaire   # vérifier ≥ 80 %
```

## Variables d'environnement éventuelles

| Variable | Défaut | Description |
|---|---|---|
| `ANNUAIRE_PER_PAGE` | 24 | Pagination par défaut. |
| `ANNUAIRE_CONSENTEMENT_EXPIRY_DAYS` | 90 | Durée de vie du jeton de retrait. |
| `ANNUAIRE_RATE_LIMIT_PUBLIC` | 60 | Requêtes API publiques par minute / IP. |

À ajouter dans `.env.example`.

## Troubleshooting

- **Caractères accentués cassés dans Excel** : vérifier que l'export contient bien le BOM (`hexdump -C export.csv | head -1` → doit commencer par `ef bb bf`).
- **Recherche qui ne trouve pas un nom avec accent** : confirmer que les colonnes `*_normalise` sont remplies (mutateur Eloquent + seeder). Si vides, lancer `php artisan annuaire:renormaliser`.
- **Email de retrait non reçu** : vérifier la file `php artisan queue:work` et la config mail (`MAIL_*`).
