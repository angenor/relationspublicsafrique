# Feature Specification: Section Projets (vitrine)

**Feature Branch**: `003-projets-vitrine`  
**Created**: 2026-06-26  
**Status**: Draft  
**Input**: User description: "(nouvelle page, nouvelle feature) Projets : présenter les projets portés par Relations Publiques Afrique (RPA), valoriser l'expertise/réalisations/impact, renforcer la crédibilité institutionnelle. Card projet (nom, visuel, brève description, thématique, zone géographique, statut Actif/Réalisé/En développement, bouton Découvrir). Page détaillée (titre + visuel principal, contexte/problématique, objectifs, description détaillée, activités réalisées, résultats/impact chiffres clés, partenaires, galerie photos/vidéos, témoignages optionnels, bouton Nous contacter). Back-office : ajout/modification/suppression, gestion statuts, upload médias."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Parcourir et découvrir les projets RPA (Priority: P1)

Un visiteur (partenaire potentiel, bailleur, journaliste, membre du public) arrive sur la page Projets et consulte la liste des projets portés par RPA, présentés sous forme de cards. Chaque card affiche le nom du projet, un visuel, une brève description, sa thématique, sa zone géographique et son statut. Le visiteur peut filtrer la liste pour trouver les projets qui l'intéressent, puis cliquer sur « Découvrir » pour en savoir plus.

**Why this priority**: C'est le cœur de la valeur de la feature : sans la page de listing publique, l'objectif de valoriser l'expertise et de renforcer la crédibilité institutionnelle ne peut pas être atteint. C'est le MVP minimal qui délivre déjà de la valeur (vitrine consultable).

**Independent Test**: Peut être entièrement testé en publiant quelques projets via le back-office, puis en consultant la page publique de listing : les cards s'affichent avec les bons champs, les filtres réduisent correctement la liste, et le bouton « Découvrir » est présent. Délivre la valeur « rendre les projets visibles ».

**Acceptance Scenarios**:

1. **Given** au moins un projet publié, **When** le visiteur ouvre la page Projets, **Then** il voit une card par projet publié avec nom, visuel, brève description, thématique, zone géographique et badge de statut.
2. **Given** des projets de thématiques et statuts variés, **When** le visiteur applique un filtre (thématique, zone géographique ou statut), **Then** seules les cards correspondantes restent affichées.
3. **Given** la page Projets affichée, **When** le visiteur clique sur « Découvrir » d'une card, **Then** il est dirigé vers la page détaillée du projet correspondant.
4. **Given** aucun projet publié, **When** le visiteur ouvre la page Projets, **Then** un message d'état vide explicite est affiché (pas d'erreur ni de page blanche).

---

### User Story 2 - Consulter le détail d'un projet (Priority: P1)

Un visiteur ouvre la page détaillée d'un projet pour comprendre son contexte, ses objectifs, ses réalisations et son impact. Il consulte le visuel principal, le contexte/problématique, les objectifs, la description détaillée, les activités réalisées, les résultats/chiffres clés, les partenaires impliqués, la galerie photos/vidéos et, le cas échéant, les témoignages. Un bouton « Nous contacter » lui permet de prendre contact à propos du projet.

**Why this priority**: La page détaillée est ce qui transforme la simple visibilité en crédibilité démontrée (preuves, chiffres, partenaires). Elle est indispensable au MVP au même titre que le listing : le bouton « Découvrir » du listing doit mener quelque part.

**Independent Test**: Peut être testé en ouvrant l'URL d'un projet publié et en vérifiant que toutes les sections renseignées s'affichent dans l'ordre, que les sections optionnelles non renseignées sont masquées proprement, et que le bouton « Nous contacter » fonctionne.

**Acceptance Scenarios**:

1. **Given** un projet publié avec toutes ses sections renseignées, **When** le visiteur ouvre sa page détaillée, **Then** il voit le titre, le visuel principal, le contexte, les objectifs, la description détaillée, les activités réalisées, les résultats/chiffres clés, les partenaires et la galerie.
2. **Given** un projet dont les témoignages ne sont pas renseignés, **When** le visiteur ouvre sa page détaillée, **Then** la section témoignages est entièrement masquée (aucun bloc vide).
3. **Given** une page détaillée de projet, **When** le visiteur clique sur « Nous contacter », **Then** il accède au moyen de contact en lien avec le projet.
4. **Given** une galerie contenant photos et vidéos, **When** le visiteur consulte la galerie, **Then** il peut visualiser les images et lire/ouvrir les vidéos.
5. **Given** un identifiant de projet inexistant ou non publié, **When** le visiteur tente d'ouvrir la page détaillée, **Then** une page « introuvable » est affichée.

---

### User Story 3 - Gérer les projets depuis le back-office (Priority: P1)

Un administrateur RPA se connecte au back-office et crée, modifie ou supprime des projets. Il renseigne tous les champs (card et page détaillée), téléverse le visuel principal et les médias de la galerie (photos et vidéos), gère le statut du projet (Actif / Réalisé / En développement) et contrôle si le projet est publié ou non.

