<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class LomeTourRegistration extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'prenom',
        'nom',
        'statut',
        'fonction',
        'telephone_whatsapp',
        'photo_professionnelle',
        'email',
        'notes',
        'status',
        'registered_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    public const STATUTS = [
        'etudiant' => 'Étudiant',
        'professionnel' => 'Professionnel',
    ];

    public const STATUS = [
        'pending' => 'En attente',
        'confirmed' => 'Confirmé',
        'rejected' => 'Rejeté',
    ];

    public function getStatutLabelAttribute(): string
    {
        $statut = (string) ($this->statut ?? '');

        return self::STATUTS[$statut] ?? $statut;
    }

    public function getStatusLabelAttribute(): string
    {
        $status = (string) ($this->status ?? 'pending');

        return self::STATUS[$status] ?? $status;
    }

    public function getFullNameAttribute(): string
    {
        return $this->prenom.' '.$this->nom;
    }
}
