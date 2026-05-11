<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfilUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'nom' => ['sometimes', 'required', 'string', 'max:120'],
            'prenom' => ['nullable', 'string', 'max:120'],
            'fonction' => ['nullable', 'string', 'max:180'],
            'organisation' => ['nullable', 'string', 'max:180'],
            'pays_id' => ['nullable', 'integer', 'exists:pays,id'],
            'nationalite' => ['nullable', 'string', 'max:120'],
            'ville' => ['nullable', 'string', 'max:120'],
            'type_profil' => ['sometimes', 'required', Rule::in(['expert', 'etudiant', 'alumni', 'partenaire', 'autre'])],
            'etat_publication' => ['nullable', Rule::in(['en_attente', 'publie', 'archive'])],
            'masquer_email' => ['boolean'],
            'masquer_tel' => ['boolean'],
            'email' => ['nullable', 'email', 'max:200'],
            'tel' => ['nullable', 'string', 'max:40'],
            'bio_courte' => ['nullable', 'string', 'max:300'],
            'bio_longue' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048', 'dimensions:min_width=400,min_height=400'],
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'domaines_expertise' => ['nullable', 'array'],
            'domaines_expertise.*' => ['integer', 'exists:domaines_expertise,id'],
            'tags' => ['nullable', 'array'],
            'liens_externes' => ['nullable', 'array'],
            'liens_externes.*.type' => ['required_with:liens_externes', Rule::in(['site', 'facebook', 'twitter', 'youtube', 'linkedin', 'instagram', 'autre'])],
            'liens_externes.*.url' => ['required_with:liens_externes', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.dimensions' => 'La photo doit faire au moins 400×400 pixels.',
            'image.mimes' => 'La photo doit être au format JPEG, PNG ou WebP (le GIF n\'est pas accepté).',
            'image.max' => 'La photo ne doit pas dépasser 2 Mo.',
        ];
    }
}
