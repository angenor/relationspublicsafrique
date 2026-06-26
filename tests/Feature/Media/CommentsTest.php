<?php

declare(strict_types=1);

namespace Tests\Feature\Media;

use App\Models\Media;
use App\Models\MediaComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_commentaire_soumis_est_en_attente_et_non_visible(): void
    {
        $media = Media::factory()->published()->create();

        $this->post(route('media.comments.store', ['id' => $media->id]), [
            'author_name' => 'Jean Dupont',
            'author_email' => 'jean@example.com',
            'body' => 'Texte du commentaire en attente',
        ])->assertRedirect();

        $this->assertDatabaseHas('media_comments', [
            'media_id' => $media->id,
            'author_name' => 'Jean Dupont',
            'status' => 'pending',
        ]);

        $this->get($media->link)
            ->assertOk()
            ->assertDontSee('Texte du commentaire en attente', false);
    }

    public function test_un_commentaire_approuve_devient_visible(): void
    {
        $media = Media::factory()->published()->create();
        $comment = MediaComment::factory()->create([
            'media_id' => $media->id,
            'body' => 'Commentaire bientot approuve',
            'status' => 'pending',
        ]);

        $comment->approve();

        $this->get($media->link)
            ->assertOk()
            ->assertSee('Commentaire bientot approuve', false);
    }

    public function test_un_commentaire_rejete_reste_masque(): void
    {
        $media = Media::factory()->published()->create();
        $comment = MediaComment::factory()->create([
            'media_id' => $media->id,
            'body' => 'Commentaire indesirable',
            'status' => 'pending',
        ]);

        $comment->reject();

        $this->get($media->link)
            ->assertOk()
            ->assertDontSee('Commentaire indesirable', false);
    }

    public function test_le_honeypot_rejette_les_bots(): void
    {
        $media = Media::factory()->published()->create();

        $this->post(route('media.comments.store', ['id' => $media->id]), [
            'author_name' => 'Bot Spammeur',
            'author_email' => 'bot@example.com',
            'body' => 'Du spam',
            'website' => 'http://spam.example',
        ]);

        $this->assertDatabaseMissing('media_comments', ['author_name' => 'Bot Spammeur']);
    }

    public function test_la_validation_des_champs_requis(): void
    {
        $media = Media::factory()->published()->create();

        $this->post(route('media.comments.store', ['id' => $media->id]), [
            'author_name' => '',
            'author_email' => 'pas-un-email',
            'body' => '',
        ])->assertSessionHasErrors(['author_name', 'author_email', 'body']);

        $this->assertSame(0, MediaComment::count());
    }
}
