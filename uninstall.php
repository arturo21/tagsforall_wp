<?php
/**
 * Limpieza al desinstalar Tags4All
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// 1. Eliminar opciones de la base de datos
delete_option('tagsforall_options');

// 2. Limpiar directorio de caché estática
$cache_dir = WP_CONTENT_DIR . '/cache/tagsforall/';
if (is_dir($cache_dir)) {
    $files = glob($cache_dir . '*');
    if (is_array($files)) {
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
    @rmdir($cache_dir);
}
