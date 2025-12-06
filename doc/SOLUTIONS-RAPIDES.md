# Solutions Rapides aux Problèmes Courants

## 🔴 PROBLÈME 1 : Je ne peux sélectionner qu'une seule image

### Solution immédiate (30 secondes)

1. **Videz le cache de votre navigateur**
   ```
   - Chrome/Edge : Ctrl + Shift + Delete
   - Cochez "Images et fichiers en cache"
   - Cliquez sur "Effacer les données"
   ```

2. **Rechargez la page admin**
   ```
   - Appuyez sur Ctrl + F5 (force le rechargement)
   - Ou fermez et rouvrez votre navigateur
   ```

3. **Testez à nouveau**
   ```
   - Allez dans Galeries > Créer une nouvelle galerie
   - Cliquez sur "Sélectionner les images"
   - Maintenez Ctrl enfoncé et cliquez sur plusieurs images
   - Ou utilisez Shift + clic pour sélectionner une plage
   ```

### Si ça ne fonctionne toujours pas

**Vérification rapide :**
```
1. F12 (ouvrir la console)
2. Onglet "Console"
3. Regardez s'il y a des erreurs en rouge
4. Si vous voyez "mediaFrame is not defined" ou similaire :
```

**Solution alternative :**
```javascript
// Ouvrez la console (F12) et collez ce code :
console.log('Multiple selection:', wp.media.view.settings.post.featuredImageId);

// Si ça retourne "undefined", il y a un conflit
```

**Fix rapide dans le code :**

Ouvrez `assets/js/admin.js` et assurez-vous que la ligne 31 contient bien :
```javascript
multiple: true
```

---

## 🔴 PROBLÈME 2 : La galerie ne ressemble pas à la démo

### Test rapide (1 minute)

1. **Ouvrez le fichier test**
   ```
   - Double-cliquez sur "test-simple.html"
   - Si ça s'affiche en 4 colonnes : le CSS est OK
   - Si ça s'affiche en 1 colonne : problème CSS
   ```

2. **Vérifiez que le CSS se charge**
   ```
   - Sur la page avec la galerie, appuyez sur F12
   - Onglet "Réseau" (ou Network)
   - Filtrez par "CSS"
   - Recherchez "frontend.css"
   - Statut doit être 200 (OK), pas 404 (Non trouvé)
   ```

3. **Inspectez la galerie**
   ```
   - Clic droit sur la galerie > Inspecter
   - Cherchez : <div class="gallery-grid" data-columns="4">
   - Si data-columns manque ou = "" : problème PHP
   - Si data-columns="4" mais 1 colonne : problème CSS
   ```

### Solutions rapides

**Solution A : Forcer le rechargement du CSS**
```
1. Dans wp-custom-gallery.php, ligne 13, changez :
   define('WP_CUSTOM_GALLERY_VERSION', '1.0.1');

2. Sauvegardez le fichier

3. Rechargez la page (Ctrl + F5)
```

**Solution B : Vérifier le nombre de colonnes**
```
1. Éditez votre galerie dans WordPress
2. Vérifiez que "Nombre de colonnes" est bien sélectionné (3, 4, 5 ou 6)
3. Sauvegardez
4. Rechargez la page du site
```

**Solution C : Ajouter le CSS manuellement**
```
Allez dans Apparence > Personnaliser > CSS additionnel

Collez ce code :

.wp-custom-gallery .gallery-grid[data-columns="4"] {
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 20px !important;
}

.wp-custom-gallery .gallery-item-inner {
    position: relative !important;
    padding-bottom: 100% !important;
    overflow: hidden !important;
}
```

---

## 🔴 PROBLÈME 3 : Pas d'overlay au survol

### Vérification visuelle

**Symptôme :** Quand je survole une image, rien ne se passe

**Test rapide :**
```
1. Ouvrez test-simple.html
2. Survolez une image
3. Si l'overlay apparaît ici : le problème vient de WordPress
4. Si ça ne marche pas non plus : problème de navigateur
```

### Solutions

**Solution 1 : CSS additionnel**
```css
/* Dans Apparence > Personnaliser > CSS additionnel */
.wp-custom-gallery .gallery-item:hover .gallery-item-overlay {
    opacity: 1 !important;
}

.wp-custom-gallery .gallery-item:hover img {
    transform: scale(1.1) !important;
}
```

