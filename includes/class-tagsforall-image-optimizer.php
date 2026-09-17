<?php
declare(strict_types=1);

namespace Tags4All\Includes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Módulo de Optimización de Imágenes en Lote (Bulk Image Optimizer) para Tags4All
 * Inspirado en soluciones como reSmush.it
 */
class ImageOptimizerManager
{
    private const RESMUSH_API_URL = 'http://api.resmush.it/ws.php';

    public function __construct()
    {
        // Optimización automática al subir archivos a la biblioteca de medios
        add_filter('wp_generate_attachment_metadata', [$this, 'autoOptimizeOnUpload'], 10, 2);

        // Endpoints AJAX para optimización masiva en WP Admin
        add_action('wp_ajax_tagsforall_bulk_optimize_image', [$this, 'ajaxOptimizeImage']);
        add_action('wp_ajax_tagsforall_restore_image_backup', [$this, 'ajaxRestoreBackup']);
        add_action('wp_ajax_tagsforall_get_optimizer_stats', [$this, 'ajaxGetStats']);
    }

    /**
     * Devuelve las opciones del optimizador con valores por defecto
     */
    public function getOptions(): array
    {
        $options = get_option('tagsforall_options', []);
        return [
            'auto_optimize_upload' => !empty($options['img_auto_optimize'] ?? true),
            'quality'              => (int) ($options['img_quality'] ?? 82),
            'preserve_exif'        => !empty($options['img_preserve_exif'] ?? false),
            'make_backup'          => !empty($options['img_make_backup'] ?? true),
            'generate_webp'        => !empty($options['img_generate_webp'] ?? true),
            'max_width'            => (int) ($options['img_max_width'] ?? 2048),
            'max_height'           => (int) ($options['img_max_height'] ?? 2048),
            'engine'               => sanitize_text_field($options['img_engine'] ?? 'resmush'),
        ];
    }

    /**
     * Hook para optimización automática tras generar metadatos en carga
     */
    public function autoOptimizeOnUpload(array $metadata, int $attachment_id): array
    {
        $opts = $this->getOptions();
        if (!$opts['auto_optimize_upload']) {
            return $metadata;
        }

        $this->optimizeAttachment($attachment_id);
        return wp_get_attachment_metadata($attachment_id) ?: $metadata;
    }

    /**
     * Optimiza un adjunto de la biblioteca de medios por ID
     */
    public function optimizeAttachment(int $attachment_id): array
    {
        $file_path = get_attached_file($attachment_id);
        if (!$file_path || !file_exists($file_path)) {
            return ['success' => false, 'error' => 'Archivo no encontrado'];
        }

        $mime_type = get_post_mime_type($attachment_id);
        if (!in_array($mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true)) {
            return ['success' => false, 'error' => 'Formato no soportado'];
        }

        $opts = $this->getOptions();

        // 1. Crear copia de seguridad antes de modificar si está habilitado
        if ($opts['make_backup']) {
            $this->createBackup($file_path, $attachment_id);
        }

        // 2. Redimensionar si supera el ancho/alto máximo
        $this->resizeImageIfExceeds($file_path, $opts['max_width'], $opts['max_height']);

        $original_size = filesize($file_path);
        $optimized_size = $original_size;
        $engine_used = 'local';

        // 3. Optimización vía API reSmush.it o Motor Local (GD/Imagick)
        if ($opts['engine'] === 'resmush' && function_exists('curl_init')) {
            $api_result = $this->compressWithReSmushApi($file_path, $opts['quality'], $opts['preserve_exif']);
            if ($api_result['success']) {
                $optimized_size = filesize($file_path);
                $engine_used = 'reSmush.it API';
            } else {
                // Fallback a compresión local si la API falla
                $this->compressLocal($file_path, $mime_type, $opts['quality']);
                $optimized_size = filesize($file_path);
            }
        } else {
            $this->compressLocal($file_path, $mime_type, $opts['quality']);
            $optimized_size = filesize($file_path);
        }

        // 4. Generar versión WebP si está activado
        if ($opts['generate_webp']) {
            $this->generateWebpCopy($file_path);
        }

        $bytes_saved = max(0, $original_size - $optimized_size);
        $savings_percent = $original_size > 0 ? round(($bytes_saved / $original_size) * 100, 2) : 0;

        // Registrar metadatos de optimización en el post_meta del adjunto
        $meta_data = [
            'optimized'        => true,
            'optimized_at'     => current_time('mysql'),
            'original_size'    => $original_size,
            'optimized_size'   => $optimized_size,
            'bytes_saved'      => $bytes_saved,
            'savings_percent'  => $savings_percent,
            'engine'           => $engine_used,
        ];
        update_post_meta($attachment_id, '_tagsforall_img_opt_data', $meta_data);

        return [
            'success'          => true,
            'attachment_id'    => $attachment_id,
            'original_size'    => $original_size,
            'optimized_size'   => $optimized_size,
            'bytes_saved'      => $bytes_saved,
            'savings_percent'  => $savings_percent,
            'engine'           => $engine_used
        ];
    }

