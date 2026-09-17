<?php
declare(strict_types=1);

namespace Tags4All\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Panel de Administración y Ajustes de Tags4All
 */
class AdminManager
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function addAdminMenu(): void
    {
        add_options_page(
            'Tags4All Configuración',
            'Tags4All SEO',
            'manage_options',
            'tagsforall-settings',
            [$this, 'renderAdminPage']
        );
    }

    public function registerSettings(): void
    {
        register_setting('tagsforall_options_group', 'tagsforall_options', [
            'sanitize_callback' => [$this, 'sanitizeOptions']
        ]);
    }

    public function sanitizeOptions(array $input): array
    {
        $sanitized = [];
        $sanitized['enable_cache']         = !empty($input['enable_cache']);
        $sanitized['enable_webp']          = !empty($input['enable_webp']);
        $sanitized['compress_html']        = !empty($input['compress_html']);
        $sanitized['optimise_assets']      = !empty($input['optimise_assets']);
        $sanitized['remove_query_strings'] = !empty($input['remove_query_strings']);
        $sanitized['remove_emoji_junk']    = !empty($input['remove_emoji_junk']);
        $sanitized['schema_geo_aeo']       = !empty($input['schema_geo_aeo']);
        $sanitized['auto_sitemap']         = !empty($input['auto_sitemap']);
        
        $sanitized['ga4_measurement_id']   = sanitize_text_field($input['ga4_measurement_id'] ?? '');
        $sanitized['ga4_api_secret']       = sanitize_text_field($input['ga4_api_secret'] ?? '');
        $sanitized['meta_pixel_id']        = sanitize_text_field($input['meta_pixel_id'] ?? '');
        $sanitized['youtube_api_key']      = sanitize_text_field($input['youtube_api_key'] ?? '');
        $sanitized['hubspot_portal_id']    = sanitize_text_field($input['hubspot_portal_id'] ?? '');
        $sanitized['site_name']            = sanitize_text_field($input['site_name'] ?? '');
        $sanitized['author_name']          = sanitize_text_field($input['author_name'] ?? '');

        return $sanitized;
    }

    public function renderAdminPage(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $options = (array) get_option('tagsforall_options', []);
        $sitemap_file = ABSPATH . 'sitemap.xml';
        $sitemap_exists = file_exists($sitemap_file) && filesize($sitemap_file) > 0;
        $sitemap_url = home_url('/sitemap.xml');
        $url_count = get_option('tagsforall_sitemap_url_count', 0);
        $last_gen = get_option('tagsforall_sitemap_last_generated', 0);
        ?>
        <div class="wrap">
            <h1>🚀 Tags4All v2.0.0 — Ajustes de SEO, GEO, Sitemap, Caché y APIs</h1>
            
            <?php if (isset($_GET['sitemap_rebuilt'])): ?>
                <div class="notice notice-success is-dismissible">
                    <p><strong>✅ Sitemap Reconstruido:</strong> El archivo <code>sitemap.xml</code> ha sido generado exitosamente con <?php echo esc_html((string)$url_count); ?> URLs catalogadas.</p>
                </div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php
                settings_fields('tagsforall_options_group');
                wp_nonce_field('save_tagsforall_settings', 'tagsforall_nonce');
                ?>

                <h2>🗺️ Estado y Configuración de Sitemap.xml</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Sitemap.xml Automático</th>
                        <td>
                            <label>
                                <input type="checkbox" name="tagsforall_options[auto_sitemap]" value="1" <?php checked(!empty($options['auto_sitemap'])); ?>>
                                Generar y actualizar <code>sitemap.xml</code> automáticamente al instalar y al publicar contenidos.
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Estado del Sitemap</th>
                        <td>
                            <?php if ($sitemap_exists): ?>
                                <p><span style="color: #2e7d32; font-weight: bold;">🟢 Creado y Activo</span> — Contiene <strong><?php echo esc_html((string)$url_count); ?></strong> URLs catalogadas.</p>
                                <p><small>Última actualización: <?php echo $last_gen ? esc_html(date('Y-m-d H:i:s', $last_gen)) : 'Reciente'; ?></small></p>
                                <p>
                                    <a href="<?php echo esc_url($sitemap_url); ?>" target="_blank" class="button button-secondary">Ver sitemap.xml ↗</a>
                                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=tagsforall_rebuild_sitemap'), 'tagsforall_rebuild_sitemap_nonce')); ?>" class="button button-primary">🔄 Reconstruir Sitemap.xml Ahora</a>
                                </p>
                            <?php else: ?>
                                <p><span style="color: #c62828; font-weight: bold;">🔴 No Detectado</span> — No se encuentra el archivo físico en la raíz.</p>
                                <p>
                                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=tagsforall_rebuild_sitemap'), 'tagsforall_rebuild_sitemap_nonce')); ?>" class="button button-primary">⚡ Generar Sitemap.xml Ahora</a>
                                </p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

                <h2>⚡ Caché y Rendimiento</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Activar Caché Estática</th>
                        <td><input type="checkbox" name="tagsforall_options[enable_cache]" value="1" <?php checked(!empty($options['enable_cache'])); ?>></td>
                    </tr>
                    <tr>
                        <th scope="row">Minificar HTML</th>
                        <td><input type="checkbox" name="tagsforall_options[compress_html]" value="1" <?php checked(!empty($options['compress_html'])); ?>></td>
                    </tr>
                    <tr>
                        <th scope="row">Soporte WebP</th>
                        <td><input type="checkbox" name="tagsforall_options[enable_webp]" value="1" <?php checked(!empty($options['enable_webp'])); ?>></td>
                    </tr>
                </table>

                <h2>🔌 APIs Externas</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Google Analytics 4 Measurement ID</th>
                        <td><input type="text" name="tagsforall_options[ga4_measurement_id]" value="<?php echo esc_attr($options['ga4_measurement_id'] ?? ''); ?>" class="regular-text" placeholder="G-XXXXXXXXXX"></td>
                    </tr>
                    <tr>
                        <th scope="row">Meta / Facebook Pixel ID</th>
                        <td><input type="text" name="tagsforall_options[meta_pixel_id]" value="<?php echo esc_attr($options['meta_pixel_id'] ?? ''); ?>" class="regular-text" placeholder="1234567890"></td>
                    </tr>
                    <tr>
                        <th scope="row">HubSpot Portal ID</th>
                        <td><input type="text" name="tagsforall_options[hubspot_portal_id]" value="<?php echo esc_attr($options['hubspot_portal_id'] ?? ''); ?>" class="regular-text" placeholder="12345678"></td>
                    </tr>
                </table>

                <?php submit_button('Guardar Cambios'); ?>
            </form>
        </div>
        <?php
    }
}
