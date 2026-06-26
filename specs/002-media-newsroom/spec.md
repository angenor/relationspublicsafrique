# Feature Specification: Section Média (newsroom / magazine)

**Feature Branch**: `002-media-newsroom`
**Created**: 2026-06-25
**Status**: Draft
**Input**: User description: "Nouvelle section « Média » : un blog/newsroom/magazine inspiré de brut.media (design minimaliste, menu latéral à gauche), accessible publiquement sur `/media`. Ce n'est PAS une refonte de la plateforme — la racine `/` et le blog `/actualites` restent inchangés. Types de contenu : article, interview, podcast (audio), vidéo (Com'Addict), reportage/dossier. Mise en avant éditoriale, recherche/filtres, pages détail SEO avec lecteurs audio/vidéo, séries/playlists, partage social, commentaires et newsletter."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Publier et organiser les contenus média (back-office éditorial) (Priority: P1)

Un membre de l'équipe éditoriale se connecte au back-office et crée un contenu média en choisissant son type (article, interview, podcast, vidéo, reportage). Il renseigne le titre, un chapô éditorial, le corps de contenu riche, une image de couverture, la ou les catégories, des mots-clés, le pays/zone concerné, le ou les auteurs et intervenants. Selon le type, il rattache un fichier audio (podcast) ou une URL vidéo externe (vidéo). Il peut mettre le contenu « à la une », l'épingler, l'attacher à une série, programmer sa publication à une date future, ou l'archiver.

**Why this priority**: Sans capacité de production éditoriale, la section n'a aucun contenu à valoriser. C'est l'activateur (enabler) de toute la feature : tout le reste consomme les contenus créés ici.

**Independent Test**: Peut être testé en créant, depuis le back-office, un contenu de chaque type avec tous ses attributs, puis en vérifiant qu'il est correctement enregistré, programmé/publié selon son statut, et invisible tant qu'il n'est pas publié.

**Acceptance Scenarios**:

1. **Given** un éditeur authentifié, **When** il crée un article avec titre, chapô, corps, couverture, catégorie et auteur puis le publie, **Then** le contenu devient accessible publiquement avec une URL unique et apparaît dans la section Média.
2. **Given** un éditeur créant un podcast, **When** il téléverse un fichier audio, renseigne la durée et (optionnellement) un fichier de sous-titres et autorise le téléchargement, **Then** le podcast est lisible en ligne avec ces options.
3. **Given** un éditeur créant une vidéo, **When** il colle une URL YouTube/Vimeo, **Then** la vidéo s'affiche intégrée sur la page de détail.
4. **Given** un contenu avec une date de publication future, **When** cette date arrive, **Then** le contenu passe automatiquement de « programmé » à « publié » et devient visible.
5. **Given** un contenu en statut brouillon, programmé ou archivé, **When** un visiteur tente d'y accéder, **Then** il n'est pas visible publiquement.
6. **Given** un éditeur, **When** il marque un contenu « à la une » et/ou « épinglé » avec une position, **Then** ce contenu remonte dans les zones de mise en avant correspondantes de la section.

---

### User Story 2 - Découvrir et lire les contenus sur /media (Priority: P1)

Un visiteur arrive sur `/media` et découvre une page d'accueil de section au design minimaliste avec un menu latéral à gauche. Il voit une zone « À la une » (1 à 3 contenus principaux), une rubrique « Dernières publications », une rubrique « Tendances / les plus lus » et une sélection éditoriale (ex. « Focus communicants africains »), ainsi qu'une grille de contenus organisée par catégories/types. Il clique sur un contenu et accède à une page de détail aérée avec le corps de contenu, le bloc auteur(s), des boutons de partage, des contenus similaires/recommandés et une navigation vers les contenus précédent/suivant.

**Why this priority**: C'est l'expérience publique cœur de la feature et le MVP visible : valoriser et faire lire les contenus.

**Independent Test**: Peut être testé en visitant `/media` avec des contenus publiés et en vérifiant la présence et l'exactitude des zones de mise en avant, la navigation vers une page de détail, et la présence du bloc auteur, du partage, des similaires et de la navigation précédent/suivant.

