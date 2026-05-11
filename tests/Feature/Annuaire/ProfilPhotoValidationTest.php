<?php

declare(strict_types=1);

namespace Tests\Feature\Annuaire;

use App\Http\Requests\ProfilStoreRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ProfilPhotoValidationTest extends TestCase
{
    use RefreshDatabase;

    private function rules(): array
    {
        return (new ProfilStoreRequest())->rules();
    }

    private function validate(array $data): \Illuminate\Validation\Validator
    {
        return Validator::make($data, $this->rules());
    }

    public function test_photo_trop_grosse_est_rejetee(): void
    {
        $rules = $this->rules();
        $this->assertContains('max:2048', $rules['image']);
    }

    public function test_format_gif_est_rejete(): void
    {
        $rules = $this->rules();
        $mimesRule = collect($rules['image'])->first(fn ($r) => is_string($r) && str_starts_with($r, 'mimes:'));
        $this->assertStringNotContainsString('gif', strtolower((string) $mimesRule));
    }

    public function test_formats_jpg_png_webp_sont_acceptes(): void
    {
        $rules = $this->rules();
        $mimesRule = collect($rules['image'])->first(fn ($r) => is_string($r) && str_starts_with($r, 'mimes:'));
        $rule = strtolower((string) $mimesRule);
        foreach (['jpeg', 'png', 'webp'] as $ext) {
            $this->assertStringContainsString($ext, $rule, "Le format {$ext} doit être autorisé.");
        }
    }

    public function test_dimensions_minimales_sont_400x400(): void
    {
        $rules = $this->rules();
        $dimensionsRule = collect($rules['image'])->first(fn ($r) => is_string($r) && str_starts_with($r, 'dimensions:'));
        $this->assertStringContainsString('min_width=400', (string) $dimensionsRule);
        $this->assertStringContainsString('min_height=400', (string) $dimensionsRule);
    }
}
