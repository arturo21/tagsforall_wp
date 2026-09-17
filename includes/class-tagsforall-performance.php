<?php
declare(strict_types=1);

namespace Tags4All\Includes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Optimización de Rendimiento, WebP, Minificación HTML y Core Web Vitals
 */
class PerformanceManager
{
    private array $options;

    public function __construct()
    {
        $this->options = (array) get_option('tagsforall_options', []);

        if (!empty($this->options['enable_webp'])) {
            add_filter('upload_mimes', [$this, 'enableWebpMimeType']);
        }

        if (!empty($this->options['remove_query_strings'])) {
            add_filter('script_loader_src', [$this, 'removeQueryStrings'], 15);
            add_filter('style_loader_src', [$this, 'removeQueryStrings'], 15);
        }

        if (!empty($this->options['remove_emoji_junk'])) {
            add_action('init', [$this, 'removeEmojiJunk']);
        }

        if (!empty($this->options['compress_html']) && !is_admin()) {
            add_action('template_redirect', [$this, 'startHtmlCompression'], 1);
        }
    }

    public function enableWebpMimeType(array $mimes): array
    {
        $mimes['webp'] = 'image/webp';
        return $mimes;
    }

    public function removeQueryStrings(string $src): string
    {
        if (str_contains($src, '?ver=') || str_contains($src, '&ver=')) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }

    public function removeEmojiJunk(): void
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    }

    public function startHtmlCompression(): void
    {
        ob_start([$this, 'compressHtmlOutput']);
    }

    public function compressHtmlOutput(string $html): string
    {
        $protected_blocks = [];
        $placeholder_prefix = '___TAGSFORALL_PROTECTED_BLOCK_';

        $html = preg_replace_callback(
            '/<(script|style|pre)[^>]*>.*?<\/\1>/is',
            function ($matches) use (&$protected_blocks, $placeholder_prefix) {
                $key = $placeholder_prefix . count($protected_blocks) . '___';
                $protected_blocks[$key] = $matches[0];
                return $key;
            },
            $html
        );

        $html = preg_replace('/<!--(?!\s*\[if)[\s\S]*?-->/', '', $html);
        $html = preg_replace('/>\s+/', '>', $html);
        $html = preg_replace('/\s+</', '<', $html);
        $html = preg_replace('/\s+/', ' ', $html);

        return str_replace(array_keys($protected_blocks), array_values($protected_blocks), $html);
    }
}
