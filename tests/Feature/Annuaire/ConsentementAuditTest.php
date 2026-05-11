<?php

declare(strict_types=1);

namespace Tests\Feature\Annuaire;

use App\Filament\Pages\AnnuaireConsentements;
use App\Models\Profil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsentementAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_peut_acceder_a_la_page_audit(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $this->actingAs($admin);

        $this->assertTrue($admin->can('viewAudit', Profil::class));
        $this->assertTrue(AnnuaireConsentements::canAccess());
    }

    public function test_editeur_ne_peut_pas_acceder_a_la_page_audit(): void
    {
        $editeur = User::factory()->create(['type' => 'editeur']);
        $this->actingAs($editeur);

        $this->assertFalse($editeur->can('viewAudit', Profil::class));
        $this->assertFalse(AnnuaireConsentements::canAccess());
    }
}
