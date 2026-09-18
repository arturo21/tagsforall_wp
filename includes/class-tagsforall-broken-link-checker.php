<?php
declare(strict_types=1);

namespace Tags4All\Includes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Detector de Enlaces Rotos (Broken Link Checker) para Tags4All
 */
class BrokenLinkCheckerManager
{
    private string $option_key = 'tagsforall_broken_links_log';

    public function __construct()
    {
        add_action('wp_ajax_tagsforall_scan_broken_links', [$this, 'ajaxScanBrokenLinks']);
        add_action('wp_ajax_tagsforall_retest_broken_link', [$this, 'ajaxRetestLink']);
        add_action('wp_ajax_tagsforall_clear_broken_links', [$this, 'ajaxClearLog']);

        // Cronjob semanal para escaneo en background
        add_action('tagsforall_weekly_broken_links_scan', [$this, 'runCronScan']);
    }

    /**
     * Obtiene el historial de enlaces probados
     */
    public function getLogData(): array
    {
        $default = [
            'last_scan'     => 0,
            'total_checked' => 0,
            'broken_count'  => 0,
            'links'         => []
        ];
        $data = get_option($this->option_key, $default);
        return is_array($data) ? array_merge($default, $data) : $default;
    }

    /**
     * Extrae todos los enlaces (href e img src) de las entradas y páginas públicas
     */
    public function extractLinksFromContent(): array
    {
        $post_types = get_post_types(['public' => true], 'names');
        unset($post_types['attachment']);

        $posts = get_posts([
            'post_type'      => array_values($post_types),
            'post_status'    => 'publish',
            'posts_per_page' => 200,
            'orderby'        => 'ID',
            'order'          => 'DESC'
        ]);

        $extracted_links = [];

        foreach ($posts as $post) {
            $content = $post->post_content ?? '';
            if (empty($content)) {
                continue;
            }

            // Buscar etiquetas <a href="...">
            preg_match_all("/<a\\s+[^>]*href=[\"']([^\"']+)[\"'][^>]*>(.*?)<\\/a>/i", $content, $a_matches, PREG_SET_ORDER);
            foreach ($a_matches as $match) {
                $url = trim($match[1]);
                $anchor = wp_strip_all_tags(trim($match[2])) ?: '[Enlace sin texto]';

                if ($this->isValidUrlToScan($url)) {
                    $extracted_links[] = [
                        'url'         => $url,
                        'post_id'     => $post->ID,
                        'post_title'  => get_the_title($post->ID),
                        'anchor_text' => mb_substr($anchor, 0, 60),
                        'type'        => 'anchor'
                    ];
                }
            }

            // Buscar etiquetas <img src="...">
            preg_match_all("/<img\\s+[^>]*src=[\"']([^\"']+)[\"'][^>]*>/i", $content, $img_matches, PREG_SET_ORDER);
            foreach ($img_matches as $match) {
                $url = trim($match[1]);
                if ($this->isValidUrlToScan($url)) {
                    $extracted_links[] = [
                        'url'         => $url,
                        'post_id'     => $post->ID,
                        'post_title'  => get_the_title($post->ID),
                        'anchor_text' => '[Imagen]',
                        'type'        => 'image'
                    ];
                }
            }
        }

        return $extracted_links;
    }

    /**
     * Filtra URLs no probables (javascript:, mailto:, tel:, #anchors)
     */
    private function isValidUrlToScan(string $url): bool
    {
        if (empty($url) || str_starts_with($url, '#') || str_starts_with($url, 'javascript:') || str_starts_with($url, 'mailto:') || str_starts_with($url, 'tel:')) {
            return false;
        }
        return filter_var($url, FILTER_VALIDATE_URL) !== false || str_starts_with($url, '/');
    }

