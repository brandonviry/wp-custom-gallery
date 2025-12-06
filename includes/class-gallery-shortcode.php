<?php
/**
 * Classe pour gérer le shortcode de la galerie
 */
class WP_Custom_Gallery_Shortcode {

    public function __construct() {
        add_shortcode('custom_gallery', array($this, 'render_gallery'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
    }

    /**
     * Charger les assets CSS/JS pour le frontend
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'wp-custom-gallery-frontend',
            WP_CUSTOM_GALLERY_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            WP_CUSTOM_GALLERY_VERSION
        );

        wp_enqueue_script(
            'wp-custom-gallery-frontend',
            WP_CUSTOM_GALLERY_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            WP_CUSTOM_GALLERY_VERSION,
            true
        );
    }

    /**
     * Rendre la galerie via le shortcode
     */
    public function render_gallery($atts) {
        $atts = shortcode_atts(array(
            'id' => 0
        ), $atts);

        $gallery_id = intval($atts['id']);

        if ($gallery_id === 0) {
            return '<p>ID de galerie invalide</p>';
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'custom_galleries';
        $gallery = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $gallery_id));

        if (!$gallery) {
            return '<p>Galerie non trouvée</p>';
        }

        $images = json_decode($gallery->images, true);
        $settings = json_decode($gallery->settings, true);

        if (empty($images)) {
            return '<p>Aucune image dans cette galerie</p>';
        }

        // Trier les images
        $images = $this->sort_images($images, $settings['sort_by'], $settings['sort_order']);

        // Extraire les tags ALT uniques
        $alt_tags = $this->extract_alt_tags($images);

        $image_style = isset($settings['image_style']) ? $settings['image_style'] : 'color';
        $gallery_class = 'wp-custom-gallery';
        if ($image_style === 'grayscale') {
            $gallery_class .= ' gallery-grayscale';
        }

        // Récupérer TOUTES les options de branding personnalisées
        // Couleurs
        $primary_color = isset($settings['primary_color']) ? $settings['primary_color'] : '#ef4444';
        $secondary_color = isset($settings['secondary_color']) ? $settings['secondary_color'] : '#f59e0b';
        $title_color = isset($settings['title_color']) ? $settings['title_color'] : '#ffffff';
        $tag_bg_color = isset($settings['tag_bg_color']) ? $settings['tag_bg_color'] : '#000000';
        $tag_text_color = isset($settings['tag_text_color']) ? $settings['tag_text_color'] : '#ffffff';
        $overlay_color = isset($settings['overlay_color']) ? $settings['overlay_color'] : '#000000';

        // Typographie
        $font_family = isset($settings['font_family']) ? $settings['font_family'] : '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        $title_font_size = isset($settings['title_font_size']) ? $settings['title_font_size'] : 16;
        $title_font_weight = isset($settings['title_font_weight']) ? $settings['title_font_weight'] : 600;
        $tag_font_size = isset($settings['tag_font_size']) ? $settings['tag_font_size'] : 11;

        // Espacements & Bordures
        $border_radius = isset($settings['border_radius']) ? $settings['border_radius'] : 16;
        $gap_size = isset($settings['gap_size']) ? $settings['gap_size'] : 20;
        $overlay_opacity = isset($settings['overlay_opacity']) ? $settings['overlay_opacity'] : 95;
        $shadow_intensity = isset($settings['shadow_intensity']) ? $settings['shadow_intensity'] : 'medium';

        // Définir les ombres selon l'intensité
        $shadow_values = array(
            'light' => '0 1px 4px rgba(0, 0, 0, 0.6), 0 1px 2px rgba(0, 0, 0, 0.7)',
            'medium' => '0 2px 8px rgba(0, 0, 0, 0.8), 0 1px 3px rgba(0, 0, 0, 0.9)',
            'strong' => '0 3px 12px rgba(0, 0, 0, 1), 0 2px 6px rgba(0, 0, 0, 1), 0 1px 3px rgba(0, 0, 0, 1)'
        );
        $text_shadow = isset($shadow_values[$shadow_intensity]) ? $shadow_values[$shadow_intensity] : $shadow_values['medium'];

        // Convertir hex to rgba pour l'overlay et les tags
        function hex_to_rgba($hex, $alpha) {
            $hex = str_replace('#', '', $hex);
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            return "rgba($r, $g, $b, $alpha)";
        }

        $overlay_rgba = hex_to_rgba($overlay_color, $overlay_opacity / 100);
        $tag_bg_rgba = hex_to_rgba($tag_bg_color, 0.7);

        // Créer le style inline avec TOUTES les variables CSS
        $custom_style = sprintf(
            '--primary-color: %s; --secondary-color: %s; --title-color: %s; --tag-bg-color: %s; --tag-text-color: %s; --overlay-color: %s; --font-family: %s; --title-font-size: %spx; --title-font-weight: %s; --tag-font-size: %spx; --border-radius: %spx; --gap-size: %spx; --overlay-opacity: %s; --shadow-medium: %s;',
            esc_attr($primary_color),
            esc_attr($secondary_color),
            esc_attr($title_color),
            esc_attr($tag_bg_rgba),
            esc_attr($tag_text_color),
            esc_attr($overlay_rgba),
            esc_attr($font_family),
            esc_attr($title_font_size),
            esc_attr($title_font_weight),
            esc_attr($tag_font_size),
            esc_attr($border_radius),
            esc_attr($gap_size),
            esc_attr($overlay_opacity / 100),
            esc_attr($text_shadow)
        );

        ob_start();
        ?>
        <div class="<?php echo $gallery_class; ?>" data-gallery-id="<?php echo $gallery_id; ?>" style="<?php echo $custom_style; ?>">

            <?php if (!empty($alt_tags)): ?>
            <!-- Filtres par badges ALT -->
            <div class="gallery-filters">
                <button class="filter-badge active" data-filter="all">Tout afficher</button>
                <?php foreach ($alt_tags as $tag): ?>
                    <button class="filter-badge" data-filter="<?php echo esc_attr($tag); ?>">
                        <?php echo esc_html($tag); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Grille de la galerie -->
            <div class="gallery-grid" data-columns="<?php echo $settings['grid_columns']; ?>">
                <?php foreach ($images as $image): ?>
                    <?php
                    $image_alt = !empty($image['alt']) ? $image['alt'] : '';
                    $image_tags = $this->parse_alt_tags($image_alt);
                    ?>
                    <div class="gallery-item" data-tags="<?php echo esc_attr(implode(',', $image_tags)); ?>">
                        <div class="gallery-item-inner">
                            <img src="<?php echo esc_url($image['url']); ?>"
                                 alt="<?php echo esc_attr($image_alt); ?>"
                                 data-full="<?php echo esc_url($image['url']); ?>">
                            <div class="gallery-item-overlay">
                                <div class="gallery-item-info">
                                    <?php if (!empty($image['title'])): ?>
                                        <h3><?php echo esc_html($image['title']); ?></h3>
                                    <?php endif; ?>
                                    <?php if (!empty($image_tags)): ?>
                                        <div class="gallery-tags">
                                            <?php foreach ($image_tags as $tag): ?>
                                                <span class="tag"><?php echo esc_html($tag); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <button class="view-fullsize" aria-label="Voir en taille réelle">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Modal pour l'image en plein écran -->
            <div class="gallery-lightbox" style="display: none;">
                <button class="lightbox-close">&times;</button>
                <button class="lightbox-prev">‹</button>
                <button class="lightbox-next">›</button>
                <div class="lightbox-content">
                    <img src="" alt="">
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Trier les images selon les paramètres
     */
    private function sort_images($images, $sort_by, $sort_order) {
        usort($images, function($a, $b) use ($sort_by, $sort_order) {
            $value_a = isset($a[$sort_by]) ? $a[$sort_by] : '';
            $value_b = isset($b[$sort_by]) ? $b[$sort_by] : '';

            $comparison = strcasecmp($value_a, $value_b);

            return $sort_order === 'desc' ? -$comparison : $comparison;
        });

        return $images;
    }

    /**
     * Extraire les tags ALT uniques de toutes les images
     */
    private function extract_alt_tags($images) {
        $tags = array();

        foreach ($images as $image) {
            if (!empty($image['alt'])) {
                $image_tags = $this->parse_alt_tags($image['alt']);
                $tags = array_merge($tags, $image_tags);
            }
        }

        return array_unique($tags);
    }

    /**
     * Parser les tags depuis l'attribut ALT
     * Supporte: "rencontre", "rencontre date", "événement spécial"
     */
    private function parse_alt_tags($alt_text) {
        if (empty($alt_text)) {
            return array();
        }

        // Diviser par virgules ou espaces
        $tags = preg_split('/[,\s]+/', $alt_text, -1, PREG_SPLIT_NO_EMPTY);

        // Nettoyer et retourner
        return array_filter(array_map('trim', $tags));
    }
}
