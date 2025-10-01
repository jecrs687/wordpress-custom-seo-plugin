# 🚀 Custom SEO Plugin

> **A comprehensive WordPress SEO solution with advanced features for modern websites**

[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-1.0.0-orange.svg)](#)

## 📋 Table of Contents

- [✨ Features](#-features)
- [🔧 Installation](#-installation)
- [🎯 Quick Start](#-quick-start)
- [📖 Usage Examples](#-usage-examples)
- [🏗️ File Structure](#️-file-structure)
- [🔌 REST API](#-rest-api)
- [🎨 Customization](#-customization)
- [🌐 Translation](#-translation)
- [📝 Changelog](#-changelog)

---

## ✨ Features

### 🎯 **Core SEO**
- ✅ **Custom Meta Titles & Descriptions** - Override default titles and descriptions
- ✅ **Focus Keywords** - Track primary keywords for each page
- ✅ **Canonical URLs** - Prevent duplicate content issues
- ✅ **Robots Directives** - Control search engine indexing (noindex, nofollow, etc.)
- ✅ **Language Support** - Set content language with HTML lang attribute and meta tags
- ✅ **301/302 Redirects** - Built-in redirect functionality

### 📱 **Social Media**
- ✅ **Open Graph Tags** - Perfect Facebook & LinkedIn sharing
- ✅ **Twitter Cards** - Rich Twitter previews
- ✅ **Default Images** - Fallback images for social sharing
- ✅ **Facebook App ID** - Enhanced social analytics

### 🗺️ **XML Sitemaps**
- ✅ **Automatic Generation** - Dynamic sitemaps at `/sitemap.xml`
- ✅ **Image Sitemaps** - Include images in sitemaps
- ✅ **Smart Filtering** - Respects SEO settings (excludes noindex)
- ✅ **Priority Calculation** - Dynamic priority based on content age & engagement

### 🍞 **Breadcrumbs**
- ✅ **Hierarchical Navigation** - Smart breadcrumb generation
- ✅ **JSON-LD Schema** - Rich snippets for search engines
- ✅ **Customizable Display** - Control separator, home text, etc.
- ✅ **Multi-level Support** - Categories, pages, custom post types

### 📊 **Analytics & Schema**
- ✅ **Google Analytics 4** - Built-in GA4 integration
- ✅ **JSON-LD Schema** - Article, Product, Organization markup
- ✅ **Search Console Verification** - Google, Bing, Pinterest
- ✅ **Custom Schema** - Add your own JSON-LD markup

### �️ **Categories & Tags Management**
- ✅ **Auto-Creation** - Automatically create categories and tags if they don't exist
- ✅ **Bulk Assignment** - Assign multiple categories/tags via comma-separated lists
- ✅ **Smart Merging** - Add to existing or replace entirely
- ✅ **REST API Support** - Full API integration for category/tag management
- ✅ **Error Handling** - Detailed feedback on creation success/failures

### �🎛️ **Admin Interface**
- ✅ **Tabbed Meta Boxes** - Clean, organized interface
- ✅ **Media Uploader** - Easy image selection
- ✅ **Global Settings** - Centralized configuration
- ✅ **REST API Ready** - All fields available via API

---

## 🔧 Installation

### Method 1: WordPress Admin (Recommended)
1. Download the plugin zip file
2. Go to **Plugins → Add New → Upload Plugin**
3. Choose the zip file and click **Install Now**
4. **Activate** the plugin

### Method 2: Manual Installation
1. Extract the zip file to `/wp-content/plugins/custom-seo/`
2. Activate the plugin through the **Plugins** menu

### Method 3: WP-CLI
```bash
wp plugin install custom-seo.zip --activate
```

---

## 🎯 Quick Start

### 1. **Configure Global Settings**
Navigate to **Settings → Custom SEO** and configure:
- Google Analytics ID
- Social media accounts
- Default images
- Search engine verification codes

### 2. **Optimize Your First Post**
1. Edit any post or page
2. Scroll to the **SEO Settings** meta box
3. Fill in your custom title and description
4. Add Open Graph image
5. **Publish** and test!

### 3. **Add Breadcrumbs to Your Theme**
```php
<?php if ( function_exists( 'custom_seo_breadcrumbs' ) ) {
    custom_seo_breadcrumbs();
} ?>
```

### 4. **Check Your Sitemap**
Visit: `https://yoursite.com/sitemap.xml`

---

## 📖 Usage Examples

### 🎨 **Breadcrumb Customization**

#### Basic Usage
```php
// Default breadcrumbs
custom_seo_breadcrumbs();
```

#### Advanced Customization
```php
// Custom breadcrumbs with options
custom_seo_breadcrumbs([
    'separator' => ' → ',
    'home_text' => 'Home',
    'show_current' => true,
    'show_home' => true,
    'structured_data' => true
]);
```

#### Styling Breadcrumbs
```css
.custom-seo-breadcrumbs {
    margin: 20px 0;
    font-size: 14px;
    color: #666;
}

.breadcrumb-item a {
    color: #0073aa;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    text-decoration: underline;
}

.breadcrumb-separator {
    margin: 0 8px;
    color: #999;
}
```

### 📊 **Analytics Setup**

#### Google Analytics 4
1. Go to **Settings → Custom SEO**
2. Enter your GA4 Measurement ID (G-XXXXXXXXXX)
3. Save settings

#### Custom Analytics Code
```javascript
// Add custom tracking in the "Google Analytics Code" field
gtag('event', 'page_view', {
    page_title: document.title,
    page_location: window.location.href
});
```

### 🌐 **Language Support**

#### Setting Default Language
```php
// In WordPress Admin → Custom SEO → Settings
// Set "Default Language" to your site's primary language
// This applies to all content unless overridden per-post
```

#### Per-Post Language Override
```php
// In post editor → Custom SEO → General tab
// Select specific language for multilingual content
// Generates proper HTML lang attributes and meta tags
```

#### Generated Output
```html
<!-- HTML tag with language attribute -->
<html lang="es">

<!-- Meta tag for content language -->
<meta http-equiv="content-language" content="es">

<!-- Open Graph locale (auto-converted) -->
<meta property="og:locale" content="es_ES">

<!-- Sitemap hreflang annotations -->
<xhtml:link rel="alternate" hreflang="es" href="https://yoursite.com/post/" />
```

### 🏷️ **Categories & Tags Auto-Creation**

#### How Auto-Creation Works
The plugin can automatically create categories and tags when saving posts. This feature works differently depending on how you save your posts:

**✅ WordPress Admin Interface (Fully Supported)**
```php
// In post editor → Custom SEO → Content tab
// 1. Enter comma-separated category names in "Categories" field
// 2. Enter comma-separated tag names in "Tags" field  
// 3. Check "Auto-create missing categories/tags" option
// 4. Save/Update the post - categories and tags are created automatically
```

**✅ REST API (Automatic Processing)**
```bash
# Create post with automatic category/tag creation
curl -X POST "${WP_SITE}/wp-json/wp/v2/posts" \
  -H "Content-Type: application/json" \
  -u "${WP_USER}:${WP_PASSWORD}" \
  -d '{
    "title": "My New Post",
    "content": "Post content here...",
    "status": "publish",
    "meta": {
      "custom_seo_categories": "WordPress, SEO, Tutorial",
      "custom_seo_tags": "wordpress tips, seo guide, tutorial",
      "custom_seo_auto_create_terms": true,
      "custom_seo_replace_categories": false,
      "custom_seo_replace_tags": false
    }
  }'
# ✅ Categories and tags are created automatically when meta is saved!
```

**🔧 Manual Processing (If Automatic Fails)**
```bash
# If automatic processing doesn't work, use the manual endpoint
curl -X POST "${WP_SITE}/wp-json/custom-seo/v1/process-terms/123" \
  -H "Content-Type: application/json" \
  -u "${WP_USER}:${WP_PASSWORD}" \
  -d '{
    "categories": "WordPress, SEO, Tutorial",
    "tags": "wordpress tips, seo guide, tutorial",
    "auto_create": true,
    "replace_categories": false,
    "replace_tags": false
  }'
```

**🐛 Troubleshooting Auto-Creation**
```bash
# 1. Verify the meta fields were saved
curl -X GET "${WP_SITE}/wp-json/wp/v2/posts/123?_fields=id,meta" \
  -u "${WP_USER}:${WP_PASSWORD}"

# 2. Check if categories/tags were assigned
curl -X GET "${WP_SITE}/wp-json/wp/v2/posts/123?_fields=id,categories,tags" \
  -u "${WP_USER}:${WP_PASSWORD}"

# 3. Enable debug logging (add to wp-config.php)
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
# Check /wp-content/debug.log for processing messages

# 4. Force processing with manual endpoint
curl -X POST "${WP_SITE}/wp-json/custom-seo/v1/process-terms/123" \
  -H "Content-Type: application/json" \
  -u "${WP_USER}:${WP_PASSWORD}" \
  -d '{
    "categories": "WordPress, SEO, Tutorial",
    "tags": "wordpress tips, seo guide, tutorial",
    "auto_create": true,
    "replace_categories": false,
    "replace_tags": false
  }'
```

#### Configuration Options

| Field | Type | Description | Default |
|-------|------|-------------|---------|
| `custom_seo_categories` | string | Comma-separated category names | "" |
| `custom_seo_tags` | string | Comma-separated tag names | "" |
| `custom_seo_auto_create_terms` | boolean | Create missing categories/tags | `true` |
| `custom_seo_replace_categories` | boolean | Replace existing categories | `false` |
| `custom_seo_replace_tags` | boolean | Replace existing tags | `false` |

#### ⚠️ Important Notes for REST API Usage

**Why Categories/Tags Might Not Be Created:**
1. **Missing `custom_seo_auto_create_terms`**: Must be set to `true`
2. **Wrong field names**: Use `custom_seo_categories` not `categories`  
3. **Permission issues**: User must have `edit_posts` capability
4. **Invalid names**: Category/tag names with special characters may fail

**✅ Recent Fixes Applied:**

**v1.0.3 (Latest):**
- 🔧 **CRITICAL FIX**: Replaced deprecated `wp_insert_category()` with modern `wp_insert_term()`
- 🔧 **IMPROVED**: Updated category existence checks to use `get_term_by()` instead of `get_category_by_slug()`
- ✅ **RESOLVED**: "Call to undefined function wp_insert_category" error
- ✅ **COMPATIBILITY**: Now works with all WordPress versions (5.0+)

**v1.0.2:**
- Added `rest_insert_post` and `rest_after_insert_post` hooks for better REST API support
- Auto-creation now works reliably when creating posts via REST API
- Added debug logging for troubleshooting (enable WP_DEBUG to see logs)
- Improved processing to avoid duplicate execution

**Best Practice for REST API:**
```bash
# Always include ALL required fields in the meta object
"meta": {
  "custom_seo_categories": "WordPress, SEO, Tutorial",
  "custom_seo_tags": "wordpress tips, seo guide, tutorial", 
  "custom_seo_auto_create_terms": true,           # 👈 REQUIRED!
  "custom_seo_replace_categories": false,
  "custom_seo_replace_tags": false
}
```

#### Auto-Creation Behavior

**Add Mode (default)**: `replace_categories: false`
- Keeps existing categories/tags
- Adds new ones to existing list
- Perfect for progressive content organization

**Replace Mode**: `replace_categories: true`  
- Removes all existing categories/tags
- Assigns only the specified ones
- Use when reorganizing content structure

#### Error Handling
```javascript
// Success response from process-terms endpoint
{
  "success": true,
  "post_id": 123,
  "results": {
    "categories": {
      "success": ["WordPress", "SEO", "Tutorial"],
      "errors": []
    },
    "tags": {
      "success": ["wordpress tips", "seo guide", "tutorial"],
      "errors": []
    }
  },
  "message": "Processed 3 categories and 3 tags for post 123"
}

// Error response
{
  "success": false,
  "post_id": 123,
  "results": {
    "categories": {
      "success": ["WordPress", "SEO"],
      "errors": ["Failed to create category 'Invalid@Name': Invalid characters"]
    },
    "tags": {
      "success": ["wordpress tips"],
      "errors": ["Tag name cannot be empty"]
    }
  },
  "message": "Processed with errors for post 123"
}
```

### 🗺️ **Sitemap Usage**

#### Main Sitemap Index
```
https://yoursite.com/sitemap.xml
```

#### Individual Sitemaps
```
https://yoursite.com/sitemap-post.xml
https://yoursite.com/sitemap-page.xml
https://yoursite.com/sitemap-product.xml
```

### 📱 **Social Media Meta Tags**

The plugin automatically generates:

```html
<!-- Basic SEO -->
<title>Your Custom Title</title>
<meta name="description" content="Your custom description">
<link rel="canonical" href="https://yoursite.com/page">

<!-- Open Graph -->
<meta property="og:title" content="Your Custom Title">
<meta property="og:description" content="Your custom description">
<meta property="og:image" content="https://yoursite.com/image.jpg">
<meta property="og:type" content="article">

<!-- Twitter Cards -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Your Custom Title">
<meta name="twitter:description" content="Your custom description">
<meta name="twitter:image" content="https://yoursite.com/image.jpg">
```

---

## 🏗️ File Structure

```
custom-seo/
├── 📄 custom-seo.php              # Main plugin file
├── 📁 admin/                      # Admin functionality
│   ├── 📄 meta-boxes.php         # SEO meta boxes
│   └── 📄 settings-page.php      # Global settings
├── 📁 includes/                   # Core functionality
│   ├── 📄 functions.php          # Utility functions
│   ├── 📄 meta-output.php        # Meta tag output
│   ├── 📄 sitemap.php            # XML sitemaps
│   ├── 📄 breadcrumbs.php        # Breadcrumb system
│   ├── 📄 schema.php             # JSON-LD markup
│   └── 📄 analytics.php          # Analytics integration
├── 📁 assets/                     # Plugin assets
│   ├── 📄 icon.png               # Plugin icon (PNG)
│   ├── 📄 icon.svg               # Plugin icon (SVG)
│   ├── 📄 icon-128x128.png       # Plugin icon (128x128)
│   ├── 📄 icon-256x256.png       # Plugin icon (256x256)
│   ├── 📄 banner-772x250.png     # Plugin banner (standard)
│   └── 📄 banner-1544x500.png    # Plugin banner (high-DPI)
├── 📁 languages/                  # Translation files
└── 📄 README.md                   # This file
```

---

## 🔌 REST API

All SEO fields are available through the WordPress REST API with comprehensive cURL examples:

### 🔐 Authentication

First, you'll need authentication. Here are the most common methods:

#### Application Passwords (Recommended)
```bash
# Generate at: WordPress Admin → Users → Your Profile → Application Passwords
export WP_USER="your-username"
export WP_PASSWORD="your-app-password"
export WP_SITE="https://yoursite.com"
```

#### JWT Token (Plugin Required)
```bash
# Login to get JWT token
curl -X POST "${WP_SITE}/wp-json/jwt-auth/v1/token" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "your-password"
  }'

# Use the returned token in Authorization header
export JWT_TOKEN="your-jwt-token-here"
```

---

### 📖 Reading SEO Data

#### Get Single Post with SEO Data
```bash
curl -X GET "${WP_SITE}/wp-json/wp/v2/posts/123" \
  -H "Content-Type: application/json" \
  -u "${WP_USER}:${WP_PASSWORD}"
```

**Response Example:**
```json
{
  "id": 123,
  "title": {
    "rendered": "My Blog Post"
  },
  "meta": {
    "custom_seo_title": "Ultimate Guide to WordPress SEO",
    "custom_seo_description": "Learn advanced WordPress SEO techniques that will boost your rankings in 2025.",
    "custom_focus_keyword": "wordpress seo",
    "custom_canonical_url": "https://yoursite.com/wordpress-seo-guide/",
    "custom_language": "en",
    "custom_og_title": "Ultimate WordPress SEO Guide",
    "custom_og_description": "Master WordPress SEO with our comprehensive guide",
    "custom_og_image_id": 456,
    "custom_twitter_title": "WordPress SEO Guide 🚀",
    "custom_twitter_description": "Boost your WordPress site rankings",
    "custom_twitter_image_id": 457,
    "custom_noindex": false,
    "custom_nofollow": false,
    "custom_noarchive": false,
    "custom_nosnippet": false,
    "custom_schema_type": "Article",
    "custom_redirect_url": "",
    "custom_redirect_type": "301"
  }
}
```

#### Get Multiple Posts with SEO Data
```bash
# Get latest 10 posts with SEO meta
curl -X GET "${WP_SITE}/wp-json/wp/v2/posts?per_page=10&_fields=id,title,meta" \
  -u "${WP_USER}:${WP_PASSWORD}"
```

#### Get Pages with SEO Data
```bash
# Get all pages
curl -X GET "${WP_SITE}/wp-json/wp/v2/pages" \
  -u "${WP_USER}:${WP_PASSWORD}"
```

#### Get Custom Post Types
```bash
# Get products (WooCommerce example)
curl -X GET "${WP_SITE}/wp-json/wp/v2/product" \
  -u "${WP_USER}:${WP_PASSWORD}"
```

---

### ✏️ Creating & Updating SEO Data

#### Create New Post with SEO
```bash
curl -X POST "${WP_SITE}/wp-json/wp/v2/posts" \
  -H "Content-Type: application/json" \
  -u "${WP_USER}:${WP_PASSWORD}" \
  -d '{
    "title": "My New SEO-Optimized Post",
    "content": "<p>This is my amazing content about WordPress SEO.</p>",
    "status": "publish",
    "meta": {
      "custom_seo_title": "Best WordPress SEO Tips for 2025",
      "custom_seo_description": "Discover the most effective WordPress SEO strategies that will skyrocket your website traffic in 2025.",
      "custom_focus_keyword": "wordpress seo 2025",
      "custom_og_title": "WordPress SEO Tips That Actually Work",
      "custom_og_description": "Proven SEO strategies for WordPress sites",
      "custom_og_image_id": 789,
      "custom_twitter_title": "🚀 WordPress SEO Mastery",
      "custom_twitter_description": "Learn SEO techniques that work",
      "custom_schema_type": "Article"
    }
  }'
```

#### Update Existing Post SEO
```bash
curl -X POST "${WP_SITE}/wp-json/wp/v2/posts/123" \
  -H "Content-Type: application/json" \
  -u "${WP_USER}:${WP_PASSWORD}" \
  -d '{
    "meta": {
      "custom_seo_title": "Updated SEO Title",
      "custom_seo_description": "Updated meta description with better keywords",
      "custom_focus_keyword": "updated keyword",
      "custom_canonical_url": "https://yoursite.com/updated-url/"
    }
  }'
```

#### Bulk Update Multiple Posts
```bash
# Update SEO for multiple posts using a script
for id in 123 124 125 126; do
  curl -X POST "${WP_SITE}/wp-json/wp/v2/posts/${id}" \
    -H "Content-Type: application/json" \
    -u "${WP_USER}:${WP_PASSWORD}" \
    -d "{
      \"meta\": {
        \"custom_seo_title\": \"Bulk Updated Title ${id}\",
        \"custom_focus_keyword\": \"bulk keyword ${id}\"
      }
    }"
  echo "Updated post ${id}"
done
```

---

### 🎨 Advanced Payloads

#### Complete SEO Payload Example
```json
{
  "title": "Complete SEO Example Post",
  "content": "<p>Full content here...</p>",
  "excerpt": "Brief excerpt for the post",
  "status": "publish",
  "featured_media": 123,
  "categories": [1, 5],
  "tags": [10, 11, 12],
  "meta": {
    // Basic SEO
    "custom_seo_title": "Complete Guide to WordPress SEO Optimization",
    "custom_seo_description": "Master WordPress SEO with this comprehensive guide covering all aspects from basic setup to advanced techniques.",
    "custom_focus_keyword": "wordpress seo optimization",
    "custom_canonical_url": "https://yoursite.com/complete-wordpress-seo-guide/",
    
    // Categories & Tags Management (New!)
    "custom_seo_categories": "SEO, WordPress, Optimization, Digital Marketing",
    "custom_seo_tags": "seo guide, wordpress tips, search optimization, rankings",
    "custom_seo_replace_categories": false,
    "custom_seo_replace_tags": false,
    "custom_seo_auto_create_terms": true,
    
    // Open Graph
    "custom_og_title": "WordPress SEO Mastery Guide",
    "custom_og_description": "Everything you need to know about WordPress SEO",
    "custom_og_image_id": 456,
    "custom_og_type": "article",
    
    // Twitter Cards
    "custom_twitter_title": "🎯 WordPress SEO Guide",
    "custom_twitter_description": "Complete SEO optimization guide",
    "custom_twitter_image_id": 457,
    "custom_twitter_card_type": "summary_large_image",
    
    // Advanced Settings
    "custom_noindex": false,
    "custom_nofollow": false,
    "custom_noarchive": false,
    "custom_nosnippet": false,
    "custom_noimageindex": false,
    
    // Schema Markup
    "custom_schema_type": "Article",
    "custom_schema_article_type": "BlogPosting",
    "custom_schema_author": "John Doe",
    "custom_schema_publisher": "Your Website",
    
    // Redirects
    "custom_redirect_url": "",
    "custom_redirect_type": "301",
    
    // Analytics
    "custom_ga_event_category": "Blog",
    "custom_ga_event_action": "Read",
    "custom_ga_event_label": "SEO Guide"
  }
}
```

#### WooCommerce Product SEO
```bash
curl -X POST "${WP_SITE}/wp-json/wp/v2/product" \
  -H "Content-Type: application/json" \
  -u "${WP_USER}:${WP_PASSWORD}" \
  -d '{
    "name": "Premium WordPress Theme",
    "type": "simple",
    "regular_price": "99.00",
    "description": "<p>Professional WordPress theme...</p>",
    "short_description": "<p>Premium theme for professionals</p>",
    "meta": {
      "custom_seo_title": "Premium WordPress Theme - Professional Design",
      "custom_seo_description": "Get our premium WordPress theme with professional design, SEO optimization, and 24/7 support.",
      "custom_focus_keyword": "premium wordpress theme",
      "custom_schema_type": "Product",
      "custom_og_title": "Premium WordPress Theme",
      "custom_og_description": "Professional WordPress theme with SEO optimization",
      "custom_twitter_title": "🎨 Premium WP Theme",
      "custom_twitter_description": "Professional design meets SEO optimization"
    }
  }'
```

---

### 🏷️ Categories & Tags Management Examples

#### Create Post with Auto-Generated Categories and Tags
```bash
curl -X POST "${WP_SITE}/wp-json/wp/v2/posts" \
  -H "Content-Type: application/json" \
  -u "${WP_USER}:${WP_PASSWORD}" \
  -d '{
    "title": "Ultimate WordPress Security Guide",
    "content": "<p>Learn how to secure your WordPress site...</p>",
    "status": "publish",
    "meta": {
      "custom_seo_title": "Complete WordPress Security Guide 2025",
      "custom_seo_description": "Comprehensive guide to WordPress security best practices",
      "custom_seo_categories": "Security, WordPress, Tutorials, Web Development",
      "custom_seo_tags": "wordpress security, website protection, hacking prevention, security plugins",
      "custom_seo_auto_create_terms": true,
      "custom_seo_replace_categories": false,
      "custom_seo_replace_tags": false
    }
  }'
```

#### Update Post Categories and Tags
```bash
curl -X POST "${WP_SITE}/wp-json/wp/v2/posts/123" \
  -H "Content-Type: application/json" \
  -u "${WP_USER}:${WP_PASSWORD}" \
  -d '{
    "meta": {
      "custom_seo_categories": "New Category, Updated Category, SEO",
      "custom_seo_tags": "new tag, updated content, seo optimization",
      "custom_seo_replace_categories": true,
      "custom_seo_replace_tags": true,
      "custom_seo_auto_create_terms": true
    }
  }'
```

#### Using the Custom Terms Processing Endpoint
```bash
# Process categories and tags for a specific post
curl -X POST "${WP_SITE}/wp-json/custom-seo/v1/process-terms/123" \
  -H "Content-Type: application/json" \
  -u "${WP_USER}:${WP_PASSWORD}" \
  -d '{
    "categories": "WordPress, SEO, Tutorial, Advanced",
    "tags": "wordpress seo, advanced techniques, tutorial guide",
    "replace_categories": false,
    "replace_tags": false,
    "auto_create": true
  }'
```

**Response Example:**
```json
{
  "success": true,
  "post_id": 123,
  "results": {
    "categories": {
      "success": ["WordPress", "SEO", "Tutorial", "Advanced"],
      "errors": []
    },
    "tags": {
      "success": ["wordpress seo", "advanced techniques", "tutorial guide"],
      "errors": []
    }
  },
  "message": "Processed 4 categories and 3 tags for post 123"
}
```

#### Bulk Category/Tag Assignment Script
```bash
#!/bin/bash
# bulk-terms-assignment.sh

WP_SITE="https://yoursite.com"
WP_USER="your-username"
WP_PASSWORD="your-app-password"

# Array of post IDs to update
POST_IDS=(123 124 125 126 127)

# Categories and tags to assign
CATEGORIES="WordPress, SEO, Marketing, Business"
TAGS="wordpress tips, seo guide, content marketing, business growth"

for post_id in "${POST_IDS[@]}"; do
  echo "Processing post ID: $post_id"
  
  response=$(curl -s -X POST "${WP_SITE}/wp-json/custom-seo/v1/process-terms/${post_id}" \
    -H "Content-Type: application/json" \
    -u "${WP_USER}:${WP_PASSWORD}" \
    -d "{
      \"categories\": \"${CATEGORIES}\",
      \"tags\": \"${TAGS}\",
      \"replace_categories\": false,
      \"replace_tags\": false,
      \"auto_create\": true
    }")
  
  echo "Response: $response"
  echo "---"
  sleep 1  # Be nice to the server
done
```

#### Python Script for Category/Tag Management
```python
#!/usr/bin/env python3
import requests
import json
from requests.auth import HTTPBasicAuth

# Configuration
WP_SITE = "https://yoursite.com"
WP_USER = "your-username"
WP_PASSWORD = "your-app-password"

class WordPressCategoryTagManager:
    def __init__(self, site_url, username, password):
        self.site_url = site_url
        self.auth = HTTPBasicAuth(username, password)
    
    def create_post_with_terms(self, title, content, categories, tags, auto_create=True):
        """Create a new post with categories and tags"""
        url = f"{self.site_url}/wp-json/wp/v2/posts"
        
        data = {
            "title": title,
            "content": content,
            "status": "publish",
            "meta": {
                "custom_seo_categories": categories,
                "custom_seo_tags": tags,
                "custom_seo_auto_create_terms": auto_create,
                "custom_seo_replace_categories": False,
                "custom_seo_replace_tags": False
            }
        }
        
        response = requests.post(url, json=data, auth=self.auth)
        return response.json()
    
    def update_post_terms(self, post_id, categories=None, tags=None, replace_categories=False, replace_tags=False):
        """Update categories and tags for existing post"""
        url = f"{self.site_url}/wp-json/custom-seo/v1/process-terms/{post_id}"
        
        data = {
            "categories": categories or "",
            "tags": tags or "",
            "replace_categories": replace_categories,
            "replace_tags": replace_tags,
            "auto_create": True
        }
        
        response = requests.post(url, json=data, auth=self.auth)
        return response.json()
    
    def bulk_assign_terms(self, post_ids, categories, tags, replace=False):
        """Bulk assign categories and tags to multiple posts"""
        results = []
        
        for post_id in post_ids:
            try:
                result = self.update_post_terms(
                    post_id, 
                    categories, 
                    tags, 
                    replace, 
                    replace
                )
                results.append({"post_id": post_id, "success": True, "data": result})
            except Exception as e:
                results.append({"post_id": post_id, "success": False, "error": str(e)})
        
        return results

# Usage example
if __name__ == "__main__":
    manager = WordPressCategoryTagManager(WP_SITE, WP_USER, WP_PASSWORD)
    
    # Create new post with categories and tags
    new_post = manager.create_post_with_terms(
        title="My New SEO-Optimized Post",
        content="<p>This is my content with auto-generated categories and tags.</p>",
        categories="SEO, WordPress, Marketing",
        tags="seo tips, wordpress guide, digital marketing"
    )
    
    print(f"Created post: {new_post.get('id', 'Error')}")
    
    # Bulk assign terms to existing posts
    post_ids = [123, 124, 125]
    bulk_results = manager.bulk_assign_terms(
        post_ids, 
        "Technology, Programming, Web Development",
        "coding, web dev, tech tips"
    )
    
    for result in bulk_results:
        if result["success"]:
            print(f"✅ Post {result['post_id']}: Updated successfully")
        else:
            print(f"❌ Post {result['post_id']}: {result['error']}")
```

---

### 🔍 Search & Filter Examples

#### Search Posts by SEO Data
```bash
# Find posts with specific focus keyword
curl -X GET "${WP_SITE}/wp-json/wp/v2/posts?meta_key=custom_focus_keyword&meta_value=wordpress%20seo" \
  -u "${WP_USER}:${WP_PASSWORD}"
```

#### Get Posts with Missing SEO Data
```bash
# Find posts without SEO titles
curl -X GET "${WP_SITE}/wp-json/wp/v2/posts?meta_query[0][key]=custom_seo_title&meta_query[0][compare]=NOT%20EXISTS" \
  -u "${WP_USER}:${WP_PASSWORD}"
```

#### Complex Meta Queries
```bash
# Posts with SEO title but no description
curl -X GET "${WP_SITE}/wp-json/wp/v2/posts" \
  -G \
  -d "meta_query[relation]=AND" \
  -d "meta_query[0][key]=custom_seo_title" \
  -d "meta_query[0][compare]=EXISTS" \
  -d "meta_query[1][key]=custom_seo_description" \
  -d "meta_query[1][compare]=NOT EXISTS" \
  -u "${WP_USER}:${WP_PASSWORD}"
```

---

### 📊 Analytics & Reporting

#### Get SEO Statistics
```bash
# Custom endpoint for SEO stats (if implemented)
curl -X GET "${WP_SITE}/wp-json/custom-seo/v1/stats" \
  -u "${WP_USER}:${WP_PASSWORD}"
```

**Example Response:**
```json
{
  "posts_with_seo": 45,
  "posts_without_seo": 12,
  "posts_with_focus_keywords": 38,
  "posts_with_og_images": 33,
  "average_title_length": 52,
  "average_description_length": 148
}
```

---

### 🛠️ Utility Scripts

#### Bash Script for Bulk SEO Updates
```bash
#!/bin/bash
# bulk-seo-update.sh

WP_SITE="https://yoursite.com"
WP_USER="your-username"
WP_PASSWORD="your-app-password"

# Get all posts without SEO titles
posts=$(curl -s -X GET "${WP_SITE}/wp-json/wp/v2/posts?per_page=100&_fields=id,title" \
  -u "${WP_USER}:${WP_PASSWORD}")

# Loop through posts and add basic SEO
echo "$posts" | jq -r '.[] | @base64' | while read -r post; do
  id=$(echo "$post" | base64 -d | jq -r '.id')
  title=$(echo "$post" | base64 -d | jq -r '.title.rendered')
  
  # Generate SEO title and description
  seo_title="${title} - Your Site Name"
  seo_desc="Read about ${title} on our website. Comprehensive guide and insights."
  
  # Update post
  curl -X POST "${WP_SITE}/wp-json/wp/v2/posts/${id}" \
    -H "Content-Type: application/json" \
    -u "${WP_USER}:${WP_PASSWORD}" \
    -d "{
      \"meta\": {
        \"custom_seo_title\": \"${seo_title}\",
        \"custom_seo_description\": \"${seo_desc}\"
      }
    }"
  
  echo "Updated post ID: ${id}"
  sleep 1  # Be nice to the server
done
```

#### Python Script for SEO Analysis
```python
#!/usr/bin/env python3
import requests
import json
from requests.auth import HTTPBasicAuth

# Configuration
WP_SITE = "https://yoursite.com"
WP_USER = "your-username"
WP_PASSWORD = "your-app-password"

def get_posts_seo_data():
    """Get all posts with SEO data"""
    url = f"{WP_SITE}/wp-json/wp/v2/posts"
    params = {
        'per_page': 100,
        '_fields': 'id,title,meta'
    }
    
    response = requests.get(
        url, 
        params=params,
        auth=HTTPBasicAuth(WP_USER, WP_PASSWORD)
    )
    
    return response.json()

def analyze_seo_data(posts):
    """Analyze SEO data for optimization opportunities"""
    analysis = {
        'total_posts': len(posts),
        'missing_seo_title': 0,
        'missing_seo_description': 0,
        'missing_focus_keyword': 0,
        'missing_og_image': 0,
        'title_length_issues': 0,
        'description_length_issues': 0
    }
    
    for post in posts:
        meta = post.get('meta', {})
        
        # Check for missing SEO elements
        if not meta.get('custom_seo_title'):
            analysis['missing_seo_title'] += 1
            
        if not meta.get('custom_seo_description'):
            analysis['missing_seo_description'] += 1
            
        if not meta.get('custom_focus_keyword'):
            analysis['missing_focus_keyword'] += 1
            
        if not meta.get('custom_og_image_id'):
            analysis['missing_og_image'] += 1
        
        # Check length issues
        seo_title = meta.get('custom_seo_title', '')
        if len(seo_title) > 60 or len(seo_title) < 30:
            analysis['title_length_issues'] += 1
            
        seo_desc = meta.get('custom_seo_description', '')
        if len(seo_desc) > 160 or len(seo_desc) < 120:
            analysis['description_length_issues'] += 1
    
    return analysis

# Run analysis
if __name__ == "__main__":
    posts = get_posts_seo_data()
    analysis = analyze_seo_data(posts)
    
    print("SEO Analysis Report")
    print("=" * 50)
    for key, value in analysis.items():
        print(f"{key.replace('_', ' ').title()}: {value}")
```

---

### 🔧 Available Meta Fields Reference

| Field Name | Type | Description | Example |
|------------|------|-------------|---------|
| `custom_seo_title` | string | Custom page title | "Best WordPress SEO Guide" |
| `custom_seo_description` | string | Meta description | "Learn WordPress SEO techniques..." |
| `custom_focus_keyword` | string | Primary keyword | "wordpress seo" |
| `custom_canonical_url` | string | Canonical URL | "https://site.com/page/" |
| `custom_language` | string | Content language | "en", "es", "fr", etc. |
| `custom_og_title` | string | Open Graph title | "Amazing WordPress Guide" |
| `custom_og_description` | string | Open Graph description | "Complete guide to WordPress" |
| `custom_og_image_id` | integer | Open Graph image ID | 456 |
| `custom_og_type` | string | Open Graph type | "article" |
| `custom_twitter_title` | string | Twitter card title | "🚀 WordPress Guide" |
| `custom_twitter_description` | string | Twitter card description | "Learn WordPress techniques" |
| `custom_twitter_image_id` | integer | Twitter card image ID | 457 |
| `custom_twitter_card_type` | string | Twitter card type | "summary_large_image" |
| `custom_noindex` | boolean | Exclude from search engines | false |
| `custom_nofollow` | boolean | No follow directive | false |
| `custom_noarchive` | boolean | No archive directive | false |
| `custom_nosnippet` | boolean | No snippet directive | false |
| `custom_noimageindex` | boolean | No image index directive | false |
| `custom_schema_type` | string | Schema markup type | "Article" |
| `custom_schema_article_type` | string | Article schema type | "BlogPosting" |
| `custom_schema_author` | string | Schema author | "John Doe" |
| `custom_schema_publisher` | string | Schema publisher | "Your Website" |
| `custom_redirect_url` | string | Redirect URL | "https://newurl.com" |
| `custom_redirect_type` | string | Redirect type | "301" |
| `custom_seo_categories` | string | Categories to assign | "SEO, WordPress, Marketing" |
| `custom_seo_tags` | string | Tags to assign | "seo tips, wordpress guide" |
| `custom_seo_replace_categories` | boolean | Replace existing categories | false |
| `custom_seo_replace_tags` | boolean | Replace existing tags | false |
| `custom_seo_auto_create_terms` | boolean | Auto-create missing terms | true |

---

### 📚 Error Handling

#### Common HTTP Status Codes
- `200` - Success
- `201` - Created successfully
- `400` - Bad request (invalid data)
- `401` - Unauthorized (check credentials)
- `403` - Forbidden (insufficient permissions)
- `404` - Not found
- `500` - Internal server error

#### Example Error Response
```json
{
  "code": "rest_invalid_param",
  "message": "Invalid parameter(s): meta",
  "data": {
    "status": 400,
    "params": {
      "meta": "Invalid meta field: custom_invalid_field"
    }
  }
}
```

---

## 🎨 Customization

### Adding Custom Schema Types

```php
// Add to your theme's functions.php
add_filter( 'custom_seo_schema_types', function( $types ) {
    $types['LocalBusiness'] = 'Local Business';
    $types['Recipe'] = 'Recipe';
    return $types;
});
```

### Custom Breadcrumb Structure

```php
// Modify breadcrumb generation
add_filter( 'custom_seo_breadcrumbs', function( $breadcrumbs, $args ) {
    // Add custom breadcrumb logic here
    return $breadcrumbs;
}, 10, 2 );
```

### Extend Meta Fields

```php
// Add custom meta fields
add_action( 'init', function() {
    register_post_meta( 'post', 'my_custom_seo_field', [
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string'
    ]);
});
```

---

## 🌐 Translation

The plugin is translation-ready! 

### Available Languages
- 🇺🇸 English (default)
- 🇪🇸 Spanish *(coming soon)*
- 🇫🇷 French *(coming soon)*
- 🇩🇪 German *(coming soon)*

### Contribute Translations
1. Download [POEdit](https://poedit.net/)
2. Open `/languages/custom-seo.pot`
3. Create your translation
4. Save as `custom-seo-{locale}.po` and `custom-seo-{locale}.mo`
5. Submit a pull request to [jecrs687/wordpress-custom-seo-plugin](https://github.com/jecrs687/wordpress-custom-seo-plugin)!

---

## 📝 Changelog

### 🔥 Version 1.0.3 - *Current*

- 🚨 **CRITICAL FIX:** Replaced deprecated `wp_insert_category()` with `wp_insert_term()` to resolve 500 errors
- 🔧 **IMPROVED:** Updated category existence checking using `get_term_by()` instead of deprecated functions
- 🐛 **FIXED:** WordPress compatibility issues that prevented category/tag auto-creation
- 🔧 **IMPROVED:** Enhanced error handling and debug logging for category processing
- ✅ **TESTED:** Full compatibility with modern WordPress versions

### 🎉 Version 1.0.0
- ✨ **NEW:** Complete plugin rewrite with modular architecture
- ✨ **NEW:** Categories & Tags auto-creation and management system
- ✨ **NEW:** Advanced breadcrumb system with JSON-LD schema
- ✨ **NEW:** XML sitemap generation with image support
- ✨ **NEW:** Google Analytics 4 integration
- ✨ **NEW:** Enhanced admin interface with tabbed meta boxes
- ✨ **NEW:** Custom schema markup support
- ✨ **NEW:** Translation-ready with text domain
- ✨ **NEW:** REST API endpoint for category/tag processing
- 🔧 **IMPROVED:** Better REST API integration with bulk operations
- 🔧 **IMPROVED:** Performance optimizations
- 🔧 **IMPROVED:** Code organization and maintainability

### 📦 Version 0.1 - *Legacy*
- 🎯 Basic SEO meta fields
- 📱 Simple Open Graph support
- 🔌 REST API integration

---

## 🤝 Contributing

We welcome contributions! Here's how you can help:

1. **🐛 Report Bugs** - Open an issue with details
2. **💡 Suggest Features** - We love new ideas!
3. **🔧 Submit Pull Requests** - Fix bugs or add features
4. **🌐 Translate** - Help make the plugin multilingual
5. **📖 Improve Documentation** - Help others understand the plugin

---

## 📞 Support

- ** Issues:** [GitHub Issues](https://github.com/jecrs687/wordpress-custom-seo-plugin/issues)
- **📚 Documentation:** [Plugin Documentation](https://github.com/jecrs687/wordpress-custom-seo-plugin/wiki)
- **💬 Community:** [WordPress.org Support](https://wordpress.org/support/plugin/custom-seo/)
- **🌟 GitHub:** [jecrs687/wordpress-custom-seo-plugin](https://github.com/jecrs687/wordpress-custom-seo-plugin)

---

## 📄 License

This plugin is licensed under the [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

```
Custom SEO Plugin - Comprehensive WordPress SEO Solution
Copyright (C) 2025 jecrs687

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
```

---

<div align="center">

**Made with ❤️ for the WordPress community**

[⭐ Star this project](https://github.com/jecrs687/wordpress-custom-seo-plugin) • [🐛 Report issues](https://github.com/jecrs687/wordpress-custom-seo-plugin/issues) • [💡 Request features](https://github.com/jecrs687/wordpress-custom-seo-plugin/issues/new)

</div>
