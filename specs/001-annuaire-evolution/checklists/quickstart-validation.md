# Quickstart Validation — Annuaire (T094)

Validation manuelle des 11 étapes du `quickstart.md`. Chaque étape doit être
**effectivement exécutée** dans un environnement de test (Docker local ou serveur
de pré-prod) puis cochée + commentée.

**Date de validation** : _à compléter au passage de la golden path_.
**Validateur** : _à compléter_.
**Environnement** : _Docker local / staging / prod ?_

---

## Pré-requis (à valider avant d'attaquer les 11 étapes)

- [ ] `composer install` exécuté sans erreur.
- [ ] `npm install` et `npm run dev` ou `npm run build` exécutés sans erreur.
- [ ] `php artisan migrate --pretend` n'affiche aucune erreur.
- [ ] `php artisan migrate` réussi (toutes les migrations `2026_05_10_*` jouées).
- [ ] `php artisan db:seed --class=AnnuaireMigrationSeeder` réussi et idempotent.
- [ ] `php artisan db:seed --class=DomainesExpertiseSeeder` réussi.
- [ ] Compte admin Filament fonctionnel.

---

## Les 11 étapes du golden path

### Étape 1 — Liste publique `/annuaire`

- [ ] Page chargée sans erreur 5xx.
- [ ] Grille de cartes affichée par défaut.
- [ ] Aucun profil `en_attente` ou `archivé` n'apparaît.
- [ ] Pagination présente si > 24 profils.

**Notes** :

---

### Étape 2 — Recherche libre

- [ ] Taper « Sénégal » → résultats filtrés.
- [ ] Taper « senegal » (sans accent) → mêmes résultats (insensibilité accents).
- [ ] Taper « SÉNÉGAL » → mêmes résultats (insensibilité casse).
- [ ] Vider le champ → liste complète rétablie.

**Notes** :

---

### Étape 3 — Filtres combinés

- [ ] Sélectionner Pays = `Côte d'Ivoire` → résultats restreints.
- [ ] Ajouter Type = `expert` → ET logique (intersection).
- [ ] Cliquer « Réinitialiser les filtres » → tous les profils reviennent.

**Notes** :

---

### Étape 4 — Tri

- [ ] Ajouter `?tri=recent` à l'URL → tri par date décroissante.
- [ ] Choisir `alpha` dans le select → tri alphabétique sur nom.
- [ ] Si une recherche libre est active, `pertinence` est appliqué par défaut.

**Notes** :

---

### Étape 5 — Bascule grille/liste

- [ ] Cliquer la bascule « Liste » en haut à droite → affichage en liste compacte.
- [ ] Cliquer « Grille » → retour aux cartes.
- [ ] Le mode est mémorisé dans l'URL (`?mode=liste`).

**Notes** :

---

### Étape 6 — Fiche détail + masquage coordonnées (SC-005)

- [ ] Cliquer sur un profil → page `/annuaire/{slug}` accessible.
- [ ] Choisir un profil dont `masquer_email = true` ET `masquer_tel = true`.
- [ ] Ouvrir « Afficher la source » du navigateur (Ctrl+U).
- [ ] **VÉRIFIER QU'AUCUN email/téléphone masqué n'apparaît dans le HTML brut.**
- [ ] Tester aussi `GET /api/annuaire/{slug}` — la réponse JSON ne doit PAS contenir
      `email`/`tel` si masqués.

**Notes** :

---

### Étape 7 — Création back-office sans consentement (FR-026)

- [ ] Se connecter en tant qu'admin sur `/admin/profils`.
- [ ] Créer un profil **sans cocher la case consentement RGPD**.
- [ ] Tenter de passer `etat_publication` à `publié` → action refusée / message d'erreur.

**Notes** :

---

### Étape 8 — Workflow de modération

- [ ] Se connecter en tant qu'éditeur, créer un profil → `etat_publication = en_attente`.
- [ ] Se connecter en tant qu'admin → voir le profil dans la liste filtrée « En attente ».
- [ ] Cliquer « Approuver » → profil passe en `publié`.
- [ ] Vérifier qu'un email `ProfilPubliePersonneNotification` est envoyé à la personne.
- [ ] Vérifier qu'une entrée `action=approuve` est créée dans `historique_profils`.

**Notes** :

---

### Étape 9 — Lien de retrait RGPD

- [ ] Depuis l'email reçu, cliquer le lien signé `/annuaire/retrait/{token}`.
- [ ] Choisir « Demander retrait définitif » → confirmation.
- [ ] Profil passe en `archivé`.
- [ ] Admins notifiés (vérifier la queue de mails).
- [ ] Vérifier qu'un lien expiré renvoie un message d'erreur clair.

**Notes** :

---

### Étape 10 — Export CSV

- [ ] Dans `/admin/profils`, cliquer « Exporter CSV ».
- [ ] Télécharger le fichier.
- [ ] Vérifier la présence du BOM : `hexdump -C export.csv | head -1` → premiers octets `ef bb bf`.
- [ ] Ouvrir directement dans Excel français/Windows (double-clic, pas via Power Query).
- [ ] Vérifier que les accents s'affichent correctement : « Côte d'Ivoire », « Sénégal », « éàêç ».
- [ ] Vérifier que les colonnes sont séparées par `;`.

**Notes** :

---

### Étape 11 — Import CSV

- [ ] Exporter 5 profils.
- [ ] Modifier 2 lignes (ex. changer la fonction).
- [ ] Ajouter 1 ligne sans email (devrait être créée).
- [ ] Ajouter 1 ligne invalide (nom manquant ou `type_profil` hors enum).
- [ ] Importer le fichier modifié.
- [ ] Vérifier le rapport : 2 mises à jour, 1 création, 1 erreur listée.
- [ ] Les 3 lignes valides sont bien importées.

**Notes** :

---

## Synthèse

- Total des étapes : **11**.
- Étapes validées : **__/11**.
- Bloquantes identifiées : _à compléter_.
- Date de fin de validation : _à compléter_.

## Critères d'acceptation pour clore le MVP

- 11/11 étapes cochées.
- Aucun bug bloquant remonté.
- SC-002, SC-004, SC-005, SC-008, SC-009, SC-010 vérifiés.
- Tests `vendor/bin/phpunit --filter=Annuaire` : verts.
- Couverture annuaire ≥ 80 % (T093).
