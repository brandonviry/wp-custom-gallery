<?php
/**
 * Classe pour gérer les requêtes AJAX
 */
class WP_Custom_Gallery_Ajax {

    public function __construct() {
        add_action('wp_ajax_save_gallery', array($this, 'save_gallery'));
        add_action('wp_ajax_delete_gallery', array($this, 'delete_gallery'));
        add_action('wp_ajax_get_gallery', array($this, 'get_gallery'));
    }

    /**
     * Sauvegarder une galerie
     */
    public function save_gallery() {
        check_ajax_referer('wp_custom_gallery_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission refusée');
        }

        // SÉCURITÉ: Vérifier le rate limit (max 20 sauvegardes par minute)
        if (!WP_Gallery_Rate_Limiter::check_rate_limit('save_gallery', 20, 60)) {
            $remaining = WP_Gallery_Rate_Limiter::get_time_remaining('save_gallery');
            wp_send_json_error('Trop de requêtes. Veuillez patienter ' . $remaining . ' secondes.');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'custom_galleries';

        $gallery_id = isset($_POST['gallery_id']) ? intval($_POST['gallery_id']) : 0;

        // Validation stricte du nom de galerie
        $gallery_name = isset($_POST['gallery_name']) ? sanitize_text_field($_POST['gallery_name']) : '';
        if (empty($gallery_name) || strlen($gallery_name) > 255) {
            wp_send_json_error('Nom de galerie invalide');
        }

        // SÉCURITÉ: Sanitizer et valider TOUTES les images
        $raw_images = isset($_POST['images']) ? $_POST['images'] : array();
        $sanitized_images = array();

        if (is_array($raw_images)) {
            foreach ($raw_images as $image) {
                // Valider que c'est bien un tableau
                if (!is_array($image)) continue;

                // Vérifier que l'ID existe et est un entier valide
                $image_id = isset($image['id']) ? intval($image['id']) : 0;
                if ($image_id <= 0) continue;

                // Vérifier que l'attachment existe et est bien une image
                $attachment = get_post($image_id);
                if (!$attachment || $attachment->post_type !== 'attachment') continue;

                // SÉCURITÉ: Vérifier le type MIME (protection contre upload malveillant)
                $mime_type = get_post_mime_type($image_id);
                if (!$mime_type || strpos($mime_type, 'image/') !== 0) continue;

                // Sanitizer tous les champs
                $sanitized_images[] = array(
                    'id' => $image_id,
                    'url' => esc_url_raw(isset($image['url']) ? $image['url'] : ''),
                    'title' => sanitize_text_field(isset($image['title']) ? $image['title'] : ''),
                    'alt' => sanitize_text_field(isset($image['alt']) ? $image['alt'] : ''),
                    'thumbnail' => esc_url_raw(isset($image['thumbnail']) ? $image['thumbnail'] : '')
                );
            }
        }

        $images = json_encode($sanitized_images);

        // Validation stricte des paramètres avec plages min/max
        $settings = array(
            // Paramètres de base avec validation stricte
            'grid_columns' => max(1, min(12, isset($_POST['grid_columns']) ? intval($_POST['grid_columns']) : 4)),
            'sort_order' => in_array($_POST['sort_order'] ?? 'asc', array('asc', 'desc')) ? $_POST['sort_order'] : 'asc',
            'sort_by' => in_array($_POST['sort_by'] ?? 'title', array('title', 'alt', 'date')) ? $_POST['sort_by'] : 'title',
            'image_style' => in_array($_POST['image_style'] ?? 'color', array('color', 'grayscale')) ? $_POST['image_style'] : 'color',

            // Couleurs (déjà sécurisé avec sanitize_hex_color)
            'primary_color' => sanitize_hex_color($_POST['primary_color'] ?? '#ef4444') ?: '#ef4444',
            'secondary_color' => sanitize_hex_color($_POST['secondary_color'] ?? '#f59e0b') ?: '#f59e0b',
            'title_color' => sanitize_hex_color($_POST['title_color'] ?? '#ffffff') ?: '#ffffff',
            'tag_bg_color' => sanitize_hex_color($_POST['tag_bg_color'] ?? '#000000') ?: '#000000',
            'tag_text_color' => sanitize_hex_color($_POST['tag_text_color'] ?? '#ffffff') ?: '#ffffff',
            'overlay_color' => sanitize_hex_color($_POST['overlay_color'] ?? '#000000') ?: '#000000',

            // Typographie avec validation stricte
            'font_family' => sanitize_text_field($_POST['font_family'] ?? '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'),
            'title_font_size' => max(10, min(72, isset($_POST['title_font_size']) ? intval($_POST['title_font_size']) : 16)),
            'title_font_weight' => in_array($_POST['title_font_weight'] ?? 600, array('400', '500', '600', '700', '800')) ? intval($_POST['title_font_weight']) : 600,
            'tag_font_size' => max(8, min(24, isset($_POST['tag_font_size']) ? intval($_POST['tag_font_size']) : 11)),

            // Espacements & Bordures avec validation stricte
            'border_radius' => max(0, min(100, isset($_POST['border_radius']) ? intval($_POST['border_radius']) : 16)),
            'gap_size' => max(0, min(100, isset($_POST['gap_size']) ? intval($_POST['gap_size']) : 20)),
            'overlay_opacity' => max(0, min(100, isset($_POST['overlay_opacity']) ? intval($_POST['overlay_opacity']) : 95)),
            'shadow_intensity' => in_array($_POST['shadow_intensity'] ?? 'medium', array('light', 'medium', 'strong')) ? $_POST['shadow_intensity'] : 'medium'
        );

        $data = array(
            'name' => $gallery_name,
            'images' => $images,
            'settings' => json_encode($settings)
        );

        if ($gallery_id > 0) {
            // Mise à jour
            $wpdb->update($table_name, $data, array('id' => $gallery_id));
            wp_send_json_success(array('message' => 'Galerie mise à jour', 'id' => $gallery_id));
        } else {
            // Création
            $data['shortcode'] = 'custom_gallery_' . time();
            $wpdb->insert($table_name, $data);
            $new_id = $wpdb->insert_id;
            wp_send_json_success(array('message' => 'Galerie créée', 'id' => $new_id));
        }
    }

    /**
     * Supprimer une galerie
     */
    public function delete_gallery() {
        check_ajax_referer('wp_custom_gallery_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission refusée');
        }

        // SÉCURITÉ: Vérifier le rate limit (max 10 suppressions par minute)
        if (!WP_Gallery_Rate_Limiter::check_rate_limit('delete_gallery', 10, 60)) {
            $remaining = WP_Gallery_Rate_Limiter::get_time_remaining('delete_gallery');
            wp_send_json_error('Trop de requêtes. Veuillez patienter ' . $remaining . ' secondes.');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'custom_galleries';
        $gallery_id = intval($_POST['gallery_id']);

        $wpdb->delete($table_name, array('id' => $gallery_id));
        wp_send_json_success('Galerie supprimée');
    }

    /**
     * Récupérer une galerie
     */
    public function get_gallery() {
        check_ajax_referer('wp_custom_gallery_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission refusée');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'custom_galleries';
        $gallery_id = intval($_POST['gallery_id']);

        $gallery = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $gallery_id));

        if ($gallery) {
            $gallery->images = json_decode($gallery->images, true);
            $gallery->settings = json_decode($gallery->settings, true);
            wp_send_json_success($gallery);
        } else {
            wp_send_json_error('Galerie non trouvée');
        }
    }
}
