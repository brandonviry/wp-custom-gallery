# 🔧 FIX - Images toujours en carré 1:1

## Problème constaté

Les images ne sont **PAS** recadrées en format carré 1:1. Elles conservent leur ratio original avec des bandes noires (letterbox).

**Exemple :**
- Image paysage (16:9) → Bandes noires en haut et en bas ❌
- Image portrait (9:16) → Bandes noires à gauche et à droite ❌
- **Attendu :** Image RECADRÉE pour remplir un carré parfait ✅

## Solution appliquée

### Modification du CSS (frontend.css)

```css
.gallery-item-inner {
    position: relative;
    width: 100%;
    padding-bottom: 100% !important; /* Force le ratio 1:1 */
    overflow: hidden;
    height: 0; /* CRITIQUE pour que padding-bottom fonctionne */
}

.gallery-item img {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important; /* RECADRE l'image */
    object-position: center !important; /* Centre l'image */
    display: block;
    max-width: none !important;
}
```

## Pourquoi `!important` partout ?

Les thèmes WordPress ajoutent souvent leurs propres styles qui surchargent notre CSS. Le `!important` garantit que nos styles ont la priorité.

## Comment vérifier que ça fonctionne

### Test 1 : Fichier test-simple.html

```bash
1. Ouvrez test-simple.html dans votre navigateur
2. Vous devez voir 8 images PARFAITEMENT CARRÉES
3. Aucune bande noire visible
4. Les images sont recadrées et centrées
```

### Test 2 : Dans WordPress

```bash
1. Créez une galerie avec 3 images :
   - Une paysage (ex: 1920x1080)
   - Une portrait (ex: 1080x1920)
   - Une carrée (ex: 1000x1000)

2. Affichez la galerie sur le site

3. Inspectez avec F12 > Sélectionnez une image

4. Vérifiez dans l'onglet "Computed" :
   ✅ object-fit: cover
   ✅ width: [quelque chose]px
   ✅ height: [MÊME valeur]px
   ✅ position: absolute
```

### Test 3 : Avec l'inspecteur

```bash
1. F12 > Clic droit sur une image > Inspecter
2. Dans le panneau Styles, vérifiez :

.gallery-item-inner {
    padding-bottom: 100%; ← Doit être là
    height: 0; ← CRITIQUE
}

.gallery-item img {
    object-fit: cover; ← Doit être là
    width: 100%;
    height: 100%;
}
```

## Si ça ne fonctionne toujours pas

### Diagnostic 1 : Conflit avec le thème

**Symptôme :** Les images ne sont pas carrées malgré le CSS

**Solution :**

```css
/* Ajoutez dans Apparence > Personnaliser > CSS additionnel */

/* Force le ratio 1:1 avec priorité maximale */
body .wp-custom-gallery .gallery-item-inner {
    position: relative !important;
    width: 100% !important;
    padding-bottom: 100% !important;
    overflow: hidden !important;
    height: 0 !important;
}

body .wp-custom-gallery .gallery-item img {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    object-position: center !important;
    max-width: none !important;
    max-height: none !important;
}
```

### Diagnostic 2 : Le thème utilise Flexbox/Grid sur les images

**Symptôme :** Les images ont une hauteur automatique

**Vérification :**

```bash
F12 > Sélectionnez .gallery-item-inner
Dans "Computed", cherchez :
- display: flex ← PROBLÈME
- display: grid ← PROBLÈME
```

**Solution :**

```css
.wp-custom-gallery .gallery-item-inner {
    display: block !important; /* Force le mode bloc */
}
```

### Diagnostic 3 : Images lazy-loaded mal implémentées

**Symptôme :** Les images ont `height: auto` en inline style

**Vérification :**

```html
<!-- Si vous voyez ça : -->
<img src="..." style="height: auto;"> ← PROBLÈME
```

**Solution :**

```javascript
// Ajoutez dans assets/js/frontend.js AVANT la ligne 1
jQuery(document).ready(function($) {
    // Enlever les styles inline qui cassent le ratio
    $('.wp-custom-gallery img').removeAttr('style');

    // Le reste du code...
});
```

