<?php
/**
 * Plugin Name: Tags4All - WP Plugin for SEO, GEO & API Integration
 * Plugin URI: https://github.com/arturo21/tagsforall_wp
 * Description: Plugin avanzado de soporte SEO, GEO (Generative Engine Optimization), AEO (Answer Engine Optimization), metadatos dinámicos, caché integrada y conexión con APIs externas (GA4, Meta, YouTube, HubSpot).
 * Version: 2.0.0
 * Author: Arturo Vásquez
 * Author URI: https://github.com/arturo21
 * License: GPL-2.0+
 * Text Domain: tagsforall
 * Requires PHP: 8.2
 * Requires at least: 6.5
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('TAGSFORALL_VERSION', '2.0.0');
define('TAGSFORALL_PATH', plugin_dir_path(__FILE__));
define('TAGSFORALL_URL', plugin_dir_url(__FILE__));

// Carga de módulos principales
require_once TAGSFORALL_PATH . 'class-tagsforall-cache.php';
require_once TAGSFORALL_PATH . 'class-tagsforall-seo-geo.php';
require_once TAGSFORALL_PATH . 'class-tagsforall-performance.php';
require_once TAGSFORALL_PATH . 'class-tagsforall-api-integrations.php';
require_once TAGSFORALL_PATH . 'class-tagsforall-admin.php';

// Inicialización de componentes del plugin
function run_tagsforall(): void {
    new \Tags4All\Includes\CacheManager();
    new \Tags4All\Includes\SeoGeoManager();
    new \Tags4All\Includes\PerformanceManager();
    new \Tags4All\Includes\ApiIntegrationsManager();
    
    if (is_admin()) {
        new \Tags4All\Admin\AdminManager();
    }
}
run_tagsforall();

// Hook de activación con inicialización defensiva de opciones
register_activation_hook(__FILE__, function(): void {
    $default_options = [
        'enable_cache'         => true,
        'enable_webp'          => true,
        'compress_html'        => true,
        'optimise_assets'      => true,
        'remove_query_strings' => true,
        'remove_emoji_junk'    => true,
        'schema_geo_aeo'       => true,
        'ga4_measurement_id'   => '',
        'ga4_api_secret'       => '',
        'meta_pixel_id'        => '',
        'youtube_api_key'      => '',
        'hubspot_portal_id'    => '',
        'site_name'            => get_bloginfo('name'),
        'author_name'          => ''
    ];

    $existing_options = get_option('tagsforall_options', []);
    $merged_options   = array_merge($default_options, is_array($existing_options) ? $existing_options : []);
    update_option('tagsforall_options', $merged_options);
});
