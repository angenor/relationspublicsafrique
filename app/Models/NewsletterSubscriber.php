<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * Abonné à la newsletter média (opt-in confirmé par URL signée).
 */
class NewsletterSubscriber extends Model
{
    use HasFactory;
    use Notifiable;

    protected $guarded = ['id'];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    public static array $statuses = [
        'pending' => 'En attente',
        'confirmed' => 'Confirmé',
        'unsubscribed' => 'Désabonné',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $subscriber): void {
            if (empty($subscriber->token)) {
                $subscriber->token = Str::random(64);
            }
        });
    }

    /** Canal mail : l'abonné reçoit sur son email. */
    public function routeNotificationForMail(): string
    {
        return $this->email;
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    public function confirm(): void
    {
        $this->update(['status' => 'confirmed', 'confirmed_at' => now()]);
    }

    public function unsubscribe(): void
    {
        $this->update(['status' => 'unsubscribed']);
    }
}