    /**
     * Compresión a través del API de reSmush.it
     */
    private function compressWithReSmushApi(string $file_path, int $quality, bool $preserve_exif): array
    {
        $ch = curl_init();
        $mime = mime_content_type($file_path);

        $data = [
            'files' => new \CURLFile($file_path, $mime, basename($file_path)),
            'qlty'  => $quality
        ];
        if ($preserve_exif) {
            $data['exif'] = 'true';
        }

        curl_setopt_array($ch, [
            CURLOPT_URL            => self::RESMUSH_API_URL,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200 || !$response) {
            return ['success' => false, 'error' => 'Error de conexión con la API de reSmush.it'];
        }

        $json = json_decode((string)$response, true);
        if (isset($json['dest']) && !empty($json['dest'])) {
            $downloaded = file_get_contents($json['dest']);
            if ($downloaded !== false) {
                file_put_contents($file_path, $downloaded);
                return ['success' => true];
            }
        }

        return ['success' => false, 'error' => $json['error_long'] ?? 'Error desconocido de API'];
    }

    /**
     * Compresión local utilizando PHP GD o Imagick
     */
    private function compressLocal(string $file_path, string $mime_type, int $quality): bool
    {
        if (!function_exists('imagecreatefromjpeg')) {
            return false;
        }

        switch ($mime_type) {
            case 'image/jpeg':
                $img = @imagecreatefromjpeg($file_path);
                if ($img) {
                    imagejpeg($img, $file_path, $quality);
                    imagedestroy($img);
                    return true;
                }
                break;
            case 'image/png':
                $img = @imagecreatefrompng($file_path);
                if ($img) {
                    imagealphablending($img, false);
                    imagesavealpha($img, true);
                    // Mapear calidad 0-100 a compresión PNG 0-9
                    $png_quality = (int) round((100 - $quality) / 10);
                    $png_quality = min(9, max(0, $png_quality));
                    imagepng($img, $file_path, $png_quality);
                    imagedestroy($img);
                    return true;
                }
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp') && function_exists('imagewebp')) {
                    $img = @imagecreatefromwebp($file_path);
                    if ($img) {
                        imagewebp($img, $file_path, $quality);
                        imagedestroy($img);
                        return true;
                    }
                }
                break;
        }
        return false;
    }

    /**
     * Genera una versión paralela en WebP (.webp) para optimización extrema
     */
    private function generateWebpCopy(string $file_path): void
    {
        $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file_path);
        if (!$webp_path || $webp_path === $file_path) {
            return;
        }

