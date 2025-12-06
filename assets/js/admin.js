jQuery(document).ready(function($) {
    let selectedImages = [];
    let currentGalleryId = 0;

    // Ouvrir le modal pour créer une galerie
    $('#create-new-gallery').on('click', function() {
        resetForm();
        $('#modal-title').text('Créer une nouvelle galerie');
        $('#gallery-modal').fadeIn();
    });

    // Fermer le modal
    $('.gallery-modal-close, #cancel-gallery').on('click', function() {
        $('#gallery-modal').fadeOut();
    });

    // Sélectionner les images - VERSION CORRIGÉE POUR SÉLECTION MULTIPLE
    $(document).on('click', '#select-images', function(e) {
        e.preventDefault();

        // Détruire l'ancien frame s'il existe
        if (window.galleryMediaFrame) {
            window.galleryMediaFrame.close();
            window.galleryMediaFrame = null;
        }

        // Créer un nouveau media frame avec configuration complète
        window.galleryMediaFrame = wp.media({
            title: 'Sélectionner les images pour la galerie (Maintenez Ctrl/Cmd pour sélectionner plusieurs images)',
            button: {
                text: 'Utiliser ces images'
            },
            library: {
                type: 'image'
            },
            multiple: 'add',  // Mode multiple forcé
            frame: 'select'
        });

        // Pré-sélectionner les images déjà choisies lors de l'édition
        window.galleryMediaFrame.on('open', function() {
            const selection = window.galleryMediaFrame.state().get('selection');

            // Vider la sélection actuelle
            selection.reset();

            // Ajouter les images déjà sélectionnées
            if (selectedImages && selectedImages.length > 0) {
                selectedImages.forEach(function(image) {
                    const attachment = wp.media.attachment(image.id);
                    attachment.fetch();
                    selection.add(attachment);
                });
            }
        });

        // Quand l'utilisateur clique sur "Utiliser ces images"
        window.galleryMediaFrame.on('select', function() {
            const selection = window.galleryMediaFrame.state().get('selection');
            selectedImages = [];

            selection.each(function(attachment) {
                attachment = attachment.toJSON();
                selectedImages.push({
                    id: attachment.id,
                    url: attachment.url,
                    title: attachment.title || 'Sans titre',
                    alt: attachment.alt || '',
                    thumbnail: attachment.sizes && attachment.sizes.thumbnail
                        ? attachment.sizes.thumbnail.url
                        : attachment.url
                });
            });

            console.log('✅ Images sélectionnées:', selectedImages.length);
            displaySelectedImages();
        });

        window.galleryMediaFrame.open();
    });

    // Afficher les images sélectionnées
    function displaySelectedImages() {
        const container = $('#selected-images');
        container.empty();

        if (selectedImages.length === 0) {
            container.html('<p class="no-images">Aucune image sélectionnée. Cliquez sur le bouton ci-dessus pour ajouter des images.</p>');
            return;
        }

        container.html(`<p class="images-count"><strong>${selectedImages.length}</strong> image(s) sélectionnée(s)</p>`);

        selectedImages.forEach(function(image, index) {
            const imageHtml = `
                <div class="selected-image-item" data-index="${index}">
                    <img src="${image.thumbnail}" alt="${image.alt}">
                    <div class="image-info">
                        <strong>${image.title}</strong>
                        ${image.alt ? `<span class="alt-tag">ALT: ${image.alt}</span>` : '<span class="no-alt">Pas d\'attribut ALT</span>'}
                    </div>
                    <button type="button" class="remove-image" data-index="${index}" title="Retirer cette image">
                        <span class="dashicons dashicons-no"></span>
                    </button>
                </div>
            `;
            container.append(imageHtml);
        });

        // Gérer la suppression d'images
        $('.remove-image').off('click').on('click', function() {
            const index = $(this).data('index');
            selectedImages.splice(index, 1);
            displaySelectedImages();
        });
    }

    // Sauvegarder la galerie
    $('#gallery-form').on('submit', function(e) {
        e.preventDefault();

        const galleryName = $('#gallery-name').val().trim();

        if (!galleryName) {
            alert('⚠️ Veuillez donner un nom à votre galerie');
            $('#gallery-name').focus();
            return;
        }

        if (selectedImages.length === 0) {
            alert('⚠️ Veuillez sélectionner au moins une image pour la galerie');
            return;
        }

        const formData = {
            action: 'save_gallery',
            nonce: wpCustomGallery.nonce,
            gallery_id: currentGalleryId,
            gallery_name: galleryName,
            images: selectedImages,
            grid_columns: $('#grid-columns').val(),
            sort_order: $('#sort-order').val(),
            sort_by: $('#sort-by').val(),
            image_style: $('#image-style').val(),

            // Couleurs
            primary_color: $('#primary-color').val(),
            secondary_color: $('#secondary-color').val(),
            title_color: $('#title-color').val(),
            tag_bg_color: $('#tag-bg-color').val(),
            tag_text_color: $('#tag-text-color').val(),
            overlay_color: $('#overlay-color').val(),

            // Typographie
            font_family: $('#font-family').val(),
            title_font_size: $('#title-font-size').val(),
            title_font_weight: $('#title-font-weight').val(),
            tag_font_size: $('#tag-font-size').val(),

            // Espacements & Bordures
            border_radius: $('#border-radius').val(),
            gap_size: $('#gap-size').val(),
            overlay_opacity: $('#overlay-opacity').val(),
            shadow_intensity: $('#shadow-intensity').val()
        };

        // Afficher un loader
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('Enregistrement...');

        $.post(wpCustomGallery.ajaxUrl, formData, function(response) {
            if (response.success) {
                alert('✅ ' + response.data.message);
                location.reload();
            } else {
                alert('❌ Erreur: ' + response.data);
                $submitBtn.prop('disabled', false).text(originalText);
            }
        }).fail(function() {
            alert('❌ Erreur de connexion. Veuillez réessayer.');
            $submitBtn.prop('disabled', false).text(originalText);
        });
    });

    // Éditer une galerie
    $(document).on('click', '.edit-gallery', function() {
        const galleryId = $(this).data('id');

        $.post(wpCustomGallery.ajaxUrl, {
            action: 'get_gallery',
            nonce: wpCustomGallery.nonce,
            gallery_id: galleryId
        }, function(response) {
            if (response.success) {
                const gallery = response.data;
                currentGalleryId = gallery.id;

                $('#modal-title').text('Éditer la galerie');
                $('#gallery-name').val(gallery.name);
                $('#grid-columns').val(gallery.settings.grid_columns || 4);
                $('#sort-order').val(gallery.settings.sort_order || 'asc');
                $('#sort-by').val(gallery.settings.sort_by || 'title');
                $('#image-style').val(gallery.settings.image_style || 'color');

                // Couleurs
                $('#primary-color').val(gallery.settings.primary_color || '#ef4444');
                $('#primary-color-text').val(gallery.settings.primary_color || '#ef4444');
                $('#secondary-color').val(gallery.settings.secondary_color || '#f59e0b');
                $('#secondary-color-text').val(gallery.settings.secondary_color || '#f59e0b');
                $('#title-color').val(gallery.settings.title_color || '#ffffff');
                $('#title-color-text').val(gallery.settings.title_color || '#ffffff');
                $('#tag-bg-color').val(gallery.settings.tag_bg_color || '#000000');
                $('#tag-bg-color-text').val(gallery.settings.tag_bg_color || '#000000');
                $('#tag-text-color').val(gallery.settings.tag_text_color || '#ffffff');
                $('#tag-text-color-text').val(gallery.settings.tag_text_color || '#ffffff');
                $('#overlay-color').val(gallery.settings.overlay_color || '#000000');
                $('#overlay-color-text').val(gallery.settings.overlay_color || '#000000');

                // Typographie
                $('#font-family').val(gallery.settings.font_family || '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif');
                $('#title-font-size').val(gallery.settings.title_font_size || 16);
                $('#title-font-weight').val(gallery.settings.title_font_weight || 600);
                $('#tag-font-size').val(gallery.settings.tag_font_size || 11);

                // Espacements & Bordures
                $('#border-radius').val(gallery.settings.border_radius || 16);
                $('#gap-size').val(gallery.settings.gap_size || 20);
                $('#overlay-opacity').val(gallery.settings.overlay_opacity || 95);
                $('#overlay-opacity-value').text(gallery.settings.overlay_opacity || 95 + '%');
                $('#shadow-intensity').val(gallery.settings.shadow_intensity || 'medium');

                selectedImages = gallery.images || [];
                displaySelectedImages();
                updateBrandingPreview();

                $('#gallery-modal').fadeIn();
            }
        });
    });

    // Supprimer une galerie
    $(document).on('click', '.delete-gallery', function() {
        if (!confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette galerie ? Cette action est irréversible.')) {
            return;
        }

        const galleryId = $(this).data('id');
        const $btn = $(this);
        $btn.prop('disabled', true).text('Suppression...');

        $.post(wpCustomGallery.ajaxUrl, {
            action: 'delete_gallery',
            nonce: wpCustomGallery.nonce,
            gallery_id: galleryId
        }, function(response) {
            if (response.success) {
                alert('✅ Galerie supprimée avec succès');
                location.reload();
            } else {
                alert('❌ Erreur lors de la suppression');
                $btn.prop('disabled', false).text('Supprimer');
            }
        });
    });

    // Copier le shortcode
    $(document).on('click', '.copy-shortcode', function() {
        const shortcode = $(this).data('shortcode');

        // Méthode moderne pour copier
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(shortcode).then(() => {
                $(this).text('✓ Copié!').css('background', '#10b981');
                setTimeout(() => {
                    $(this).text('Copier').css('background', '');
                }, 2000);
            });
        } else {
            // Fallback pour navigateurs plus anciens
            const tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(shortcode).select();
            document.execCommand('copy');
            tempInput.remove();

            $(this).text('✓ Copié!');
            setTimeout(() => {
                $(this).text('Copier');
            }, 2000);
        }
    });

    // Réinitialiser le formulaire
    function resetForm() {
        currentGalleryId = 0;
        selectedImages = [];
        $('#gallery-form')[0].reset();
        $('#selected-images').empty();
        $('#primary-color').val('#ef4444');
        $('#secondary-color').val('#f59e0b');
    }

    // Fermer le modal en cliquant à l'extérieur
    $(window).on('click', function(e) {
        if ($(e.target).hasClass('gallery-modal')) {
            $('#gallery-modal').fadeOut();
        }
    });

    // Synchroniser TOUS les sélecteurs de couleur avec les champs texte
    $(document).on('input', '#primary-color', function() {
        $('#primary-color-text').val($(this).val());
        updateBrandingPreview();
    });

    $(document).on('input', '#secondary-color', function() {
        $('#secondary-color-text').val($(this).val());
        updateBrandingPreview();
    });

    $(document).on('input', '#title-color', function() {
        $('#title-color-text').val($(this).val());
        updateBrandingPreview();
    });

    $(document).on('input', '#tag-bg-color', function() {
        $('#tag-bg-color-text').val($(this).val());
        updateBrandingPreview();
    });

    $(document).on('input', '#tag-text-color', function() {
        $('#tag-text-color-text').val($(this).val());
        updateBrandingPreview();
    });

    $(document).on('input', '#overlay-color', function() {
        $('#overlay-color-text').val($(this).val());
        updateBrandingPreview();
    });

    // Synchroniser les contrôles de typographie et espacements
    $(document).on('input change', '#font-family, #title-font-size, #title-font-weight, #tag-font-size, #border-radius, #gap-size, #shadow-intensity', function() {
        updateBrandingPreview();
    });

    // Gérer le slider d'opacité
    $(document).on('input', '#overlay-opacity', function() {
        $('#overlay-opacity-value').text($(this).val() + '%');
        updateBrandingPreview();
    });

    // Preview COMPLET du branding en temps réel
    function updateBrandingPreview() {
        const $container = $('#branding-preview-container');
        if (!$container.length) return;

        // Récupérer toutes les valeurs
        const primaryColor = $('#primary-color').val() || '#ef4444';
        const secondaryColor = $('#secondary-color').val() || '#f59e0b';
        const titleColor = $('#title-color').val() || '#ffffff';
        const tagBgColor = $('#tag-bg-color').val() || '#000000';
        const tagTextColor = $('#tag-text-color').val() || '#ffffff';
        const overlayColor = $('#overlay-color').val() || '#000000';

        const fontFamily = $('#font-family').val() || '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        const titleFontSize = $('#title-font-size').val() || 16;
        const titleFontWeight = $('#title-font-weight').val() || 600;
        const tagFontSize = $('#tag-font-size').val() || 11;

        const borderRadius = $('#border-radius').val() || 16;
        const gapSize = $('#gap-size').val() || 20;
        const overlayOpacity = ($('#overlay-opacity').val() || 95) / 100;
        const shadowIntensity = $('#shadow-intensity').val() || 'medium';

        // Définir les ombres selon l'intensité
        const shadows = {
            light: '0 1px 4px rgba(0,0,0,0.6), 0 1px 2px rgba(0,0,0,0.7)',
            medium: '0 2px 8px rgba(0,0,0,0.8), 0 1px 3px rgba(0,0,0,0.9)',
            strong: '0 3px 12px rgba(0,0,0,1), 0 2px 6px rgba(0,0,0,1), 0 1px 3px rgba(0,0,0,1)'
        };
        const textShadow = shadows[shadowIntensity];

        // Convertir hex to rgba pour l'overlay
        function hexToRgba(hex, alpha) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        const overlayRgba = hexToRgba(overlayColor, overlayOpacity);
        const tagBgRgba = hexToRgba(tagBgColor, 0.7);

        $container.html(`
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <!-- Aperçu des badges -->
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h5 style="margin: 0 0 15px 0; font-size: 13px; color: #666;">Badges de filtres</h5>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
                        <div style="
                            background: white;
                            color: #374151;
                            padding: 10px 20px;
                            border-radius: 9999px;
                            font-weight: 500;
                            font-size: 14px;
                            font-family: ${fontFamily};
                            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        ">Badge inactif</div>
                        <div style="
                            background: linear-gradient(to right, ${primaryColor}, ${secondaryColor});
                            color: white;
                            padding: 10px 20px;
                            border-radius: 9999px;
                            font-weight: 500;
                            font-size: 14px;
                            font-family: ${fontFamily};
                            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
                        ">Badge actif</div>
                    </div>
                </div>

                <!-- Aperçu de l'image avec overlay -->
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h5 style="margin: 0 0 15px 0; font-size: 13px; color: #666;">Carte image (hover)</h5>
                    <div style="
                        position: relative;
                        width: 100%;
                        padding-bottom: 100%;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        border-radius: ${borderRadius}px;
                        overflow: hidden;
                    ">
                        <div style="
                            position: absolute;
                            bottom: 0;
                            left: 0;
                            right: 0;
                            padding: 16px;
                            background: linear-gradient(to top, ${overlayRgba} 0%, transparent 100%);
                        ">
                            <h3 style="
                                margin: 0 0 8px 0;
                                font-size: ${titleFontSize}px;
                                font-weight: ${titleFontWeight};
                                font-family: ${fontFamily};
                                color: ${titleColor};
                                text-shadow: ${textShadow};
                                line-height: 1.3;
                            ">Titre de l'image</h3>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                <span style="
                                    background: ${tagBgRgba};
                                    color: ${tagTextColor};
                                    padding: 4px 10px;
                                    border-radius: 12px;
                                    font-size: ${tagFontSize}px;
                                    font-weight: 600;
                                    font-family: ${fontFamily};
                                    text-transform: uppercase;
                                    letter-spacing: 0.5px;
                                    text-shadow: ${textShadow};
                                ">TAG 1</span>
                                <span style="
                                    background: ${tagBgRgba};
                                    color: ${tagTextColor};
                                    padding: 4px 10px;
                                    border-radius: 12px;
                                    font-size: ${tagFontSize}px;
                                    font-weight: 600;
                                    font-family: ${fontFamily};
                                    text-transform: uppercase;
                                    letter-spacing: 0.5px;
                                    text-shadow: ${textShadow};
                                ">TAG 2</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 15px; padding: 12px; background: #e0f2fe; border-left: 4px solid #0284c7; border-radius: 4px; font-size: 12px; color: #075985;">
                <strong>💡 Astuce:</strong> Toutes les valeurs utilisent des variables CSS. Chaque galerie peut avoir son propre branding unique !
            </div>
        `);
    }

    // Initialiser le preview quand le modal s'ouvre
    $(document).on('DOMNodeInserted', '#branding-preview-container', function() {
        if ($('#primary-color').length) {
            updateBrandingPreview();
        }
    });
});
