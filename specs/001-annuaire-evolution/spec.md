# Spécification fonctionnelle : Évolution de l'annuaire

**Branche** : `001-annuaire-evolution`
**Créé le** : 2026-05-10
**Statut** : Brouillon
**Entrée** : Description utilisateur : « Évolution de la rubrique annuaire avec enrichissement des champs profils, recherche multicritères, filtres, tri, et back-office complet avec modération, import/export et historique. »

## Clarifications

### Session 2026-05-10

- Q: Faut-il fusionner « état de publication » et « type de profil » dans un seul champ, ou les séparer ? → A: Deux champs séparés — `état_publication` (`en_attente`, `publié`, `archivé`) pour le workflow modération, et `type_profil` (`expert`, `étudiant`, `alumni`, `partenaire`, `autre`) pour la catégorie métier filtrable.
- Q: Comment traiter le modèle `Profil` déjà existant dans le projet ? → A: Étendre le modèle existant via migrations additives ; préservation des données et relations actuelles, pas de table parallèle.
- Q: Comment gérer le consentement RGPD des personnes référencées ? → A: Case d'attestation cochée par l'éditeur au moment de la création/édition + email automatique de notification à la personne avec lien de demande de rectification ou retrait ; date du consentement tracée.
- Q: Quel mode d'affichage par défaut pour l'annuaire public ? → A: Grille de cartes par défaut (photo, nom, fonction, organisation, pays) avec bascule utilisateur vers liste compacte.
- Q: Quel format CSV (délimiteur + encodage) pour l'import/export ? → A: UTF-8 avec BOM et délimiteur `;` (compatibilité Excel Windows français, accents préservés).

## Scénarios utilisateur et tests *(obligatoire)*

### User Story 1 — Consulter et rechercher des profils dans l'annuaire public (Priorité : P1)

Un visiteur du site arrive sur la page Annuaire et voit la liste des profils publiés. Il peut saisir un terme dans une barre de recherche pour trouver une personne par son nom, son organisation, son pays ou son domaine d'expertise. Les résultats s'affichent avec photo, nom, fonction et organisation.

**Pourquoi cette priorité** : C'est l'objet même de la rubrique. Sans consultation et recherche, l'annuaire n'a aucune valeur. C'est le MVP livrable.

**Test indépendant** : Avec un jeu de profils publiés en base, un visiteur non authentifié doit pouvoir ouvrir la page Annuaire, taper « Sénégal » dans la barre de recherche, et voir uniquement les profils correspondants.

**Scénarios d'acceptation** :

1. **Étant donné** une liste de profils publiés, **quand** le visiteur arrive sur la page Annuaire, **alors** il voit par défaut une **grille de cartes paginée** affichant photo, nom complet, fonction, organisation et pays, et il dispose d'un bouton de bascule pour basculer en **liste compacte**.
2. **Étant donné** la page Annuaire ouverte, **quand** le visiteur saisit un nom partiel dans la barre de recherche, **alors** la liste se met à jour pour ne montrer que les profils dont le nom, prénom, organisation ou domaine d'expertise contient le terme saisi.
3. **Étant donné** un profil dans la liste, **quand** le visiteur clique dessus, **alors** la fiche détaillée s'ouvre avec biographie, coordonnées (si non masquées), liens externes, tags, statut et localisation.
4. **Étant donné** un profil dont l'email ou le téléphone est marqué comme masqué, **quand** le visiteur consulte la fiche, **alors** ces champs n'apparaissent pas (ou sont remplacés par un formulaire de contact).

---

### User Story 2 — Filtrer et trier les profils selon plusieurs critères (Priorité : P1)

Un visiteur veut affiner sa recherche en combinant plusieurs filtres : pays/région, domaine d'expertise, type de profil (expert, étudiant, partenaire, alumni). Il peut également choisir un tri (alphabétique, plus récent, pertinence).

**Pourquoi cette priorité** : Avec un volume croissant de profils, la recherche libre ne suffit plus. Les filtres rendent l'annuaire exploitable.

**Test indépendant** : Avec au moins 20 profils variés en base, un visiteur peut sélectionner « Pays = Côte d'Ivoire » et « Type = Expert » et voir uniquement les profils correspondants, triés alphabétiquement.

**Scénarios d'acceptation** :

