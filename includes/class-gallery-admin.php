<?php
/**
 * Classe pour gérer l'interface d'administration
 */
class WP_Custom_Gallery_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    /**
     * Ajouter le menu dans le dashboard
     */
    public function add_admin_menu() {
        add_menu_page(
            'Custom Gallery',
            'Galeries',
            'manage_options',
            'wp-custom-gallery',
            array($this, 'render_admin_page'),
            'dashicons-images-alt2',
            30
        );
    }

    /**
     * Charger les assets CSS/JS pour l'admin
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'toplevel_page_wp-custom-gallery') {
            return;
        }

        // CSS
        wp_enqueue_style(
            'wp-custom-gallery-admin',
            WP_CUSTOM_GALLERY_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            WP_CUSTOM_GALLERY_VERSION
        );

        // JS
        wp_enqueue_media();
        wp_enqueue_script(
            'wp-custom-gallery-admin',
            WP_CUSTOM_GALLERY_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            WP_CUSTOM_GALLERY_VERSION,
            true
        );

        wp_localize_script('wp-custom-gallery-admin', 'wpCustomGallery', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_custom_gallery_nonce')
        ));
    }

    /**
     * Afficher la page d'administration
     */
    public function render_admin_page() {
        global $wpdb;

        // SÉCURITÉ: Utiliser une requête préparée même pour les requêtes simples
        $galleries = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}custom_galleries ORDER BY created_at DESC"
        ));

        ?>
        <div class="wrap wp-custom-gallery-admin">
            <h1>Galeries Photo</h1>

            <div class="gallery-actions">
                <button class="button button-primary" id="create-new-gallery">
                    <span class="dashicons dashicons-plus-alt"></span> Créer une nouvelle galerie
                </button>
            </div>

            <!-- Liste des galeries existantes -->
            <div class="galleries-list">
                <h2>Galeries existantes</h2>
                <?php if (empty($galleries)): ?>
                    <p>Aucune galerie créée pour le moment.</p>
                <?php else: ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Shortcode</th>
                                <th>Images</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($galleries as $gallery): ?>
                                <?php
                                $images = json_decode($gallery->images, true);
                                $image_count = is_array($images) ? count($images) : 0;
                                ?>
                                <tr>
                                    <td><strong><?php echo esc_html($gallery->name); ?></strong></td>
                                    <td>
                                        <code class="shortcode-display">[custom_gallery id="<?php echo $gallery->id; ?>"]</code>
                                        <button class="button button-small copy-shortcode" data-shortcode='[custom_gallery id="<?php echo $gallery->id; ?>"]'>Copier</button>
                                    </td>
                                    <td><?php echo $image_count; ?> image(s)</td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($gallery->created_at)); ?></td>
                                    <td>
                                        <button class="button edit-gallery" data-id="<?php echo $gallery->id; ?>">Éditer</button>
                                        <button class="button delete-gallery" data-id="<?php echo $gallery->id; ?>">Supprimer</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Modal pour créer/éditer une galerie -->
            <div id="gallery-modal" class="gallery-modal" style="display: none;">
                <div class="gallery-modal-content">
                    <span class="gallery-modal-close">&times;</span>
                    <h2 id="modal-title">Créer une galerie</h2>

                    <form id="gallery-form">
                        <input type="hidden" id="gallery-id" name="gallery_id" value="">

                        <div class="form-group">
                            <label for="gallery-name">Nom de la galerie:</label>
                            <input type="text" id="gallery-name" name="gallery_name" required>
                        </div>

                        <div class="form-group">
                            <label>Images de la galerie:</label>
                            <button type="button" class="button" id="select-images">
                                <span class="dashicons dashicons-images-alt2"></span> Sélectionner les images
                            </button>
                            <div id="selected-images" class="selected-images-grid"></div>
                        </div>

                        <div class="form-group">
                            <h3>Paramètres de la galerie</h3>

                            <label for="grid-columns">Nombre de colonnes:</label>
                            <select id="grid-columns" name="grid_columns">
                                <option value="3">3 colonnes</option>
                                <option value="4" selected>4 colonnes</option>
                                <option value="5">5 colonnes</option>
                                <option value="6">6 colonnes</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sort-order">Ordre d'affichage:</label>
                            <select id="sort-order" name="sort_order">
                                <option value="asc">Croissant (A-Z)</option>
                                <option value="desc">Décroissant (Z-A)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sort-by">Trier par:</label>
                            <select id="sort-by" name="sort_by">
                                <option value="title">Titre</option>
                                <option value="alt">Attribut ALT</option>
                                <option value="date">Date</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="image-style">Style des images:</label>
                            <select id="image-style" name="image_style">
                                <option value="color">Couleur</option>
                                <option value="grayscale">Noir et blanc (grayscale)</option>
                            </select>
                        </div>

                        <div class="form-group branding-section">
                            <h3>🎨 Branding & Design Complet</h3>
                            <p style="margin: 0 0 20px 0; color: #666; font-size: 13px;">
                                Personnalisez TOUS les éléments de votre galerie avec des variables CSS professionnelles
                            </p>

                            <!-- COULEURS -->
                            <div class="branding-subsection">
                                <h4>🌈 Couleurs principales</h4>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                                    <div>
                                        <label for="primary-color">Couleur primaire (badges actifs, gradient):</label>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <input type="color" id="primary-color" name="primary_color" value="#ef4444" style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                                            <input type="text" id="primary-color-text" value="#ef4444" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9; font-family: monospace;">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="secondary-color">Couleur secondaire (fin du gradient):</label>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <input type="color" id="secondary-color" name="secondary_color" value="#f59e0b" style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                                            <input type="text" id="secondary-color-text" value="#f59e0b" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9; font-family: monospace;">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="title-color">Couleur du titre d'image:</label>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <input type="color" id="title-color" name="title_color" value="#ffffff" style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                                            <input type="text" id="title-color-text" value="#ffffff" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9; font-family: monospace;">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="tag-bg-color">Couleur de fond des tags:</label>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <input type="color" id="tag-bg-color" name="tag_bg_color" value="#000000" style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                                            <input type="text" id="tag-bg-color-text" value="#000000" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9; font-family: monospace;">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="tag-text-color">Couleur du texte des tags:</label>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <input type="color" id="tag-text-color" name="tag_text_color" value="#ffffff" style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                                            <input type="text" id="tag-text-color-text" value="#ffffff" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9; font-family: monospace;">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="overlay-color">Couleur de l'overlay (hover):</label>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <input type="color" id="overlay-color" name="overlay_color" value="#000000" style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                                            <input type="text" id="overlay-color-text" value="#000000" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9; font-family: monospace;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TYPOGRAPHIE -->
                            <div class="branding-subsection">
                                <h4>✍️ Typographie</h4>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                                    <div>
                                        <label for="font-family">Police de caractères:</label>
                                        <input type="text" id="font-family" name="font_family" value="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
                                               style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace;">
                                        <small style="color: #999;">Ex: 'Poppins', sans-serif</small>
                                    </div>

                                    <div>
                                        <label for="title-font-size">Taille du titre (px):</label>
                                        <input type="number" id="title-font-size" name="title_font_size" value="16" min="10" max="40"
                                               style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                    </div>

                                    <div>
                                        <label for="title-font-weight">Épaisseur du titre:</label>
                                        <select id="title-font-weight" name="title_font_weight" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                            <option value="400">Normal (400)</option>
                                            <option value="500">Medium (500)</option>
                                            <option value="600" selected>Semi-Bold (600)</option>
                                            <option value="700">Bold (700)</option>
                                            <option value="800">Extra-Bold (800)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="tag-font-size">Taille des tags (px):</label>
                                        <input type="number" id="tag-font-size" name="tag_font_size" value="11" min="8" max="20"
                                               style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                    </div>
                                </div>
                            </div>

                            <!-- ESPACEMENTS & BORDURES -->
                            <div class="branding-subsection">
                                <h4>📐 Espacements & Bordures</h4>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                                    <div>
                                        <label for="border-radius">Arrondi des images (px):</label>
                                        <input type="number" id="border-radius" name="border_radius" value="16" min="0" max="50"
                                               style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                        <small style="color: #999;">0 = carré, 16 = moderne, 50 = très arrondi</small>
                                    </div>

                                    <div>
                                        <label for="gap-size">Espacement entre images (px):</label>
                                        <input type="number" id="gap-size" name="gap_size" value="20" min="5" max="50"
                                               style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                    </div>

                                    <div>
                                        <label for="overlay-opacity">Opacité de l'overlay (%):</label>
                                        <input type="range" id="overlay-opacity" name="overlay_opacity" value="95" min="50" max="100"
                                               style="width: 100%;">
                                        <output id="overlay-opacity-value" style="display: block; margin-top: 5px; color: #666;">95%</output>
                                    </div>

                                    <div>
                                        <label for="shadow-intensity">Intensité de l'ombre du texte:</label>
                                        <select id="shadow-intensity" name="shadow_intensity" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                            <option value="light">Légère</option>
                                            <option value="medium" selected>Moyenne</option>
                                            <option value="strong">Forte</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- PREVIEW EN TEMPS RÉEL -->
                            <div class="branding-subsection">
                                <h4>👁️ Aperçu en temps réel</h4>
                                <div id="branding-preview-container" style="padding: 20px; background: #f5f5f5; border-radius: 8px; border: 2px dashed #ddd;"></div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="button button-primary">💾 Enregistrer la galerie</button>
                            <button type="button" class="button" id="cancel-gallery">✕ Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
}
