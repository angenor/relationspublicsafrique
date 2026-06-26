<?php

declare(strict_types=1);

namespace Tests\Feature\Media;

use App\Models\Media;
use App\Models\NewsletterSubscriber;
use App\Notifications\NewMediaPublishedNotification;
use App\Notifications\NewsletterConfirmationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_l_inscription_cree_un_abonne_pending_et_envoie_la_confirmation(): void
    {
        Notification::fake();

        $this->post(route('media.newsletter.subscribe'), ['email' => 'nouveau@example.com'])
            ->assertRedirect();

        $subscriber = NewsletterSubscriber::where('email', 'nouveau@example.com')->first();
        $this->assertNotNull($subscriber);
        $this->assertSame('pending', $subscriber->status);

        Notification::assertSentTo($subscriber, NewsletterConfirmationNotification::class);
    }

    public function test_la_confirmation_via_url_signee_active_l_abonne(): void
    {
        $subscriber = NewsletterSubscriber::factory()->create();

        $url = URL::temporarySignedRoute('media.newsletter.confirm', now()->addDay(), ['token' => $subscriber->token]);

        $this->get($url)->assertOk();

        $this->assertSame('confirmed', $subscriber->fresh()->status);
        $this->assertNotNull($subscriber->fresh()->confirmed_at);
    }

    public function test_une_signature_invalide_est_refusee(): void
    {
        $subscriber = NewsletterSubscriber::factory()->create();

        $this->get(route('media.newsletter.confirm', ['token' => $subscriber->token]))
            ->assertForbidden();
    }

    public function test_la_publication_alerte_les_abonnes_confirmes(): void
    {
        Notification::fake();

        $confirme = NewsletterSubscriber::factory()->confirmed()->create();
        $enAttente = NewsletterSubscriber::factory()->create();

        $media = Media::factory()->scheduled()->create();
        $media->publish();

        Notification::assertSentTo($confirme, NewMediaPublishedNotification::class);
        Notification::assertNotSentTo($enAttente, NewMediaPublishedNotification::class);
    }

    public function test_pas_de_doublon_d_email(): void
    {
        $this->post(route('media.newsletter.subscribe'), ['email' => 'doublon@example.com']);
        $this->post(route('media.newsletter.subscribe'), ['email' => 'doublon@example.com']);

        $this->assertSame(1, NewsletterSubscriber::where('email', 'doublon@example.com')->count());
    }

    public function test_le_honeypot_rejette_les_bots(): void
    {
        Notification::fake();

        $this->post(route('media.newsletter.subscribe'), [
            'email' => 'bot@example.com',
            'website' => 'http://spam.example',
        ]);

        $this->assertSame(0, NewsletterSubscriber::where('email', 'bot@example.com')->count());
        Notification::assertNothingSent();
    }

    public function test_le_desabonnement_via_url_signee(): void
    {
        $subscriber = NewsletterSubscriber::factory()->confirmed()->create();

        $url = URL::signedRoute('media.newsletter.unsubscribe', ['token' => $subscriber->token]);

        $this->get($url)->assertOk();

        $this->assertSame('unsubscribed', $subscriber->fresh()->status);
    }
}