**Acceptance Scenarios**:

1. **Given** des contenus publiés dont certains « à la une », **When** un visiteur ouvre `/media`, **Then** la zone « À la une » présente 1 à 3 contenus mis en avant, et les rubriques « Dernières publications » et « Tendances / les plus lus » affichent les contenus attendus.
2. **Given** un visiteur sur la grille, **When** il fait défiler ou demande « Charger plus », **Then** des contenus supplémentaires se chargent sans recharger la page.
3. **Given** un article publié, **When** un visiteur ouvre sa page de détail, **Then** il voit le titre, le chapô, le corps riche, la durée estimée de lecture, le ou les auteurs (bio courte + lien vers le profil), la date de publication, la catégorie et les mots-clés.
4. **Given** une page de détail, **When** le visiteur utilise un bouton de partage, **Then** le contenu peut être partagé vers LinkedIn, X, Facebook ou par email.
5. **Given** une page de détail, **Then** le visiteur voit des contenus similaires/recommandés (même type/catégorie/mots-clés) et peut naviguer vers le contenu précédent/suivant.
6. **Given** un appareil mobile, tablette ou ordinateur, **When** un visiteur consulte la section, **Then** la mise en page reste lisible et utilisable (responsive), le menu latéral s'adaptant aux petits écrans.

---

### User Story 3 - Rechercher, filtrer et trier les contenus (Priority: P2)

Un visiteur souhaite retrouver un contenu précis ou explorer une thématique. Il utilise une barre de recherche (par titre, mot-clé ou auteur) et des filtres : type de contenu (article, interview, podcast, vidéo, reportage), thématique (catégorie), pays/zone géographique, auteur. Il peut trier les résultats par récence, popularité ou recommandation. Les résultats se mettent à jour de façon réactive et sont paginés.

**Why this priority**: Améliore fortement la découvrabilité dès qu'un volume de contenus existe, mais la section reste consultable sans elle (via la grille et les rubriques).

**Independent Test**: Peut être testé en saisissant une recherche et en appliquant chaque filtre et chaque tri, puis en vérifiant que l'ensemble de résultats correspond aux critères et que la pagination fonctionne.

**Acceptance Scenarios**:

1. **Given** un corpus de contenus publiés, **When** le visiteur saisit un terme dans la barre de recherche, **Then** seuls les contenus dont le titre, les mots-clés ou l'auteur correspondent sont affichés.
2. **Given** des filtres disponibles, **When** le visiteur sélectionne un type et/ou une thématique et/ou un pays et/ou un auteur, **Then** la liste se restreint aux contenus correspondant à tous les critères combinés.
3. **Given** une liste filtrée, **When** le visiteur change le tri (récent / populaire / recommandé), **Then** l'ordre des résultats reflète le critère choisi.
4. **Given** des filtres actifs, **When** le visiteur réinitialise les filtres, **Then** la liste complète des contenus publiés réapparaît.
5. **Given** une recherche sans résultat, **Then** un état vide explicite est présenté au visiteur.

---

### User Story 4 - Écouter les podcasts et regarder les vidéos (Priority: P2)

Un visiteur écoute un podcast directement dans la page grâce à un lecteur audio, voit la durée, peut activer des sous-titres et, si l'option est autorisée, télécharger l'audio. Pour les vidéos, il regarde le contenu intégré (lecteur vidéo / iframe externe). Lorsqu'un contenu appartient à une série/playlist (émission récurrente), il voit les autres épisodes classés par saison/épisode et peut passer de l'un à l'autre.

**Why this priority**: Différencie la section d'un blog classique et porte les rubriques Podcasts et Com'Addict, mais s'appuie sur le socle de lecture (US2) déjà livré.

**Independent Test**: Peut être testé en ouvrant un podcast (lecture, durée, sous-titres, téléchargement) et une vidéo (lecture intégrée), puis en parcourant une série multi-épisodes.

**Acceptance Scenarios**:

