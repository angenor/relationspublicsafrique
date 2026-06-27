# Feature Specification: Section Événements (vitrine)

**Feature Branch**: `004-evenements`  
**Created**: 2026-06-27  
**Status**: Draft  
**Input**: User description: "(nouvelle page, nouvelle feature) Événements : présenter tous les événements organisés par RPA, informer et orienter, faciliter la participation. Listing avec bannière, sections/filtres (en cours / à venir / passés), cards (titre, visuel, dates, lieu, statut, description, CTA). Page individuelle (description complète, objectifs, programme, intervenants, public cible, CTA dynamique). Filtrage par statut/type/pays, tri par date, compte à rebours optionnel. Back-office : création/gestion, statuts automatiques selon date, liens d'inscription interne/externe, upload médias post-événement."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Parcourir les événements de RPA (Priority: P1)

Un visiteur arrive sur la section Événements et veut comprendre, en un coup d'œil, quels événements RPA se déroulent actuellement, lesquels approchent et lesquels sont passés. Il voit une bannière d'accroche, puis les événements présentés en cards regroupés (ou filtrables) par statut temporel — **en cours**, **à venir**, **passés (clos)** — chaque card affichant le titre, le visuel, les dates, le lieu (ville ou « en ligne »), un badge de statut, une courte description et un bouton d'action contextuel.

**Why this priority**: C'est la porte d'entrée de la section et la valeur minimale livrable : sans le listing, aucune autre fonctionnalité n'a de point d'accès. Avec uniquement cette story (et des événements existants), RPA dispose déjà d'une vitrine exploitable.

**Independent Test**: Peut être testée intégralement en chargeant la page de listing avec des événements de chaque statut temporel et en vérifiant qu'ils apparaissent dans le bon groupe avec les bons champs et le bon CTA.

**Acceptance Scenarios**:

1. **Given** des événements publiés couvrant les trois statuts temporels, **When** le visiteur ouvre la page Événements, **Then** il voit la bannière puis les cards réparties entre « en cours », « à venir » et « passés ».
2. **Given** un événement dont la date de début est future, **When** la card s'affiche, **Then** son badge indique « À venir » et son CTA principal est « S'inscrire » (si les inscriptions sont ouvertes) ou « Voir détails ».
3. **Given** un événement déjà terminé disposant d'un replay/compte rendu, **When** la card s'affiche, **Then** son badge indique « Clos » et le CTA propose « Voir le replay » (ou « Voir détails » à défaut de contenu post-événement).
4. **Given** un grand nombre d'événements passés, **When** le visiteur atteint le bas de la liste, **Then** il peut accéder aux événements suivants via pagination (ou chargement progressif).
5. **Given** aucun événement dans une catégorie temporelle, **When** la page s'affiche, **Then** un état vide explicite est montré pour ce groupe plutôt qu'une section vide silencieuse.

---

### User Story 2 - Consulter la page détaillée d'un événement (Priority: P2)

Depuis une card, le visiteur ouvre la page individuelle d'un événement pour obtenir toutes les informations nécessaires à sa décision de participer : bannière (titre + accroche), visuel, date et heure, lieu ou format (présentiel / en ligne), description complète, objectifs, programme / agenda, intervenants, public cible, et un bouton d'action **dynamique** selon le statut de l'événement.

**Why this priority**: Convertit l'intérêt en participation. Indispensable pour « faciliter la participation », mais dépend de l'existence du listing (P1) pour être atteignable.

**Independent Test**: Peut être testée en ouvrant l'URL d'un événement donné et en vérifiant l'affichage complet des champs et le CTA correspondant à son statut temporel.

**Acceptance Scenarios**:

1. **Given** un événement à venir avec inscriptions ouvertes, **When** le visiteur ouvre sa page, **Then** il voit l'ensemble des informations (objectifs, programme, intervenants, public cible) et un CTA « S'inscrire ».
2. **Given** un événement à venir dont l'inscription se fait via un lien externe, **When** le visiteur clique sur « S'inscrire », **Then** il est redirigé vers le lien externe configuré ; sinon il accède au parcours d'inscription interne.
3. **Given** un événement clos disposant de médias post-événement, **When** le visiteur ouvre sa page, **Then** le CTA propose « Voir replay / photos / compte rendu » et les médias sont accessibles.
4. **Given** un événement complet (capacité atteinte) ou dont la date limite d'inscription est dépassée, **When** la page s'affiche, **Then** le CTA d'inscription est désactivé/masqué avec une mention explicite (« Complet » / « Inscriptions closes »).
5. **Given** un slug d'événement inexistant ou un événement non publié, **When** le visiteur tente d'y accéder, **Then** il reçoit une page d'erreur appropriée (introuvable).

