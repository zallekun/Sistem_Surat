# Assets Directory Structure

Folder ini berisi semua resource static untuk Sistem Surat Menyurat Fakultas UNJANI.

## 📁 Structure

```
assets/
├── css/                    # Custom CSS files
│   ├── app.css            # Main application styles
│   ├── auth.css           # Authentication pages styles
│   └── components.css     # Reusable component styles
├── js/                     # JavaScript files
│   ├── app.js             # Main application scripts
│   ├── workflow.js        # Workflow related scripts
│   └── file-upload.js     # File upload functionality
├── images/                 # Image assets
│   ├── backgrounds/       # Background images
│   │   └── Background_Gedung.webp
│   ├── logos/             # Brand logos
│   │   └── logo_unjani.png
│   └── icons/             # Application icons
│       ├── favicon.ico
│       └── app-icon.png
└── fonts/                  # Custom fonts (if any)
    └── custom-fonts.woff2
```

## 🎨 Image Guidelines

### Backgrounds
- **Format**: WebP preferred, PNG/JPG fallback
- **Size**: Recommended 1920x1080 or higher
- **Usage**: Login pages, hero sections

### Logos
- **Format**: PNG with transparency, SVG preferred
- **Sizes**: Multiple sizes (32x32, 64x64, 128x128, 256x256)
- **Usage**: Navigation, headers, branding

### Icons
- **Format**: PNG/SVG
- **Sizes**: 16x16, 24x24, 32x32, 48x48
- **Style**: Consistent with overall design

## 🔧 Usage in Views

### Background Images
```php
background: url('<?= base_url('assets/images/backgrounds/Background_Gedung.webp') ?>');
```

### Logos
```php
<img src="<?= base_url('assets/images/logos/logo_unjani.png') ?>" alt="Logo UNJANI">
```

### CSS Files
```php
<link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
```

### JavaScript Files
```php
<script src="<?= base_url('assets/js/app.js') ?>"></script>
```

## 📝 Naming Conventions

- Use lowercase with hyphens for file names: `background-login.webp`
- Be descriptive: `logo-unjani-white.png` instead of `logo1.png`
- Include size in filename if multiple sizes: `icon-32x32.png`
- Use semantic prefixes: `bg-`, `logo-`, `icon-`, `btn-`

## 🚀 Optimization

- Compress images before uploading
- Use WebP format for backgrounds when possible
- Optimize SVGs by removing unnecessary metadata
- Minify CSS and JS files for production

## 📱 Responsive Considerations

- Provide multiple image sizes for different screen densities
- Use CSS media queries for different background images
- Consider mobile-first approach for image loading