1. **Étant donné** la page Annuaire, **quand** le visiteur applique un filtre « Pays », **alors** seuls les profils localisés dans ce pays apparaissent.
2. **Étant donné** plusieurs filtres actifs (pays + domaine + statut), **quand** le visiteur les combine, **alors** les résultats respectent simultanément tous les critères (ET logique).
3. **Étant donné** des résultats filtrés, **quand** le visiteur change l'option de tri à « Plus récent », **alors** les profils sont réordonnés par date de création/publication décroissante.
4. **Étant donné** des filtres actifs, **quand** le visiteur clique sur « Réinitialiser », **alors** tous les filtres se vident et la liste complète revient.

---

### User Story 3 — Gérer les profils en back-office (Priorité : P1)

Un administrateur ou éditeur connecté au panneau d'administration peut créer, modifier et supprimer un profil. Il renseigne tous les champs (identité, biographie, photo, fonction, organisation, localisation, coordonnées avec option de masquage, liens externes, tags, statut). Il peut prévisualiser avant publication.

**Pourquoi cette priorité** : Sans gestion administrable des profils, le contenu de l'annuaire ne peut pas exister ni évoluer.

**Test indépendant** : Un administrateur se connecte au back-office, crée un nouveau profil avec photo et tous les champs requis, l'enregistre, puis vérifie qu'il apparaît dans l'annuaire public après publication.

**Scénarios d'acceptation** :

1. **Étant donné** un administrateur connecté, **quand** il ouvre la liste des profils en back-office, **alors** il voit tous les profils (publiés, en attente, archivés) avec actions d'édition et suppression.
2. **Étant donné** le formulaire de création, **quand** l'éditeur soumet un profil avec un champ obligatoire manquant (ex. nom), **alors** la validation échoue avec un message clair.
3. **Étant donné** un profil existant, **quand** l'administrateur modifie le statut (actif → alumni), **alors** le changement est immédiatement reflété sur l'annuaire public.
4. **Étant donné** un profil avec photo, **quand** l'éditeur téléverse une nouvelle image, **alors** elle est validée (format, taille, ratio) avant enregistrement.

---

### User Story 4 — Modération et validation des profils avant publication (Priorité : P2)

Un éditeur crée un profil et le soumet pour validation. Un administrateur reçoit la demande, prévisualise le profil, puis approuve ou rejette avec un commentaire. Tant qu'il n'est pas approuvé, le profil n'apparaît pas dans l'annuaire public.

**Pourquoi cette priorité** : La modération protège la qualité et l'image de l'annuaire, surtout si plusieurs éditeurs contribuent. Pas critique pour le MVP mais essentiel pour passer à l'échelle.

**Test indépendant** : Un éditeur crée un profil en statut « en attente » ; un administrateur l'approuve ; le profil apparaît côté public.

**Scénarios d'acceptation** :

1. **Étant donné** un éditeur connecté, **quand** il crée un profil, **alors** celui-ci passe au statut « En attente de validation » par défaut (non visible côté public).
2. **Étant donné** un profil en attente, **quand** un administrateur l'approuve, **alors** le statut passe à « Publié » et le profil devient visible publiquement.
3. **Étant donné** un profil en attente, **quand** un administrateur le rejette avec un motif, **alors** l'éditeur reçoit une notification avec le motif, et peut modifier puis re-soumettre.

---

### User Story 5 — Import/export en masse (CSV/Excel) (Priorité : P2)

Un administrateur peut exporter l'annuaire complet (ou filtré) au format CSV ou Excel. Il peut aussi importer un fichier CSV/Excel pour créer ou mettre à jour des profils en lot.

**Pourquoi cette priorité** : Indispensable pour la migration de données existantes et la maintenance, mais non bloquant pour l'usage quotidien.

**Test indépendant** : Un administrateur exporte 50 profils en Excel, modifie 5 lignes dans le fichier, le réimporte, et constate que les 5 profils sont mis à jour sans doublons.

**Scénarios d'acceptation** :