**Why this priority**: Sans gestion de contenu, aucun projet ne peut être créé ni mis à jour : le listing et la page détaillée seraient vides en permanence. C'est donc une dépendance directe et indispensable des stories P1 publiques.

**Independent Test**: Peut être testé en se connectant au back-office, en créant un projet complet avec médias, en le publiant, en le modifiant, puis en le supprimant — et en vérifiant à chaque étape la répercussion côté public.

**Acceptance Scenarios**:

1. **Given** un administrateur connecté, **When** il crée un projet avec tous les champs requis et le publie, **Then** le projet apparaît sur la page publique de listing et sa page détaillée est accessible.
2. **Given** un projet existant, **When** l'administrateur modifie son statut ou son contenu et enregistre, **Then** les changements sont reflétés côté public.
3. **Given** un projet existant, **When** l'administrateur le supprime, **Then** il disparaît du listing public et sa page détaillée n'est plus accessible.
4. **Given** le formulaire de création/édition, **When** l'administrateur téléverse des images et des vidéos pour la galerie, **Then** ces médias sont associés au projet et visibles sur la page détaillée.
5. **Given** un projet en brouillon (non publié), **When** un visiteur public navigue sur le site, **Then** ce projet n'apparaît ni dans le listing ni via son URL détaillée.

---

### Edge Cases

- **Visuel manquant** : si un projet n'a pas de visuel principal ou de visuel de card, un visuel par défaut (placeholder) est affiché plutôt qu'une image cassée.
- **Brève description trop longue** : la description de card est tronquée à une longueur d'affichage cohérente sans casser la mise en page.
- **Chiffres clés vides** : si aucun résultat/chiffre clé n'est renseigné, la section résultats est masquée.
- **Vidéo de galerie indisponible** (lien externe rompu / fichier manquant) : la galerie continue d'afficher les autres médias sans planter.
- **Zone géographique régionale vs pays** : un projet peut couvrir un pays précis ou une portée régionale/continentale ; les deux cas doivent être représentables et filtrables.
- **Grand nombre de projets** : la page de listing reste utilisable (pagination ou chargement progressif) lorsque le nombre de projets devient important.
- **Statut incohérent** : un projet « En développement » ne doit pas afficher de résultats/impact définitifs trompeurs ; l'affichage reste cohérent avec le statut.
- **Suppression d'un projet référencé** : la suppression d'un projet ne laisse aucun lien mort depuis le listing.

## Requirements *(mandatory)*

### Functional Requirements

#### Listing public

- **FR-001**: Le système MUST exposer une page publique « Projets » listant tous les projets publiés sous forme de cards.
- **FR-002**: Chaque card MUST afficher : le nom du projet, un visuel, une brève description, la thématique, la zone géographique, et le statut du projet.
- **FR-003**: Le système MUST afficher un badge ou indicateur visuel distinct pour chacun des statuts : Actif, Réalisé, En développement.
- **FR-004**: Chaque card MUST fournir un bouton/action « Découvrir » menant à la page détaillée du projet.
- **FR-005**: Le système MUST permettre au visiteur de filtrer les projets par thématique, par zone géographique et par statut.
- **FR-006**: Le système MUST n'afficher publiquement que les projets dont l'état est « publié » ; les brouillons MUST être exclus du listing et de l'accès direct.
- **FR-007**: Le système MUST gérer l'affichage d'un état vide explicite lorsqu'aucun projet ne correspond (liste vide ou filtre sans résultat).
- **FR-008**: Le système MUST rester performant et lisible quand le nombre de projets augmente (pagination ou chargement progressif).

#### Page détaillée publique

- **FR-009**: Le système MUST exposer une page détaillée par projet publié, accessible via une URL stable et lisible.
- **FR-010**: La page détaillée MUST pouvoir présenter : le titre, le visuel principal, le contexte/problématique, les objectifs, la description détaillée, les activités réalisées, les résultats/impact (chiffres clés), les partenaires impliqués et une galerie de médias (photos et vidéos).
- **FR-011**: La page détaillée MUST pouvoir présenter des témoignages, considérés comme optionnels.
- **FR-012**: Le système MUST masquer proprement toute section optionnelle non renseignée (ex. témoignages, partenaires, chiffres clés) sans laisser de bloc vide.
- **FR-013**: La page détaillée MUST présenter les résultats/impact sous forme de chiffres clés mis en valeur (libellé + valeur).
- **FR-014**: La galerie MUST permettre l'affichage de photos et la lecture/ouverture de vidéos.
- **FR-015**: La page détaillée MUST proposer un bouton « Nous contacter » permettant au visiteur de prendre contact en lien avec le projet consulté.
- **FR-016**: Le système MUST retourner une page « introuvable » lorsqu'un projet demandé n'existe pas ou n'est pas publié.

#### Back-office (administration)

