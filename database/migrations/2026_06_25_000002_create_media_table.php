<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table): void {
            $table->id();

            // Contenu éditorial
            $table->string('titre');
            $table->string('slug')->unique();
            $table->string('titre_normalise', 200)->nullable();
            $table->string('type', 20); // article | interview | podcast | video | reportage
            $table->text('chapo')->nullable();
            $table->longText('content')->nullable();
            $table->string('cover_image')->nullable();

            // Média (audio / vidéo embarquée)
            $table->string('media_kind', 16)->nullable(); // audio | youtube | vimeo
            $table->string('embed_url')->nullable();
            $table->string('audio_file')->nullable();
            $table->string('subtitles_path')->nullable();
            $table->boolean('audio_downloadable')->default(false);
            $table->unsignedInteger('duration')->nullable(); // secondes
            $table->unsignedInteger('reading_time')->nullable(); // minutes (observer)

            // Mise en avant éditoriale
            $table->boolean('featured')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->unsignedInteger('pinned_position')->nullable();

            // SEO / Open Graph
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('og_image')->nullable();

            // Publication
            $table->string('status', 16)->default('draft'); // draft | scheduled | published | archived
            $table->timestamp('published_at')->nullable();

            // Popularité / tendances
            $table->unsignedBigInteger('view')->default(0);
            $table->unsignedBigInteger('popularity_score')->default(0);

            // Série / playlist
            $table->foreignId('serie_id')->nullable()->constrained('media_series')->nullOnDelete();
            $table->unsignedInteger('saison')->nullable();
            $table->unsignedInteger('episode')->nullable();
            $table->unsignedInteger('serie_position')->nullable();

            // Référentiels — colonnes integer signées sans FK DB : les tables legacy
            // pays/users ont un id `int` (cf. profils.pays_id, idem sans FK enforced).
            $table->integer('pays_id')->nullable()->index();
            $table->integer('user_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            // Index (cf. data-model.md §Indices & performance)
            $table->index(['status', 'published_at'], 'idx_media_status_pub');
            $table->index('published_at', 'idx_media_pub');
            $table->index('titre_normalise', 'idx_media_titre_norm');
            $table->index('type', 'idx_media_type');
            $table->index('featured', 'idx_media_featured');
            $table->index('popularity_score', 'idx_media_popularity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