    /**
     * Prueba el estado HTTP de una URL dada
     */
    public function testUrlStatus(string $url): array
    {
        // Convertir ruta relativa a URL absoluta si es necesario
        $full_url = str_starts_with($url, '/') ? home_url($url) : $url;

        $response = wp_remote_head($full_url, [
            'timeout'     => 6,
            'redirection' => 5,
            'user-agent'  => 'Tags4All-BrokenLinkChecker/2.0 (+https://tagsforall.org)'
        ]);

        // Si HEAD falla o no está permitido, intentar GET corto
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) === 405) {
            $response = wp_remote_get($full_url, [
                'timeout'     => 6,
                'redirection' => 5,
                'user-agent'  => 'Tags4All-BrokenLinkChecker/2.0'
            ]);
        }

        if (is_wp_error($response)) {
            return [
                'status_code' => 0,
                'status_text' => 'Error de conexión (' . $response->get_error_message() . ')',
                'is_broken'   => true
            ];
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        $message = wp_remote_retrieve_response_message($response);

        $is_broken = ($code >= 400 || $code === 0);

        return [
            'status_code' => $code,
            'status_text' => $code . ' ' . ($message ?: ($is_broken ? 'Error' : 'OK')),
            'is_broken'   => $is_broken
        ];
    }

    /**
     * Escaneo ejecutado en background por el Cronjob de WordPress
     */
    public function runCronScan(): void
    {
        $raw_links = $this->extractLinksFromContent();
        $results = [];
        $broken_count = 0;
        $broken_items = [];

        foreach ($raw_links as $item) {
            $status = $this->testUrlStatus($item['url']);

            $link_entry = [
                'url'         => $item['url'],
                'post_id'     => $item['post_id'],
                'post_title'  => $item['post_title'],
                'edit_url'    => admin_url('post.php?post=' . $item['post_id'] . '&action=edit'),
                'anchor_text' => $item['anchor_text'],
                'status_code' => $status['status_code'],
                'status_text' => $status['status_text'],
                'is_broken'   => $status['is_broken'],
                'check_date'  => date('Y-m-d H:i:s')
            ];

            if ($status['is_broken']) {
                $broken_count++;
                $broken_items[] = $link_entry;
            }

            $results[] = $link_entry;
        }

        $log_data = [
            'last_scan'     => time(),
            'total_checked' => count($results),
            'broken_count'  => $broken_count,
            'links'         => $results
        ];

        update_option($this->option_key, $log_data);

        // Si existen enlaces rotos, enviar reporte por correo al admin
        if ($broken_count > 0) {
            $this->sendEmailReport($broken_count, count($results), $broken_items);
        }
    }

    /**
     * Envía un reporte por correo electrónico al administrador
     */
    private function sendEmailReport(int $broken_count, int $total_checked, array $broken_items): void
    {
        $to = get_option('admin_email');
        if (empty($to)) {
            return;
        }

        $site_name = get_bloginfo('name');
        $subject   = sprintf('⚠️ [%s] Reporte Semanal de Enlaces Rotos — Tags4All', $site_name);

        $message  = '<h2>⚠️ Alerta de Enlaces Rotos en ' . esc_html($site_name) . '</h2>';
        $message .= '<p>Se ha completado el escaneo automático semanal y se han detectado <strong>' . $broken_count . '</strong> enlaces rotos de un total de ' . $total_checked . ' revisados.</p>';
        $message .= '<table border="1" cellpadding="8" cellspacing="0" style="border-collapse:collapse; width:100%;">';
        $message .= '<tr style="background:#f4f4f4;"><th>Estado</th><th>URL Afectada</th><th>Origen</th><th>Acción</th></tr>';

        foreach (array_slice($broken_items, 0, 20) as $item) {
            $message .= '<tr>';
            $message .= '<td style="color:#d63638; font-weight:bold;">' . esc_html($item['status_text']) . '</td>';
            $message .= '<td><a href="' . esc_url($item['url']) . '" target="_blank">' . esc_html($item['url']) . '</a></td>';
            $message .= '<td>' . esc_html($item['post_title']) . ' (' . esc_html($item['anchor_text']) . ')</td>';
            $message .= '<td><a href="' . esc_url($item['edit_url']) . '">Editar Entrada</a></td>';
            $message .= '</tr>';
        }

        $message .= '</table>';
        $message .= '<p><a href="' . esc_url(admin_url('options-general.php?page=tagsforall-settings&tab=broken-links')) . '" style="display:inline-block; padding:10px 15px; background:#0284c7; color:#fff; text-decoration:none; border-radius:4px;">Ver panel completo en WordPress</a></p>';

        $headers = ['Content-Type: text/html; charset=UTF-8'];

        wp_mail($to, $subject, $message, $headers);
    }

    /**
     * AJAX: Inicia el escaneo masivo de enlaces
     */
    public function ajaxScanBrokenLinks(): void
    {
        check_ajax_referer('tagsforall_scan_links_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Acceso no autorizado']);
        }

        $this->runCronScan();
        $log_data = $this->getLogData();

        wp_send_json_success([
            'message' => 'Escaneo completado exitosamente',
            'data'    => $log_data
        ]);
    }

    /**
     * AJAX: Re-prueba una URL específica de la lista
     */
    public function ajaxRetestLink(): void
    {
        check_ajax_referer('tagsforall_scan_links_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Acceso no autorizado']);
        }

        $url = sanitize_text_field($_POST['url'] ?? '');
        if (empty($url)) {
            wp_send_json_error(['message' => 'URL no proporcionada']);
        }

        $status = $this->testUrlStatus($url);

        // Actualizar registro guardado
        $log_data = $this->getLogData();
        $updated_broken_count = 0;

        foreach ($log_data['links'] as &$link) {
            if ($link['url'] === $url) {
                $link['status_code'] = $status['status_code'];
                $link['status_text'] = $status['status_text'];
                $link['is_broken']   = $status['is_broken'];
                $link['check_date']  = date('Y-m-d H:i:s');
            }
            if ($link['is_broken']) {
                $updated_broken_count++;
            }
        }

        $log_data['broken_count'] = $updated_broken_count;
        update_option($this->option_key, $log_data);

        wp_send_json_success([
            'url'         => $url,
            'status_code' => $status['status_code'],
            'status_text' => $status['status_text'],
            'is_broken'   => $status['is_broken']
        ]);
    }

    /**
     * AJAX: Limpia el registro de enlaces
     */
    public function ajaxClearLog(): void
    {
        check_ajax_referer('tagsforall_scan_links_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Acceso no autorizado']);
        }

        delete_option($this->option_key);

        wp_send_json_success([
            'message' => 'Registro de enlaces rotos borrado correctamente'
        ]);
    }
}

class_alias('\\Tags4All\\Includes\\BrokenLinkCheckerManager', '\\Tags4All\\Includes\\BrokenLinkChecker');
