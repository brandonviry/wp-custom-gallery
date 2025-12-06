# Guide d'installation - WP Custom Gallery

## Installation rapide

### Méthode 1 : Installation via FTP

1. **Téléchargez le plugin**
   - Téléchargez ou clonez ce dossier `wp-custom-gallery`

2. **Uploadez via FTP**
   - Connectez-vous à votre serveur via FTP
   - Naviguez vers `/wp-content/plugins/`
   - Uploadez le dossier complet `wp-custom-gallery`

3. **Activez le plugin**
   - Connectez-vous à votre dashboard WordPress
   - Allez dans **Extensions** > **Extensions installées**
   - Trouvez "WP Custom Gallery" et cliquez sur **Activer**

### Méthode 2 : Installation via ZIP

1. **Créez une archive ZIP**
   - Compressez le dossier `wp-custom-gallery` en fichier ZIP

2. **Uploadez via WordPress**
   - Dans le dashboard WordPress, allez dans **Extensions** > **Ajouter**
   - Cliquez sur **Téléverser une extension**
   - Sélectionnez le fichier ZIP
   - Cliquez sur **Installer maintenant**
   - Cliquez sur **Activer**

## Vérification de l'installation

Après l'activation, vérifiez que :

- ✅ Un nouveau menu **"Galeries"** apparaît dans la barre latérale du dashboard
- ✅ La base de données contient une nouvelle table `wp_custom_galleries`

Si vous ne voyez pas le menu, essayez de :
1. Désactiver puis réactiver le plugin
2. Vider le cache de WordPress
3. Vérifier les permissions des fichiers

## Première utilisation

### 1. Créer votre première galerie

1. Cliquez sur **Galeries** dans le menu WordPress
2. Cliquez sur **Créer une nouvelle galerie**
3. Donnez un nom à votre galerie (ex: "Photos 2024")
4. Cliquez sur **Sélectionner les images**
5. Choisissez vos images depuis la médiathèque
6. Configurez les paramètres :
   - **Colonnes** : 4 (recommandé)
   - **Ordre** : Décroissant
   - **Trier par** : Date
   - **Style** : Couleur ou Grayscale
7. Cliquez sur **Enregistrer**

### 2. Ajouter des attributs ALT pour les filtres

Pour activer le système de filtrage, ajoutez des attributs ALT à vos images :

1. Allez dans **Médias** > **Bibliothèque**
2. Cliquez sur une image
3. Dans le champ **Texte alternatif**, ajoutez des mots-clés séparés par des espaces
   - Exemple : `rencontre evenement concert`
   - Exemple : `nature paysage montagne`
4. Cliquez sur **Mettre à jour**

### 3. Afficher la galerie sur votre site

1. Copiez le shortcode de votre galerie (exemple: `[custom_gallery id="1"]`)
2. Collez-le dans :
   - Un article ou une page WordPress
   - Un widget texte
   - Un bloc personnalisé

**Pour l'ajouter dans un template PHP :**
```php
<?php echo do_shortcode('[custom_gallery id="1"]'); ?>
```

## Configuration avancée

### Personnaliser les couleurs des badges

1. Allez dans **Apparence** > **Personnaliser** > **CSS additionnel**
2. Copiez le contenu du fichier `custom-colors-example.css`
3. Modifiez les couleurs selon vos besoins
4. Cliquez sur **Publier**

### Modifier le nombre de colonnes par défaut

Éditez le fichier `includes/class-gallery-admin.php` ligne 149 :
```php
<option value="4" selected>4 colonnes</option>
```

### Désactiver la lightbox

Éditez le fichier `assets/js/frontend.js` et commentez les lignes du gestionnaire d'événements de la lightbox.

## Résolution de problèmes

### Le menu "Galeries" n'apparaît pas

**Solution :**
- Vérifiez que vous avez les droits d'administrateur
- Désactivez puis réactivez le plugin
- Vérifiez les logs d'erreur PHP

### Les images ne s'affichent pas

**Solution :**
- Vérifiez que les images existent dans la médiathèque
- Vérifiez les permissions des fichiers
- Ouvrez la console du navigateur pour voir les erreurs JavaScript

### Le filtrage ne fonctionne pas

**Solution :**
- Vérifiez que jQuery est chargé sur votre site
- Vérifiez que les attributs ALT sont bien renseignés
- Ouvrez la console du navigateur pour voir les erreurs

### Le CSS ne se charge pas

**Solution :**
- Videz le cache de WordPress et du navigateur
- Vérifiez que le fichier `assets/css/frontend.css` existe
- Vérifiez les permissions des fichiers

### Erreur lors de la sauvegarde

**Solution :**
- Augmentez `max_input_vars` dans votre php.ini (minimum 3000)
- Vérifiez que la base de données est accessible
- Vérifiez les logs d'erreur PHP

## Support technique

### Fichiers de log

Activez le mode debug de WordPress dans `wp-config.php` :
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Les logs seront dans `/wp-content/debug.log`

### Informations système requises

- WordPress 5.0+
- PHP 7.0+
- MySQL 5.6+
- jQuery 1.12+

### Vérifier la version PHP

Ajoutez ce code dans un fichier `phpinfo.php` à la racine de WordPress :
```php
<?php phpinfo(); ?>
```

Puis visitez `https://votresite.com/phpinfo.php`

**N'oubliez pas de supprimer ce fichier après !**

## Désinstallation

Pour désinstaller complètement le plugin :

1. **Désactiver le plugin**
   - Allez dans **Extensions** > **Extensions installées**
   - Cliquez sur **Désactiver** sous "WP Custom Gallery"

2. **Supprimer les données** (optionnel)
   - Connectez-vous à phpMyAdmin
   - Exécutez cette requête SQL :
   ```sql
   DROP TABLE IF EXISTS wp_custom_galleries;
   ```

3. **Supprimer le plugin**
   - Cliquez sur **Supprimer** sous "WP Custom Gallery"
   - Ou supprimez le dossier `/wp-content/plugins/wp-custom-gallery/` via FTP

## Mise à jour

Pour mettre à jour le plugin :

1. **Sauvegardez vos données**
   - Exportez vos galeries depuis phpMyAdmin
   - Sauvegardez le dossier du plugin

2. **Désactivez l'ancienne version**

3. **Supprimez l'ancienne version** (ne supprimez PAS via l'interface WordPress)
   - Supprimez uniquement le dossier via FTP

4. **Installez la nouvelle version**

5. **Réactivez le plugin**

La base de données sera automatiquement mise à jour si nécessaire.

---

**Besoin d'aide ?** Consultez le fichier [README.md](README.md) pour plus d'informations.
