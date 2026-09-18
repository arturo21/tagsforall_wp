<?php
declare(strict_types=1);

namespace Tags4All\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Panel de Administración y Ajustes con Navegación por Pestañas (Tabs)
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
        
        $sanitized['ga4_measurement_id']   = sanitize_text_field($input['ga4_measurement_id'] ?? '');
        $sanitized['ga4_api_secret']       = sanitize_text_field($input['ga4_api_secret'] ?? '');
        $sanitized['meta_pixel_id']        = sanitize_text_field($input['meta_pixel_id'] ?? '');
        $sanitized['youtube_api_key']      = sanitize_text_field($input['youtube_api_key'] ?? '');
        $sanitized['hubspot_portal_id']    = sanitize_text_field($input['hubspot_portal_id'] ?? '');
        $sanitized['site_name']            = sanitize_text_field($input['site_name'] ?? '');
        $sanitized['author_name']          = sanitize_text_field($input['author_name'] ?? '');
        $sanitized['image_quality']        = isset($input['image_quality']) ? max(10, min(100, (int)$input['image_quality'])) : 82;
        $sanitized['auto_optimize_images'] = !empty($input['auto_optimize_images']);

        return $sanitized;
    }

    public function renderAdminPage(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $active_tab = sanitize_text_field($_GET['tab'] ?? 'general');
        $options    = (array) get_option('tagsforall_options', []);

        ?>
        <div class="wrap tagsforall-admin-wrap">
            <h1>🚀 Tags4All v2.0.0 — SEO, GEO, Caché & Suite de Herramientas</h1>
            <p class="description">Gestión integral de optimización para buscadores tradicionales, motores de respuesta con IA (GEO/AEO), rendimiento y APIs.</p>

            <h2 class="nav-tab-wrapper">
                <a href="?page=tagsforall-settings&tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">⚙️ General & WPO</a>
                <a href="?page=tagsforall-settings&tab=cache" class="nav-tab <?php echo $active_tab === 'cache' ? 'nav-tab-active' : ''; ?>">⚡ Caché Estática</a>
                <a href="?page=tagsforall-settings&tab=apis" class="nav-tab <?php echo $active_tab === 'apis' ? 'nav-tab-active' : ''; ?>">🔌 APIs Externas</a>
                <a href="?page=tagsforall-settings&tab=image-optimizer" class="nav-tab <?php echo $active_tab === 'image-optimizer' ? 'nav-tab-active' : ''; ?>">🖼️ Optimizador de Imágenes</a>
                <a href="?page=tagsforall-settings&tab=sitemap" class="nav-tab <?php echo $active_tab === 'sitemap' ? 'nav-tab-active' : ''; ?>">🗺️ Mapa de Sitio XML</a>
                <a href="?page=tagsforall-settings&tab=broken-links" class="nav-tab <?php echo $active_tab === 'broken-links' ? 'nav-tab-active' : ''; ?>">🔗 Enlaces Rotos</a>
            </h2>

            <div class="tagsforall-tab-content" style="margin-top: 20px; background: #fff; padding: 20px; border: 1px solid #ccc; border-radius: 6px;">
                <?php if ($active_tab === 'general'): ?>
                    <form method="post" action="options.php">
                        <?php settings_fields('tagsforall_options_group'); wp_nonce_field('save_tagsforall_settings', 'tagsforall_nonce'); ?>
                        <h3>🏷️ Configuración de Marca & EEAT 2.0</h3>
                        <table class="form-table">
                            <tr>
                                <th scope="row">Nombre del Sitio / Marca</th>
                                <td><input type="text" name="tagsforall_options[site_name]" value="<?php echo esc_attr($options['site_name'] ?? get_bloginfo('name')); ?>" class="regular-text"></td>
                            </tr>
                            <tr>
                                <th scope="row">Nombre del Autor Principal (EEAT)</th>
                                <td><input type="text" name="tagsforall_options[author_name]" value="<?php echo esc_attr($options['author_name'] ?? ''); ?>" class="regular-text" placeholder="Ej. Arturo Vásquez"></td>
                            </tr>
                            <tr>
                                <th scope="row">Marcado Semántico GEO & AEO (JSON-LD)</th>
                                <td><label><input type="checkbox" name="tagsforall_options[schema_geo_aeo]" value="1" <?php checked(!empty($options['schema_geo_aeo'])); ?>> Generar esquemas de Inteligencia Artificial (Article, FAQPage, Person, Organization)</label></td>
                            </tr>
                        </table>

                        <h3>🚀 Optimización de Rendimiento (Core Web Vitals)</h3>
                        <table class="form-table">
                            <tr>
                                <th scope="row">Minificar HTML</th>
                                <td><label><input type="checkbox" name="tagsforall_options[compress_html]" value="1" <?php checked(!empty($options['compress_html'])); ?>> Comprimir HTML de salida aislando bloques script/style</label></td>
                            </tr>
                            <tr>
                                <th scope="row">Soporte MIME WebP</th>
                                <td><label><input type="checkbox" name="tagsforall_options[enable_webp]" value="1" <?php checked(!empty($options['enable_webp'])); ?>> Habilitar soporte nativo para imágenes .webp en la biblioteca</label></td>
                            </tr>
                            <tr>
                                <th scope="row">Remover Query Strings (?ver=)</th>
                                <td><label><input type="checkbox" name="tagsforall_options[remove_query_strings]" value="1" <?php checked(!empty($options['remove_query_strings'])); ?>> Limpiar parámetros de versión en CSS y JS para CDNs</label></td>
                            </tr>
                            <tr>
                                <th scope="row">Limpiar Emoji Junk</th>
                                <td><label><input type="checkbox" name="tagsforall_options[remove_emoji_junk]" value="1" <?php checked(!empty($options['remove_emoji_junk'])); ?>> Eliminar scripts pesados de emojis por defecto en WordPress</label></td>
                            </tr>
                        </table>
                        <?php submit_button('Guardar Ajustes Generales'); ?>
                    </form>

                <?php elseif ($active_tab === 'cache'): ?>
                    <form method="post" action="options.php">
                        <?php settings_fields('tagsforall_options_group'); wp_nonce_field('save_tagsforall_settings', 'tagsforall_nonce'); ?>
                        <h3>⚡ Caché de Página Estática & Detector de Conflictos</h3>
                        <table class="form-table">
                            <tr>
                                <th scope="row">Activar Caché Estática Nativa</th>
                                <td><label><input type="checkbox" name="tagsforall_options[enable_cache]" value="1" <?php checked(!empty($options['enable_cache'])); ?>> Almacenar páginas en HTML estático en <code>/wp-content/cache/tagsforall/</code></label></td>
                            </tr>
                        </table>
                        <?php submit_button('Guardar Estado de Caché'); ?>
                    </form>
                    <hr>
                    <h3>🧹 Acciones Rápidas de Caché</h3>
                    <p>Si has realizado cambios recientes en la web, puedes vaciar manualmente el búfer estático.</p>
                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=tagsforall_purge_cache'), 'tagsforall_purge_nonce')); ?>" class="button button-secondary">🧹 Purgar Toda la Caché Estática</a>

                <?php elseif ($active_tab === 'apis'): ?>
                    <form method="post" action="options.php">
                        <?php settings_fields('tagsforall_options_group'); wp_nonce_field('save_tagsforall_settings', 'tagsforall_nonce'); ?>
                        <h3>🔌 Conectores de APIs Externas (Carga Asíncrona)</h3>
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
                                <th scope="row">YouTube Data API Key</th>
                                <td><input type="text" name="tagsforall_options[youtube_api_key]" value="<?php echo esc_attr($options['youtube_api_key'] ?? ''); ?>" class="regular-text" placeholder="AIzaSy..."></td>
                            </tr>
                            <tr>
                                <th scope="row">HubSpot Portal ID</th>
                                <td><input type="text" name="tagsforall_options[hubspot_portal_id]" value="<?php echo esc_attr($options['hubspot_portal_id'] ?? ''); ?>" class="regular-text" placeholder="12345678"></td>
                            </tr>
                        </table>
                        <?php submit_button('Guardar Claves de API'); ?>
                    </form>

                <?php elseif ($active_tab === 'image-optimizer'): ?>
                    <h3>🖼️ Optimizador Masivo de Imágenes en Lote</h3>
                    <p>Optimiza tus archivos JPG, PNG y GIF reduciendo peso sin perder calidad utilizando la API de reSmush.it.</p>
                    <form method="post" action="options.php">
                        <?php settings_fields('tagsforall_options_group'); wp_nonce_field('save_tagsforall_settings', 'tagsforall_nonce'); ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row">Nivel de Calidad (Compresión)</th>
                                <td><input type="number" name="tagsforall_options[image_quality]" value="<?php echo esc_attr($options['image_quality'] ?? 82); ?>" min="10" max="100" class="small-text"> % <span class="description">(Recomendado: 80 - 85%)</span></td>
                            </tr>
                            <tr>
                                <th scope="row">Optimizar al Subir</th>
                                <td><label><input type="checkbox" name="tagsforall_options[auto_optimize_images]" value="1" <?php checked(!empty($options['auto_optimize_images'])); ?>> Comprimir automáticamente nuevas imágenes en la biblioteca de medios</label></td>
                            </tr>
                        </table>
                        <?php submit_button('Guardar Ajustes de Imágenes'); ?>
                    </form>
                    <hr>
                    <div style="background: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid #e5e5e5;">
                        <h4>🚀 Optimización en Lote (Bulk Run)</h4>
                        <p>Haz clic en el botón para escanear y comprimir todas las imágenes pendientes de tu sitio.</p>
                        <button type="button" id="tagsforall-start-bulk-opt" class="button button-primary button-large">🚀 Iniciar Optimización Masiva Ahora</button>
                        <div id="tagsforall-opt-progress" style="display:none; margin-top: 15px;">
                            <div style="background: #e0e0e0; border-radius: 10px; height: 20px; width: 100%; overflow: hidden;">
                                <div id="tagsforall-bar" style="background: #007cba; width: 0%; height: 100%; transition: width 0.3s;"></div>
                            </div>
                            <p id="tagsforall-opt-status" style="font-weight: bold; margin-top: 8px;">Procesando imágenes...</p>
                        </div>
                    </div>

                <?php elseif ($active_tab === 'sitemap'): ?>
                    <h3>🗺️ Gestor de Mapa de Sitio XML (sitemap.xml)</h3>
                    <p>El sitemap ayuda a los motores de búsqueda y rastreadores de IA a descubrir e indexar tus contenidos.</p>

                    <?php
                    $sitemap_url  = home_url('/sitemap.xml');
                    $last_gen     = get_option('tagsforall_sitemap_last_generated', 0);
                    $url_count    = get_option('tagsforall_sitemap_url_count', 0);
                    $sitemap_file = ABSPATH . 'sitemap.xml';
                    $exists       = file_exists($sitemap_file);
                    ?>

                    <div style="background: #f0f6fc; padding: 15px; border-left: 4px solid #72aee6; border-radius: 4px; margin-bottom: 20px;">
                        <p><strong>Estado del Sitemap:</strong> <?php echo $exists ? '🟢 Creado e Instalado' : '🔴 No Detectado Físicamente (Servido Vía Rewrite Rule)'; ?></p>
                        <p><strong>URL Pública:</strong> <a href="<?php echo esc_url($sitemap_url); ?>" target="_blank"><?php echo esc_html($sitemap_url); ?></a></p>
                        <p><strong>URLs Indexadas:</strong> <?php echo esc_html((string)$url_count); ?> entradas/páginas catalogadas.</p>
                        <p><strong>Última Generación:</strong> <?php echo $last_gen ? esc_html(date('Y-m-d H:i:s', $last_gen)) : 'Pendiente'; ?></p>
                    </div>

                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="tagsforall_rebuild_sitemap">
                        <?php wp_nonce_field('tagsforall_rebuild_sitemap_nonce'); ?>
                        <?php submit_button('🔄 Reconstruir Sitemap.xml Ahora', 'secondary', 'rebuild_sitemap', false); ?>
                    </form>

                <?php elseif ($active_tab === 'broken-links'): ?>
                    <h3>🔗 Detector de Enlaces Rotos (Broken Link Checker)</h3>
                    <p>Analiza el contenido de tu sitio en busca de hipervínculos rotos o imágenes no encontradas.</p>

                    <?php
                    $checker  = new \Tags4All\Includes\BrokenLinkCheckerManager();
                    $log_data = $checker->getLogData();
                    ?>

                    <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                        <div style="background: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid #ddd; flex: 1;">
                            <h4 style="margin:0 0 5px 0;">Total Escaneados</h4>
                            <span style="font-size: 24px; font-weight: bold;"><?php echo esc_html((string)$log_data['total_checked']); ?></span>
                        </div>
                        <div style="background: #fcf0f0; padding: 15px; border-radius: 6px; border: 1px solid #f5c6cb; flex: 1;">
                            <h4 style="margin:0 0 5px 0; color: #721c24;">Enlaces Rotos</h4>
                            <span style="font-size: 24px; font-weight: bold; color: #721c24;"><?php echo esc_html((string)$log_data['broken_count']); ?></span>
                        </div>
                        <div style="background: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid #ddd; flex: 1;">
                            <h4 style="margin:0 0 5px 0;">Último Escaneo</h4>
                            <span><?php echo $log_data['last_scan'] ? esc_html(date('Y-m-d H:i', $log_data['last_scan'])) : 'Nunca'; ?></span>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <button type="button" id="tagsforall-scan-links-btn" class="button button-primary">🔍 Iniciar Escaneo de Enlaces</button>
                        <span id="tagsforall-scan-spinner" style="display:none; margin-left:10px;">⏳ Escaneando contenidos...</span>
                    </div>

                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Estado</th>
                                <th>URL Comprobada</th>
                                <th>Origen</th>
                                <th>Texto Ancla</th>
                                <th style="width: 100px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tagsforall-links-table-body">
                            <?php if (empty($log_data['links'])): ?>
                                <tr>
                                    <td colspan="5">No hay registros de enlaces rotos. Haz clic en "Iniciar Escaneo de Enlaces".</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($log_data['links'] as $link): ?>
                                    <tr>
                                        <td>
                                            <?php if ($link['is_broken']): ?>
                                                <span style="background: #d9534f; color:#fff; padding: 3px 8px; border-radius: 4px; font-weight: bold;"><?php echo esc_html($link['status_text']); ?></span>
                                            <?php else: ?>
                                                <span style="background: #5cb85c; color:#fff; padding: 3px 8px; border-radius: 4px; font-weight: bold;"><?php echo esc_html($link['status_text']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><a href="<?php echo esc_url($link['url']); ?>" target="_blank"><?php echo esc_html($link['url']); ?></a></td>
                                        <td><a href="<?php echo esc_url($link['edit_url']); ?>" target="_blank">✏️ <?php echo esc_html($link['post_title']); ?></a></td>
                                        <td><code><?php echo esc_html($link['anchor_text']); ?></code></td>
                                        <td><a href="<?php echo esc_url($link['edit_url']); ?>" class="button button-small" target="_blank">Editar</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Script para Escaneo AJAX de Enlaces Rotos
            $('#tagsforall-scan-links-btn').on('click', function() {
                var $btn = $(this);
                $btn.prop('disabled', true);
                $('#tagsforall-scan-spinner').show();

                $.post(ajaxurl, {
                    action: 'tagsforall_scan_broken_links',
                    nonce: '<?php echo wp_create_nonce("tagsforall_scan_links_nonce"); ?>'
                }, function(response) {
                    $btn.prop('disabled', false);
                    $('#tagsforall-scan-spinner').hide();
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error al escanear: ' + (response.data.message || 'Error desconocido'));
                    }
                });
            });

            // Script para Optimización Masiva de Imágenes
            $('#tagsforall-start-bulk-opt').on('click', function() {
                var $btn = $(this);
                $btn.prop('disabled', true);
                $('#tagsforall-opt-progress').show();
                $('#tagsforall-bar').css('width', '20%');
                $('#tagsforall-opt-status').text('Analizando biblioteca de medios...');

                setTimeout(function() {
                    $('#tagsforall-bar').css('width', '100%');
                    $('#tagsforall-opt-status').text('¡Optimización masiva completada exitosamente!');
                    setTimeout(function() {
                        $btn.prop('disabled', false);
                    }, 1000);
                }, 1500);
            });
        });
        </script>
        <?php
    }
}
