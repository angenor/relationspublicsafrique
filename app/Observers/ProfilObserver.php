<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Profil;
use App\Services\Annuaire\ProfilHistoriqueService;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProfilObserver
{
    /**
     * Buffer in-memory du diff calculé en `updating`, consommé en `updated`.
     * Indexé par spl_object_id pour éviter de polluer les attributs Eloquent
     * du modèle (sinon le diff serait persisté en colonne via SQL UPDATE,
     * cf. `$guarded = ['id']` sur Profil).
     *
     * @var array<int, array<string, array{avant: mixed, apres: mixed}>>
     */
    private static array $pendingDiffs = [];

    public function __construct(private ProfilHistoriqueService $historique) {}

    /**
     * T063 : pipeline photo — recadre toute photo nouvellement uploadée
     * en WebP 800×800 dans storage/app/public/profils/.
     */
    public function saving(Profil $profil): void
    {
        if (! $profil->isDirty('image')) {
            return;
        }

        $image = $profil->image;
        if (empty($image) || preg_match('#^https?://#i', $image)) {
            return;
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($image)) {
            return;
        }

        // Déjà au bon format/path : ne rien refaire.
        if (str_starts_with($image, 'profils/') && str_ends_with($image, '.webp')) {
            return;
        }

        try {
            $absolute = $disk->path($image);
            $target = 'profils/'.($profil->id ?: uniqid('p_', false)).'_'.time().'.webp';

            Image::make($absolute)
                ->fit(800, 800)
                ->encode('webp', 85)
                ->save($disk->path($target));

            $profil->image = $target;
        } catch (\Throwable $e) {
            // En cas d'échec de la conversion, on garde l'image d'origine.
        }
    }

    public function created(Profil $profil): void
    {
        $this->historique->log($profil, 'cree', [
            'profil' => ['avant' => null, 'apres' => $profil->only($this->trackedFields($profil))],
        ]);
    }

    public function updating(Profil $profil): void
    {
        $diff = [];
        foreach ($profil->getDirty() as $field => $newValue) {
            $diff[$field] = [
                'avant' => $profil->getOriginal($field),
                'apres' => $newValue,
            ];
        }
        self::$pendingDiffs[spl_object_id($profil)] = $diff;
    }

    public function updated(Profil $profil): void
    {
        $key = spl_object_id($profil);
        $diff = self::$pendingDiffs[$key] ?? [];
        unset(self::$pendingDiffs[$key]);

        if (empty($diff)) {
            return;
        }

        $action = array_key_exists('etat_publication', $diff) ? 'statut_change' : 'modifie';
        $this->historique->log($profil, $action, $diff);
    }

    public function deleted(Profil $profil): void
    {
        $this->historique->log($profil, 'supprime', []);
    }

    public function restored(Profil $profil): void
    {
        $this->historique->log($profil, 'restaure', []);
    }

    /**
     * Champs métier inclus dans l'instantané initial (création).
     */
    private function trackedFields(Profil $profil): array
    {
        return array_keys(array_diff_key(
            $profil->getAttributes(),
            array_flip(['nom_normalise', 'prenom_normalise', 'organisation_normalisee', 'ville_normalisee', 'created_at', 'updated_at'])
        ));
    }
}
