# Contract — Console, notifications, observers & partage

## Commandes Artisan (enregistrées dans `app/Console/Kernel.php::schedule()`)

Le scheduler est **déjà actif** (`annuaire:envoyer-rappels-consentement`->daily()). On ajoute :

| Commande | Signature | Planification | Rôle |
|---|---|---|---|
| `media:publish-scheduled` | `MediaPublishScheduled` | `->everyMinute()` | bascule `status` `scheduled→published` quand `published_at<=now()`, déclenche newsletter |
| `media:recompute-popularity` | `MediaRecomputePopularity` | `->hourly()` | recalcule `popularity_score` = vues pondérées par fraîcheur |

> Dépendance ops : `php artisan schedule:run` via cron en production (déjà requis pour l'annuaire).

### `media:publish-scheduled`
```
foreach (Media::where('status','scheduled')->where('published_at','<=',now())->cursor() as $m) {
    $m->update(['status' => 'published']);   // le MediaObserver::saved déclenche la notif
}
```

### `media:recompute-popularity`
- `popularity_score = round(view * facteur_fraicheur(published_at))` (ex. décroissance exponentielle sur l'âge en jours). Paramètres dans `config/media.php`.

## Observer `App\Observers\MediaObserver`
- `creating` : slug depuis `titre` si vide.
- `saving` : `titre_normalise` (TextNormalizer), `reading_time` (mots/200).
- `saved` : si transition `→ published` (détection via `wasChanged('status')` ou flag), dispatch des notifications newsletter (queued).
- Enregistré dans `AppServiceProvider::boot()` : `Media::observe(MediaObserver::class)`.

## Notifications (`app/Notifications/`)
Patron : `ProfilPubliePersonneNotification` (`via()=>['mail']`, `Queueable`, `MailMessage`).

| Classe | Destinataire | Contenu |
|---|---|---|
| `NewMediaPublishedNotification` | abonnés `confirmed` (via `Notification::route('mail',$email)`) | titre + chapô + lien `media.show` + lien désabonnement signé |
| `NewsletterConfirmationNotification` | nouvel abonné `pending` | lien de confirmation **URL signée** (`URL::temporarySignedRoute('media.newsletter.confirm', ..., ['token'=>$token])`) |

> En local, queue `sync` (envoi immédiat) + MailHog (`docker-compose.yml`, UI :8025). En prod : driver `database`/`redis` + table `jobs` (`php artisan queue:table && migrate`) — noté dans quickstart.

## Partage social (`\Share`, package `jorenvanhocht/laravel-share`)
- Page détail : `\Share::page($media->link, $media->titre)->facebook()->twitter()->linkedin()->whatsapp()->getRawLinks()` → clés `facebook/twitter/linkedin/whatsapp` (cf. `config/laravel-share.php`).
- **Email** : bouton `mailto:?subject={titre}&body={url}` rendu directement en Blade (le package n'a pas de service email).
- **X** : utilise le bouton `twitter`.

## Anti-spam (nouveau, aucun existant)
- **Honeypot** : champ caché `website` (doit rester vide) sur formulaires commentaire et newsletter.
- **Throttling** : `RateLimiter::for('media-public', ...)` ou middleware `throttle` sur `media.comments.store` et `media.newsletter.subscribe` (réutilise l'idée de `config('annuaire.rate_limit_public')` → `config('media.rate_limit_public')`).

## Tests de contrat
- `media:publish-scheduled` publie un média `scheduled` arrivé à échéance et envoie 1 notif par abonné confirmé (assert `Notification::fake`).
- confirmation/désabonnement via URL signée valide ; signature falsifiée → 403.
- honeypot rempli → soumission rejetée silencieusement.
