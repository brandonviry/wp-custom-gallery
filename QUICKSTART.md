# Guide de démarrage rapide - WP Custom Gallery

## 🚀 Installation en 3 étapes

### Étape 1 : Installer le plugin
```bash
# Via FTP : Uploadez le dossier dans /wp-content/plugins/
# Via ZIP : Compressez et uploadez via WordPress > Extensions > Ajouter
```

### Étape 2 : Activer
Dans le dashboard WordPress : **Extensions** > **Extensions installées** > **Activer**

### Étape 3 : Créer une galerie
**Galeries** > **Créer une nouvelle galerie** > Sélectionner les images > **Enregistrer**

---

## 📸 Utilisation rapide

### Créer une galerie
1. Menu **Galeries** > **Créer une nouvelle galerie**
2. Nom : "Ma galerie"
3. Cliquer **Sélectionner les images**
4. Choisir vos images
5. Configurer :
   - Colonnes : 4
   - Style : Couleur ou Grayscale
6. **Enregistrer**

### Afficher la galerie
Copiez le shortcode et collez-le dans une page :
```
[custom_gallery id="1"]
```

---

## 🎨 Design moderne

Le plugin utilise un design inspiré des meilleures pratiques UI/UX :

### Badges de filtrage
- Style moderne avec coins arrondis
- Gradient rouge-orange pour le badge actif
- Animation au survol

### Grille d'images
- Format carré (aspect-ratio 1:1)
- Coins arrondis (16px)
- Zoom au survol (scale 1.1)
- Overlay avec gradient

### Deux modes d'affichage

**Mode Couleur** (par défaut)
- Images en couleur
- Effet de zoom au survol

**Mode Grayscale**
- Images en noir et blanc
- Passage en couleur au survol
- Idéal pour un portfolio professionnel

---

## 🏷️ Système de filtrage par tags

### Comment ça marche ?

Le plugin utilise les **attributs ALT** des images pour créer des filtres automatiques.

### Exemple pratique

**1. Ajoutez des ALT à vos images :**

Image 1 : `rencontre evenement`
Image 2 : `pro conference`
Image 3 : `rencontre soiree`
Image 4 : `nature paysage`

**2. Les badges sont créés automatiquement :**
- 📸 Toutes les photos
- ❤️ Rencontre
- 💼 Pro
- 🎯 Événement
- 🌿 Nature

**3. Les visiteurs cliquent pour filtrer !**

---

## ⚙️ Configuration

### Paramètres disponibles

| Paramètre | Options | Description |
|-----------|---------|-------------|
| **Colonnes** | 3, 4, 5, 6 | Nombre d'images par ligne |
| **Ordre** | Croissant, Décroissant | A-Z ou Z-A |
| **Trier par** | Titre, ALT, Date | Critère de tri |
| **Style** | Couleur, Grayscale | Apparence des images |

### Recommandations

- **Desktop** : 4 colonnes
- **Portfolio** : 3 colonnes + Grayscale
- **Événements** : 4 colonnes + Couleur
- **Produits** : 5 colonnes + Couleur

---

## 🎯 Exemples d'utilisation

### Galerie d'événements
```
Nom : "Événements 2024"
Colonnes : 4
Style : Couleur
Tags ALT : rencontre, conference, atelier, soiree
```

### Portfolio photographe
```
Nom : "Portfolio"
Colonnes : 3
Style : Grayscale
Tags ALT : portrait, paysage, mariage, famille
```

### Site e-commerce
```
Nom : "Produits"
Colonnes : 5
Style : Couleur
Tags ALT : vetement, accessoire, chaussure, sac
```

---

## 🎨 Personnalisation des couleurs

### Étape 1 : Copier le CSS d'exemple
Ouvrez le fichier `custom-colors-example.css`

### Étape 2 : Ajouter dans WordPress
**Apparence** > **Personnaliser** > **CSS additionnel**

### Étape 3 : Modifier les couleurs

```css
/* Badge "Rencontres" en rose */
.filter-badge[data-filter="rencontres"].active {
    background: #ec4899;
    box-shadow: 0 4px 6px rgba(236, 72, 153, 0.3);
}

/* Ajouter un emoji */
.filter-badge[data-filter="rencontres"]::before {
    content: "❤️ ";
}
```

---

## 🔧 Fonctionnalités avancées

### Lightbox intégrée
- Clic sur une image → Affichage plein écran
- Navigation : flèches ← →
- Fermer : Échap ou clic sur ×

### Navigation clavier
- `←` : Image précédente
- `→` : Image suivante
- `Échap` : Fermer la lightbox

### Responsive design
- Desktop : Grille configurable
- Tablette : Ajustement automatique
- Mobile : 2 colonnes

---

## 📱 Shortcuts utiles

### Dans l'éditeur WordPress

**Bloc Gutenberg :**
1. Ajouter un bloc
2. Rechercher "Shortcode"
3. Coller : `[custom_gallery id="1"]`

**Éditeur classique :**
Coller directement le shortcode dans l'éditeur

**Widget :**
1. **Apparence** > **Widgets**
2. Ajouter un widget "Texte personnalisé"
3. Coller le shortcode

### Dans un template PHP

```php
<?php echo do_shortcode('[custom_gallery id="1"]'); ?>
```

---

## 🐛 Problèmes courants

### Le menu n'apparaît pas
✅ Solution : Désactiver puis réactiver le plugin

### Les images ne s'affichent pas
✅ Solution : Vider le cache du navigateur

### Le filtrage ne marche pas
✅ Solution : Vérifier que les images ont des attributs ALT

### CSS ne se charge pas
✅ Solution : Vider le cache WordPress et du navigateur

---

## 📚 Ressources

- **README complet** : [README.md](README.md)
- **Installation détaillée** : [INSTALLATION.md](INSTALLATION.md)
- **Démo visuelle** : [demo.html](demo.html)
- **Exemples CSS** : [custom-colors-example.css](custom-colors-example.css)

---

## ✨ Astuces pro

### 1. Optimiser les images
Utilisez des images de taille appropriée (800x800px) pour de meilleures performances

### 2. Nommer les images
Donnez des noms descriptifs à vos images avant de les uploader

### 3. Attributs ALT cohérents
Utilisez toujours les mêmes mots-clés pour un meilleur filtrage

### 4. Tester sur mobile
Vérifiez toujours l'affichage sur smartphone et tablette

### 5. Lazy loading
Activez le lazy loading WordPress pour de meilleures performances

---

**Prêt à créer votre première galerie ? 🎉**

Allez dans **Galeries** > **Créer une nouvelle galerie** !
