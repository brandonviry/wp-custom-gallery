# 🎨 Système de Branding Complet - Documentation

## Vue d'ensemble

Ce plugin WordPress Gallery dispose d'un système de personnalisation **PROFESSIONNEL et COMPLET** utilisant des **variables CSS**. Chaque galerie peut avoir son propre branding unique.

---

## 📋 Table des matières

1. [Variables CSS disponibles](#variables-css-disponibles)
2. [Interface d'administration](#interface-dadministration)
3. [Comment ça fonctionne](#comment-ça-fonctionne)
4. [Exemples de personnalisation](#exemples-de-personnalisation)
5. [Cas d'usage](#cas-dusage)
6. [Variables CSS avancées](#variables-css-avancées)

---

## Variables CSS disponibles

### 🌈 Couleurs principales

| Variable CSS | Description | Valeur par défaut | Modifiable dans l'admin |
|--------------|-------------|-------------------|------------------------|
| `--primary-color` | Couleur primaire (début du gradient badges) | `#ef4444` | ✅ Oui |
| `--secondary-color` | Couleur secondaire (fin du gradient badges) | `#f59e0b` | ✅ Oui |
| `--title-color` | Couleur du titre des images | `#ffffff` | ✅ Oui |
| `--tag-bg-color` | Couleur de fond des tags | `rgba(0,0,0,0.7)` | ✅ Oui |
| `--tag-text-color` | Couleur du texte des tags | `#ffffff` | ✅ Oui |
| `--overlay-color` | Couleur de l'overlay au hover | `rgba(0,0,0,0.95)` | ✅ Oui |

### ✍️ Typographie

| Variable CSS | Description | Valeur par défaut | Modifiable dans l'admin |
|--------------|-------------|-------------------|------------------------|
| `--font-family` | Police de caractères globale | `-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif` | ✅ Oui |
| `--title-font-size` | Taille du titre (px) | `16px` | ✅ Oui |
| `--title-font-weight` | Épaisseur du titre | `600` | ✅ Oui |
| `--tag-font-size` | Taille des tags (px) | `11px` | ✅ Oui |

### 📐 Espacements & Bordures

| Variable CSS | Description | Valeur par défaut | Modifiable dans l'admin |
|--------------|-------------|-------------------|------------------------|
| `--border-radius` | Arrondi des images (px) | `16px` | ✅ Oui |
| `--gap-size` | Espacement entre images (px) | `20px` | ✅ Oui |
| `--overlay-opacity` | Opacité de l'overlay (0-1) | `0.95` | ✅ Oui |

### 🔆 Ombres du texte

| Variable CSS | Description | Valeur | Utilisation |
|--------------|-------------|---------|-------------|
| `--shadow-light` | Ombre légère | `0 1px 4px rgba(0,0,0,0.6), 0 1px 2px rgba(0,0,0,0.7)` | Images très claires |
| `--shadow-medium` | Ombre moyenne (défaut) | `0 2px 8px rgba(0,0,0,0.8), 0 1px 3px rgba(0,0,0,0.9)` | Usage général |
| `--shadow-strong` | Ombre forte | `0 3px 12px rgba(0,0,0,1), 0 2px 6px rgba(0,0,0,1)` | Maximum de contraste |

---

## Interface d'administration

### Accès

1. Dans le dashboard WordPress : **Galeries** (menu latéral)
2. Cliquez sur **"Créer une nouvelle galerie"** ou **"Éditer"** une galerie existante
3. Scrollez jusqu'à la section **"🎨 Branding & Design Complet"**

### Sections de personnalisation

#### 1. 🌈 Couleurs principales (6 options)

```
┌─────────────────────────────────────────────────────┐
│ Couleur primaire        │ [🎨] #ef4444             │
│ Couleur secondaire      │ [🎨] #f59e0b             │
│ Couleur du titre        │ [🎨] #ffffff             │
│ Couleur fond des tags   │ [🎨] #000000             │
│ Couleur texte des tags  │ [🎨] #ffffff             │
│ Couleur overlay         │ [🎨] #000000             │
└─────────────────────────────────────────────────────┘
```

Chaque couleur dispose de :
- Un sélecteur de couleur visuel
- Un champ texte affichant le code hexadécimal
- Synchronisation en temps réel

#### 2. ✍️ Typographie (4 options)

```
┌─────────────────────────────────────────────────────┐
│ Police de caractères    │ [Input: Poppins, sans...] │
│ Taille du titre (px)    │ [Nombre: 16]             │
│ Épaisseur du titre      │ [Select: Semi-Bold 600]  │
│ Taille des tags (px)    │ [Nombre: 11]             │
└─────────────────────────────────────────────────────┘
```

Polices recommandées :
- `'Poppins', sans-serif`
- `'Inter', sans-serif`
- `'Montserrat', sans-serif`
- `'Roboto', sans-serif`

#### 3. 📐 Espacements & Bordures (4 options)

```
┌─────────────────────────────────────────────────────┐
│ Arrondi des images (px) │ [Nombre: 16]             │
│ Espacement images (px)  │ [Nombre: 20]             │
│ Opacité overlay (%)     │ [━━━●━━━━━━] 95%        │
│ Intensité ombre texte   │ [Select: Moyenne]        │
└─────────────────────────────────────────────────────┘
```

#### 4. 👁️ Aperçu en temps réel

L'admin affiche un **APERÇU LIVE** qui se met à jour instantanément quand vous modifiez une valeur :

```
┌────────────────────┬────────────────────┐
│  Badges de filtres │ Carte image (hover)│
│                    │                    │
│ [Badge inactif]    │  ┌──────────────┐  │
│ [Badge actif]      │  │  Image       │  │
│                    │  │              │  │
│                    │  │ Titre        │  │
│                    │  │ [TAG] [TAG]  │  │
│                    │  └──────────────┘  │
└────────────────────┴────────────────────┘
```

---

## Comment ça fonctionne

### Architecture technique

```
┌─────────────────────────────────────────────────────────────┐
│                    ADMIN (WordPress Dashboard)              │
│                                                             │
│  1. Utilisateur modifie les options de branding            │
│     └─> JavaScript (admin.js) met à jour le preview        │
│                                                             │
│  2. Sauvegarde de la galerie                               │
│     └─> AJAX (class-gallery-ajax.php)                      │
│         └─> Stockage en JSON dans la base de données       │
│                                                             │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                    FRONTEND (Site WordPress)                │
│                                                             │
│  1. Shortcode [custom_gallery id="X"] affiché              │
│     └─> class-gallery-shortcode.php                        │
│                                                             │
│  2. Récupération des settings depuis la BDD                │
│                                                             │
│  3. Génération du style inline avec variables CSS          │
│     <div class="wp-custom-gallery" style="                 │
│       --primary-color: #ef4444;                            │
│       --secondary-color: #f59e0b;                          │
│       --title-color: #ffffff;                              │
│       ... (toutes les autres variables)                    │
│     ">                                                      │
│                                                             │
│  4. Le CSS (frontend.css) utilise ces variables            │
│     .filter-badge.active {                                 │
│       background: linear-gradient(                         │
│         to right,                                          │
│         var(--primary-color),                              │
│         var(--secondary-color)                             │
│       );                                                   │
│     }                                                      │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Flux de données

```
Utilisateur modifie une couleur
    ↓
JavaScript synchronise le champ texte
    ↓
Preview mis à jour instantanément
    ↓
Clic sur "Enregistrer"
    ↓
AJAX envoie toutes les données au serveur
    ↓
PHP valide et nettoie les données (sanitize)
    ↓
Stockage en JSON dans la table custom_galleries
    ↓
[Au frontend]
    ↓
Shortcode récupère les settings
    ↓
Génère le HTML avec style inline
    ↓
CSS utilise var(--nom-variable)
    ↓
Rendu final avec le branding personnalisé
```

---

## Exemples de personnalisation

### Exemple 1 : Branding d'entreprise (bleu corporate)

```css
--primary-color: #0066cc;
--secondary-color: #00aaff;
--title-color: #ffffff;
--tag-bg-color: rgba(0, 102, 204, 0.8);
--tag-text-color: #ffffff;
--overlay-color: rgba(0, 51, 102, 0.95);
--font-family: 'Roboto', sans-serif;
--title-font-size: 18px;
--title-font-weight: 700;
--border-radius: 12px;
--shadow-intensity: strong;
```

**Rendu** : Badges bleus, overlay bleu foncé, texte en gras, coins légèrement arrondis.

---

### Exemple 2 : Style moderne & coloré (gradient rose-violet)

```css
--primary-color: #ec4899;
--secondary-color: #8b5cf6;
--title-color: #ffffff;
--tag-bg-color: rgba(139, 92, 246, 0.7);
--tag-text-color: #ffffff;
--overlay-color: rgba(109, 40, 217, 0.9);
--font-family: 'Poppins', sans-serif;
--title-font-size: 16px;
--title-font-weight: 600;
--border-radius: 20px;
--gap-size: 25px;
--shadow-intensity: medium;
```

**Rendu** : Gradient rose-violet, coins très arrondis, espacement généreux.

---

### Exemple 3 : Minimaliste noir & blanc

```css
--primary-color: #000000;
--secondary-color: #333333;
--title-color: #ffffff;
--tag-bg-color: rgba(0, 0, 0, 0.9);
--tag-text-color: #ffffff;
--overlay-color: rgba(0, 0, 0, 0.85);
--font-family: 'Inter', sans-serif;
--title-font-size: 14px;
--title-font-weight: 500;
--border-radius: 8px;
--gap-size: 15px;
--shadow-intensity: light;
```

**Rendu** : Noir et blanc élégant, coins peu arrondis, compact.

---

### Exemple 4 : Nature & écologie (vert)

```css
--primary-color: #10b981;
--secondary-color: #34d399;
--title-color: #ffffff;
--tag-bg-color: rgba(16, 185, 129, 0.8);
--tag-text-color: #ffffff;
--overlay-color: rgba(5, 150, 105, 0.9);
--font-family: 'Montserrat', sans-serif;
--title-font-size: 17px;
--title-font-weight: 600;
--border-radius: 16px;
--shadow-intensity: medium;
```

**Rendu** : Couleurs vertes naturelles, look organique.

---

## Cas d'usage

### Cas 1 : Site multi-clients (agence web)

**Problème** : Vous gérez plusieurs galeries pour différents clients, chacun avec sa charte graphique.

**Solution** :
```
Galerie Client A (banque) :
  → Bleu corporate (#003d82, #0066cc)
  → Police Roboto
  → Ombre forte

Galerie Client B (restaurant) :
  → Rouge-orange (#dc2626, #f59e0b)
  → Police Poppins
  → Coins très arrondis

Galerie Client C (tech startup) :
  → Violet-cyan (#8b5cf6, #06b6d4)
  → Police Inter
  → Style moderne
```

Chaque galerie a son propre branding, **PAS besoin de CSS custom**.

---

### Cas 2 : Site e-commerce avec catégories

**Problème** : Vous avez différentes catégories de produits avec des ambiances différentes.

**Solution** :
```
Galerie "Vêtements Femmes" :
  → Rose (#ec4899) + Violet (#a855f7)

Galerie "Vêtements Hommes" :
  → Bleu foncé (#1e40af) + Gris (#475569)

Galerie "Enfants" :
  → Jaune (#fbbf24) + Orange (#f97316)
  → Police ludique, coins arrondis
```

---

### Cas 3 : Portfolio photographe

**Problème** : Vous voulez des ambiances différentes selon le type de photo.

**Solution** :
```
Galerie "Mariage" :
  → Blanc (#ffffff) + Beige rosé (#fecdd3)
  → Overlay très léger (50%)
  → Ombre douce

Galerie "Sport" :
  → Rouge vif (#dc2626) + Noir (#000000)
  → Overlay foncé (95%)
  → Ombre forte
  → Police en gras

Galerie "Nature" :
  → Vert (#10b981) + Bleu ciel (#38bdf8)
  → Overlay moyen (70%)
```

---

## Variables CSS avancées

### Surcharger les variables avec CSS custom

Si vous voulez aller encore plus loin, vous pouvez ajouter du CSS dans **Apparence > Personnaliser > CSS additionnel** :

```css
/* Surcharge pour UNE galerie spécifique */
.wp-custom-gallery[data-gallery-id="5"] {
    --primary-color: #custom-color;
    --border-radius: 30px;
}

/* Surcharge pour TOUTES les galeries */
.wp-custom-gallery {
    --gap-size: 30px;
    --title-font-size: 20px;
}

/* Ajouter de NOUVELLES variables */
.wp-custom-gallery {
    --custom-hover-scale: 1.05;
}

.wp-custom-gallery .gallery-item:hover img {
    transform: scale(var(--custom-hover-scale));
}
```

### Variables disponibles dans le code

Si vous êtes développeur, voici où modifier le code :

**CSS** : `assets/css/frontend.css` lignes 2-35
```css
.wp-custom-gallery {
    --primary-color: #ef4444;
    --secondary-color: #f59e0b;
    /* ... toutes les variables ... */
}
```

**PHP (shortcode)** : `includes/class-gallery-shortcode.php` lignes 73-131
```php
$custom_style = sprintf(
    '--primary-color: %s; --secondary-color: %s; ...',
    $primary_color,
    $secondary_color,
    // ...
);
```

**JavaScript (admin)** : `assets/js/admin.js` lignes 135-165 (sauvegarde)
```javascript
const formData = {
    primary_color: $('#primary-color').val(),
    secondary_color: $('#secondary-color').val(),
    // ...
};
```

---

## 🎯 Résumé

### Avantages du système

✅ **Variables CSS professionnelles** : Architecture moderne et maintenable
✅ **Branding par galerie** : Chaque galerie = son propre style
✅ **Preview en temps réel** : Voir les changements instantanément
✅ **Aucun code requis** : Interface visuelle complète
✅ **Surcharge possible** : CSS custom pour aller plus loin
✅ **Performance** : Variables CSS natives = rapide

### Points clés

1. **Toutes les valeurs sont stockées en BDD** (table `custom_galleries`, colonne `settings`)
2. **Chaque galerie est indépendante** (pas de conflit)
3. **CSS moderne** (variables CSS = support IE11+, tous navigateurs modernes)
4. **Extensible** (facile d'ajouter de nouvelles variables)

---

## 📞 Support

Si vous avez des questions sur le système de branding :

1. Consultez ce fichier (BRANDING-COMPLET.md)
2. Vérifiez les exemples ci-dessus
3. Testez dans l'admin avec le preview en temps réel

---

**Version** : 2.0.0
**Date** : 2024-12-05
**Auteur** : Plugin WP Custom Gallery avec système de branding complet