---

### User Story 3 - Filtrer, trier et anticiper les événements (Priority: P3)

Le visiteur affine la liste selon le **statut**, le **type d'événement** et le **pays**, et **trie** par date (le plus proche / le plus récent). Pour les événements à venir, un **compte à rebours** peut indiquer le temps restant avant le début.

**Why this priority**: Améliore la découvrabilité quand le volume d'événements augmente, mais le listing reste utilisable sans ces options. Couche d'amélioration.

**Independent Test**: Peut être testée en appliquant chaque filtre et tri sur un jeu d'événements varié et en vérifiant que le résultat affiché correspond exactement aux critères.

**Acceptance Scenarios**:

1. **Given** des événements de types et pays variés, **When** le visiteur sélectionne un type et un pays, **Then** seuls les événements correspondant aux deux critères s'affichent.
2. **Given** une liste filtrée, **When** le visiteur choisit un tri par date, **Then** les événements sont réordonnés (du plus proche au plus lointain, ou du plus récent au plus ancien).
3. **Given** un événement à venir, **When** sa card ou sa page s'affiche, **Then** un compte à rebours montre le temps restant et disparaît/s'actualise une fois l'événement commencé.
4. **Given** une combinaison de filtres ne renvoyant aucun résultat, **When** elle est appliquée, **Then** un état vide invite à élargir les critères.

---

### User Story 4 - Gérer les événements depuis le back-office (Priority: P2)

Un administrateur RPA crée et gère les événements depuis le back-office : saisie des informations (titre, visuel, dates, lieu/format, description, objectifs, programme, intervenants, public cible, type, pays), configuration du **mode d'inscription** (interne ou lien externe), et **upload de médias post-événement** (replay, photos, compte rendu). Le **statut temporel** (en cours / à venir / clos) est déterminé **automatiquement** à partir des dates, sans intervention manuelle.

**Why this priority**: Permet d'alimenter et de maintenir la vitrine de façon autonome. Élevée car sans gestion de contenu la vitrine ne peut être tenue à jour, mais le listing public peut être démontré avec des données pré-existantes/seedées.

**Independent Test**: Peut être testée en créant un événement complet en back-office, puis en vérifiant qu'il apparaît correctement côté public avec le statut temporel attendu déduit de ses dates.

**Acceptance Scenarios**:

1. **Given** un administrateur authentifié, **When** il crée un événement avec toutes ses informations et le publie, **Then** l'événement devient visible côté public dans le bon groupe temporel.
2. **Given** un événement dont la date de début est passée et la date de fin future, **When** on consulte son statut, **Then** il est automatiquement classé « En cours » sans modification manuelle.
3. **Given** un événement passé, **When** l'administrateur ajoute un replay et des photos, **Then** le CTA public bascule vers « Voir replay / photos / compte rendu ».
4. **Given** un administrateur configurant l'inscription, **When** il renseigne un lien externe, **Then** le CTA public pointe vers ce lien ; **When** il choisit l'inscription interne, **Then** le parcours d'inscription interne est utilisé.
5. **Given** un événement en brouillon (non publié), **When** un visiteur tente d'y accéder, **Then** il n'est ni listé ni accessible publiquement.

---

### Edge Cases

- **Événement sur plusieurs jours** chevauchant l'instant présent : il doit être classé « En cours » tant que la date de fin n'est pas atteinte.
- **Événement annulé** : doit être distingué visuellement (badge « Annulé ») et ne pas proposer d'inscription, quel que soit son statut temporel.
- **Date limite d'inscription dépassée** alors que l'événement est encore à venir : les inscriptions se ferment mais l'événement reste affiché comme « À venir ».
- **Capacité maximale atteinte** : le CTA d'inscription affiche « Complet » et empêche de nouvelles inscriptions internes.
- **Événement en ligne sans ville** : le lieu affiche « En ligne » au lieu d'une ville.
- **Événement clos sans média post-événement** : le CTA retombe sur « Voir détails » plutôt que « Voir le replay ».
- **Fuseaux horaires** : l'heure affichée doit être cohérente et non ambiguë pour les événements en ligne touchant plusieurs pays.
- **Lien d'inscription externe invalide/expiré** : le visiteur ne doit pas être bloqué côté RPA (l'erreur provient du service externe).
- **Visuel manquant** : une image de remplacement cohérente est utilisée.