        if (function_exists('imagewebp')) {
            $mime = mime_content_type($file_path);
            $img = null;
            if ($mime === 'image/jpeg') {
                $img = @imagecreatefromjpeg($file_path);
            } elseif ($mime === 'image/png') {
                $img = @imagecreatefrompng($file_path);
                if ($img) {
                    imagealphablending($img, false);
                    imagesavealpha($img, true);
                }
            }
            if ($img) {
                imagewebp($img, $webp_path, 80);
                imagedestroy($img);
            }
        }
    }

    /**
     * Redimensiona la imagen si excede el ancho o alto máximo
     */
    private function resizeImageIfExceeds(string $file_path, int $max_w, int $max_h): void
    {
        $size = @getimagesize($file_path);
        if (!$size) {
            return;
        }

        list($width, $height) = $size;
        if ($width <= $max_w && $height <= $max_h) {
            return;
        }

        $editor = wp_get_image_editor($file_path);
        if (!is_wp_error($editor)) {
            $editor->resize($max_w, $max_h, false);
            $editor->save($file_path);
        }
    }

    /**
     * Crea una copia de seguridad en el directorio de backups de Tags4All
     */
    private function createBackup(string $file_path, int $attachment_id): void
    {
        $backup_dir = WP_CONTENT_DIR . '/uploads/tagsforall-backups/';
        if (!file_exists($backup_dir)) {
            wp_mkdir_p($backup_dir);
        }

        $backup_file = $backup_dir . $attachment_id . '_' . basename($file_path);
        if (!file_exists($backup_file)) {
            @copy($file_path, $backup_file);
            update_post_meta($attachment_id, '_tagsforall_img_backup_path', $backup_file);
        }
    }

    /**
     * Restaura la imagen desde la copia de seguridad si existe
     */
    public function restoreBackup(int $attachment_id): bool
    {
        $backup_path = get_post_meta($attachment_id, '_tagsforall_img_backup_path', true);
        $current_path = get_attached_file($attachment_id);

        if ($backup_path && file_exists($backup_path) && $current_path) {
            @copy($backup_path, $current_path);
            delete_post_meta($attachment_id, '_tagsforall_img_opt_data');
            return true;
        }
        return false;
    }

    /**
     * Endpoint AJAX para optimizar una imagen individual en lote
     */
    public function ajaxOptimizeImage(): void
    {
        check_ajax_referer('tagsforall_bulk_opt_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('No autorizado');
        }

        $attachment_id = isset($_POST['attachment_id']) ? (int) $_POST['attachment_id'] : 0;
        if (!$attachment_id) {
            wp_send_json_error('ID de adjunto no válido');
        }

        $res = $this->optimizeAttachment($attachment_id);
        if ($res['success']) {
            wp_send_json_success($res);
        } else {
            wp_send_json_error($res['error'] ?? 'Error al optimizar imagen');
        }
    }

    /**
     * Endpoint AJAX para restaurar copia de seguridad
     */
    public function ajaxRestoreBackup(): void
    {
        check_ajax_referer('tagsforall_bulk_opt_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('No autorizado');
        }

        $attachment_id = isset($_POST['attachment_id']) ? (int) $_POST['attachment_id'] : 0;
        if ($this->restoreBackup($attachment_id)) {
            wp_send_json_success(['message' => 'Imagen restaurada correctamente']);
        } else {
            wp_send_json_error('No se encontró copia de seguridad para esta imagen');
        }
    }

    /**
     * Endpoint AJAX para obtener estadísticas globales de la biblioteca de medios
     */
    public function ajaxGetStats(): void
    {
        check_ajax_referer('tagsforall_bulk_opt_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('No autorizado');
        }

        $args = [
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'post_mime_type' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ];

        $query = new \WP_Query($args);
        $total_images = count($query->posts);
        $optimized_count = 0;
        $total_bytes_saved = 0;
        $unoptimized_ids = [];

        foreach ($query->posts as $id) {
            $opt_data = get_post_meta($id, '_tagsforall_img_opt_data', true);
            if (!empty($opt_data['optimized'])) {
                $optimized_count++;
                $total_bytes_saved += (int) ($opt_data['bytes_saved'] ?? 0);
            } else {
                $unoptimized_ids[] = $id;
            }
        }

        wp_send_json_success([
            'total_images'      => $total_images,
            'optimized_count'   => $optimized_count,
            'pending_count'     => $total_images - $optimized_count,
            'total_saved_bytes' => $total_bytes_saved,
            'total_saved_mb'    => round($total_bytes_saved / 1048576, 2),
            'pending_ids'       => $unoptimized_ids,
        ]);
    }
}
