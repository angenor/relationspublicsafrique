# Contracts : Schéma CSV / XLSX (import et export)

## Format général

- **Encodage** : UTF-8 avec BOM (export). À l'import : UTF-8 (BOM facultatif) toléré ; tentative `,` puis `;` à la détection.
- **Délimiteur** : `;` (point-virgule).
- **Échappement** : guillemets doubles `"` pour encadrer les valeurs contenant `;`, `"`, retour-ligne. Les `"` internes sont doublés.
- **Encodage saut de ligne** : `\r\n` (compat Windows/Excel).
- **Première ligne** : en-tête obligatoire avec les noms de colonnes exacts ci-dessous.

## Colonnes (en-tête CSV)

| Colonne | Type / format | Requis (import) | Notes |
|---|---|---|---|
| `id` | int | non | Si présent, met à jour le profil existant. Sinon, crée. |
| `nom` | string ≤ 100 | oui | |
| `prenom` | string ≤ 100 | oui | |
| `nationalite` | string ≤ 100 | non | |
| `email` | string format email | non | **Clé d'unicité** pour la détection des doublons à l'import. |
| `telephone` | string ≤ 30 | non | |
| `masquer_email` | bool (`0`/`1`, `oui`/`non`, `true`/`false`) | non (défaut `1`) | |
| `masquer_tel` | bool | non (défaut `1`) | |
| `fonction` | string ≤ 200 | non | |
| `organisation` | string ≤ 200 | non | |
| `ville` | string ≤ 150 | non | |
| `pays` | string | non | Code ISO 2 lettres (préféré) OU libellé exact. Résolu via `Pays`. |
| `type_profil` | enum | non (défaut `autre`) | `expert` / `étudiant` / `alumni` / `partenaire` / `autre`. |
| `etat_publication` | enum | non (défaut `en_attente`) | Import : seul un admin peut forcer `publié` ; sinon ignoré. |
| `bio_courte` | string ≤ 280 | non | |
| `bio_longue` | text | non | |
| `domaines_expertise` | string | non | Liste séparée par `|` (pipe) — ex : `communication|lobbying|presse`. Création automatique si inconnu. |
| `tags` | string | non | Liste séparée par `|`. Création auto. |
| `liens_linkedin` | string URL | non | |
| `liens_site_web` | string URL | non | |
| `liens_portfolio` | string URL | non | |
| `liens_autre` | string | non | Format `type:url|type:url` pour liens additionnels (facebook, twitter, youtube). |
| `consentement_atteste` | bool | non (défaut `0`) | À l'import par admin : `1` enregistre le consentement à `now()` avec l'admin importateur comme attestateur. |
| `photo_url` | string URL | non | Si présente, téléchargement et validation FFM (FR-004). Échec → ligne en avertissement (profil créé sans photo). |

## Règles d'import

1. Détection du séparateur automatique : tester `;` puis `,` sur la première ligne ; échouer explicitement si > 1 colonne dans aucun cas.
2. Validation ligne par ligne :
   - `nom`, `prenom` obligatoires (sinon → ligne en erreur).
   - `email` doit être un email valide si présent.
   - `type_profil`, `etat_publication` : doivent être dans l'enum (sinon → erreur).
   - `pays` : résolu via `App\Models\Pays::where('code', $value)->orWhere('libelle', $value)` (sinon → erreur).
3. Détection doublons : si `email` correspond à un profil existant → choix utilisateur dans le formulaire d'import : `créer en doublon` / `mettre à jour` (défaut) / `ignorer`.
4. Création des relations :
   - `domaines_expertise` et `tags` : `firstOrCreate(['slug' => Str::slug($v)], ['libelle' => $v])`.
   - `liens_externes` : remplacement total (supprime puis recrée pour le profil concerné).
5. Lignes en erreur : ajoutées au rapport d'import avec numéro de ligne, colonne fautive, message. Les lignes valides sont toujours importées (transaction par ligne).
6. Rapport final affiché en interface + téléchargeable :
   ```
   Lignes lues       : 500
   Lignes créées     : 432
   Lignes mises à jour : 51
   Lignes ignorées   : 8
   Lignes en erreur  : 9
   Détail des erreurs : [tableau]
   ```

## Règles d'export

1. Sélection des profils selon les filtres actifs côté Filament (la requête utilisée pour l'export est exactement celle de la liste back-office).
2. Inclut **tous les champs**, y compris coordonnées non masquées (l'export est destiné aux admins ; cf. spec — hypothèses).
3. Format produit :
   - CSV : `UTF-8 BOM` + `;`. Nom de fichier : `annuaire-YYYYMMDD-HHMMSS.csv`.
   - XLSX : feuille `Profils` avec en-têtes en gras, largeur auto ; types Excel respectés (date, booléen, string).
4. Export en streaming pour ne pas saturer la mémoire (> 500 profils).

## Exemple minimal CSV (extrait)

```csv
id;nom;prenom;nationalite;email;telephone;masquer_email;masquer_tel;fonction;organisation;ville;pays;type_profil;etat_publication;bio_courte;domaines_expertise;tags;liens_linkedin;consentement_atteste
;Diop;Marie;Sénégalaise;marie.diop@example.com;+221770000000;1;1;Consultante;Cabinet RPA;Dakar;SN;expert;publié;"Spécialiste de la communication d'influence";communication|lobbying;francophone|afrique-ouest;https://linkedin.com/in/mariediop;1
12;Touré;Amadou;Ivoirienne;amadou.toure@example.com;;1;1;Doctorant;Université FHB;Abidjan;CI;étudiant;en_attente;"Thèse sur la diplomatie publique africaine";relations-internationales;;https://linkedin.com/in/atoure;0
```

Note : la ligne 1 (`id` vide) crée un nouveau profil ; la ligne 2 (`id=12`) met à jour le profil existant.