## Requirements *(mandatory)*

### Functional Requirements

#### Listing public

- **FR-001**: Le système MUST proposer une page de listing des événements organisés par RPA, introduite par une bannière (titre + accroche).
- **FR-002**: Le système MUST présenter les événements en **cards** regroupées et/ou filtrables selon trois statuts temporels : **en cours**, **à venir**, **passés (clos)**.
- **FR-003**: Chaque card MUST afficher : titre, visuel, date(s), lieu (ville ou « en ligne »), badge de statut, courte description, et un bouton d'action contextuel.
- **FR-004**: Le bouton d'action d'une card MUST s'adapter au statut : « S'inscrire » (si les inscriptions sont ouvertes), « Voir détails », ou « Voir le replay » (événement passé disposant de contenu post-événement).
- **FR-005**: Le système MUST permettre de parcourir l'intégralité des événements via pagination ou chargement progressif.
- **FR-006**: Le système MUST n'exposer publiquement que les événements **publiés** (les brouillons restent invisibles côté public).
- **FR-007**: Le système MUST afficher un état vide explicite lorsqu'un groupe ou un résultat de filtre ne contient aucun événement.

#### Page détaillée

- **FR-008**: Le système MUST fournir une page individuelle par événement, accessible via une URL stable (slug).
- **FR-009**: La page individuelle MUST afficher : bannière (titre + accroche), visuel, date et heure, lieu / format (présentiel ou en ligne), description complète, objectifs, programme / agenda, intervenants et public cible.
- **FR-010**: La page individuelle MUST présenter un bouton d'action **dynamique** : « S'inscrire » (événement à venir, inscriptions ouvertes) ou « Voir replay / photos / compte rendu » (événement clos avec médias).
- **FR-011**: Le système MUST gérer un état d'inscription indisponible (complet, date limite dépassée, événement annulé) avec une mention explicite remplaçant le bouton « S'inscrire ».
- **FR-012**: Le système MUST retourner une réponse « introuvable » pour un événement inexistant ou non publié.

#### Inscription

- **FR-013**: Le système MUST supporter deux modes d'inscription configurables par événement : **interne** (parcours d'inscription du site) et **externe** (redirection vers un lien fourni).
- **FR-014**: Pour l'inscription interne, le système MUST permettre au visiteur de s'inscrire et MUST respecter la capacité maximale et la date limite d'inscription définies pour l'événement.

#### Filtrage, tri, compte à rebours

- **FR-015**: Le système MUST permettre de filtrer les événements par **statut**, par **type d'événement** et par **pays**.
- **FR-016**: Le système MUST permettre de **trier** les événements par date (du plus proche au plus lointain, et du plus récent au plus ancien).
- **FR-017**: Le système MAY afficher un **compte à rebours** pour les événements à venir, indiquant le temps restant avant le début.

#### Statut temporel automatique

- **FR-018**: Le système MUST déterminer **automatiquement** le statut temporel d'un événement (à venir / en cours / clos) à partir de ses dates de début et de fin, sans saisie manuelle de ce statut.
- **FR-019**: Le système MUST distinguer le statut **éditorial** (brouillon, publié, annulé) du statut **temporel** dérivé des dates, et refléter les deux dans l'affichage public (ex. badge « Annulé »).

#### Back-office

- **FR-020**: Le système MUST permettre aux administrateurs de créer, modifier, publier et supprimer des événements avec l'ensemble de leurs champs (incluant objectifs, programme, intervenants, public cible, type, pays).
- **FR-021**: Le système MUST permettre de configurer, par événement, le mode d'inscription (interne ou lien externe) et, le cas échéant, l'URL externe.
- **FR-022**: Le système MUST permettre l'**upload de médias post-événement** (replay, photos, compte rendu) rattachés à un événement clos.
- **FR-023**: Le back-office MUST refléter automatiquement le statut temporel calculé pour aider l'administrateur à suivre le cycle de vie des événements.

### Key Entities *(include if feature involves data)*

