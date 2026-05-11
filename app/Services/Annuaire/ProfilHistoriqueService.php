<?php

declare(strict_types=1);

namespace App\Services\Annuaire;

use App\Models\HistoriqueProfil;
use App\Models\Profil;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ProfilHistoriqueService
{
    /**
     * Champs à exclure du diff (techniques / dérivés).
     */
    private const IGNORED_FIELDS = [
        'updated_at', 'created_at',
        'nom_normalise', 'prenom_normalise',
        'organisation_normalisee', 'ville_normalisee',
    ];

    public function log(
        Profil $profil,
        string $action,
        array $diff = [],
        ?string $motif = null
    ): HistoriqueProfil {
        $clean = $this->sanitizeDiff($diff);

        return HistoriqueProfil::create([
            'profil_id' => $profil->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'diff' => $clean,
            'motif' => $motif,
            'ip' => $this->safeIp(),
            'user_agent' => $this->safeUserAgent(),
            'created_at' => Carbon::now(),
        ]);
    }

    public function historiquePourProfil(Profil $profil): Collection
    {
        return HistoriqueProfil::where('profil_id', $profil->id)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Restaure un profil à l'état « avant » consigné dans une entrée d'historique.
     *
     * - Réapplique les valeurs `avant` pour chaque champ présent dans le diff.
     * - L'observer `ProfilObserver::updated` journalise automatiquement le diff
     *   inverse, puis une entrée explicite `action=restaure` est ajoutée pour
     *   tracer l'intention (référence : entrée source + admin).
     */
    public function restaurer(HistoriqueProfil $entree, \App\Models\User $admin): Profil
    {
        $profil = $entree->profil()->withTrashed()->firstOrFail();

        $valeursAvant = [];
        foreach ($entree->diff ?? [] as $champ => $change) {
            if (! is_array($change) || ! array_key_exists('avant', $change)) {
                continue;
            }
            if (in_array($champ, self::IGNORED_FIELDS, true)) {
                continue;
            }
            $valeursAvant[$champ] = $change['avant'];
        }

        if (! empty($valeursAvant)) {
            Auth::setUser($admin);
            // fill() (pas forceFill) afin de déclencher les mutateurs des
            // colonnes normalisées et de respecter la garde $guarded.
            $profil->fill($valeursAvant)->save();
        }

        $this->log(
            $profil,
            'restaure',
            [
                'historique_id' => ['avant' => null, 'apres' => $entree->id],
            ],
            sprintf('Restauration de l\'entrée #%d (action initiale : %s)', $entree->id, $entree->action)
        );

        return $profil;
    }

    /**
     * Filtre les champs ignorés et normalise le format `{champ:{avant,apres}}`.
     */
    public function sanitizeDiff(array $diff): array
    {
        $clean = [];
        foreach ($diff as $field => $change) {
            if (in_array($field, self::IGNORED_FIELDS, true)) {
                continue;
            }
            $clean[$field] = $change;
        }

        return $clean;
    }

    private function safeIp(): ?string
    {
        try {
            return Request::ip();
        } catch (\Throwable) {
            return null;
        }
    }

    private function safeUserAgent(): ?string
    {
        try {
            $ua = Request::userAgent();

            return $ua ? mb_substr($ua, 0, 255) : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
