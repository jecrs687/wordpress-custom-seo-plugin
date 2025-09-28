# Custom SEO Plugin

A comprehensive WordPress SEO plugin with REST API fields, XML sitemaps, breadcrumbs, and advanced social sharing capabilities.

## File Structure

```
custom-seo/
├── custom-seo.php              # Main plugin file (initialization)
├── admin/                      # Admin functionality
│   ├── meta-boxes.php         # Post/page SEO meta boxes
│   └── settings-page.php      # Plugin settings page
├── includes/                   # Core functionality
│   ├── functions.php          # Utility functions
│   ├── meta-output.php        # Frontend meta tag output  
│   ├── sitemap.php            # XML sitemap generation
│   ├── breadcrumbs.php        # Breadcrumb functionality
│   ├── schema.php             # JSON-LD schema markup
│   └── analytics.php          # Analytics integration
├── assets/                     # Static assets (CSS, JS, images)
└── languages/                  # Translation files
```

## Features

### ✅ Meta Fields & REST API
- Custom SEO titles and descriptions
- Focus keywords
- Canonical URLs
- Robots directives (noindex, nofollow, etc.)
- Open Graph tags for Facebook
- Twitter Card support
- Custom redirects (301/302)

### ✅ XML Sitemaps
- Automatic sitemap generation at `/sitemap.xml`
- Separate sitemaps for post types and taxonomies
- Image sitemaps included
- Respects SEO settings (excludes noindex content)
- Dynamic priority calculation

### ✅ Breadcrumbs
- Hierarchical breadcrumb navigation
- JSON-LD structured data
- Customizable display options
- Template function: `custom_seo_breadcrumbs()`

### ✅ Schema Markup
- Article schema for posts
- Organization schema
- Breadcrumb schema
- Custom JSON-LD support
- Product, Event, and FAQ schema types

### ✅ Analytics Integration
- Google Analytics 4 support
- Custom analytics code
- Search engine verification codes
- Facebook App ID integration

### ✅ Admin Interface
- Tabbed meta boxes in post editor
- Global settings page
- Media uploader integration
- Translatable strings

## Usage

### Display Breadcrumbs
Add this code to your theme templates:

```php
<?php if ( function_exists( 'custom_seo_breadcrumbs' ) ) {
    custom_seo_breadcrumbs();
} ?>
```

### Access Meta Fields via REST API
All SEO fields are available through the WordPress REST API:

```
GET /wp-json/wp/v2/posts/123
```

### Configure Global Settings
Navigate to **Settings > Custom SEO** in your WordPress admin.

## Development

### Adding New Meta Fields
1. Add the field to the `register_meta_fields()` method in `custom-seo.php`
2. Update the `custom_seo_get_meta_fields()` function in `includes/functions.php`
3. Add the field to the meta box HTML in `admin/meta-boxes.php`
4. Handle the field in the meta output functions in `includes/meta-output.php`

### Extending Schema Types
Add new schema types by creating methods in the `Custom_SEO_Schema` class in `includes/schema.php`.

### Customizing Breadcrumbs
Modify the breadcrumb generation logic in the `Custom_SEO_Breadcrumbs` class in `includes/breadcrumbs.php`.

## Translation

The plugin is translation-ready with the text domain `custom-seo`. Translation files should be placed in the `languages/` directory.

## Version History

- **1.0.0** - Initial release with comprehensive SEO features

## Requirements

- WordPress 5.0+
- PHP 7.4+

## License

GPL v2 or later