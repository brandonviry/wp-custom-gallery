# 🔧 Fix : Badges ne prennent pas les couleurs de branding

## 🔍 Diagnostic du problème

### Symptôme
Les badges de filtres ("Tout Afficher", "Rencontre", "Lapin", "Pro", "Date") utilisent les couleurs par défaut (bleu, orange) au lieu des couleurs personnalisées dans l'admin.

### Cause probable

**Option 1** : La galerie n'a pas été **sauvegardée** après l'ajout du système de branding complet
- Les nouvelles variables CSS ne sont pas stockées en base de données
- Le shortcode utilise les valeurs par défaut

**Option 2** : Les variables CSS ne sont pas appliquées en style inline

---

## ✅ Solution : Re-sauvegarder la galerie

### Étape 1 : Ouvrir l'admin WordPress

1. Allez dans **Dashboard WordPress** → **Galeries**
2. Cliquez sur **"Éditer"** pour votre galerie existante

### Étape 2 : Vérifier les couleurs

Dans la section **"🎨 Branding & Design Complet"** :

```
🌈 Couleurs principales
┌────────────────────────────────────┐
│ Couleur primaire    : #ef4444     │ ← Rouge (défaut)
│ Couleur secondaire  : #f59e0b     │ ← Orange (défaut)
└────────────────────────────────────┘
```

**Modifiez ces couleurs** pour votre branding :
- Exemple bleu : `#0066cc` (primaire) + `#00aaff` (secondaire)
- Exemple vert : `#10b981` (primaire) + `#34d399` (secondaire)

### Étape 3 : Sauvegarder

Cliquez sur **"💾 Enregistrer la galerie"** en bas du formulaire.

### Étape 4 : Vider le cache

Si vous utilisez un plugin de cache (WP Rocket, Autoptimize, etc.) :
1. Videz le cache du plugin
2. Videz le cache du navigateur (Ctrl + Shift + Delete)

### Étape 5 : Vérifier

Rechargez la page avec la galerie :
- Les badges actifs doivent maintenant utiliser le gradient personnalisé
- Les couleurs doivent correspondre à celles choisies dans l'admin

---

## 🧪 Test rapide : Inspecter le HTML

### Avec F12 (DevTools)

1. Ouvrez la page avec la galerie
2. Appuyez sur **F12**
3. Trouvez l'élément `.wp-custom-gallery` dans l'inspecteur
4. Vérifiez l'attribut `style` :

**AVANT (pas sauvegardé)** ❌
```html
<div class="wp-custom-gallery" data-gallery-id="1">
    <!-- Pas de style inline ou style vide -->
</div>
```

**APRÈS (sauvegardé)** ✅
```html
<div class="wp-custom-gallery" data-gallery-id="1" style="--primary-color: #ef4444; --secondary-color: #f59e0b; --title-color: #ffffff; ...">
    <!-- Toutes les variables CSS sont définies -->
</div>
```

### Vérifier les variables CSS appliquées

Dans DevTools :
1. Sélectionnez `.wp-custom-gallery`
2. Onglet **"Computed"** (Calculé)
3. Cherchez `--primary-color` et `--secondary-color`
4. Elles doivent afficher vos couleurs personnalisées

---

## 🎨 Vérification du CSS

### Le CSS est correct

```css
/* Badge actif utilise bien les variables */
.filter-badge.active {
    background: linear-gradient(
        to right,
        var(--primary-color, #ef4444),    /* ✅ Variable CSS */
        var(--secondary-color, #f59e0b)    /* ✅ Variable CSS */
    );
    color: #ffffff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}
```

Cela signifie que le CSS **fonctionne correctement**. Le problème est que les variables ne sont pas définies sur le conteneur `.wp-custom-gallery`.

---

## 🔧 Vérification du code PHP

### Le shortcode applique bien les variables

**Fichier** : `includes/class-gallery-shortcode.php` (lignes 115-131)

```php
$custom_style = sprintf(
    '--primary-color: %s; --secondary-color: %s; --title-color: %s; ...',
    esc_attr($primary_color),
    esc_attr($secondary_color),
    esc_attr($title_color),
    // ...
);
```

**Ligne 135** :
```php
<div class="<?php echo $gallery_class; ?>" style="<?php echo $custom_style; ?>">
```

Le code PHP est correct et applique bien les variables en style inline.

---

## ❓ Pourquoi ça ne marche pas ?

### Raison 1 : Anciennes galeries

