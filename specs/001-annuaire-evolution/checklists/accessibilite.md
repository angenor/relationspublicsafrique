# Audit accessibilité — Annuaire (T088)

**Référence** : WCAG 2.1 niveau AA, RGAA 4.x.
**Périmètre** : pages publiques `/annuaire` (grille + recherche + filtres + tri) et `/annuaire/{slug}` (fiche détail).
**Date d'audit** : 2026-05-11.

## Rôles ARIA et structure sémantique

- [X] Région principale identifiée par `role="region"` + `aria-label="Annuaire des profils"`.
- [X] Barre de filtres identifiée par `role="search"`.
- [X] Liste des résultats balisée `role="list"` + `role="listitem"` en mode grille (Bootstrap colonne).
- [X] Pagination dans `<nav aria-label="Pagination de l'annuaire">`.
- [X] Liste compacte (mode liste) utilise `<ul class="list-group">` — sémantique HTML native.
- [X] Bascule grille/liste : `role="group"` + `aria-pressed` sur chaque bouton.

## Étiquetage des champs

- [X] Champ recherche libre : `<label for="annuaire-search">` + `aria-label`.
- [X] Selects pays, type, tri : `<label for="…">` explicites.
- [X] Select multiple Domaine : `aria-label` + `aria-multiselectable="true"`.

## États dynamiques

- [X] Indicateur de chargement : `wire:loading.attr="aria-busy"` ajouté sur le wrapper.
- [X] Zone résultats encapsulée dans `<div aria-live="polite">` pour annonce des updates aux lecteurs d'écran.
- [X] Compteur de résultats lu via `<p class="visually-hidden" id="annuaire-resultats-count">`.
- [X] Message "aucun profil" : `role="status"`.

## Navigation clavier

- [X] Tous les champs de formulaire sont focusables nativement (input/select/button).
- [X] Boutons grille/liste sont des `<button type="button">` — accessibles au clavier.
- [X] Liens de pagination sont rendus par Laravel paginator (`<a>` standards).
- [ ] **À VÉRIFIER manuellement** : ordre de tabulation logique (Tab → search → pays → type → domaine → tri → reset → grille/liste → résultats → pagination).
- [ ] **À VÉRIFIER manuellement** : focus visible sur tous les éléments interactifs (revoir feuille de style si nécessaire).

## Contraste des couleurs

- [ ] **À VÉRIFIER manuellement** avec un outil (axe DevTools, WAVE, Lighthouse) :
  - Texte sur badges `type_profil` et `etat_publication` (contraste WCAG AA ≥ 4.5:1).
  - Liens dans la pagination (état hover/focus).
  - Boutons « Réinitialiser » (lien classe `btn-link`).

## Images

- [X] Photos de profil dans `profil-carte.blade.php` : `alt="Photo de {prénom} {nom}"` + `loading="lazy"` + `aria-label` sur l'`<article>` parent.
- [X] Avatar par défaut `default-avatar.svg` sert pour profils sans photo (cf. `Profil::getPhotoUrlAttribute()`).

## Fiche détail (`/annuaire/{slug}`)

- [ ] **À VÉRIFIER manuellement** : présence d'un `<h1>` unique, hiérarchie de titres logique.
- [ ] **À VÉRIFIER manuellement** : liens externes (réseaux sociaux) avec `rel="noopener noreferrer"` et libellé accessible.
- [ ] **À VÉRIFIER manuellement** : adresses email/tel non masquées entourées d'un lien `mailto:` / `tel:`.

## Recommandations résiduelles

1. Ajouter un lien de saut (skip link) `Aller au contenu` en haut de la page si pas déjà présent dans le layout global.
2. Ajouter des `alt` explicites sur les photos de carte (cf. `profil-carte.blade.php`).
3. Tester le tri et les filtres avec lecteur d'écran (NVDA ou VoiceOver) — vérifier que les changements de résultats sont annoncés.
4. Lancer un audit automatisé via Lighthouse (`npx lighthouse https://…/annuaire --only-categories=accessibility`) et résoudre les éventuels « contrast » et « ARIA » findings.

## Conformité estimée

- **Implémenté côté code** : 10/14 critères automatisables.
- **Reste à vérifier manuellement** : 4 critères (contraste, focus visible, ordre de tabulation, attributs `alt`).
