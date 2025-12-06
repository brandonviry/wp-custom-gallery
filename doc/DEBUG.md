# Guide de débogage - WP Custom Gallery

## Problèmes courants et solutions

### 1. Je ne peux sélectionner qu'une seule image

**Symptôme :** Lors de la sélection d'images dans la médiathèque, je ne peux choisir qu'une image au lieu de plusieurs.

**Causes possibles :**
- Le mode "multiple" n'est pas activé
- Conflit avec un autre plugin
- Cache JavaScript

**Solutions :**

**Solution 1 : Vider le cache**
```
1. Ctrl + Shift + Delete (Chrome/Firefox)
2. Cocher "Images et fichiers en cache"
3. Vider
4. Recharger la page (Ctrl + F5)
```

**Solution 2 : Vérifier la console JavaScript**
```
1. F12 pour ouvrir les outils développeur
2. Onglet "Console"
3. Rechercher des erreurs en rouge
4. Si vous voyez des erreurs liées à wp.media, notez-les
```

**Solution 3 : Désactiver les autres plugins**
```
1. Allez dans Extensions
2. Désactivez tous les plugins sauf WP Custom Gallery
3. Testez la sélection d'images
4. Réactivez les plugins un par un pour identifier le conflit
```

**Solution 4 : Forcer le rechargement des assets**
```php
// Dans wp-custom-gallery.php, changez la version
define('WP_CUSTOM_GALLERY_VERSION', '1.0.1'); // Au lieu de 1.0.0
```

### 2. La galerie ne s'affiche pas comme la démo

**Symptôme :** Les images ne sont pas disposées en grille, ou le design est différent.

**Vérifications :**

**Vérification 1 : Le CSS est-il chargé ?**
```
1. F12 > Onglet "Réseau" (Network)
2. Filtrer par "CSS"
3. Rechercher "frontend.css"
4. Si absent ou erreur 404, le fichier n'est pas chargé
```

**Vérification 2 : Inspecter la galerie**
```
1. Clic droit sur la galerie > Inspecter
2. Vérifier que l'attribut data-columns existe
3. Exemple : <div class="gallery-grid" data-columns="4">
4. Si data-columns est absent ou vide, il y a un problème
```

**Vérification 3 : Conflit CSS**
```
1. F12 > Onglet "Éléments"
2. Sélectionner un .gallery-item
3. Regarder l'onglet "Styles" à droite
4. Si des styles sont barrés, il y a un conflit
```

**Solutions :**

**Solution 1 : Forcer le rechargement du CSS**
```
1. Vider le cache du navigateur
2. Vider le cache WordPress (si plugin de cache installé)
3. Ctrl + F5 sur la page
```

**Solution 2 : Vérifier les permissions des fichiers**
```bash
# Via SSH, vérifier que les fichiers sont accessibles
chmod 644 wp-content/plugins/wp-custom-gallery/assets/css/frontend.css
chmod 644 wp-content/plugins/wp-custom-gallery/assets/js/frontend.js
```

**Solution 3 : Augmenter la spécificité CSS**
```css
/* Dans Apparence > Personnaliser > CSS additionnel */
body .wp-custom-gallery .gallery-grid[data-columns="4"] {
    grid-template-columns: repeat(4, 1fr) !important;
}
```

### 3. Les filtres par tags ne fonctionnent pas

**Symptôme :** Les badges s'affichent mais ne filtrent pas les images.

**Vérifications :**

**Vérification 1 : jQuery est-il chargé ?**
```javascript
// Dans la console (F12)
typeof jQuery
// Doit retourner "function", pas "undefined"
```

**Vérification 2 : Les attributs ALT sont-ils définis ?**
```
1. Dans la médiathèque WordPress
2. Cliquer sur une image
3. Vérifier que le champ "Texte alternatif" contient des mots-clés
4. Exemple : "rencontre evenement"
```

