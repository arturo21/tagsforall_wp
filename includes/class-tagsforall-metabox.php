<?php
declare(strict_types=1);

namespace Tags4All\Includes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * On-Site SEO Metabox para Entradas y Páginas con Angular Material Chips
 */
class SeoMetaboxManager
{
    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'addSeoMetaBox']);
        add_action('save_post', [$this, 'saveSeoMetaBoxData']);
    }

    public function addSeoMetaBox(): void
    {
        $screens = ['post', 'page'];
        if (function_exists('get_post_types')) {
            $custom_post_types = get_post_types(['public' => true, '_builtin' => false], 'names');
            $screens = array_merge($screens, array_values($custom_post_types));
        }

        foreach ($screens as $screen) {
            add_meta_box(
                'tagsforall_seo_metabox',
                '🔍 Tags4All — SEO On-Site (Título, Descripción y Palabras Clave)',
                [$this, 'renderMetaboxHtml'],
                $screen,
                'normal',
                'high'
            );
        }
    }

    public function renderMetaboxHtml(\WP_Post $post): void
    {
        wp_nonce_field('tagsforall_seo_metabox_action', 'tagsforall_seo_metabox_nonce');

        $seo_title       = get_post_meta($post->ID, '_tagsforall_seo_title', true);
        $seo_description = get_post_meta($post->ID, '_tagsforall_seo_description', true);
        $seo_keywords    = get_post_meta($post->ID, '_tagsforall_seo_keywords', true);

        ?>
        <style>
            .tagsforall-metabox-wrapper { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; margin-top: 10px; }
            .tagsforall-field-group { margin-bottom: 20px; }
            .tagsforall-field-group label { display: block; font-weight: 600; margin-bottom: 6px; color: #1d2327; font-size: 14px; }
            .tagsforall-field-group input[type="text"],
            .tagsforall-field-group textarea { width: 100%; border: 1px solid #8c8f94; border-radius: 4px; padding: 8px 12px; font-size: 14px; box-sizing: border-box; }
            .tagsforall-field-group textarea { resize: vertical; min-height: 80px; }
            .tagsforall-counter { font-size: 12px; color: #646970; margin-top: 4px; text-align: right; }
            .tagsforall-counter.warning { color: #d63638; font-weight: 600; }

            /* Angular Material Chips Container Style */
            .tagsforall-chips-container {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                min-height: 48px;
                padding: 6px 10px;
                border: 1px solid #8c8f94;
                border-radius: 6px;
                background-color: #fafafa;
                transition: border-color 0.2s ease, box-shadow 0.2s ease;
                cursor: text;
            }
            .tagsforall-chips-container:focus-within {
                border-color: #3f51b5;
                box-shadow: 0 0 0 2px rgba(63, 81, 181, 0.25);
                background-color: #ffffff;
            }
            /* Angular Material Chip Styling */
            .tagsforall-chip {
                display: inline-flex;
                align-items: center;
                background-color: #e0e0e0;
                color: #212121;
                border-radius: 16px;
                padding: 4px 10px 4px 14px;
                margin: 4px;
                font-size: 13px;
                font-weight: 500;
                letter-spacing: 0.25px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
                animation: tagsforallChipFade 0.2s ease-in-out;
            }
            @keyframes tagsforallChipFade {
                from { opacity: 0; transform: scale(0.8); }
                to { opacity: 1; transform: scale(1); }
            }
            .tagsforall-chip-remove {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 18px;
                height: 18px;
                margin-left: 8px;
                border-radius: 50%;
                background-color: #9e9e9e;
                color: #ffffff;
                font-size: 12px;
                line-height: 1;
                cursor: pointer;
                transition: background-color 0.15s ease;
            }
            .tagsforall-chip-remove:hover {
                background-color: #e53935;
            }
            .tagsforall-chip-input {
                border: none !important;
                outline: none !important;
                box-shadow: none !important;
                background: transparent !important;
                flex: 1;
                min-width: 180px;
                padding: 6px 4px !important;
                font-size: 14px;
                color: #212121;
            }
            /* Google Search Preview Card */
            .tagsforall-snippet-preview {
                background: #ffffff;
                border: 1px solid #dadce0;
                border-radius: 8px;
                padding: 14px 18px;
                margin-top: 10px;
                max-width: 600px;
                font-family: arial, sans-serif;
            }
            .tagsforall-preview-url { font-size: 12px; color: #202124; margin-bottom: 4px; word-break: break-all; }
            .tagsforall-preview-title { font-size: 18px; color: #1a0dab; line-height: 1.3; text-decoration: none; font-weight: 400; cursor: pointer; display: block; margin-bottom: 4px; }
            .tagsforall-preview-title:hover { text-decoration: underline; }
            .tagsforall-preview-desc { font-size: 14px; color: #4d5156; line-height: 1.5; word-wrap: break-word; }
        </style>

        <div class="tagsforall-metabox-wrapper">
            <!-- SERP Preview -->
            <div class="tagsforall-field-group">
                <label>Vista Previa en Google (SERP Preview)</label>
                <div class="tagsforall-snippet-preview">
                    <div class="tagsforall-preview-url"><?php echo esc_url(get_permalink($post->ID) ?: home_url('/ejemplo-slug/')); ?></div>
                    <a href="#" class="tagsforall-preview-title" id="tagsforall_preview_title_text"><?php echo esc_html($seo_title ?: get_the_title($post) ?: 'Título de Ejemplo para Google'); ?></a>
                    <div class="tagsforall-preview-desc" id="tagsforall_preview_desc_text"><?php echo esc_html($seo_description ?: 'Esta es la meta descripción que aparecerá en los resultados de búsqueda de Google e Inteligencias Artificiales.'); ?></div>
                </div>
            </div>

            <!-- Meta Title -->
            <div class="tagsforall-field-group">
                <label for="tagsforall_seo_title">Título SEO (Meta Title)</label>
                <input type="text" id="tagsforall_seo_title" name="tagsforall_seo_title" value="<?php echo esc_attr($seo_title); ?>" placeholder="Ej: Guía Completa de SEO On-Site 2026 | Mi Sitio Web" maxlength="100">
                <div class="tagsforall-counter"><span id="tagsforall_title_count">0</span> / 60 caracteres recomendados</div>
            </div>

            <!-- Meta Description -->
            <div class="tagsforall-field-group">
                <label for="tagsforall_seo_description">Meta Descripción</label>
                <textarea id="tagsforall_seo_description" name="tagsforall_seo_description" placeholder="Escribe un resumen atractivo de tu contenido para los buscadores e IA..." maxlength="200"><?php echo esc_textarea($seo_description); ?></textarea>
                <div class="tagsforall-counter"><span id="tagsforall_desc_count">0</span> / 160 caracteres recomendados</div>
            </div>

            <!-- Keywords Chips -->
            <div class="tagsforall-field-group">
                <label for="tagsforall_chip_input">Palabras Clave (Focus Keywords — Estilo Angular Material Chips)</label>
                <div class="tagsforall-chips-container" id="tagsforall_chips_wrapper">
                    <!-- Chips dinámicas se renderizan aquí -->
                    <input type="text" id="tagsforall_chip_input" class="tagsforall-chip-input" placeholder="Escribe una palabra clave y presiona Enter o coma (,)...">
                </div>
                <input type="hidden" name="tagsforall_seo_keywords" id="tagsforall_seo_keywords" value="<?php echo esc_attr($seo_keywords); ?>">
                <p class="description">Introduce palabras clave estratégicas para el SEO On-Page y optimización AEO/GEO. Pulsa <code>Enter</code> o escribe una <code>coma</code> para crear un chip.</p>
            </div>
        </div>

        <script>
        (function() {
            document.addEventListener('DOMContentLoaded', function() {
                var titleInput = document.getElementById('tagsforall_seo_title');
                var descInput = document.getElementById('tagsforall_seo_description');
                var titleCount = document.getElementById('tagsforall_title_count');
                var descCount = document.getElementById('tagsforall_desc_count');
                var previewTitle = document.getElementById('tagsforall_preview_title_text');
                var previewDesc = document.getElementById('tagsforall_preview_desc_text');

                var chipInput = document.getElementById('tagsforall_chip_input');
                var chipsWrapper = document.getElementById('tagsforall_chips_wrapper');
                var hiddenKeywords = document.getElementById('tagsforall_seo_keywords');

                if (!chipInput || !chipsWrapper || !hiddenKeywords) return;

                var keywordsList = [];
                if (hiddenKeywords.value.trim() !== '') {
                    keywordsList = hiddenKeywords.value.split(',').map(function(k) { return k.trim(); }).filter(Boolean);
                }

                function renderChips() {
                    var existingChips = chipsWrapper.querySelectorAll('.tagsforall-chip');
                    existingChips.forEach(function(chip) { chip.remove(); });

                    keywordsList.forEach(function(keyword, index) {
                        var chip = document.createElement('div');
                        chip.className = 'tagsforall-chip';
                        chip.innerHTML = '<span>' + escapeHtml(keyword) + '</span><span class="tagsforall-chip-remove" data-index="' + index + '">&times;</span>';
                        chipsWrapper.insertBefore(chip, chipInput);
                    });

                    hiddenKeywords.value = keywordsList.join(', ');
                }

                function addKeyword(val) {
                    var trimmed = val.trim().replace(/,/g, '');
                    if (trimmed !== '' && keywordsList.indexOf(trimmed) === -1) {
                        keywordsList.push(trimmed);
                        renderChips();
                    }
                }

                function removeKeyword(index) {
                    keywordsList.splice(index, 1);
                    renderChips();
                }

                function escapeHtml(text) {
                    var div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                chipsWrapper.addEventListener('click', function(e) {
                    if (e.target.classList.contains('tagsforall-chip-remove')) {
                        var idx = parseInt(e.target.getAttribute('data-index'), 10);
                        removeKeyword(idx);
                    } else if (e.target === chipsWrapper) {
                        chipInput.focus();
                    }
                });

                chipInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ',') {
                        e.preventDefault();
                        addKeyword(chipInput.value);
                        chipInput.value = '';
                    } else if (e.key === 'Backspace' && chipInput.value === '' && keywordsList.length > 0) {
                        removeKeyword(keywordsList.length - 1);
                    }
                });

                chipInput.addEventListener('blur', function() {
                    if (chipInput.value.trim() !== '') {
                        addKeyword(chipInput.value);
                        chipInput.value = '';
                    }
                });

                chipInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    var pasteData = (e.clipboardData || window.clipboardData).getData('text');
                    var parts = pasteData.split(',');
                    parts.forEach(function(p) { addKeyword(p); });
                    chipInput.value = '';
                });

                function updateCounters() {
                    if (!titleInput || !descInput) return;
                    var tLen = titleInput.value.length;
                    var dLen = descInput.value.length;
                    if (titleCount) titleCount.textContent = tLen;
                    if (descCount) descCount.textContent = dLen;

                    if (previewTitle) {
                        previewTitle.textContent = titleInput.value.trim() !== '' ? titleInput.value : '<?php echo esc_js(get_the_title($post) ?: "Título de Ejemplo"); ?>';
                    }
                    if (previewDesc) {
                        previewDesc.textContent = descInput.value.trim() !== '' ? descInput.value : 'Esta es la meta descripción que aparecerá en los resultados de búsqueda de Google e Inteligencias Artificiales.';
                    }
                }

                if (titleInput) titleInput.addEventListener('input', updateCounters);
                if (descInput) descInput.addEventListener('input', updateCounters);

                renderChips();
                updateCounters();
            });
        })();
        </script>
        <?php
    }

    public function saveSeoMetaBoxData(int $post_id): void
    {
        if (!isset($_POST['tagsforall_seo_metabox_nonce']) || !wp_verify_nonce($_POST['tagsforall_seo_metabox_nonce'], 'tagsforall_seo_metabox_action')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['tagsforall_seo_title'])) {
            update_post_meta($post_id, '_tagsforall_seo_title', sanitize_text_field($_POST['tagsforall_seo_title']));
        }

        if (isset($_POST['tagsforall_seo_description'])) {
            update_post_meta($post_id, '_tagsforall_seo_description', sanitize_textarea_field($_POST['tagsforall_seo_description']));
        }

        if (isset($_POST['tagsforall_seo_keywords'])) {
            update_post_meta($post_id, '_tagsforall_seo_keywords', sanitize_text_field($_POST['tagsforall_seo_keywords']));
        }
    }
}