1. **Given** un podcast publié, **When** le visiteur ouvre sa page, **Then** un lecteur audio permet la lecture/pause, affiche la durée, et propose les sous-titres et le téléchargement si activés.
2. **Given** une vidéo publiée avec une URL externe, **When** le visiteur ouvre sa page, **Then** la vidéo est lisible en intégration directe et sa durée est indiquée.
3. **Given** un contenu rattaché à une série, **When** le visiteur consulte sa page, **Then** il voit la liste ordonnée des épisodes (saison/épisode) et peut naviguer vers un autre épisode.
4. **Given** une vidéo ou un podcast avec sous-titres, **When** le visiteur active les sous-titres, **Then** ceux-ci s'affichent en synchronisation.

---

### User Story 5 - S'abonner à la newsletter et être alerté des nouvelles publications (Priority: P3)

Un visiteur intéressé saisit son adresse email pour s'abonner aux nouvelles publications de la section Média. Il confirme son abonnement et reçoit ensuite une alerte lors de la publication de nouveaux contenus. Il peut se désabonner à tout moment.

**Why this priority**: Augmente la rétention et la diffusion, mais n'est pas requise pour consulter la section.

**Independent Test**: Peut être testé en s'abonnant avec une adresse email, en confirmant l'abonnement, en publiant un contenu, puis en vérifiant l'émission d'une alerte et la possibilité de se désabonner.

**Acceptance Scenarios**:

1. **Given** un visiteur, **When** il saisit une adresse email valide et s'abonne, **Then** son abonnement est enregistré et une confirmation lui est demandée/envoyée.
2. **Given** un abonné confirmé, **When** un nouveau contenu est publié, **Then** une alerte lui est envoyée.
3. **Given** un abonné, **When** il utilise le lien de désabonnement, **Then** il ne reçoit plus d'alertes.
4. **Given** une adresse déjà abonnée, **When** elle tente de s'abonner à nouveau, **Then** aucun doublon n'est créé.

---

### User Story 6 - Commenter les contenus avec modération (Priority: P3)

Un visiteur laisse un commentaire sur une page de détail. Le commentaire n'apparaît publiquement qu'après validation par un modérateur depuis le back-office. Le modérateur peut approuver, rejeter ou supprimer les commentaires.

**Why this priority**: Favorise l'engagement, mais optionnelle pour la consultation et la diffusion du contenu.

**Independent Test**: Peut être testé en soumettant un commentaire (qui reste masqué), puis en l'approuvant depuis le back-office (il devient visible) et en le rejetant/supprimant (il disparaît).

**Acceptance Scenarios**:

1. **Given** une page de détail, **When** un visiteur soumet un commentaire, **Then** il est enregistré en attente de modération et n'est pas affiché publiquement.
2. **Given** un commentaire en attente, **When** un modérateur l'approuve, **Then** il devient visible sous le contenu.
3. **Given** un commentaire indésirable, **When** un modérateur le rejette ou le supprime, **Then** il n'apparaît pas publiquement.

---

### Edge Cases

- **Contenu programmé** : un contenu dont la date de publication est future ne doit jamais être visible ni indexable avant l'échéance.
- **Vidéo externe indisponible** (URL invalide, vidéo supprimée côté plateforme) : la page reste consultable et signale gracieusement l'indisponibilité du média.
- **Fichier audio manquant ou corrompu** : le lecteur affiche un état d'erreur lisible sans casser la page.
- **Contenu sans image de couverture** : une couverture/visuel par défaut est utilisée pour préserver la cohérence de la grille.
- **Recherche/filtres sans résultat** : affichage d'un état vide explicite avec possibilité de réinitialiser.
- **Auteur sans profil public** : le bloc auteur s'affiche sans lien de profil cassé.
- **Série à un seul épisode** : la navigation entre épisodes reste cohérente (pas de lien précédent/suivant inopérant).
- **Égalité de popularité** : un critère secondaire déterministe (ex. récence) départage le tri « tendances ».
- **Soumission massive/abusive de commentaires ou d'abonnements** : protection contre le spam et les doublons.
- **Non-régression** : aucune modification du comportement de la racine `/` ni du blog `/actualites`.

