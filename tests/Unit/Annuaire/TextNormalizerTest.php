<?php

declare(strict_types=1);

namespace Tests\Unit\Annuaire;

use App\Services\Annuaire\TextNormalizer;
use PHPUnit\Framework\TestCase;

final class TextNormalizerTest extends TestCase
{
    /** @dataProvider normalizationCases */
    public function test_normalize(?string $input, string $expected): void
    {
        $this->assertSame($expected, TextNormalizer::normalize($input));
    }

    public static function normalizationCases(): array
    {
        return [
            'null returns empty' => [null, ''],
            'empty stays empty' => ['', ''],
            'spaces only' => ['   ', ''],
            'simple lowercase' => ['Senegal', 'senegal'],
            'accents removed' => ['Sénégal', 'senegal'],
            'mixed case + accents' => ['CÔTE D\'IVOIRE', 'cote d ivoire'],
            'œ ligature' => ['Cœur', 'coeur'],
            'ñ' => ['España', 'espana'],
            'ü' => ['Müller', 'muller'],
            'punctuation collapsed' => ['Hello, World!', 'hello world'],
            'multiple spaces collapsed' => ['Jean   Dupont', 'jean dupont'],
            'trim whitespace' => ['  Paris  ', 'paris'],
            'digits preserved' => ['Abidjan 2024', 'abidjan 2024'],
        ];
    }
}
