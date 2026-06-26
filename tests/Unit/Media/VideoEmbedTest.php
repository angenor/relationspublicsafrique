<?php

declare(strict_types=1);

namespace Tests\Unit\Media;

use App\Helper\VideoEmbed;
use Tests\TestCase;

class VideoEmbedTest extends TestCase
{
    public function test_parse_youtube_watch(): void
    {
        $parsed = VideoEmbed::parse('https://www.youtube.com/watch?v=dQw4w9WgXcQ');

        $this->assertSame('youtube', $parsed['provider']);
        $this->assertSame('dQw4w9WgXcQ', $parsed['id']);
    }

    public function test_parse_youtu_be(): void
    {
        $parsed = VideoEmbed::parse('https://youtu.be/dQw4w9WgXcQ');

        $this->assertSame('youtube', $parsed['provider']);
        $this->assertSame('dQw4w9WgXcQ', $parsed['id']);
    }

    public function test_parse_vimeo(): void
    {
        $parsed = VideoEmbed::parse('https://vimeo.com/123456789');

        $this->assertSame('vimeo', $parsed['provider']);
        $this->assertSame('123456789', $parsed['id']);
    }

    public function test_parse_vimeo_player_url(): void
    {
        $parsed = VideoEmbed::parse('https://player.vimeo.com/video/987654321');

        $this->assertSame('vimeo', $parsed['provider']);
        $this->assertSame('987654321', $parsed['id']);
    }

    public function test_embed_url_youtube(): void
    {
        $this->assertSame(
            'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            VideoEmbed::embedUrl('https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
        );
    }

    public function test_iframe_est_responsive(): void
    {
        $html = VideoEmbed::iframe('https://youtu.be/dQw4w9WgXcQ', 'Ma vidéo');

        $this->assertStringContainsString('ratio-16x9', $html);
        $this->assertStringContainsString('<iframe', $html);
        $this->assertStringContainsString('youtube-nocookie.com/embed/dQw4w9WgXcQ', $html);
    }

    public function test_urls_invalides_renvoient_null(): void
    {
        $this->assertNull(VideoEmbed::parse('https://example.com/video'));
        $this->assertNull(VideoEmbed::parse(''));
        $this->assertNull(VideoEmbed::parse(null));
        $this->assertSame('', VideoEmbed::iframe('https://example.com/video'));
    }
}
