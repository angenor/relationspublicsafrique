<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestProfilStore extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'nom' => ['required', 'min:2'],
            'prenom' => ['required', 'min:2'],
            'title' => ['nullable', 'min:2'],
            'fonction' => ['nullable', 'min:2'],
            'domaine' => ['nullable', 'min:2'],
            'facebook' => ['nullable', 'url'],
            'twitter' => ['nullable', 'url'],
            'youtube' => ['nullable', 'url'],
            'linkding' => ['nullable', 'url'],
            'site' => ['nullable', 'url'],
            'contact' => ['nullable'],
            'adresse' => ['nullable'],
            'tel' => ['nullable', 'min:8'],
            'email' => ['required', 'email'],
            'bio' => ['nullable', 'min:5'],
            'online' => ['nullable', 'boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'online.required' => 'Le profil est obligatoire',
        ];
    }
}