## Requirements *(mandatory)*

### Functional Requirements

#### Contenu et types

- **FR-001**: Le système MUST permettre de gérer des contenus média de cinq types : article, interview, podcast, vidéo, reportage/dossier.
- **FR-002**: Chaque contenu MUST porter au minimum : un titre, une URL unique optimisée pour le référencement, un chapô éditorial saisi et stocké de façon autonome (distinct du corps), un corps de contenu riche (texte, images, intégrations), une image de couverture, un type, au moins une catégorie, une date de publication.
- **FR-003**: Chaque contenu MUST pouvoir porter des mots-clés, un pays/zone géographique, un ou plusieurs auteurs/intervenants avec un rôle (auteur, interviewer, invité), et des métadonnées de partage social (titre, description, visuel de partage).
- **FR-004**: Le système MUST gérer un cycle de vie de publication à plusieurs états : brouillon, programmé, publié, archivé ; et MUST publier automatiquement un contenu programmé à l'échéance de sa date de publication.
- **FR-005**: Le système MUST masquer au public tout contenu non publié (brouillon, programmé, archivé).
- **FR-006**: Le système MUST permettre de marquer un contenu « à la une » (featured) et de l'« épingler » avec une position d'ordre.
- **FR-007**: Le système MUST afficher une durée estimée : temps de lecture pour les articles/interviews/reportages, durée d'écoute/visionnage pour les podcasts/vidéos.

#### Page d'accueil de la section et mise en avant éditoriale

- **FR-008**: La section MUST être accessible publiquement sur `/media` avec une page d'accueil de section présentant une grille de contenus responsive.
- **FR-009**: La page d'accueil de section MUST présenter une zone « À la une » de 1 à 3 contenus principaux, une rubrique « Dernières publications », une rubrique « Tendances / les plus lus » et une sélection éditoriale paramétrable.
- **FR-010**: La section MUST proposer une navigation par type et par thématique (catégorie) via un menu latéral gauche au design minimaliste, adapté aux écrans mobiles.
- **FR-011**: La grille MUST permettre de charger davantage de contenus sans rechargement complet de la page (pagination progressive / « Charger plus »).
- **FR-012**: Le système MUST permettre de paramétrer la sélection éditoriale mise en avant (ex. « Focus communicants africains »).

#### Page de détail

- **FR-013**: Chaque contenu MUST disposer d'une page de détail avec une URL unique stable et des métadonnées de référencement et de partage propres.
- **FR-014**: La page de détail MUST présenter le titre, le chapô, le corps, la durée estimée, la date, la/les catégorie(s), les mots-clés et un bloc auteur(s) (bio courte + lien vers le profil public quand il existe).
- **FR-015**: La page de détail MUST offrir des boutons de partage vers LinkedIn, X, Facebook et email.
- **FR-016**: La page de détail MUST proposer des contenus similaires/recommandés (même type, catégorie ou mots-clés) et une navigation vers le contenu précédent/suivant.
- **FR-017**: La page de détail MUST inclure des appels à l'action : s'abonner à la newsletter, partager, et commenter (lorsque les commentaires sont actifs).

#### Recherche, filtrage et tri

- **FR-018**: Le système MUST fournir une recherche par titre, mot-clé et auteur.
- **FR-019**: Le système MUST fournir des filtres par type de contenu, thématique (catégorie), pays/zone et auteur, combinables entre eux.
- **FR-020**: Le système MUST fournir un tri par récence, popularité et recommandation.
- **FR-021**: Le système MUST permettre la réinitialisation des filtres et présenter un état vide explicite en l'absence de résultat.
- **FR-022**: Le système MUST conserver l'état de recherche/filtre dans l'URL afin de rendre une vue filtrée partageable et rechargeable.

#### Podcasts, vidéos et séries