**Vérification 3 : Le JavaScript est-il chargé ?**
```
1. F12 > Réseau > JS
2. Rechercher "frontend.js"
3. Si erreur 404, le fichier n'est pas trouvé
```

**Solutions :**

**Solution 1 : Réenregistrer les images**
```
1. Éditer la galerie
2. Supprimer toutes les images (bouton X)
3. Re-sélectionner les images
4. Sauvegarder
```

**Solution 2 : Vérifier les attributs data-tags**
```html
<!-- Inspecter avec F12, chaque gallery-item doit avoir -->
<div class="gallery-item" data-tags="rencontre,evenement">
```

### 4. La lightbox ne s'ouvre pas

**Symptôme :** Cliquer sur une image ne fait rien.

**Vérifications :**

**Console JavaScript**
```
1. F12 > Console
2. Cliquer sur une image
3. Vérifier s'il y a des erreurs
```

**Solutions :**

**Solution 1 : Vérifier jQuery**
```javascript
// Dans la console
jQuery('.view-fullsize').length
// Doit retourner un nombre > 0
```

**Solution 2 : Recharger le JavaScript**
```
1. Désactiver le plugin
2. Réactiver le plugin
3. Vider le cache
4. Tester à nouveau
```

### 5. Les images ne se chargent pas

**Symptôme :** La galerie s'affiche mais les images sont cassées.

**Vérifications :**

**URLs des images**
```
1. F12 > Console
2. Vérifier les erreurs 404 pour les images
3. Noter les URLs qui échouent
```

**Solutions :**

**Solution 1 : Régénérer les miniatures**
```
1. Installer le plugin "Regenerate Thumbnails"
2. Aller dans Outils > Regenerate Thumbnails
3. Lancer la régénération
4. Attendre la fin du processus
```

**Solution 2 : Vérifier les permissions**
```bash
# Via SSH ou FTP
# Les dossiers uploads doivent être en 755
chmod 755 wp-content/uploads
chmod 755 wp-content/uploads/2024
chmod 644 wp-content/uploads/2024/*/*.jpg
```

## Informations de débogage utiles

### Obtenir les informations système

**1. Version WordPress**
```
Dashboard > Mises à jour
```

**2. Version PHP**
```php
<?php phpinfo(); ?>
// Créer un fichier test-php.php à la racine
// Visiter votresite.com/test-php.php
// SUPPRIMER le fichier après !
```

**3. Vérifier la base de données**
```sql
-- Dans phpMyAdmin
SELECT * FROM wp_custom_galleries;

-- Vérifier qu'une galerie existe
-- Vérifier que la colonne 'images' contient du JSON
-- Vérifier que la colonne 'settings' contient du JSON
```

### Activer le mode debug WordPress

**Dans wp-config.php :**
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
```

**Consulter les logs :**
```
Fichier : wp-content/debug.log
```

### Tester avec les valeurs par défaut

**Shortcode de test :**
```
[custom_gallery id="1"]
```

**Inspecter le HTML généré :**
```html
<!-- Doit ressembler à ça -->
<div class="wp-custom-gallery" data-gallery-id="1">
    <div class="gallery-filters">...</div>
    <div class="gallery-grid" data-columns="4">
        <div class="gallery-item" data-tags="...">
            <div class="gallery-item-inner">
                <img src="..." alt="...">
                <div class="gallery-item-overlay">...</div>
            </div>
        </div>
    </div>
</div>
```

## Support

Si les problèmes persistent :

1. **Collectez ces informations :**
   - Version WordPress
   - Version PHP
   - Thème utilisé
   - Liste des plugins actifs
   - Messages d'erreur dans la console
   - Capture d'écran du problème

2. **Vérifiez la compatibilité :**
   - WordPress 5.0+ requis
   - PHP 7.0+ requis
   - jQuery doit être chargé

3. **Testez avec un thème par défaut :**
   - Activez Twenty Twenty-Four
   - Testez la galerie
   - Si ça fonctionne, le problème vient du thème
