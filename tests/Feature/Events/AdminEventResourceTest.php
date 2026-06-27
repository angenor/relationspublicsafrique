<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Filament\Resources\EventResource\Pages\CreateEvent;
use App\Filament\Resources\EventResource\Pages\ListEvents;
use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminEventResourceTest extends TestCase
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

    private function eventCategory(): Category
    {
        return Category::factory()->create(['type' => 'event', 'name' => 'Conférence', 'slug' => 'conference-adm', 'online' => 1]);
    }

    public function test_creation_complete_publiee_rend_visible_cote_public(): void
    {
        $this->actingAs($this->admin());
        $cat = $this->eventCategory();
        $organisateur = User::factory()->create();

        Livewire::test(CreateEvent::class)
            ->fillForm([
                'title' => 'Sommet RP back-office',
                'slug' => 'sommet-rp-back-office',
                'description' => '<p>Contenu détaillé du sommet.</p>',
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(11),
                'status' => 'published',
                'registration_mode' => 'internal',
                'category_id' => $cat->id,
                'user_id' => $organisateur->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('events', [
            'slug' => 'sommet-rp-back-office',
            'status' => 'published',
        ]);

        $event = Event::where('slug', 'sommet-rp-back-office')->firstOrFail();
        $this->get(route('events.show', ['slug' => $event->slug, 'id' => $event->id]))
            ->assertOk()
            ->assertSee('Sommet RP back-office');
    }

    public function test_action_publier_puis_depublier_reflete_le_public(): void
    {
        $this->actingAs($this->admin());
        $event = Event::factory()->upcoming()->draft()->create(['slug' => 'a-publier']);

        Livewire::test(ListEvents::class)->callTableAction('publish', $event);
        $event->refresh();
        $this->assertSame('published', $event->status);
        $this->assertSame(1, Event::published()->where('slug', 'a-publier')->count());

        Livewire::test(ListEvents::class)->callTableAction('unpublish', $event);
        $event->refresh();
        $this->assertSame('draft', $event->status);
        $this->assertSame(0, Event::published()->where('slug', 'a-publier')->count());
    }

    public function test_inscription_externe_sans_url_est_rejetee(): void
    {
        $this->actingAs($this->admin());
        $cat = $this->eventCategory();
        $organisateur = User::factory()->create();

        Livewire::test(CreateEvent::class)
            ->fillForm([
                'title' => 'Externe sans lien',
                'slug' => 'externe-sans-lien',
                'description' => '<p>x</p>',
                'start_date' => now()->addDays(5),
                'end_date' => now()->addDays(6),
                'status' => 'published',
                'registration_mode' => 'external',
                'registration_url' => null,
                'category_id' => $cat->id,
                'user_id' => $organisateur->id,
            ])
            ->call('create')
            ->assertHasFormErrors(['registration_url']);
    }

    public function test_un_media_replay_sur_un_clos_bascule_le_cta_public(): void
    {
        $event = Event::factory()->past()->create(['slug' => 'clos-avec-replay']);
        $event->medias()->create([
            'type' => 'replay',
            'url_embed' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'position' => 0,
        ]);

        $this->get(route('events.show', ['slug' => $event->slug, 'id' => $event->id]))
            ->assertOk()
            ->assertSee('Voir replay / photos / documents');
    }

    public function test_le_backoffice_est_protege_pour_un_anonyme(): void
    {
        $this->assertContains($this->get('/admin/events')->status(), [302, 403, 401]);
        $this->assertContains($this->get('/admin/events/create')->status(), [302, 403, 401]);
    }
}