- **FR-017**: Le système MUST permettre à un administrateur autorisé de créer, modifier et supprimer des projets.
- **FR-018**: Le back-office MUST permettre de renseigner l'ensemble des champs de la card et de la page détaillée.
- **FR-019**: Le back-office MUST permettre de définir et modifier le statut d'un projet parmi Actif, Réalisé, En développement.
- **FR-020**: Le back-office MUST permettre de contrôler la publication d'un projet (publié / brouillon) indépendamment de son statut métier.
- **FR-021**: Le back-office MUST permettre le téléversement et la gestion de médias : visuel de card, visuel principal, et médias de galerie (images et vidéos).
- **FR-022**: Le back-office MUST permettre de gérer les partenaires impliqués et les témoignages associés à un projet.
- **FR-023**: Le back-office MUST permettre de saisir les chiffres clés (résultats/impact) sous forme de paires libellé/valeur.
- **FR-024**: Le système MUST garantir que seules les personnes autorisées peuvent accéder aux fonctions de gestion des projets.
- **FR-025**: Le système MUST permettre de définir l'ordre d'affichage et/ou la mise en avant des projets dans le listing public.

### Key Entities *(include if feature involves data)*

- **Projet** : entité centrale de la feature. Attributs : nom/titre, visuel de card, visuel principal, brève description, description détaillée, contexte/problématique, objectifs, activités réalisées, thématique, zone géographique (pays ou portée régionale), statut métier (Actif / Réalisé / En développement), état de publication, ordre/mise en avant. Relations : thématique, zone géographique, chiffres clés, partenaires, médias de galerie, témoignages.
- **Thématique** : catégorie d'un projet (ex. communication, formation, média). Un projet a une (ou plusieurs) thématiques ; sert au filtrage. Réutilise vraisemblablement la notion de catégorie existante du site.
- **Zone géographique** : pays ou portée régionale/continentale couvert(e) par le projet ; sert au filtrage. Réutilise vraisemblablement le référentiel pays existant.
- **Chiffre clé (résultat/impact)** : paire libellé + valeur (et éventuelle unité) rattachée à un projet, mise en valeur sur la page détaillée.
- **Partenaire** : entité ou organisation impliquée dans un projet (nom, éventuel logo/lien) ; relation multiple avec un projet.
- **Média de galerie** : image ou vidéo rattachée à un projet, avec un ordre d'affichage ; une vidéo peut être un fichier hébergé ou un lien externe (ex. YouTube/Vimeo).
- **Témoignage** : citation optionnelle rattachée à un projet (auteur, fonction/organisation, contenu).

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Un visiteur peut, depuis la page Projets, identifier un projet pertinent et accéder à sa page détaillée en 3 clics ou moins.
- **SC-002**: 100 % des projets publiés apparaissent sur le listing public et 0 % des projets en brouillon y apparaissent.
- **SC-003**: Un administrateur peut créer et publier un projet complet (tous champs + médias) en moins de 15 minutes sans assistance.
- **SC-004**: Le filtrage par thématique, zone géographique ou statut renvoie un résultat cohérent dans 100 % des cas testés (seuls les projets correspondants restent affichés).
- **SC-005**: La page de listing reste utilisable et lisible avec au moins 50 projets (chargement progressif/pagination effectif).
- **SC-006**: Sur la page détaillée, toute section optionnelle non renseignée est masquée dans 100 % des cas (aucun bloc vide observé).
- **SC-007**: Le bouton « Nous contacter » d'une page détaillée aboutit à un moyen de contact fonctionnel dans 100 % des cas testés.

## Assumptions

- **Périmètre linguistique** : la section est en français uniquement, conformément au reste du site public RPA.
- **Réutilisation des référentiels** : les thématiques s'appuient sur le système de catégories existant et les zones géographiques sur le référentiel pays existant, plutôt que sur de nouveaux référentiels indépendants.
- **« Nous contacter »** : le bouton renvoie vers le dispositif de contact existant du site (page/formulaire de contact), en référençant le projet concerné, plutôt que de créer un nouveau canal de contact dédié. (À confirmer via `/speckit-clarify` si un formulaire spécifique aux projets est souhaité.)
- **Partenaires et témoignages** : saisis librement au niveau de chaque projet (texte/logo), sans liaison obligatoire à l'annuaire des membres existant.
- **Back-office** : la gestion s'intègre au panneau d'administration existant du site (cohérence avec les autres entités gérées), avec contrôle d'accès réservé aux administrateurs.
- **Vidéos de galerie** : prise en charge des fichiers téléversés et/ou des liens d'intégration externes (YouTube/Vimeo), réutilisant les mécanismes média déjà présents sur le site.
- **Multi-thématique** : un projet peut être rattaché à une ou plusieurs thématiques ; le filtrage en tient compte.
- **Statut métier vs publication** : le statut (Actif/Réalisé/En développement) est une information éditoriale distincte de l'état de publication (visible ou non sur le site).
- **Médias** : l'upload des images et vidéos réutilise l'infrastructure média existante du site (stockage, traitement éventuel).