Si votre galerie a été créée **AVANT** l'ajout du système de branding complet, elle ne contient **PAS** les nouvelles options dans la base de données.

**Solution** : Re-sauvegarder la galerie dans l'admin.

### Raison 2 : Cache

Votre navigateur ou un plugin de cache affiche une version ancienne du HTML.

**Solution** : Vider tous les caches.

### Raison 3 : Valeurs par défaut

Les variables utilisent les valeurs par défaut si aucune valeur n'est définie.

**Vérification** :
```css
var(--primary-color, #ef4444)
                    ↑
                    Valeur par défaut si --primary-color n'existe pas
```

Si vous voyez **exactement** `#ef4444` (rouge) et `#f59e0b` (orange), c'est que les variables ne sont pas définies.

---

## 🚀 Solution rapide : Forcer les variables

Si re-sauvegarder ne fonctionne pas, ajoutez ce CSS dans **Apparence > Personnaliser > CSS additionnel** :

```css
/* Forcer les couleurs de branding pour TOUTES les galeries */
.wp-custom-gallery {
    --primary-color: #0066cc !important;    /* Votre bleu */
    --secondary-color: #00aaff !important;  /* Votre bleu clair */
}
```

Remplacez `#0066cc` et `#00aaff` par vos couleurs de marque.

---

## 📊 Tableau de dépannage

| Symptôme | Cause probable | Solution |
|----------|----------------|----------|
| Badges toujours rouge/orange | Galerie pas sauvegardée | Re-sauvegarder dans l'admin |
| `style=""` vide dans le HTML | Galerie créée avant branding | Re-sauvegarder dans l'admin |
| Variables CSS non définies | Cache | Vider cache plugin + navigateur |
| Couleurs pas appliquées | Conflit CSS | Ajouter CSS custom avec `!important` |

---

## 🎯 Checklist finale

Après avoir suivi les étapes :

- [ ] Galerie sauvegardée dans l'admin
- [ ] Cache vidé (plugin + navigateur)
- [ ] Attribut `style="--primary-color: ..."` présent dans le HTML
- [ ] Badge "Tout Afficher" (actif par défaut) affiche le gradient
- [ ] Cliquer sur un autre badge affiche le gradient
- [ ] Les couleurs correspondent à celles de l'admin

---

## 📝 Exemple complet fonctionnel

### Dans l'admin

```
Couleur primaire   : #10b981 (vert)
Couleur secondaire : #34d399 (vert clair)
```

### Dans le HTML généré

```html
<div class="wp-custom-gallery" data-gallery-id="1" style="--primary-color: #10b981; --secondary-color: #34d399; ...">
    <div class="gallery-filters">
        <button class="filter-badge active">Tout afficher</button>
        <button class="filter-badge">Rencontre</button>
    </div>
</div>
```

### Résultat visuel

Le badge actif affiche un **gradient vert** (#10b981 → #34d399) au lieu de rouge/orange !

---

## 🆘 Si rien ne fonctionne

### Diagnostic avancé

Copiez-collez ce code dans la **Console** (F12) sur la page de la galerie :

```javascript
const gallery = document.querySelector('.wp-custom-gallery');
console.log('HTML style attribute:', gallery.getAttribute('style'));
console.log('Computed --primary-color:', getComputedStyle(gallery).getPropertyValue('--primary-color'));
console.log('Computed --secondary-color:', getComputedStyle(gallery).getPropertyValue('--secondary-color'));
```

**Résultat attendu** :
```
HTML style attribute: --primary-color: #10b981; --secondary-color: #34d399; ...
Computed --primary-color:  #10b981
Computed --secondary-color:  #34d399
```

**Si vide** → La galerie n'est pas sauvegardée correctement.

### Vérifier la base de données

Dans **phpMyAdmin** ou équivalent :

```sql
SELECT name, settings FROM wp_custom_galleries WHERE id = 1;
```

Le champ `settings` doit contenir un JSON avec toutes les options :

```json
{
  "grid_columns": 4,
  "primary_color": "#10b981",
  "secondary_color": "#34d399",
  "title_color": "#ffffff",
  ...
}
```

**Si anciennes clés seulement** → Re-sauvegarder la galerie.

---

**Version** : 2.0.3
**Date** : 2024-12-05
**Problème** : Badges ne prennent pas les couleurs de branding
**Solution** : Re-sauvegarder la galerie dans l'admin WordPress