1. **Étant donné** la liste back-office, **quand** l'administrateur clique « Exporter CSV », **alors** un fichier contenant tous les profils visibles avec leurs champs est téléchargé.
2. **Étant donné** un fichier CSV valide, **quand** l'administrateur l'importe, **alors** chaque ligne crée un nouveau profil ou met à jour un existant (clé d'unicité : email).
3. **Étant donné** un fichier d'import avec des erreurs (champ manquant, format invalide), **quand** l'import est lancé, **alors** un rapport listant les lignes en erreur s'affiche, et les lignes valides sont quand même importées.

---

### User Story 6 — Historique des modifications d'un profil (Priorité : P3)

Un administrateur consulte l'historique des changements d'un profil : qui a modifié quoi et quand. Il peut comparer deux versions ou restaurer une version antérieure.

**Pourquoi cette priorité** : Utile pour la traçabilité et l'audit, mais ce n'est pas nécessaire pour les premières versions opérationnelles.

**Test indépendant** : Un éditeur modifie un profil deux fois ; un administrateur ouvre l'historique et voit les deux modifications datées et attribuées.

**Scénarios d'acceptation** :

1. **Étant donné** un profil modifié plusieurs fois, **quand** l'administrateur ouvre l'historique, **alors** il voit la liste chronologique des modifications avec auteur, date et champs modifiés.
2. **Étant donné** une version antérieure, **quand** l'administrateur la restaure, **alors** le profil reprend son état précédent et l'action est elle-même enregistrée dans l'historique.

---

### Cas limites

- Profil sans photo : afficher un avatar par défaut sur la liste et la fiche.
- Photo téléversée hors format/taille/ratio : refus avec message clair et indication des contraintes.
- Recherche sans résultat : afficher un message « Aucun profil ne correspond » et un bouton « Réinitialiser les filtres ».
- Profil supprimé : retirer immédiatement de l'annuaire public ; conserver une trace dans l'historique pour audit.
- Coordonnées masquées : ne jamais les transmettre côté client (pas seulement masquage CSS).
- Liste de plus de 1000 profils : pagination obligatoire pour préserver les performances.
- Importation avec doublons (même email) : mettre à jour le profil existant plutôt que de créer un doublon.
- Caractères spéciaux/diacritiques dans la recherche (« Côte d'Ivoire ») : la recherche doit être insensible aux accents et à la casse.

## Exigences *(obligatoire)*

### Exigences fonctionnelles

**Champs des profils :**

- **FR-001** : Le système DOIT stocker pour chaque profil les champs suivants : nom, prénom, nationalité, biographie longue, biographie courte (limitée à 280 caractères), photo, fonction/titre, organisation, ville, pays, domaines d'expertise (multiples), email, téléphone, indicateurs de masquage email/téléphone, liens externes (LinkedIn, site web, portfolio), tags, `type_profil`, `état_publication`, date de création, date de mise à jour.
- **FR-002** : Le système DOIT permettre plusieurs domaines d'expertise par profil (relation N-N avec un référentiel de domaines).
- **FR-003** : Le système DOIT permettre plusieurs tags libres par profil.
- **FR-004** : Le système DOIT valider la photo selon les contraintes suivantes : formats JPG/PNG/WebP, taille max 2 Mo, ratio carré recommandé (1:1) avec recadrage automatique si nécessaire, résolution minimale 400×400 px.
- **FR-005a** : L'`état_publication` d'un profil DOIT être l'une des valeurs : `en_attente`, `publié`, `archivé`. Il pilote exclusivement la visibilité publique et le workflow de modération.
- **FR-005b** : Le `type_profil` DOIT être l'une des valeurs : `expert`, `étudiant`, `alumni`, `partenaire`, `autre`. Il est indépendant de l'`état_publication` : modifier le type ne déclenche pas un nouveau cycle de modération.
- **FR-006** : Le système DOIT permettre de masquer indépendamment l'email et le téléphone côté public ; un champ masqué ne DOIT pas être transmis dans la réponse publique.

**Annuaire public :**

- **FR-007** : Les utilisateurs DOIVENT pouvoir consulter la liste des profils dont l'`état_publication` est `publié`, indépendamment de leur `type_profil`.
- **FR-008** : Les utilisateurs DOIVENT pouvoir rechercher en texte libre sur les champs : nom, prénom, organisation, domaine d'expertise, tags, ville, pays.
- **FR-009** : La recherche DOIT être insensible à la casse et aux accents.
- **FR-010** : Les utilisateurs DOIVENT pouvoir appliquer simultanément plusieurs filtres : pays, domaine d'expertise, `type_profil` (expert / étudiant / alumni / partenaire / autre), tags.
- **FR-011** : Les utilisateurs DOIVENT pouvoir trier les résultats par : ordre alphabétique (nom), date de création décroissante, pertinence (lorsqu'une recherche texte est active).
- **FR-012** : Le système DOIT paginer la liste avec un nombre configurable de résultats par page (défaut : 24).
- **FR-012a** : L'affichage par défaut DOIT être une **grille de cartes** (photo, nom, fonction, organisation, pays). Une bascule visible et persistante (préférence utilisateur conservée le temps de la session) DOIT permettre de passer en **liste compacte**.
- **FR-013** : Chaque profil DOIT être consultable sur une fiche détaillée dédiée et indexable par les moteurs de recherche (URL canonique stable).

**Back-office :**

- **FR-014** : Les administrateurs et éditeurs authentifiés DOIVENT pouvoir créer, modifier et supprimer (suppression logique) un profil.
- **FR-015** : Le système DOIT proposer un workflow de modération sur l'`état_publication` : un profil créé par un éditeur passe en `en_attente` ; un administrateur l'approuve (`publié`) ou le rejette avec motif (retour en `en_attente` avec commentaire). Une modification du `type_profil` ou des autres attributs métier ne déclenche PAS de nouvelle modération.
- **FR-016** : Le système DOIT distinguer au minimum deux rôles : `admin` (toutes actions, y compris validation et suppression définitive) et `éditeur` (création/modification de ses propres profils, soumission pour validation).
- **FR-017** : Le système DOIT enregistrer un historique horodaté et nominatif pour chaque création, modification, changement de statut, suppression et restauration de profil.
- **FR-018** : Le système DOIT permettre d'exporter en CSV et XLSX la liste des profils, en respectant les filtres actifs. Le CSV exporté DOIT utiliser l'encodage **UTF-8 avec BOM** et le délimiteur **point-virgule (`;`)** afin d'être directement exploitable par Excel sur Windows en environnement francophone, sans casser les caractères accentués.
- **FR-019** : Le système DOIT permettre d'importer des profils depuis un fichier CSV ou XLSX, avec détection des doublons sur la clé email et choix « créer / mettre à jour / ignorer ». L'import CSV DOIT accepter par défaut l'encodage UTF-8 (avec ou sans BOM) et le délimiteur `;` ; il DOIT tolérer également UTF-8 + `,` (RFC 4180) en détectant le séparateur automatiquement, et signaler explicitement les fichiers à l'encodage non reconnu.
- **FR-020** : Le système DOIT générer un rapport d'import listant les lignes acceptées, mises à jour et en erreur (avec motif).
- **FR-021** : Le back-office DOIT permettre de prévisualiser un profil tel qu'il apparaîtra publiquement avant publication.

**Sécurité & confidentialité :**

- **FR-022** : Les coordonnées masquées NE DOIVENT JAMAIS être incluses dans les réponses publiques (HTML rendu, API, exports publics).
- **FR-023** : Seuls les utilisateurs avec rôle `admin` ou `éditeur` connectés DOIVENT pouvoir accéder au back-office de l'annuaire.
- **FR-024** : Toute action de modification ou suppression DOIT être journalisée avec l'utilisateur, la date et l'IP.
- **FR-025** : Lors de la création ou modification d'un profil, l'éditeur DOIT cocher une case attestant que la personne référencée a donné son consentement pour être publiée dans l'annuaire avec les coordonnées renseignées. La date et l'auteur du consentement DOIVENT être enregistrés sur le profil.
- **FR-026** : Un profil ne PEUT PAS passer en `état_publication = publié` tant que l'attestation de consentement n'a pas été enregistrée.
- **FR-027** : Lors de la première publication d'un profil, le système DOIT envoyer automatiquement un email à l'adresse renseignée (si présente) informant la personne de sa publication dans l'annuaire et lui proposant un lien sécurisé (jeton unique, à durée limitée) pour : (a) consulter les données stockées la concernant, (b) demander une rectification, (c) demander le retrait du profil.
- **FR-028** : Une demande de retrait reçue via le lien DOIT mettre le profil en `état_publication = archivé` automatiquement, notifier les administrateurs, et être tracée dans l'historique.
- **FR-029** : Les administrateurs DOIVENT pouvoir consulter l'historique des consentements (qui a attesté, à quelle date, pour quel profil) à fins d'audit.

### Entités clés

- **Profil** : représente une personne référencée. Attributs principaux : nom, prénom, nationalité, biographies (courte/longue), photo, fonction, organisation, ville, pays, email, téléphone, drapeaux de masquage, `type_profil`, `état_publication`, `consentement_attesté_par`, `consentement_date`, `consentement_jeton_retrait` (jeton à durée limitée pour le lien envoyé par email), dates de création/mise à jour. Relations : N-N avec `DomaineExpertise`, N-N avec `Tag`, 1-N avec `LienExterne`, N-1 avec `Pays`, N-1 avec un utilisateur créateur/responsable.
- **DomaineExpertise** : référentiel de catégories d'expertise (libellé, slug). Utilisé pour le filtrage.
- **Tag** : mot-clé libre attaché à un profil. Utilisé pour le filtrage transverse.
- **LienExterne** : couple (type, URL) avec types prédéfinis (LinkedIn, site web, portfolio, autre).
- **Pays** : référentiel des pays (code ISO, nom). Existe déjà dans le projet (modèle `Pays`).
- **HistoriqueProfil** : entrée d'audit (profil concerné, utilisateur, action, valeurs avant/après, date).
- **DemandeModeration** : associe un profil à une décision (en attente / approuvé / rejeté), un modérateur et un motif éventuel.

## Critères de succès *(obligatoire)*

### Résultats mesurables

- **SC-001** : Un visiteur trouve un profil pertinent (nom ou domaine) en moins de 10 secondes depuis l'arrivée sur la page Annuaire (mesure : temps moyen recherche → clic sur fiche).
- **SC-002** : 95 % des recherches retournent leurs résultats en moins de 1 seconde, pour un volume jusqu'à 5 000 profils.
- **SC-003** : Un éditeur crée un nouveau profil complet (avec photo et tous les champs) en moins de 5 minutes en back-office.
- **SC-004** : Un administrateur importe un fichier de 500 profils en moins de 2 minutes avec un rapport d'erreurs clair.
- **SC-005** : 0 fuite de coordonnées masquées : aucune réponse publique ne contient un email ou téléphone marqué « masqué » (vérifiable par test automatisé).
- **SC-006** : 100 % des modifications de profil sont traçables dans l'historique (auteur + date + champs modifiés).
- **SC-007** : Aucun profil dont l'`état_publication` est `en_attente` ou `archivé` n'apparaît dans l'annuaire public (vérifié par test), quel que soit son `type_profil`.
- **SC-008** : La combinaison « recherche + 3 filtres + tri » produit un résultat correct et reproductible en moins de 1,5 seconde.
- **SC-009** : 100 % des profils publiés disposent d'une attestation de consentement traçable (auteur + date) ; aucun profil sans consentement enregistré n'est en `état_publication = publié` (vérifié par test automatisé).
- **SC-010** : Une demande de retrait soumise via le lien email passe le profil en `archivé` en moins de 5 secondes et notifie les administrateurs.

## Hypothèses

- L'annuaire évolue à partir des modèles `Profil` et `Pays` existants ; les nouveaux champs et entités viennent en extension (migration additive sur la table `Profil` actuelle, sans création de table parallèle).
- Les profils déjà présents en base sont conservés et reçoivent par défaut `état_publication = publié` (s'ils étaient déjà visibles) et `type_profil = autre` (réajustable a posteriori) ; aucune perte de données ni de relations existantes.
- L'authentification du back-office réutilise le système Filament 3 / Laravel existant et ses rôles ; aucune refonte d'authentification n'est requise.
- Les éditeurs et administrateurs sont des utilisateurs internes ; l'auto-inscription publique pour créer un profil n'est pas dans le périmètre de cette évolution.
- Le volume cible à 12 mois est de l'ordre de 5 000 profils ; au-delà, un index de recherche dédié pourra être envisagé en évolution ultérieure.
- Les exports CSV/XLSX sont destinés à des administrateurs : ils peuvent contenir les coordonnées non masquées (les masquages s'appliquent uniquement côté annuaire public).
- L'historique des modifications conserve toutes les versions pendant au moins 24 mois.
- La page publique Annuaire réutilise la stratégie i18n déjà en place dans le projet ; la traduction multilingue des biographies n'est pas dans le périmètre.
- L'historique capture un diff par champ ; la fonctionnalité « restaurer une version » (User Story 6) est nice-to-have et peut être livrée après l'historique en lecture seule.

## Dépendances

- Système d'authentification et de rôles existant (Filament/Laravel).
- Modèle `Pays` existant pour la liste des pays.
- Pipeline de téléversement et de redimensionnement d'images (Intervention Image déjà disponible).
- Bibliothèque d'export Excel (à choisir au stade de la planification).