- **Événement**: l'entité centrale présentée et gérée. Attributs clés : titre, slug, accroche/résumé, description complète, objectifs, programme/agenda, public cible, visuel, date(s) et heure de début/fin, lieu (ville) ou indicateur « en ligne », type d'événement, pays, statut éditorial, capacité maximale et nombre de participants, date limite d'inscription, mode d'inscription (interne/externe) et lien externe éventuel. Le **statut temporel** (à venir / en cours / clos) est **dérivé** des dates et n'est pas stocké comme champ saisissable.
- **Type d'événement**: classification permettant le filtrage (ex. conférence, atelier, webinaire). Relation : un événement appartient à un type.
- **Pays**: rattachement géographique d'un événement, support du filtre par pays. Relation : un événement est associé à un pays (réutilise l'annuaire pays existant).
- **Intervenant**: personne présentant ou animant l'événement (nom, rôle/qualité, éventuellement visuel). Relation : un événement peut avoir plusieurs intervenants.
- **Média post-événement**: contenu publié après l'événement (replay vidéo, galerie photos, document de compte rendu). Relation : un événement clos peut avoir plusieurs médias.
- **Inscription**: enregistrement d'un participant pour une inscription interne (réutilise le mécanisme d'inscription existant). Relation : un événement a plusieurs inscriptions, soumises à la capacité et à la date limite.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Un visiteur peut, depuis la page Événements, identifier en moins de 10 secondes quels événements sont en cours, à venir et passés.
- **SC-002**: 100 % des événements affichés présentent un statut temporel cohérent avec leurs dates (aucun événement à venir affiché comme clos, ni l'inverse) à tout moment de la journée.
- **SC-003**: Depuis une card, un visiteur atteint le parcours d'inscription (interne ou externe) en au plus 2 clics.
- **SC-004**: Le statut temporel de chaque événement se met à jour sans intervention manuelle au passage des dates de début et de fin (vérifiable en comparant l'affichage avant/après une échéance).
- **SC-005**: Un administrateur peut créer et publier un événement complet, qui apparaît correctement côté public, en moins de 5 minutes.
- **SC-006**: Pour un événement clos disposant de médias post-événement, le CTA « Voir replay / photos / compte rendu » est présent dans 100 % des cas (card et page détail).
- **SC-007**: L'application d'un filtre (statut, type ou pays) ne renvoie que des événements satisfaisant le critère, vérifié sur un jeu d'événements varié.
- **SC-008**: La page de listing reste exploitable et lisible quel que soit le volume d'événements (au-delà de plusieurs dizaines), grâce à la pagination/chargement progressif.

## Assumptions

- **Réutilisation du domaine existant** : la feature s'appuie sur l'entité Événement déjà présente dans l'application (dates de début/fin, lieu, format en ligne, capacité, date limite d'inscription, prix, statut éditorial brouillon/publié/annulé/terminé, rattachement pays) et l'enrichit (objectifs, programme, intervenants, public cible, type, médias post-événement, mode d'inscription externe) plutôt que de créer un domaine parallèle.
- **Mécanisme d'inscription interne existant réutilisé** : l'inscription interne s'appuie sur le dispositif d'inscription aux événements déjà en place ; la nouveauté est l'option de lien externe par événement.
- **Statut temporel calculé, statut éditorial saisi** : « en cours / à venir / clos » est toujours calculé à partir des dates ; l'administrateur ne saisit que le statut éditorial (publication/annulation). Un événement « terminé » au sens éditorial coïncide avec un statut temporel « clos ».
- **Type d'événement** : un champ/taxonomie dédié de typologie est utilisé pour le filtre « par type », distinct du rattachement pays.
- **Pays** : le filtre par pays réutilise le référentiel pays de l'annuaire existant ; un événement en ligne peut néanmoins être rattaché à un pays organisateur ou marqué sans pays.
- **Compte à rebours optionnel** : considéré comme une amélioration (P3) pouvant être livrée après le cœur de la feature.
- **Médias post-événement** : replay vidéo (lien embarqué ou hébergé), galerie photos et document/texte de compte rendu sont les types attendus ; ils ne s'appliquent qu'aux événements clos.
- **Langue** : l'interface publique et le back-office sont en français, cohérents avec le reste du site.
- **Authentification back-office** : la gestion des événements est réservée aux administrateurs via le panneau d'administration existant.

## Dependencies

- Référentiel **Pays** de l'annuaire (filtre et rattachement).
- Dispositif d'**inscription aux événements** existant (parcours interne, capacité, date limite).
- Panneau d'**administration** existant pour la gestion de contenu.
- Hébergement/embarquement de **médias** (vidéo replay, images) selon les capacités média déjà présentes dans le projet.
