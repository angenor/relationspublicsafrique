<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Représentation publique d'un profil pour l'annuaire.
 *
 * Règles strictes :
 *  - `email` omis si `masquer_email = true`
 *  - `tel` omis si `masquer_tel = true`
 *  - Aucun champ administratif (`etat_publication`, `legacy_sans_consentement`, jetons) jamais sérialisé
 *
 * @property \App\Models\Profil $resource
 */
class ProfilPublicResource extends JsonResource
{
    public bool $withDetail = false;

    public function avecDetail(): self
    {
        $this->withDetail = true;

        return $this;
    }

    public function toArray($request): array
    {
        $profil = $this->resource;

        $base = [
            'id' => $profil->id,
            'slug' => $profil->slug,
            'nom' => $profil->nom,
            'prenom' => $profil->prenom,
            'nom_complet' => trim(($profil->prenom ?? '').' '.($profil->nom ?? '')),
            'nationalite' => $profil->nationalite,
            'fonction' => $profil->fonction,
            'organisation' => $profil->organisation,
            'ville' => $profil->ville,
            'type_profil' => $profil->type_profil,
            'bio_courte' => $profil->bio_courte,
            'photo_url' => $profil->photo_url ?? null,
            'pays' => $profil->relationLoaded('pays') && $profil->pays
                ? [
                    'code' => $profil->pays->slug,
                    'libelle' => $profil->pays->name,
                ]
                : null,
            'domaines_expertise' => $profil->relationLoaded('domainesExpertise')
                ? $profil->domainesExpertise->map(fn ($d) => [
                    'slug' => $d->slug,
                    'libelle' => $d->libelle,
                ])->values()
                : [],
            'tags' => $profil->relationLoaded('tags')
                ? $profil->tags->pluck('slug')->values()
                : [],
            'liens_externes' => $profil->relationLoaded('liensExternes')
                ? $profil->liensExternes->map(fn ($l) => [
                    'type' => $l->type,
                    'url' => $l->url,
                    'libelle' => $l->libelle,
                ])->values()
                : [],
        ];

        if (! $profil->masquer_email && $profil->email) {
            $base['email'] = $profil->email;
        }

        if (! $profil->masquer_tel && $profil->tel) {
            $base['tel'] = $profil->tel;
        }

        if ($this->withDetail) {
            $base['bio_longue'] = $profil->bio_longue;
        }

        return $base;
    }
}