- **FR-023**: Le système MUST lire les podcasts via un lecteur audio intégré (lecture/pause, progression, durée) directement dans la page.
- **FR-024**: Le système MUST permettre l'activation de sous-titres pour les podcasts et les vidéos lorsqu'un fichier de sous-titres est fourni.
- **FR-025**: Le système MUST permettre le téléchargement de l'audio d'un podcast lorsque l'option est activée par l'éditeur.
- **FR-026**: Le système MUST afficher les vidéos en intégration directe à partir d'une URL de plateforme externe (YouTube/Vimeo).
- **FR-027**: Le système MUST permettre de regrouper des podcasts/vidéos en séries/playlists, classées par saison et épisode, et de naviguer entre épisodes.

#### Back-office éditorial

- **FR-028**: Le système MUST offrir un back-office permettant la création, l'édition, la suppression et la publication des contenus de tous les types, avec des champs adaptés au type sélectionné (audio pour podcast, URL externe pour vidéo, etc.).
- **FR-029**: Le back-office MUST permettre de gérer les séries/playlists et l'ordre des épisodes qui les composent.
- **FR-030**: Le back-office MUST permettre de gérer les catégories/thématiques, les mots-clés, le pays/zone, les auteurs/intervenants et leurs rôles, la mise en avant, l'épinglage, la programmation et les métadonnées de référencement de chaque contenu.
- **FR-031**: Le back-office MUST permettre la modération des commentaires (approuver, rejeter, supprimer).

#### Commentaires

- **FR-032**: Le système MUST permettre aux visiteurs de soumettre des commentaires sur les pages de détail.
- **FR-033**: Le système MUST n'afficher publiquement un commentaire qu'après approbation par un modérateur.
- **FR-034**: Le système MUST se prémunir contre les soumissions abusives/spam de commentaires.

#### Newsletter / abonnement

- **FR-035**: Le système MUST permettre à un visiteur de s'abonner aux nouvelles publications via son adresse email, avec une étape de confirmation et sans créer de doublon.
- **FR-036**: Le système MUST alerter les abonnés confirmés lors de la publication de nouveaux contenus.
- **FR-037**: Le système MUST permettre le désabonnement à tout moment.

#### Popularité et recommandation

- **FR-038**: Le système MUST comptabiliser les consultations de chaque contenu afin d'alimenter le tri « populaire / tendances ».
- **FR-039**: Le système MUST produire un classement « recommandé » combinant mise en avant éditoriale et popularité.

#### Intégration, navigation et non-régression

