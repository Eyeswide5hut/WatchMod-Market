# WatchModMarket WordPress Theme

A custom WordPress theme for WatchModMarket - a premium watch parts marketplace and custom watch builder platform.

## Features

- Neo-Brutalist design aesthetic with bold typography, strong contrasts, and geometric shapes
- Fully responsive layout that works on all devices
- WooCommerce integration for the online shop
- Custom Watch Builder tool for users to design their own timepieces
- Custom post types for Watch Parts, Custom Builds, and Community Posts
- Custom taxonomies for advanced filtering and categorization
- AJAX cart and quick view functionality
- Built with performance and SEO in mind

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- WooCommerce 7.0 or higher (for shop functionality)

## Installation

1. Download the theme zip file from the releases page or clone this repository
2. In your WordPress admin, go to Appearance > Themes > Add New
3. Click "Upload Theme" and select the zip file
4. Activate the theme after installation

## Required Plugins

For full functionality, the theme requires the following plugins:

- WooCommerce (for shop functionality)
- Advanced Custom Fields (for extended content management)
- Contact Form 7 (for contact forms)

## Theme Structure

```
watchmodmarket/
├── assets/              # Theme assets
│   ├── css/             # CSS files
│   ├── js/              # JavaScript files
│   ├── images/          # Theme images
│   ├── fonts/           # Custom fonts
│   └── models/          # 3D models for watch builder
├── inc/                 # Theme includes
│   ├── post-types.php   # Custom post types
│   ├── taxonomies.php   # Custom taxonomies
│   ├── woocommerce.php  # WooCommerce integration
│   └── widgets.php      # Custom widgets
├── template-parts/      # Template parts
├── woocommerce/         # WooCommerce templates
├── functions.php        # Theme functions
├── style.css            # Main stylesheet
├── index.php            # Main template
├── header.php           # Header template
├── footer.php           # Footer template
└── page-templates/      # Custom page templates
    ├── page-builder.php # Watch Builder template
    └── page-contact.php # Contact page template
```

## Watch Builder Features

The custom Watch Builder tool allows users to:

- Visualize their custom watch in 3D
- Select compatible parts (cases, dials, hands, straps, movements)
- View the watch from different angles (front, side, back, 3D)
- Get compatibility warnings for incompatible parts
- Save their designs to their account
- Add completed builds to cart

## Customization

The theme includes several customization options available in the WordPress Customizer:

1. **Site Identity**: Logo, site title, and tagline
2. **Colors**: Primary, secondary, and accent colors
3. **Typography**: Font selections for headings and body text
4. **Header Options**: Navigation style, sticky header, search bar
5. **Footer Options**: Widget areas, copyright text
6. **Shop Settings**: Product display options, related products
7. **Social Media**: Social media profile links

## Development

### CSS Architecture

The theme uses a modular CSS approach:

- `main.css`: Core theme styles and variables
- Component-specific CSS files (e.g., `shop.css`, `builder.css`)
- Responsive styles integrated within each file

### JavaScript

- jQuery for DOM manipulation and AJAX requests
- ES6+ features for modern browser support
- Three.js for 3D watch visualization
- Modular code organization for maintainability

### Building from Source

1. Clone the repository
2. Install dependencies: `npm install`
3. Run build script: `npm run build`

## Credits

- Icons: Font Awesome
- 3D models: Original creations by WatchModMarket design team
- Photography: WatchModMarket product imagery

## License

This theme is licensed under the GPL v2 or later.

## Support

For theme support, please contact support@watchmodmarket.com or visit our [support forum](https://watchmodmarket.com/support)."# WatchMod-Market" 
