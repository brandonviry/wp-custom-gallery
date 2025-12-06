# ✅ Fix : Hover + Conflit Breakdance résolu

## 🔧 Problèmes résolus

### 1. Titre et tags toujours visibles ❌ → Uniquement au hover ✅

**AVANT** (incorrect) :
```
┌──────────────────┐
│                  │
│   IMAGE          │
│   ▓▓▓▓▓▓▓▓▓▓▓   │ ← Overlay toujours visible
│   "Titre"        │ ← Titre toujours visible
│   [TAG] [TAG]    │ ← Tags toujours visibles
└──────────────────┘
```

**APRÈS** (correct) :
```
Sans hover              Avec hover
┌──────────────┐       ┌──────────────┐
│              │       │              │
│   IMAGE      │   →   │   IMAGE      │
│              │       │   ▓▓▓▓▓▓▓▓▓ │ ← Overlay apparaît
│              │       │   "Titre"   │ ← Titre apparaît
└──────────────┘       │   [TAG]     │ ← Tags apparaissent
                       └──────────────┘
```

### 2. Conflit CSS Breakdance ❌ → Résolu sans !important ✅

**Problème** :
```css
/* Breakdance (page builder) force sa couleur */
.breakdance h1, .breakdance h2, .breakdance h3, .breakdance h4, .breakdance h5, .breakdance h6 {
    color: var(--bde-headings-color); /* Surcharge notre couleur */
}
```

**Solution** : Augmenter la spécificité CSS (SANS `!important`)

```css
/* AVANT (spécificité faible) */
.gallery-item-info h3 {
    color: var(--title-color);
}
/* Spécificité : 0,0,1,1 (1 classe + 1 élément) */
/* Breakdance gagne : 0,0,1,1 aussi, mais chargé après */

/* APRÈS (spécificité forte) */
.wp-custom-gallery .gallery-item-overlay .gallery-item-info h3 {
    color: var(--title-color); /* Pas de !important */
}
/* Spécificité : 0,0,4,1 (4 classes + 1 élément) */
/* On gagne ! Plus fort que Breakdance */
```

---

## 🎨 Modifications appliquées

### Fichier : `assets/css/frontend.css`

#### 1. Overlay caché par défaut (lignes 153-177)

```css
/* AVANT */
.gallery-item-overlay {
    opacity: 1; /* ❌ Toujours visible */
    transition: background 0.3s ease;
}

.gallery-item:hover .gallery-item-overlay {
    background: linear-gradient(...); /* Changement de gradient */
}

/* APRÈS */
.gallery-item-overlay {
    opacity: 0; /* ✅ CACHÉ par défaut */
    transition: opacity 0.3s ease;
}

.gallery-item:hover .gallery-item-overlay {
    opacity: 1; /* ✅ VISIBLE au hover */
}
```

#### 2. Titre/tags cachés par défaut (lignes 179-189)

```css
/* AVANT */
.gallery-item-info {
    transform: translateY(0); /* ❌ Visible immédiatement */
}

/* APRÈS */
.gallery-item-info {
    transform: translateY(10px); /* ✅ DÉCALÉ (caché) */
}

.gallery-item:hover .gallery-item-info {
    transform: translateY(0); /* ✅ VISIBLE au hover */
}
```

#### 3. Spécificité CSS augmentée (lignes 191-200)

```css
/* AVANT */
.gallery-item-info h3 {
    color: var(--title-color);
}

/* APRÈS */
.wp-custom-gallery .gallery-item-overlay .gallery-item-info h3 {
    color: var(--title-color); /* Pas de !important */
}
```

---

## 📊 Calcul de spécificité CSS

### Pourquoi la spécificité est importante ?

En CSS, quand deux règles ciblent le même élément, c'est la **spécificité** qui détermine laquelle gagne (pas l'ordre de chargement).

### Calcul (format : a,b,c,d)

- **a** : Styles inline (`style="..."`) = 1,0,0,0
- **b** : ID (`#id`) = 0,1,0,0
- **c** : Classes (`.class`), attributs (`[type="text"]`), pseudo-classes (`:hover`) = 0,0,1,0
- **d** : Éléments (`div`, `h3`) et pseudo-éléments (`::before`) = 0,0,0,1

### Comparaison

```
Breakdance :
.breakdance h3
= 1 classe + 1 élément
= 0,0,1,1

Notre ancien CSS :
.gallery-item-info h3
= 1 classe + 1 élément
= 0,0,1,1
→ ÉGALITÉ → Breakdance chargé après = gagne ❌

Notre nouveau CSS :
.wp-custom-gallery .gallery-item-overlay .gallery-item-info h3
= 4 classes + 1 élément
= 0,0,4,1
→ PLUS FORT → On gagne ✅
```

---

## ✅ Résultat final

### État par défaut (sans survol)

```css
Overlay : opacity: 0 (invisible)
Titre : transform: translateY(10px) (décalé vers le bas)
Tags : transform: translateY(10px) (décalés vers le bas)
Image : visible normalement
```

**Visuel** :
```
┌────────────────┐
│                │
│                │
│     IMAGE      │
│                │
│                │
└────────────────┘
Rien d'autre visible
```

### État hover (au survol)

```css
Overlay : opacity: 1 (visible avec gradient noir)
Titre : transform: translateY(0) (remonte, visible)
Tags : transform: translateY(0) (remontent, visibles)
Image : scale(1.1) (zoom)
Bouton "voir" : opacity: 1 (apparaît)
```

