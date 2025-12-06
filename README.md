# WP Custom Gallery - Plugin WordPress

Un plugin WordPress élégant pour créer des galeries photo avec effet grayscale, filtres par tags et design moderne.

## Fonctionnalités

- **Interface d'administration intuitive** dans le dashboard WordPress
- **Sélection d'images** depuis la médiathèque WordPress
- **Deux styles d'affichage** : couleur ou noir et blanc (grayscale avec animation au survol)
- **Filtrage dynamique** par attributs ALT (badges colorés)
- **Configuration flexible** :
  - Choix du nombre de colonnes (3, 4, 5, ou 6)
  - Tri croissant ou décroissant
  - Tri par titre, alt ou date
  - Style des images (couleur ou grayscale)
- **Design moderne et responsive** inspiré des meilleures pratiques UI/UX
- **Lightbox** pour afficher les images en plein écran
- **Navigation au clavier** (flèches gauche/droite, Échap)
- **Shortcode simple** pour intégrer la galerie n'importe où

## Installation

1. **Télécharger le plugin** :
   - Placez le dossier `wp-custom-gallery` dans `/wp-content/plugins/`

2. **Activer le plugin** :
   - Allez dans le dashboard WordPress
   - Naviguez vers "Extensions" > "Extensions installées"
   - Activez "WP Custom Gallery"

3. **Vérification** :
   - Un nouveau menu "Galeries" devrait apparaître dans la barre latérale

## Utilisation

### 1. Créer une galerie

1. Cliquez sur **"Galeries"** dans le menu WordPress
2. Cliquez sur **"Créer une nouvelle galerie"**
3. Donnez un nom à votre galerie
4. Cliquez sur **"Sélectionner les images"**
5. Choisissez vos images depuis la médiathèque
6. Configurez les paramètres :
   - **Nombre de colonnes** : 3, 4, 5 ou 6
   - **Ordre d'affichage** : Croissant (A-Z) ou Décroissant (Z-A)
   - **Trier par** : Titre, ALT ou Date
7. Cliquez sur **"Enregistrer la galerie"**

### 2. Ajouter des tags ALT pour le filtrage

Pour que le filtrage fonctionne, ajoutez des attributs ALT à vos images :

1. Dans la médiathèque WordPress, éditez une image
2. Dans le champ **"Texte alternatif"**, ajoutez des mots-clés séparés par des espaces ou virgules
   - Exemple : `rencontre evenement`
   - Exemple : `paysage montagne`
   - Exemple : `portrait famille`

### 3. Afficher la galerie sur votre site

Après avoir créé une galerie, copiez le shortcode et collez-le où vous voulez :

```
[custom_gallery id="1"]
```

**Où placer le shortcode ?**
- Dans un article ou une page
- Dans un widget texte
- Dans un template PHP : `<?php echo do_shortcode('[custom_gallery id="1"]'); ?>`

### 4. Utiliser les filtres

Les visiteurs peuvent filtrer les images en cliquant sur les badges en haut de la galerie. Les badges sont automatiquement générés à partir des attributs ALT de vos images.

## Structure du plugin

```
wp-custom-gallery/
├── wp-custom-gallery.php          # Fichier principal
├── includes/
│   ├── class-gallery-admin.php    # Interface d'administration
│   ├── class-gallery-shortcode.php # Rendu de la galerie
│   └── class-gallery-ajax.php     # Gestion AJAX
├── assets/
│   ├── css/
│   │   ├── admin.css              # Styles administration
│   │   └── frontend.css           # Styles frontend
│   └── js/
│       ├── admin.js               # Scripts administration
│       └── frontend.js            # Scripts frontend
└── README.md
```

## Design et personnalisation

### Styles d'affichage

Le plugin propose deux styles d'affichage :

1. **Mode couleur** (par défaut) : Les images sont affichées en couleur avec un effet de zoom au survol
2. **Mode grayscale** : Les images sont affichées en noir et blanc et repassent en couleur au survol

Vous pouvez choisir le style lors de la création ou de l'édition d'une galerie.

### Personnalisation CSS

Vous pouvez personnaliser l'apparence en ajoutant du CSS personnalisé dans votre thème. Un fichier d'exemple complet est fourni : [custom-colors-example.css](custom-colors-example.css)

**Exemples de personnalisation :**

```css
/* Modifier la couleur d'un badge spécifique */
.filter-badge[data-filter="rencontres"].active {
    background: #ec4899; /* Rose */
    box-shadow: 0 4px 6px rgba(236, 72, 153, 0.3);
}

/* Ajouter des icônes aux badges */
.filter-badge[data-filter="rencontres"]::before {
    content: "❤️ ";
}

/* Personnaliser le fond de la galerie */
.wp-custom-gallery {
    background: linear-gradient(
        to bottom right,
        rgba(255, 237, 213, 0.3),
        rgba(255, 255, 255, 1),
        rgba(254, 243, 199, 0.3)
    );
    padding: 40px 20px;
    border-radius: 24px;
}
```

### Prévisualisation

Un fichier de démonstration HTML est inclus pour prévisualiser le design : [demo.html](demo.html)

Ouvrez ce fichier dans votre navigateur pour voir la galerie en action sans WordPress.

## Exemples d'utilisation

### Exemple 1 : Galerie d'événements

1. Créez une galerie "Événements 2024"
2. Ajoutez vos photos avec des ALT comme :
   - `rencontre networking`
   - `conference tech`
   - `atelier formation`
3. Les visiteurs pourront filtrer par "rencontre", "conference", etc.

### Exemple 2 : Portfolio photographe

1. Créez une galerie "Portfolio"
2. Ajoutez vos photos avec des ALT comme :
   - `portrait famille`
   - `paysage montagne`
   - `mariage ceremonie`
3. Configurez 4 colonnes avec tri par date décroissant

## Compatibilité

- WordPress 5.0+
- PHP 7.0+
- Tous les navigateurs modernes
- Responsive (mobile, tablette, desktop)

## Support

Pour toute question ou problème, consultez la documentation WordPress ou contactez le support.

## Licence

GPL v2 

---

**Développé  pour WordPress**
