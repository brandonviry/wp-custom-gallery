<?php
/**
 * Plugin Name: WP Custom Gallery
 * Description: Plugin de galerie photo personnalisée avec filtres par alt, tri et affichage en grayscale
 * Version: 1.0.0
 * Author: VIRY brandon 
 * Author URI: https://viry-brandon.fr
 * License: GPL v2 
 * Text Domain: wp-custom-gallery
 */

// Empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

// Définir les constantes du plugin
define('WP_CUSTOM_GALLERY_VERSION', '1.0.0');
define('WP_CUSTOM_GALLERY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_CUSTOM_GALLERY_PLUGIN_URL', plugin_dir_url(__FILE__));

// Inclure les fichiers nécessaires
require_once WP_CUSTOM_GALLERY_PLUGIN_DIR . 'includes/class-gallery-rate-limiter.php';
require_once WP_CUSTOM_GALLERY_PLUGIN_DIR . 'includes/class-gallery-admin.php';
require_once WP_CUSTOM_GALLERY_PLUGIN_DIR . 'includes/class-gallery-shortcode.php';
require_once WP_CUSTOM_GALLERY_PLUGIN_DIR . 'includes/class-gallery-ajax.php';

// Initialiser le plugin
function wp_custom_gallery_init() {
    // Initialiser les classes
    new WP_Custom_Gallery_Admin();
    new WP_Custom_Gallery_Shortcode();
    new WP_Custom_Gallery_Ajax();
}
add_action('plugins_loaded', 'wp_custom_gallery_init');

// Activer le plugin
function wp_custom_gallery_activate() {
    // Créer la table pour stocker les galeries
    global $wpdb;

    // SÉCURITÉ: Valider le préfixe WordPress avant utilisation
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $wpdb->prefix)) {
        wp_die('Invalid database prefix detected. Plugin activation aborted for security reasons.');
    }

    $table_name = $wpdb->prefix . 'custom_galleries';
    $charset_collate = $wpdb->get_charset_collate();

    // SÉCURITÉ: Utiliser le nom de table sécurisé dans la requête
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        images longtext NOT NULL,
        settings longtext NOT NULL,
        shortcode varchar(100) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'wp_custom_gallery_activate');

// Désactiver le plugin
function wp_custom_gallery_deactivate() {
    // Nettoyage si nécessaire
}
register_deactivation_hook(__FILE__, 'wp_custom_gallery_deactivate');
