<?php
declare(strict_types=1);

namespace Tags4All\Includes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Gestor de Sitemap XML Automático para Tags4All
 */
class SitemapManager
{
    private string $sitemap_path;
    private string $sitemap_url;

    public function __construct()
    {
        $this->sitemap_path = ABSPATH . 'sitemap.xml';
        $this->sitemap_url  = home_url('/sitemap.xml');

        // Hook de verificación inicial al cargar
        add_action('init', [$this, 'checkAndCreateSitemap']);
        add_action('init', [$this, 'addSitemapRewriteRules']);
        add_filter('query_vars', [$this, 'addSitemapQueryVars']);
        add_action('template_redirect', [$this, 'renderVirtualSitemap']);

        // Actualización automática al publicar/editar contenido
        add_action('save_post', [$this, 'regenerateSitemapOnContentChange'], 10, 2);
        add_action('delete_post', [$this, 'generateSitemapXml']);

        // Añadir directiva en robots.txt
        add_filter('robots_txt', [$this, 'appendSitemapToRobots'], 10, 2);

        // Notificación en WP Admin si se creó tras la instalación
        add_action('admin_notices', [$this, 'displaySitemapNotice']);
        add_action('admin_post_tagsforall_rebuild_sitemap', [$this, 'handleManualRebuild']);
    }

    /**
     * Comprueba si sitemap.xml existe físicamente o está registrado. Si no, lo crea de inmediato.
     */
    public function checkAndCreateSitemap(): void
    {
        // Si no existe el archivo sitemap.xml en la raíz de WordPress, se genera automáticamente
        if (!file_exists($this->sitemap_path) || filesize($this->sitemap_path) === 0) {
            $this->generateSitemapXml();
            update_option('tagsforall_sitemap_created_on_install', true);
        }
    }

    /**
     * Genera el archivo XML del Sitemap con todos los contenidos públicos
     */
    public function generateSitemapXml(): string
    {
        $urls = [];

        // 1. Página de Inicio (Home)
        $urls[] = [
            'loc'        => home_url('/'),
            'lastmod'    => date('Y-m-d\TH:i:s+00:00'),
            'changefreq' => 'daily',
            'priority'   => '1.0'
        ];

        // 2. Entradas, Páginas y CPTs Públicos
        $post_types = get_post_types(['public' => true], 'names');
        unset($post_types['attachment']);

        $posts = get_posts([
            'post_type'      => array_values($post_types),
            'post_status'    => 'publish',
            'posts_per_page' => 1000,
            'orderby'        => 'modified',
            'order'          => 'DESC'
        ]);

        foreach ($posts as $post) {
            $permalink = get_permalink($post->ID);
            if (!$permalink) {
                continue;
            }

            $modified_date = get_post_modified_time('Y-m-d\TH:i:s+00:00', true, $post->ID);
            $priority      = ($post->post_type === 'page') ? '0.8' : '0.7';

            $urls[] = [
                'loc'        => $permalink,
                'lastmod'    => $modified_date ?: date('Y-m-d\TH:i:s+00:00'),
                'changefreq' => 'weekly',
                'priority'   => $priority
            ];
        }

        // 3. Categorías y Taxonomías
        $categories = get_categories(['hide_empty' => true]);
        foreach ($categories as $cat) {
            $cat_link = get_category_link($cat->term_id);
            if ($cat_link) {
                $urls[] = [
                    'loc'        => $cat_link,
                    'lastmod'    => date('Y-m-d\TH:i:s+00:00'),
                    'changefreq' => 'weekly',
                    'priority'   => '0.5'
                ];
            }
        }

        // Construcción del documento XML
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<!-- Sitemap.xml generado por Tags4All v2.0 -->' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $item) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . esc_url($item['loc']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . esc_html($item['lastmod']) . '</lastmod>' . "\n";
            $xml .= '    <changefreq>' . esc_html($item['changefreq']) . '</changefreq>' . "\n";
            $xml .= '    <priority>' . esc_html($item['priority']) . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        // Guardar físicamente si la raíz es escribible
        if (is_writable(ABSPATH) || (file_exists($this->sitemap_path) && is_writable($this->sitemap_path))) {
            @file_put_contents($this->sitemap_path, $xml);
        }

        update_option('tagsforall_sitemap_last_generated', time());
        update_option('tagsforall_sitemap_url_count', count($urls));

        return $xml;
    }

    /**
     * Regenera el sitemap cuando se publica o actualiza contenido
     */
    public function regenerateSitemapOnContentChange(int $post_id, \WP_Post $post): void
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if ($post->post_status === 'publish') {
            $this->generateSitemapXml();
        }
    }

    /**
     * Reglas de reescritura virtuales para servirse dinámicamente si no existe el archivo físico
     */
    public function addSitemapRewriteRules(): void
    {
        add_rewrite_rule('^sitemap\.xml$', 'index.php?tagsforall_sitemap=1', 'top');
    }

    public function addSitemapQueryVars(array $vars): array
    {
        $vars[] = 'tagsforall_sitemap';
        return $vars;
    }

    public function renderVirtualSitemap(): void
    {
        if (get_query_var('tagsforall_sitemap') == 1) {
            header('Content-Type: application/xml; charset=utf-8');
            echo $this->generateSitemapXml();
            exit;
        }
    }

    /**
     * Inyecta la referencia al sitemap en robots.txt
     */
    public function appendSitemapToRobots(string $output, bool $public): string
    {
        if ($public) {
            $output .= "\n# Sitemap añadido por Tags4All\n";
            $output .= "Sitemap: " . esc_url($this->sitemap_url) . "\n";
        }
        return $output;
    }

    /**
     * Notificación en WP Admin avisando que se ha creado/verificado el sitemap
     */
    public function displaySitemapNotice(): void
    {
        if (get_option('tagsforall_sitemap_created_on_install')) {
            $url_count = get_option('tagsforall_sitemap_url_count', 0);
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p><strong>🗺️ Sitemap.xml — Tags4All:</strong> Se ha verificado e instalado automáticamente el archivo <code>sitemap.xml</code> en tu sitio con ' . esc_html((string)$url_count) . ' URLs catalogadas.</p>';
            echo '<p><a href="' . esc_url($this->sitemap_url) . '" target="_blank" class="button button-secondary">Ver Sitemap.xml</a></p>';
            echo '</div>';

            delete_option('tagsforall_sitemap_created_on_install');
        }
    }

    /**
     * Acción para reconstruir manualmente el Sitemap desde el panel admin
     */
    public function handleManualRebuild(): void
    {
        if (!current_user_can('manage_options') || !check_admin_referer('tagsforall_rebuild_sitemap_nonce')) {
            wp_die(__('Acceso no autorizado', 'tagsforall'));
        }

        $this->generateSitemapXml();
        wp_redirect(admin_url('options-general.php?page=tagsforall-settings&sitemap_rebuilt=1'));
        exit;
    }
}
