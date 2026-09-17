<?php
declare(strict_types=1);

namespace Tags4All\Includes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Gestión Integrada de Caché Nativa y Detección de Conflictos
 */
class CacheManager
{
    private const KNOWN_CACHE_PLUGINS = [
        'wp-rocket/wp-rocket.php'             => 'WP Rocket',
        'w3-total-cache/w3-total-cache.php'   => 'W3 Total Cache',
        'wp-super-cache/wp-cache.php'         => 'WP Super Cache',
        'litespeed-cache/litespeed-cache.php' => 'LiteSpeed Cache',
        'wp-fastest-cache/wpFastestCache.php' => 'WP Fastest Cache',
        'autoptimize/autoptimize.php'         => 'Autoptimize',
        'sg-cachepress/sg-cachepress.php'     => 'SiteGround Speed Optimizer',
    ];

    public function __construct()
    {
        add_action('admin_notices', [$this, 'checkConflictingPlugins']);
        add_action('save_post', [$this, 'purgeCache']);
        add_action('wp_before_admin_bar_render', [$this, 'addAdminBarPurgeButton']);
    }

    public function checkConflictingPlugins(): void
    {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        $active_plugins = (array) get_option('active_plugins', []);
        $detected_plugins = [];

        foreach (self::KNOWN_CACHE_PLUGINS as $plugin_path => $plugin_name) {
            if (in_array($plugin_path, $active_plugins, true)) {
                $detected_plugins[] = $plugin_name;
            }
        }

        if (!empty($detected_plugins)) {
            $plugin_list = implode(', ', array_map('esc_html', $detected_plugins));
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p><strong>⚠️ Advertencia de Rendimiento - Tags4All:</strong> Se han detectado los siguientes plugins de caché activos: <strong>' . $plugin_list . '</strong>.</p>';
            echo '<p>Tags4All incluye su propio motor de caché y optimización de assets. Mantener múltiples sistemas de caché activos puede generar conflictos de renderizado. Te recomendamos desactivar o desinstalar los otros plugins de caché.</p>';
            echo '</div>';
        }
    }

    public function purgeCache(): bool
    {
        $cache_dir = WP_CONTENT_DIR . '/cache/tagsforall/';
        if (!is_dir($cache_dir)) {
            return true;
        }

        $files = glob($cache_dir . '*');
        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
        return true;
    }

    public function addAdminBarPurgeButton(): void
    {
        global $wp_admin_bar;
        if (!current_user_can('manage_options') || !is_object($wp_admin_bar)) {
            return;
        }

        $wp_admin_bar->add_node([
            'id'    => 'tagsforall_purge_cache',
            'title' => '🧹 Limpiar Caché Tags4All',
            'href'  => wp_nonce_url(admin_url('admin-post.php?action=tagsforall_purge_cache'), 'tagsforall_purge_nonce'),
        ]);
    }
}
