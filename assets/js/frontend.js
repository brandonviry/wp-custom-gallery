jQuery(document).ready(function($) {

    // Gestion des filtres par tags
    $('.filter-badge').on('click', function() {
        const filter = $(this).data('filter');
        const gallery = $(this).closest('.wp-custom-gallery');
        const items = gallery.find('.gallery-item');

        // Mettre à jour les badges actifs
        $(this).siblings().removeClass('active');
        $(this).addClass('active');

        // Filtrer les images
        if (filter === 'all') {
            items.removeClass('hidden').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
            });
        } else {
            items.each(function(index) {
                const tags = $(this).data('tags').toString().toLowerCase().split(',');
                const filterLower = filter.toLowerCase();

                if (tags.some(tag => tag.includes(filterLower))) {
                    $(this).removeClass('hidden').css('animation-delay', (index * 0.1) + 's');
                } else {
                    $(this).addClass('hidden');
                }
            });
        }

        // Animer l'apparition des items filtrés
        items.not('.hidden').each(function() {
            $(this).css('animation', 'none');
            setTimeout(() => {
                $(this).css('animation', '');
            }, 10);
        });
    });

    // Lightbox
    let currentImageIndex = 0;
    let galleryImages = [];

    $('.view-fullsize').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const gallery = $(this).closest('.wp-custom-gallery');
        const item = $(this).closest('.gallery-item');
        const visibleItems = gallery.find('.gallery-item:not(.hidden)');

        // Construire le tableau d'images visibles
        galleryImages = [];
        visibleItems.each(function() {
            const img = $(this).find('img');
            galleryImages.push({
                url: img.data('full'),
                alt: img.attr('alt')
            });
        });

        // Trouver l'index de l'image actuelle
        currentImageIndex = visibleItems.index(item);

        // Afficher la lightbox
        showLightbox(gallery);
    });

    function showLightbox(gallery) {
        const lightbox = gallery.find('.gallery-lightbox');
        const img = lightbox.find('img');

        img.attr('src', galleryImages[currentImageIndex].url);
        img.attr('alt', galleryImages[currentImageIndex].alt);

        lightbox.fadeIn(300);
        $('body').css('overflow', 'hidden');
    }

    function closeLightbox() {
        $('.gallery-lightbox').fadeOut(300);
        $('body').css('overflow', '');
    }

    // Fermer la lightbox
    $('.lightbox-close').on('click', function() {
        closeLightbox();
    });

    // Navigation précédent
    $('.lightbox-prev').on('click', function(e) {
        e.stopPropagation();
        currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
        const lightbox = $(this).closest('.gallery-lightbox');
        const img = lightbox.find('img');

        img.fadeOut(200, function() {
            img.attr('src', galleryImages[currentImageIndex].url);
            img.attr('alt', galleryImages[currentImageIndex].alt);
            img.fadeIn(200);
        });
    });

    // Navigation suivant
    $('.lightbox-next').on('click', function(e) {
        e.stopPropagation();
        currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
        const lightbox = $(this).closest('.gallery-lightbox');
        const img = lightbox.find('img');

        img.fadeOut(200, function() {
            img.attr('src', galleryImages[currentImageIndex].url);
            img.attr('alt', galleryImages[currentImageIndex].alt);
            img.fadeIn(200);
        });
    });

    // Fermer en cliquant sur le fond
    $('.gallery-lightbox').on('click', function(e) {
        if ($(e.target).hasClass('gallery-lightbox')) {
            closeLightbox();
        }
    });

    // Navigation clavier
    $(document).on('keydown', function(e) {
        if ($('.gallery-lightbox').is(':visible')) {
            if (e.key === 'Escape') {
                closeLightbox();
            } else if (e.key === 'ArrowLeft') {
                $('.lightbox-prev').click();
            } else if (e.key === 'ArrowRight') {
                $('.lightbox-next').click();
            }
        }
    });

    // Animation au scroll (optionnel)
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    $('.gallery-item').each(function() {
        observer.observe(this);
    });
});