- **FR-040**: Le système MUST ajouter une entrée « Média » à la navigation existante du site, pointant vers `/media`.
- **FR-041**: Le système MUST NOT modifier le comportement de la racine `/` (page d'accueil actuelle de la plateforme) ni du blog `/actualites` et de ses contenus existants.
- **FR-042**: Le système MUST réutiliser les taxonomies existantes (catégories, mots-clés, pays) et les comptes utilisateurs existants pour les auteurs, sans dupliquer ces référentiels.

#### Accessibilité et responsive

- **FR-043**: La section MUST être pleinement responsive (mobile, tablette, ordinateur).
- **FR-044**: Les lecteurs audio/vidéo MUST être utilisables au clavier et proposer les sous-titres comme moyen d'accessibilité lorsqu'ils sont disponibles.

### Key Entities *(include if feature involves data)*

- **Contenu média** : unité éditoriale publiable. Attributs clés : titre, identifiant d'URL unique, type (article/interview/podcast/vidéo/reportage), chapô, corps riche, image de couverture, durée estimée, statut de publication, date de publication, indicateurs « à la une » et « épinglé » (+ position), métadonnées de référencement/partage, compteur de consultations, score de popularité. Relations : appartient à un ou plusieurs **Catégories**, porte des **Mots-clés**, rattaché à un **Pays/zone**, associé à un ou plusieurs **Auteurs/Intervenants** (avec rôle), peut appartenir à une **Série** (avec position), peut porter un **média audio** (podcast) ou une **référence vidéo externe** (vidéo) et des **sous-titres**.
- **Série / Playlist** : regroupement de contenus média récurrents (émission). Attributs : titre, identifiant d'URL, description, visuel, type (podcast/vidéo), état. Relation : contient des **Contenus média** ordonnés par saison/épisode.
- **Auteur / Intervenant** : personne créditée sur un contenu, rattachée à un compte utilisateur existant, avec un rôle (auteur, interviewer, invité) et une bio courte/lien de profil.
- **Catégorie / Thématique** : taxonomie existante hiérarchique réutilisée pour classer les contenus média.
- **Mot-clé (Tag)** : taxonomie existante réutilisée et reliée aux contenus média.
- **Pays / Zone géographique** : référentiel existant réutilisé pour le filtrage géographique.
- **Commentaire** : contribution d'un visiteur sur un contenu, avec un état de modération (en attente, approuvé, rejeté).
- **Abonné newsletter** : adresse email abonnée aux nouvelles publications, avec état de confirmation et capacité de désabonnement.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Un visiteur arrivant sur `/media` identifie les contenus « à la une » et ouvre une page de détail en moins de 2 clics.
- **SC-002**: 95 % des recherches/filtrages courants renvoient des résultats pertinents en moins de 1 seconde perçue.
- **SC-003**: La section s'affiche et reste utilisable correctement sur mobile, tablette et ordinateur pour 100 % des pages de la feature.
- **SC-004**: Un éditeur publie un nouveau contenu complet (de n'importe quel type) en moins de 5 minutes via le back-office.
- **SC-005**: 100 % des contenus programmés deviennent visibles automatiquement à l'échéance prévue, sans intervention manuelle.
- **SC-006**: Les abonnés confirmés reçoivent une alerte de nouvelle publication, et 0 commentaire non modéré n'apparaît publiquement.
- **SC-007**: Chaque page de détail expose un titre, une description et un aperçu social uniques pour le partage et le référencement.
- **SC-008**: La racine `/` et le blog `/actualites` conservent un comportement strictement identique à l'existant (0 régression vérifiable).
- **SC-009**: Les podcasts et vidéos proposent une durée affichée et, lorsque fournis, des sous-titres activables, sur 100 % des contenus audio/vidéo.
- **SC-010**: Une vue filtrée est partageable par son URL et restitue le même ensemble de résultats au rechargement.

## Assumptions

- **Section autonome** : `/media` est une nouvelle section indépendante ; la page d'accueil de la plateforme (`/`) et le blog historique (`/actualites`) ne sont ni remplacés ni migrés ; les contenus du blog existant restent gérés séparément.
- **Vidéos** : décision produit retenue — les vidéos sont intégrées depuis des plateformes externes (YouTube/Vimeo) via une URL, sans hébergement ni transcodage interne.
- **Podcasts/audio** : les épisodes audio sont téléversés sous forme de fichiers audio standard (MP3) et lus dans la page ; téléchargement et sous-titres optionnels par contenu.
- **Médias intégrés indépendants du type** : un attribut de *mode média* pilote la présence d'un audio ou d'une vidéo embarquée indépendamment du type éditorial. Ainsi une **interview** (ou un **reportage**) peut être multimédia — porter un audio ou une vidéo — et pas seulement les types « podcast » et « vidéo ».
- **Réutilisation des référentiels** : les catégories, mots-clés et pays existants sont réutilisés ; les auteurs sont des comptes utilisateurs existants de la plateforme.
- **Production de contenu** : les contenus sont créés par l'équipe éditoriale via le back-office d'administration ; aucune soumission de contenu par le public.
- **Commentaires** : par défaut, un visiteur peut commenter en fournissant un nom et un email ; tous les commentaires sont modérés avant affichage.
- **Newsletter** : abonnement par email avec confirmation (opt-in) et désabonnement ; les alertes portent sur les nouvelles publications de la section Média.
- **Langue** : le français est la langue principale de la section.
- **Tri « recommandé »** : combine la mise en avant éditoriale (à la une/épinglé) et la popularité (consultations dans le temps).
- **Popularité** : mesurée à partir des consultations, pondérées par la fraîcheur, pour alimenter la rubrique « Tendances / les plus lus ».
- **Performance/responsive** : attentes standard d'une application web de presse en ligne, sans exigence temps réel particulière au-delà des critères ci-dessus.