### Diagnostic 4 : Plugin d'optimisation d'images

**Symptôme :** Un plugin (WP Rocket, Autoptimize, etc.) modifie les images

**Plugins problématiques connus :**
- WP Rocket (lazy load)
- Autoptimize
- Jetpack Photon
- EWWW Image Optimizer
- ShortPixel

**Solution :**

```bash
1. Désactivez TOUS les plugins d'optimisation
2. Testez la galerie
3. Si ça marche, réactivez un par un
4. Identifiez le coupable
5. Excluez la galerie dans les paramètres du plugin
```

### Diagnostic 5 : Thème avec normalize/reset CSS agressif

**Symptôme :** Le thème force `img { max-width: 100%; height: auto; }`

**Vérification :**

```bash
F12 > Styles > Cherchez :
img {
    max-width: 100%;
    height: auto; ← PROBLÈME
}
```

**Solution :**

```css
.wp-custom-gallery .gallery-item img {
    height: 100% !important; /* Annule le height: auto */
    max-width: none !important;
}
```

## Explication technique : Pourquoi `padding-bottom: 100%` ?

Le padding en pourcentage se calcule **par rapport à la LARGEUR** du parent, pas à sa hauteur.

```
Largeur du parent : 300px
padding-bottom: 100% = 100% de 300px = 300px de hauteur
→ Résultat : Carré parfait 300x300
```

C'est la technique standard pour créer des ratios d'aspect responsive !

## Test visuel rapide

### AVANT (Incorrect) ❌
```
┌─────────────────┐
│                 │
│  ┌───────────┐  │  ← Bandes noires
│  │  Image    │  │
│  └───────────┘  │
│                 │
└─────────────────┘
```

### APRÈS (Correct) ✅
```
┌─────────────────┐
│█████████████████│  ← Image recadrée
│█████████████████│     remplit tout
│█████████████████│     l'espace
└─────────────────┘
```

## Commandes de vérification rapide

### Chrome DevTools Console

```javascript
// Vérifier si object-fit est appliqué
getComputedStyle(document.querySelector('.gallery-item img')).objectFit
// Doit retourner: "cover"

// Vérifier les dimensions
const img = document.querySelector('.gallery-item img');
console.log('Width:', img.offsetWidth, 'Height:', img.offsetHeight);
// Width et Height doivent être identiques

// Vérifier le ratio du container
const inner = document.querySelector('.gallery-item-inner');
console.log('Width:', inner.offsetWidth, 'Height:', inner.offsetHeight);
// Width et Height doivent être identiques
```

### CSS à copier-coller d'urgence

Si RIEN ne fonctionne, copiez-collez ce CSS dans **Apparence > Personnaliser > CSS additionnel** :

```css
/* 🔥 FIX URGENT - RATIO 1:1 FORCÉ */
body .wp-custom-gallery .gallery-item {
    position: relative !important;
    width: 100% !important;
}

body .wp-custom-gallery .gallery-item-inner {
    position: relative !important;
    width: 100% !important;
    padding-bottom: 100% !important;
    overflow: hidden !important;
    height: 0 !important;
    display: block !important;
}

body .wp-custom-gallery .gallery-item-inner img {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    object-position: center center !important;
    max-width: none !important;
    max-height: none !important;
    min-width: 100% !important;
    min-height: 100% !important;
    display: block !important;
}
```

## Checklist finale

- [ ] `test-simple.html` affiche des carrés parfaits
- [ ] Aucune bande noire visible
- [ ] Images paysage recadrées correctement
- [ ] Images portrait recadrées correctement
- [ ] Images carrées affichées correctement
- [ ] Zoom au survol fonctionne
- [ ] Responsive OK (mobile/tablette/desktop)

## Si le problème persiste

Envoyez-moi :
1. Capture d'écran de la galerie
2. Capture d'écran de F12 > Computed styles pour une image
3. Nom du thème WordPress utilisé
4. Liste des plugins actifs (surtout optimisation/cache/images)

---

**Date de dernière modification :** 2024-12-05
**Version :** 2.0.1 - Fix ratio 1:1