**Visuel** :
```
┌────────────────┐
│           [🔍] │ ← Bouton "voir en taille réelle"
│     IMAGE      │ ← Zoom 1.1x
│   ▓▓▓▓▓▓▓▓▓▓  │ ← Gradient noir (95% opacité en bas)
│   "Titre"      │ ← Titre blanc avec ombre
│   [TAG] [TAG]  │ ← Tags avec fond noir
└────────────────┘
```

---

## 🎯 Gestion de la couleur du titre

### Variables CSS utilisées

```css
.wp-custom-gallery .gallery-item-overlay .gallery-item-info h3 {
    color: var(--title-color); /* Variable CSS personnalisable */
}
```

### Valeur par défaut

```css
/* Dans .wp-custom-gallery (ligne 15) */
--title-color: #ffffff; /* Blanc par défaut */
```

### Personnalisation par galerie

Chaque galerie peut avoir sa propre couleur via l'admin :

```html
<!-- Exemple : Galerie avec titre rouge -->
<div class="wp-custom-gallery" style="--title-color: #ff0000;">
    <!-- Les h3 seront rouges -->
</div>

<!-- Exemple : Galerie avec titre bleu -->
<div class="wp-custom-gallery" style="--title-color: #0066cc;">
    <!-- Les h3 seront bleus -->
</div>
```

**Breakdance ne peut PAS surcharger** car notre spécificité est plus forte !

---

## 🧪 Test de validation

### 1. Test hover

```bash
✅ Sans survol : Aucun texte visible
✅ Au survol : Titre + tags apparaissent
✅ Overlay apparaît au survol
✅ Image zoom au survol
```

### 2. Test Breakdance

```bash
✅ Sur une page Breakdance : le titre de la galerie utilise var(--title-color)
✅ Pas d'override par var(--bde-headings-color)
✅ Couleur personnalisée respectée
```

### 3. Test couleurs personnalisées

```bash
✅ Admin : Changer "Couleur du titre" → #ff0000
✅ Frontend : Le titre est rouge au survol
✅ Breakdance ne force PAS sa couleur
```

---

## 📝 Code CSS complet (pour référence)

```css
/* Overlay - CACHÉ par défaut, visible au hover */
.gallery-item-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        to top,
        var(--overlay-color, rgba(0, 0, 0, 0.95)) 0%,
        rgba(0, 0, 0, 0.3) 50%,
        transparent 100%
    );
    opacity: 0; /* CACHÉ par défaut */
    transition: opacity 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 16px;
    pointer-events: none;
}

.gallery-item:hover .gallery-item-overlay {
    opacity: 1; /* VISIBLE au hover */
}

/* Informations de l'image - cachées par défaut */
.gallery-item-info {
    color: #fff;
    transform: translateY(10px); /* DÉCALÉ (caché) par défaut */
    transition: transform 0.3s ease;
    pointer-events: auto;
}

.gallery-item:hover .gallery-item-info {
    transform: translateY(0); /* VISIBLE au hover */
}

/* Titre avec spécificité forte pour contrer Breakdance (sans !important) */
.wp-custom-gallery .gallery-item-overlay .gallery-item-info h3 {
    margin: 0 0 8px 0;
    font-size: var(--title-font-size);
    font-weight: var(--title-font-weight);
    font-family: var(--font-family);
    color: var(--title-color); /* Pas de !important - la spécificité suffit */
    text-shadow: var(--shadow-medium); /* Ombre pour lisibilité */
    line-height: 1.3;
}

/* Tags */
.gallery-tags .tag {
    background: var(--tag-bg-color);
    color: var(--tag-text-color);
    font-size: var(--tag-font-size);
    font-family: var(--font-family);
    text-shadow: var(--shadow-light);
    /* ... */
}
```

---

## 🚀 Avantages de cette approche

### ✅ Pas de !important

```css
/* ❌ AVANT (mauvaise pratique) */
.gallery-item-info h3 {
    color: var(--title-color) !important;
}

/* ✅ APRÈS (bonne pratique) */
.wp-custom-gallery .gallery-item-overlay .gallery-item-info h3 {
    color: var(--title-color); /* Spécificité naturelle */
}
```

### ✅ Maintenabilité

- Pas de guerre de `!important`
- CSS propre et compréhensible
- Facile à débugger

### ✅ Performance

- Pas de recalcul CSS inutile
- Animations fluides
- Transitions optimisées

---

## 🔍 Dépannage

### Si Breakdance force toujours sa couleur

Si malgré tout Breakdance utilise des `!important`, utiliser cette solution de secours :

```css
/* Dernier recours : ajouter [data-gallery-id] */
.wp-custom-gallery[data-gallery-id] .gallery-item-overlay .gallery-item-info h3 {
    color: var(--title-color) !important;
}
```

**Spécificité** : 0,0,5,1 (4 classes + 1 attribut + 1 élément) = ULTRA FORT

---

## 📌 Résumé

### Changements

1. **Overlay** : `opacity: 1` → `opacity: 0` (caché par défaut)
2. **Titre/Tags** : `translateY(0)` → `translateY(10px)` (cachés par défaut)
3. **Spécificité** : `.gallery-item-info h3` → `.wp-custom-gallery .gallery-item-overlay .gallery-item-info h3`
4. **Transition** : `background 0.3s` → `opacity 0.3s` (plus simple et performant)

### Résultat

✅ Titre et tags apparaissent **UNIQUEMENT au hover**
✅ Overlay apparaît **UNIQUEMENT au hover**
✅ Couleur du titre **respectée** même avec Breakdance
✅ **Aucun !important** sur la couleur
✅ Code **propre et maintenable**

---

**Version** : 2.0.2
**Date** : 2024-12-05
**Modification** : Fix hover + conflit Breakdance résolu
