<?php

declare(strict_types=1);

namespace Tests\Feature\Projets;

use App\Models\Category;
use App\Models\Partenaire;
use App\Models\Projet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetailTest extends TestCase
{
    use RefreshDatabase;

    private function projetComplet(array $overrides = []): Projet
    {
        $projet = Projet::factory()->create(array_merge([
            'titre' => 'Projet vitrine complet',
            'slug' => 'projet-vitrine-complet',
            'resume' => 'Résumé du projet vitrine.',
            'contexte' => 'Le contexte détaillé du projet.',
            'objectifs' => 'Les objectifs mesurables.',
            'description' => '<p>La description riche du projet.</p>',
            'activites' => '<p>Les activités menées.</p>',
        ], $overrides));

        $cat = Category::factory()->create(['type' => 'projet', 'slug' => 'comm-inst', 'name' => 'Communication institutionnelle', 'online' => 1]);
        $projet->categories()->attach($cat->id, ['position' => 0]);

        $projet->resultats()->create(['libelle' => 'Bénéficiaires formés', 'valeur' => '1 200', 'unite' => 'personnes', 'position' => 0]);

        $partenaire = Partenaire::factory()->create(['nom' => 'Partenaire Démo', 'url' => 'https://exemple.org']);
        $projet->partenaires()->attach($partenaire->id, ['position' => 0]);

        $projet->medias()->create(['type' => 'image', 'chemin' => 'projets/galerie/photo.jpg', 'legende' => 'Une photo terrain', 'position' => 0]);
        $projet->medias()->create(['type' => 'video', 'url_embed' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'legende' => 'Vidéo de présentation', 'position' => 1]);

        return $projet;
    }

    public function test_le_detail_affiche_toutes_les_sections(): void
    {
        $projet = $this->projetComplet();
        $projet->temoignages()->create(['auteur' => 'Aïssata Diallo', 'fonction' => 'Coordinatrice', 'contenu' => 'Un projet marquant.', 'position' => 0]);

        $this->get('/projets/'.$projet->slug)
            ->assertOk()
            ->assertSee('Projet vitrine complet', false)
            ->assertSee('Le contexte détaillé du projet.', false)
            ->assertSee('Les objectifs mesurables.', false)
            ->assertSee('La description riche du projet.', false)
            ->assertSee('Les activités menées.', false)
            ->assertSee('Chiffres clés', false)
            ->assertSee('Bénéficiaires formés', false)
            ->assertSee('Partenaires', false)
            ->assertSee('Galerie', false)
            ->assertSee('Témoignages', false)
            ->assertSee('Aïssata Diallo', false);
    }

    public function test_la_galerie_affiche_images_et_videos(): void
    {
        $projet = $this->projetComplet();

        $response = $this->get('/projets/'.$projet->slug)->assertOk();
        // Image : déclencheur lightbox + source.
        $response->assertSee('data-projet-lightbox', false);
        $response->assertSee('projets/galerie/photo.jpg', false);
        // Vidéo : iframe d'embed produite par VideoEmbed.
        $response->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false);
    }

    public function test_la_section_temoignages_est_masquee_quand_aucun(): void
    {
        $projet = $this->projetComplet(['titre' => 'Projet sans temoignage', 'slug' => 'projet-sans-temoignage']);
        // Aucun témoignage créé.

        $this->get('/projets/'.$projet->slug)
            ->assertOk()
            ->assertSee('Galerie', false)
            ->assertDontSee('Témoignages', false);
    }

    public function test_le_bouton_nous_contacter_pointe_vers_contact_avec_le_projet(): void
    {
        $projet = $this->projetComplet();

        $this->get('/projets/'.$projet->slug)
            ->assertOk()
            ->assertSee(route('contact', ['projet' => $projet->slug]), false);
    }

    public function test_un_brouillon_ou_slug_inconnu_renvoie_404(): void
    {
        $brouillon = Projet::factory()->draft()->create(['slug' => 'brouillon-cache']);
        $programme = Projet::factory()->scheduled()->create(['slug' => 'programme-futur']);

        $this->get('/projets/'.$brouillon->slug)->assertNotFound();
        $this->get('/projets/'.$programme->slug)->assertNotFound();
        $this->get('/projets/slug-inexistant-xyz')->assertNotFound();
    }

    public function test_le_formulaire_de_contact_consomme_le_parametre_projet(): void
    {
        $projet = Projet::factory()->create(['titre' => 'Projet reference contact', 'slug' => 'projet-reference-contact']);

        $this->get('/contact?projet='.$projet->slug)
            ->assertOk()
            ->assertSee('Au sujet du projet', false) // note de référence affichée
            ->assertSee($projet->slug, false)        // référence portée (note + champ caché + snapshot)
            ->assertSee('Projet reference contact', false); // titre pré-rempli dans l'état du message
    }

    public function test_le_contact_sans_parametre_reste_inchange(): void
    {
        // Non-régression : aucune référence projet ne doit apparaître.
        $this->get('/contact')
            ->assertOk()
            ->assertDontSee('Au sujet du projet', false);
    }
}