**Solution 2 : Désactiver lazy loading**
```
Si votre thème utilise lazy loading agressif, ça peut casser l'effet.

Testez en désactivant temporairement les plugins :
- Lazy Load
- WP Rocket
- Autoptimize
```

---

## 🔴 PROBLÈME 4 : Les filtres ne fonctionnent pas

### Test JavaScript

```
1. F12 > Console
2. Tapez : jQuery('.filter-badge').length
3. Si ça retourne 0 : les badges ne sont pas trouvés
4. Si ça retourne un nombre > 0 : jQuery fonctionne
```

### Solutions

**Solution 1 : Vérifier les attributs ALT**
```
1. Allez dans Médias > Bibliothèque
2. Cliquez sur chaque image de votre galerie
3. Dans "Texte alternatif", ajoutez des mots-clés
   Exemple : rencontre evenement
4. Cliquez "Mettre à jour"
5. Rééditez la galerie et re-sélectionnez les images
```

**Solution 2 : Recharger le JavaScript**
```
1. Désactivez le plugin WP Custom Gallery
2. Réactivez-le
3. Videz le cache
4. Rechargez la page (Ctrl + F5)
```

---

## 📋 Checklist de vérification complète

Avant de chercher plus loin, vérifiez ces points :

### ✅ Fichiers présents
```
wp-content/plugins/wp-custom-gallery/
  ├── wp-custom-gallery.php ✓
  ├── assets/css/frontend.css ✓
  ├── assets/css/admin.css ✓
  ├── assets/js/frontend.js ✓
  └── assets/js/admin.js ✓
```

### ✅ Plugin activé
```
Extensions > Extensions installées
→ WP Custom Gallery doit être bleu (activé)
```

### ✅ Galerie créée
```
Galeries > doit montrer au moins 1 galerie
→ Avec un shortcode comme [custom_gallery id="1"]
```

### ✅ Images sélectionnées
```
Éditer la galerie
→ Doit montrer des miniatures d'images
→ Pas "Aucune image sélectionnée"
```

### ✅ Paramètres définis
```
Nombre de colonnes : 3, 4, 5 ou 6 (pas vide)
Style : Couleur ou Grayscale (pas vide)
```

### ✅ Shortcode correct
```
[custom_gallery id="1"]

PAS :
[gallery id="1"]
[custom-gallery id="1"]
[wp_gallery id="1"]
```

---

## 🆘 Solution d'urgence

Si vraiment rien ne fonctionne :

### Réinstallation propre

```
1. SAUVEGARDEZ VOS DONNÉES
   - Exportez la table wp_custom_galleries depuis phpMyAdmin
   - Prenez une capture d'écran de vos galeries

2. Désactivez le plugin
   Extensions > WP Custom Gallery > Désactiver

3. Supprimez les fichiers
   Via FTP, supprimez : wp-content/plugins/wp-custom-gallery/

4. Ré-uploadez les fichiers frais

5. Réactivez le plugin

6. Recréez une galerie de test avec 2-3 images

7. Testez sur une page vierge
```

---

## 📞 Informations à fournir si vous demandez de l'aide

```
1. Version WordPress : ___________
2. Version PHP : ___________
3. Thème utilisé : ___________
4. Le test-simple.html fonctionne-t-il ? Oui / Non
5. Erreurs dans la console (F12) : ___________
6. Le CSS se charge-t-il (200 ou 404) ? ___________
7. Capture d'écran du problème : ___________
```

---

## 🎯 Tests à faire maintenant

1. ✅ Ouvrez `test-simple.html` dans votre navigateur
2. ✅ Vérifiez que vous voyez 4 colonnes d'images
3. ✅ Survolez une image et vérifiez l'overlay
4. ✅ Si ça marche ici, le problème vient de WordPress
5. ✅ Si ça ne marche pas, le problème vient du navigateur/CSS

**Le test-simple.html fonctionne ?**
- **OUI** → Le problème est dans WordPress (cache, conflit, CSS non chargé)
- **NON** → Le problème est dans votre navigateur (désactiver extensions, tester en navigation privée)
