<?php
declare(strict_types=1);

namespace Tags4All\Includes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Gestión de Metadatos y Schema Markup para GEO & AEO
 */
class SeoGeoManager
{
    private array $options;

    public function __construct()
    {
        $this->options = (array) get_option('tagsforall_options', []);
        add_action('wp_head', [$this, 'injectMetaData'], 1);
        add_action('wp_head', [$this, 'injectSchemaGeoAeo'], 2);
    }

    public function injectMetaData(): void
    {
        if (is_singular()) {
            global $post;
            $title       = get_the_title($post);
            $description = has_excerpt($post) ? get_the_excerpt($post) : wp_strip_all_tags(mb_substr($post->post_content ?? '', 0, 160));
            $url         = get_permalink($post);
            $site_name   = $this->options['site_name'] ?? get_bloginfo('name');

            echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
            echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
            echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
            echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
            echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
            echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        }
    }

    public function injectSchemaGeoAeo(): void
    {
        if (empty($this->options['schema_geo_aeo'])) {
            return;
        }

        $graph = [];

        if (is_singular('post')) {
            global $post;
            $graph[] = [
                '@type'       => 'Article',
                '@id'         => get_permalink($post) . '#article',
                'headline'    => get_the_title($post),
                'description' => wp_strip_all_tags(mb_substr($post->post_content ?? '', 0, 160)),
                'author'      => [
                    '@type' => 'Person',
                    'name'  => $this->options['author_name'] ?: get_the_author_meta('display_name', (int)$post->post_author)
                ],
                'publisher'   => [
                    '@type' => 'Organization',
                    'name'  => $this->options['site_name'] ?: get_bloginfo('name'),
                    'url'   => home_url()
                ]
            ];
        }

        if (!empty($graph)) {
            $schema = [
                '@context' => 'https://schema.org',
                '@graph'   => $graph
            ];
            echo '<script type="application/ld+json">' . "\n";
            echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            echo "\n" . '</script>' . "\n";
        }
    }
}
