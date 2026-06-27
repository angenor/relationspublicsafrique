<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projets', function (Blueprint $table): void {
            $table->id();

            // Identité & card
            $table->string('titre');
            $table->string('slug')->unique();
            $table->string('titre_normalise', 200)->nullable();
            $table->string('resume', 280)->nullable(); // brève description card (FR-002)
            $table->string('visuel_card')->nullable();      // projets/cards/…
            $table->string('visuel_principal')->nullable(); // projets/principal/…

            // Contenu détaillé (FR-010)
            $table->text('contexte')->nullable();
            $table->text('objectifs')->nullable();
            $table->longText('description')->nullable();
            $table->longText('activites')->nullable();

            // Statut métier (badge) — enum applicatif, colonne string (compat SQLite).
            $table->string('statut', 20)->default('en_developpement'); // actif | realise | en_developpement

            // Publication (distincte du statut métier — R4)
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();

            // Zone géographique (R3) — pays_id : colonne integer sans FK DB (legacy `pays.id` int).
            $table->integer('pays_id')->nullable()->index();
            $table->string('portee', 16)->default('pays'); // pays | regional | continental
            $table->string('zone_libelle')->nullable();

            // Mise en avant / ordre (FR-025)
            $table->boolean('featured')->default(false);
            $table->integer('position')->default(0);

            // SEO (optionnel)
            $table->string('meta_titre')->nullable();
            $table->string('meta_description', 500)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index (cf. data-model.md §1)
            $table->index('titre_normalise', 'idx_projets_titre_norm');
            $table->index('is_published', 'idx_projets_published');
            $table->index('position', 'idx_projets_position');
            $table->index(['is_published', 'published_at'], 'idx_projets_pub_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};
