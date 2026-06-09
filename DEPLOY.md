# Déploiement (hébergement mutualisé sans Node.js)

## En local — avant chaque déploiement

```bash
# 1. Builder les assets front
npm run build

# 2. Committer et pousser (build inclus)
git add public/build
git commit -m "build: assets pour déploiement"
git push origin main
```

## Sur le serveur

```bash
# 1. Récupérer la dernière version
git fetch origin && git reset --hard origin/main

# 2. Dépendances PHP (sans les paquets de dev)
composer install --no-dev --optimize-autoloader

# 3. Base de données
php artisan migrate --force

# 4. Lien symbolique storage (à exécuter une seule fois après le 1er deploy)
php artisan storage:link

# 5. Caches Laravel (à régénérer à chaque deploy)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Redémarrer les queues si utilisées
php artisan queue:restart
```

## En cas de problème

```bash
# Vider tous les caches
php artisan optimize:clear

# Régénérer ensuite les caches (étape 5 ci-dessus)
```

## Notes

- `/public/build` est **committé** car l'hébergeur n'a pas Node.js.
- `/vendor` reste ignoré : `composer install` le régénère sur le serveur.
- `.env` n'est jamais committé : il doit être configuré manuellement sur le serveur.
- Ne pas builder directement sur `main` en équipe → utiliser une branche `deploy` pour éviter les conflits sur `public/build/`.
