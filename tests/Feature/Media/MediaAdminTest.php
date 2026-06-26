<?php

declare(strict_types=1);

namespace Tests\Feature\Media;

use App\Filament\Resources\MediaResource\Pages\CreateMedia;
use App\Models\Media;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function admin(): User
    {
        return User::factory()->create(['type' => 'admin']);
    }

    public function test_creation_de_chaque_type_via_le_form(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        foreach (array_keys(Media::$types) as $type) {
            // fillForm déclenche afterStateUpdated : type=podcast→media_kind=audio (audio requis),
            // type=video→media_kind=youtube (embed requis). On fournit le média attendu.
            $extra = match ($type) {
                'podcast' => ['audio_file' => UploadedFile::fake()->create('demo.mp3', 100, 'audio/mpeg')],
                'video' => ['embed_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
                default => [],
            };

            Livewire::test(CreateMedia::class)
                ->fillForm(array_merge([
                    'titre' => 'Contenu '.$type,
                    'slug' => 'contenu-'.$type,
                    'type' => $type,
                    'status' => 'draft',
                ], $extra))
                ->call('create')
                ->assertHasNoFormErrors();

            $this->assertDatabaseHas('media', [
                'slug' => 'contenu-'.$type,
                'type' => $type,
                'status' => 'draft',
            ]);
        }
    }

    public function test_une_video_avec_embed_valide_est_enregistree(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateMedia::class)
            ->fillForm([
                'titre' => 'Ma vidéo',
                'slug' => 'ma-video',
                'type' => 'video',
                'status' => 'draft',
                'media_kind' => 'youtube',
                'embed_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('media', ['slug' => 'ma-video', 'media_kind' => 'youtube']);
    }

    public function test_une_embed_url_invalide_est_rejetee(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateMedia::class)
            ->fillForm([
                'titre' => 'Vidéo cassée',
                'slug' => 'video-cassee',
                'type' => 'video',
                'status' => 'draft',
                'media_kind' => 'youtube',
                'embed_url' => 'https://example.com/pas-une-video',
            ])
            ->call('create')
            ->assertHasFormErrors(['embed_url']);
    }

    public function test_bulk_publish_rend_visible_publiquement(): void
    {
        $draft = Media::factory()->draft()->create();
        $scheduled = Media::factory()->scheduled()->create();

        $this->assertSame(0, Media::published()->count());

        // Action utilisée par la bulk action « Publier ».
        $draft->publish();
        $scheduled->publish();

        $this->assertSame('published', $draft->fresh()->status);
        $this->assertNotNull($draft->fresh()->published_at);
        $this->assertSame(2, Media::published()->count());
    }

    public function test_visibilite_par_statut(): void
    {
        Media::factory()->published()->create();
        Media::factory()->draft()->create();
        Media::factory()->scheduled()->create();
        Media::factory()->archived()->create();

        $this->assertSame(1, Media::published()->count());
    }

    public function test_le_backoffice_est_protege_pour_un_anonyme(): void
    {
        $response = $this->get('/admin/media');

        $this->assertContains($response->status(), [302, 403, 401]);
    }
}
