<?php
/**
 * Plugin Name: Custom SEO
 * Description: A comprehensive SEO plugin with REST API fields, sitemaps, breadcrumbs, and advanced social sharing.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: custom-seo
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

// Define plugin constants
define( 'CUSTOM_SEO_VERSION', '1.0.0' );
define( 'CUSTOM_SEO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CUSTOM_SEO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CUSTOM_SEO_PLUGIN_FILE', __FILE__ );

/**
 * Main Custom SEO Class
 */
class Custom_SEO {

    /**
     * Initialize the plugin
     */
    public static function init() {
        // Load text domain
        add_action( 'plugins_loaded', [ __CLASS__, 'load_textdomain' ] );
        
        // Register meta fields
        add_action( 'init', [ __CLASS__, 'register_meta_fields' ] );
        
        // Load includes
        self::includes();
        
        // Activation/Deactivation hooks
        register_activation_hook( CUSTOM_SEO_PLUGIN_FILE, [ __CLASS__, 'activate' ] );
        register_deactivation_hook( CUSTOM_SEO_PLUGIN_FILE, [ __CLASS__, 'deactivate' ] );
    }
    
    /**
     * Load plugin text domain
     */
    public static function load_textdomain() {
        load_plugin_textdomain( 'custom-seo', false, dirname( plugin_basename( CUSTOM_SEO_PLUGIN_FILE ) ) . '/languages' );
    }
    
    /**
     * Include required files
     */
    private static function includes() {
        // Core includes
        require_once CUSTOM_SEO_PLUGIN_DIR . 'includes/functions.php';
        require_once CUSTOM_SEO_PLUGIN_DIR . 'includes/meta-output.php';
        require_once CUSTOM_SEO_PLUGIN_DIR . 'includes/sitemap.php';
        require_once CUSTOM_SEO_PLUGIN_DIR . 'includes/breadcrumbs.php';
        require_once CUSTOM_SEO_PLUGIN_DIR . 'includes/schema.php';
        require_once CUSTOM_SEO_PLUGIN_DIR . 'includes/analytics.php';
        
        // Admin includes
        if ( is_admin() ) {
            require_once CUSTOM_SEO_PLUGIN_DIR . 'admin/meta-boxes.php';
            require_once CUSTOM_SEO_PLUGIN_DIR . 'admin/settings-page.php';
        }
    }
    
    /**
     * Register meta fields for REST API
     */
    public static function register_meta_fields() {
        $fields = [
            // Basic SEO
            'custom_seo_title'        => 'string',
            'custom_seo_description'  => 'string',
            'custom_focus_keyword'    => 'string',
            'custom_canonical_url'    => 'string',
            'custom_robots'           => 'string',
            
            // Open Graph
            'custom_og_title'         => 'string',
            'custom_og_description'   => 'string',
            'custom_og_image_id'      => 'integer',
            'custom_og_type'          => 'string',
            'custom_og_locale'        => 'string',
            
            // Twitter
            'custom_twitter_title'    => 'string',
            'custom_twitter_description' => 'string',
            'custom_twitter_image_id' => 'integer',
            'custom_twitter_card'     => 'string',
            'custom_twitter_site'     => 'string',
            'custom_twitter_creator'  => 'string',
            
            // Schema.org
            'custom_schema_type'      => 'string',
            'custom_schema_data'      => 'string',
            
            // Additional SEO
            'custom_meta_keywords'    => 'string',
            'custom_redirect_url'     => 'string',
            'custom_redirect_type'    => 'integer',
            'custom_noindex'          => 'boolean',
            'custom_nofollow'         => 'boolean',
            'custom_noarchive'        => 'boolean',
            'custom_nosnippet'        => 'boolean',
            'custom_noimageindex'     => 'boolean',
        ];

        $post_types = get_post_types( [ 'public' => true ] );
        
        foreach ( $post_types as $post_type ) {
            foreach ( $fields as $field => $type ) {
                register_post_meta( $post_type, $field, [
                    'show_in_rest'  => true,
                    'single'        => true,
                    'type'          => $type,
                    'auth_callback' => function() {
                        return current_user_can( 'edit_posts' );
                    }
                ] );
            }
        }
    }
    
    /**
     * Plugin activation
     */
    public static function activate() {
        // Initialize sitemap rewrite rules
        Custom_SEO_Sitemap::init_rewrite_rules();
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }
}

// Initialize the plugin
Custom_SEO::init();
    global $post;
    
    echo "\n<!-- Custom SEO Plugin -->\n";
    
    // Viewport meta tag
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
    
    if ( is_singular() && $post ) {
        custom_seo_output_singular_meta();
    } else {
        custom_seo_output_archive_meta();
    }
    
    // Global site verification and analytics
    custom_seo_output_global_meta();
    
    echo "<!-- /Custom SEO Plugin -->\n";
}

function custom_seo_output_singular_meta() {
    global $post;
    
    // Check for redirect first
    $redirect_url = get_post_meta( $post->ID, 'custom_redirect_url', true );
    $redirect_type = get_post_meta( $post->ID, 'custom_redirect_type', true ) ?: 301;
    
    if ( $redirect_url ) {
        wp_redirect( $redirect_url, $redirect_type );
        exit;
    }
    
    // Basic SEO
    $title = get_post_meta( $post->ID, 'custom_seo_title', true ) ?: get_the_title( $post );
    $description = get_post_meta( $post->ID, 'custom_seo_description', true ) ?: wp_trim_words( get_the_excerpt( $post ), 25 );
    $keywords = get_post_meta( $post->ID, 'custom_meta_keywords', true );
    $canonical = get_post_meta( $post->ID, 'custom_canonical_url', true ) ?: get_permalink( $post );
    
    // Robots directives
    $robots = [];
    if ( get_post_meta( $post->ID, 'custom_noindex', true ) ) $robots[] = 'noindex';
    if ( get_post_meta( $post->ID, 'custom_nofollow', true ) ) $robots[] = 'nofollow';
    if ( get_post_meta( $post->ID, 'custom_noarchive', true ) ) $robots[] = 'noarchive';
    if ( get_post_meta( $post->ID, 'custom_nosnippet', true ) ) $robots[] = 'nosnippet';
    if ( get_post_meta( $post->ID, 'custom_noimageindex', true ) ) $robots[] = 'noimageindex';
    
    $robots_content = get_post_meta( $post->ID, 'custom_robots', true );
    if ( $robots_content ) {
        $robots[] = $robots_content;
    }
    
    if ( empty( $robots ) ) {
        $robots[] = 'index,follow';
    }
    
    // Output basic meta tags
    echo '<title>' . esc_html( $title ) . "</title>\n";
    echo '<meta name="description" content="' . esc_attr( $description ) . "\">\n";
    if ( $keywords ) {
        echo '<meta name="keywords" content="' . esc_attr( $keywords ) . "\">\n";
    }
    echo '<meta name="robots" content="' . esc_attr( implode( ',', $robots ) ) . "\">\n";
    echo '<link rel="canonical" href="' . esc_url( $canonical ) . "\">\n";
    
    // Open Graph
    $og_title = get_post_meta( $post->ID, 'custom_og_title', true ) ?: $title;
    $og_description = get_post_meta( $post->ID, 'custom_og_description', true ) ?: $description;
    $og_type = get_post_meta( $post->ID, 'custom_og_type', true ) ?: 'article';
    $og_locale = get_post_meta( $post->ID, 'custom_og_locale', true ) ?: get_locale();
    $og_image_id = get_post_meta( $post->ID, 'custom_og_image_id', true );
    $og_image_url = $og_image_id ? wp_get_attachment_url( $og_image_id ) : '';
    
    echo '<meta property="og:title" content="' . esc_attr( $og_title ) . "\">\n";
    echo '<meta property="og:description" content="' . esc_attr( $og_description ) . "\">\n";
    echo '<meta property="og:type" content="' . esc_attr( $og_type ) . "\">\n";
    echo '<meta property="og:url" content="' . esc_url( get_permalink( $post ) ) . "\">\n";
    echo '<meta property="og:locale" content="' . esc_attr( $og_locale ) . "\">\n";
    echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . "\">\n";
    
    // Use default OG image if no custom image is set
    if ( ! $og_image_url ) {
        $og_image_url = custom_seo_get_default_og_image();
    }
    
    if ( $og_image_url ) {
        echo '<meta property="og:image" content="' . esc_url( $og_image_url ) . "\">\n";
        if ( $og_image_id ) {
            $image_data = wp_get_attachment_metadata( $og_image_id );
            if ( $image_data ) {
                echo '<meta property="og:image:width" content="' . esc_attr( $image_data['width'] ) . "\">\n";
                echo '<meta property="og:image:height" content="' . esc_attr( $image_data['height'] ) . "\">\n";
                echo '<meta property="og:image:alt" content="' . esc_attr( get_post_meta( $og_image_id, '_wp_attachment_image_alt', true ) ) . "\">\n";
            }
        }
    }
    
    // Article specific Open Graph
    if ( $og_type === 'article' ) {
        echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c', $post ) ) . "\">\n";
        echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c', $post ) ) . "\">\n";
        echo '<meta property="article:author" content="' . esc_attr( get_the_author_meta( 'display_name', $post->post_author ) ) . "\">\n";
        
        $categories = get_the_category( $post->ID );
        foreach ( $categories as $category ) {
            echo '<meta property="article:section" content="' . esc_attr( $category->name ) . "\">\n";
        }
        
        $tags = get_the_tags( $post->ID );
        if ( $tags ) {
            foreach ( $tags as $tag ) {
                echo '<meta property="article:tag" content="' . esc_attr( $tag->name ) . "\">\n";
            }
        }
    }
    
    // Twitter Card
    $tw_card = get_post_meta( $post->ID, 'custom_twitter_card', true ) ?: 'summary_large_image';
    $tw_title = get_post_meta( $post->ID, 'custom_twitter_title', true ) ?: $title;
    $tw_description = get_post_meta( $post->ID, 'custom_twitter_description', true ) ?: $description;
    $tw_image_id = get_post_meta( $post->ID, 'custom_twitter_image_id', true );
    $tw_image_url = $tw_image_id ? wp_get_attachment_url( $tw_image_id ) : $og_image_url;
    $tw_site = get_post_meta( $post->ID, 'custom_twitter_site', true );
    $tw_creator = get_post_meta( $post->ID, 'custom_twitter_creator', true );
    
    echo '<meta name="twitter:card" content="' . esc_attr( $tw_card ) . "\">\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $tw_title ) . "\">\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $tw_description ) . "\">\n";
    
    if ( $tw_image_url ) {
        echo '<meta name="twitter:image" content="' . esc_url( $tw_image_url ) . "\">\n";
    }
    if ( $tw_site ) {
        echo '<meta name="twitter:site" content="' . esc_attr( $tw_site ) . "\">\n";
    }
    if ( $tw_creator ) {
        echo '<meta name="twitter:creator" content="' . esc_attr( $tw_creator ) . "\">\n";
    }
    
    // JSON-LD Schema
    custom_seo_output_schema( $post );
}

function custom_seo_output_archive_meta() {
    $title = wp_get_document_title();
    $description = get_bloginfo( 'description' );
    
    if ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();
        if ( $term && ! empty( $term->description ) ) {
            $description = wp_trim_words( $term->description, 25 );
        }
    } elseif ( is_author() ) {
        $author = get_queried_object();
        if ( $author && ! empty( $author->description ) ) {
            $description = wp_trim_words( $author->description, 25 );
        }
    }
    
    echo '<title>' . esc_html( $title ) . "</title>\n";
    echo '<meta name="description" content="' . esc_attr( $description ) . "\">\n";
    echo '<meta name="robots" content="index,follow">' . "\n";
    
    // Basic Open Graph for archives
    echo '<meta property="og:title" content="' . esc_attr( $title ) . "\">\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . "\">\n";
    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( get_pagenum_link() ) . "\">\n";
    echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . "\">\n";
}

function custom_seo_output_schema( $post ) {
    $schema_type = get_post_meta( $post->ID, 'custom_schema_type', true );
    $custom_schema = get_post_meta( $post->ID, 'custom_schema_data', true );
    
    if ( $custom_schema ) {
        echo '<script type="application/ld+json">' . "\n";
        echo $custom_schema . "\n";
        echo '</script>' . "\n";
        return;
    }
    
    // Default Article schema
    if ( ! $schema_type || $schema_type === 'Article' ) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title( $post ),
            'description' => wp_trim_words( get_the_excerpt( $post ), 25 ),
            'author' => [
                '@type' => 'Person',
                'name' => get_the_author_meta( 'display_name', $post->post_author ),
                'url' => get_author_posts_url( $post->post_author )
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => get_bloginfo( 'name' ),
                'url' => home_url()
            ],
            'datePublished' => get_the_date( 'c', $post ),
            'dateModified' => get_the_modified_date( 'c', $post ),
            'mainEntityOfPage' => get_permalink( $post ),
            'url' => get_permalink( $post )
        ];
        
        $og_image_id = get_post_meta( $post->ID, 'custom_og_image_id', true );
        if ( $og_image_id ) {
            $image_url = wp_get_attachment_url( $og_image_id );
            if ( $image_url ) {
                $schema['image'] = $image_url;
            }
        }
        
        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . "\n";
        echo '</script>' . "\n";
    }
}

function custom_seo_output_global_meta() {
    // Add global verification codes and analytics here
    $google_verification = get_option( 'custom_seo_google_verification' );
    $bing_verification = get_option( 'custom_seo_bing_verification' );
    $pinterest_verification = get_option( 'custom_seo_pinterest_verification' );
    
    if ( $google_verification ) {
        echo '<meta name="google-site-verification" content="' . esc_attr( $google_verification ) . "\">\n";
    }
    if ( $bing_verification ) {
        echo '<meta name="msvalidate.01" content="' . esc_attr( $bing_verification ) . "\">\n";
    }
    if ( $pinterest_verification ) {
        echo '<meta name="p:domain_verify" content="' . esc_attr( $pinterest_verification ) . "\">\n";
    }
}
add_action( 'wp_head', 'custom_seo_output_meta', 5 );

// Admin Interface
function custom_seo_add_meta_boxes() {
    $post_types = get_post_types( ['public' => true] );
    foreach ( $post_types as $post_type ) {
        add_meta_box(
            'custom-seo-meta',
            'SEO Settings',
            'custom_seo_meta_box_callback',
            $post_type,
            'normal',
            'high'
        );
    }
}
add_action( 'add_meta_boxes', 'custom_seo_add_meta_boxes' );

function custom_seo_meta_box_callback( $post ) {
    wp_nonce_field( 'custom_seo_save_meta', 'custom_seo_nonce' );
    
    // Get current values
    $values = [];
    $fields = [
        'custom_seo_title', 'custom_seo_description', 'custom_focus_keyword', 'custom_canonical_url',
        'custom_robots', 'custom_og_title', 'custom_og_description', 'custom_og_image_id', 'custom_og_type',
        'custom_og_locale', 'custom_twitter_title', 'custom_twitter_description', 'custom_twitter_image_id',
        'custom_twitter_card', 'custom_twitter_site', 'custom_twitter_creator', 'custom_schema_type',
        'custom_schema_data', 'custom_meta_keywords', 'custom_redirect_url', 'custom_redirect_type',
        'custom_noindex', 'custom_nofollow', 'custom_noarchive', 'custom_nosnippet', 'custom_noimageindex'
    ];
    
    foreach ( $fields as $field ) {
        $values[$field] = get_post_meta( $post->ID, $field, true );
    }
    
    ?>
    <div id="custom-seo-tabs">
        <ul class="custom-seo-tab-nav">
            <li><a href="#seo-general">General SEO</a></li>
            <li><a href="#seo-social">Social Media</a></li>
            <li><a href="#seo-advanced">Advanced</a></li>
            <li><a href="#seo-schema">Schema</a></li>
        </ul>
        
        <div id="seo-general" class="custom-seo-tab-content">
            <table class="form-table">
                <tr>
                    <th><label for="custom_seo_title">SEO Title</label></th>
                    <td>
                        <input type="text" id="custom_seo_title" name="custom_seo_title" value="<?php echo esc_attr( $values['custom_seo_title'] ); ?>" class="large-text" />
                        <p class="description">Recommended length: 50-60 characters. Leave blank to use post title.</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_seo_description">Meta Description</label></th>
                    <td>
                        <textarea id="custom_seo_description" name="custom_seo_description" rows="3" class="large-text"><?php echo esc_textarea( $values['custom_seo_description'] ); ?></textarea>
                        <p class="description">Recommended length: 150-160 characters. Leave blank to use excerpt.</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_focus_keyword">Focus Keyword</label></th>
                    <td>
                        <input type="text" id="custom_focus_keyword" name="custom_focus_keyword" value="<?php echo esc_attr( $values['custom_focus_keyword'] ); ?>" class="large-text" />
                        <p class="description">Main keyword you want this page to rank for.</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_canonical_url">Canonical URL</label></th>
                    <td>
                        <input type="url" id="custom_canonical_url" name="custom_canonical_url" value="<?php echo esc_attr( $values['custom_canonical_url'] ); ?>" class="large-text" />
                        <p class="description">Leave blank to use default permalink.</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_meta_keywords">Meta Keywords</label></th>
                    <td>
                        <input type="text" id="custom_meta_keywords" name="custom_meta_keywords" value="<?php echo esc_attr( $values['custom_meta_keywords'] ); ?>" class="large-text" />
                        <p class="description">Comma-separated keywords (mostly ignored by search engines).</p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="seo-social" class="custom-seo-tab-content">
            <h3>Open Graph (Facebook)</h3>
            <table class="form-table">
                <tr>
                    <th><label for="custom_og_title">OG Title</label></th>
                    <td>
                        <input type="text" id="custom_og_title" name="custom_og_title" value="<?php echo esc_attr( $values['custom_og_title'] ); ?>" class="large-text" />
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_og_description">OG Description</label></th>
                    <td>
                        <textarea id="custom_og_description" name="custom_og_description" rows="3" class="large-text"><?php echo esc_textarea( $values['custom_og_description'] ); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_og_image_id">OG Image</label></th>
                    <td>
                        <input type="hidden" id="custom_og_image_id" name="custom_og_image_id" value="<?php echo esc_attr( $values['custom_og_image_id'] ); ?>" />
                        <button type="button" class="button" id="custom_og_image_button">Select Image</button>
                        <button type="button" class="button" id="custom_og_image_remove">Remove</button>
                        <div id="custom_og_image_preview">
                            <?php if ( $values['custom_og_image_id'] ): ?>
                                <?php echo wp_get_attachment_image( $values['custom_og_image_id'], 'medium' ); ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_og_type">OG Type</label></th>
                    <td>
                        <select id="custom_og_type" name="custom_og_type">
                            <option value="article" <?php selected( $values['custom_og_type'], 'article' ); ?>>Article</option>
                            <option value="website" <?php selected( $values['custom_og_type'], 'website' ); ?>>Website</option>
                            <option value="product" <?php selected( $values['custom_og_type'], 'product' ); ?>>Product</option>
                        </select>
                    </td>
                </tr>
            </table>
            
            <h3>Twitter Card</h3>
            <table class="form-table">
                <tr>
                    <th><label for="custom_twitter_card">Card Type</label></th>
                    <td>
                        <select id="custom_twitter_card" name="custom_twitter_card">
                            <option value="summary" <?php selected( $values['custom_twitter_card'], 'summary' ); ?>>Summary</option>
                            <option value="summary_large_image" <?php selected( $values['custom_twitter_card'], 'summary_large_image' ); ?>>Summary Large Image</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_twitter_title">Twitter Title</label></th>
                    <td>
                        <input type="text" id="custom_twitter_title" name="custom_twitter_title" value="<?php echo esc_attr( $values['custom_twitter_title'] ); ?>" class="large-text" />
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_twitter_description">Twitter Description</label></th>
                    <td>
                        <textarea id="custom_twitter_description" name="custom_twitter_description" rows="3" class="large-text"><?php echo esc_textarea( $values['custom_twitter_description'] ); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_twitter_site">Twitter Site</label></th>
                    <td>
                        <input type="text" id="custom_twitter_site" name="custom_twitter_site" value="<?php echo esc_attr( $values['custom_twitter_site'] ); ?>" placeholder="@yoursite" />
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_twitter_creator">Twitter Creator</label></th>
                    <td>
                        <input type="text" id="custom_twitter_creator" name="custom_twitter_creator" value="<?php echo esc_attr( $values['custom_twitter_creator'] ); ?>" placeholder="@yourcreator" />
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="seo-advanced" class="custom-seo-tab-content">
            <table class="form-table">
                <tr>
                    <th>Robots Meta</th>
                    <td>
                        <label><input type="checkbox" name="custom_noindex" value="1" <?php checked( $values['custom_noindex'], 1 ); ?> /> No Index</label><br>
                        <label><input type="checkbox" name="custom_nofollow" value="1" <?php checked( $values['custom_nofollow'], 1 ); ?> /> No Follow</label><br>
                        <label><input type="checkbox" name="custom_noarchive" value="1" <?php checked( $values['custom_noarchive'], 1 ); ?> /> No Archive</label><br>
                        <label><input type="checkbox" name="custom_nosnippet" value="1" <?php checked( $values['custom_nosnippet'], 1 ); ?> /> No Snippet</label><br>
                        <label><input type="checkbox" name="custom_noimageindex" value="1" <?php checked( $values['custom_noimageindex'], 1 ); ?> /> No Image Index</label>
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_robots">Custom Robots</label></th>
                    <td>
                        <input type="text" id="custom_robots" name="custom_robots" value="<?php echo esc_attr( $values['custom_robots'] ); ?>" class="large-text" />
                        <p class="description">Additional robots directives (e.g., max-snippet:160, max-image-preview:large)</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_redirect_url">301 Redirect</label></th>
                    <td>
                        <input type="url" id="custom_redirect_url" name="custom_redirect_url" value="<?php echo esc_attr( $values['custom_redirect_url'] ); ?>" class="large-text" />
                        <select name="custom_redirect_type">
                            <option value="301" <?php selected( $values['custom_redirect_type'], 301 ); ?>>301 Permanent</option>
                            <option value="302" <?php selected( $values['custom_redirect_type'], 302 ); ?>>302 Temporary</option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        
        <div id="seo-schema" class="custom-seo-tab-content">
            <table class="form-table">
                <tr>
                    <th><label for="custom_schema_type">Schema Type</label></th>
                    <td>
                        <select id="custom_schema_type" name="custom_schema_type">
                            <option value="Article" <?php selected( $values['custom_schema_type'], 'Article' ); ?>>Article</option>
                            <option value="Product" <?php selected( $values['custom_schema_type'], 'Product' ); ?>>Product</option>
                            <option value="Event" <?php selected( $values['custom_schema_type'], 'Event' ); ?>>Event</option>
                            <option value="FAQ" <?php selected( $values['custom_schema_type'], 'FAQ' ); ?>>FAQ</option>
                            <option value="custom" <?php selected( $values['custom_schema_type'], 'custom' ); ?>>Custom JSON-LD</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="custom_schema_data">Custom Schema JSON-LD</label></th>
                    <td>
                        <textarea id="custom_schema_data" name="custom_schema_data" rows="10" class="large-text code"><?php echo esc_textarea( $values['custom_schema_data'] ); ?></textarea>
                        <p class="description">Enter valid JSON-LD markup. Only used if Schema Type is set to "Custom JSON-LD".</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <style>
        .custom-seo-tab-nav { list-style: none; margin: 0; padding: 0; border-bottom: 1px solid #ccc; }
        .custom-seo-tab-nav li { display: inline-block; margin: 0; }
        .custom-seo-tab-nav a { display: block; padding: 10px 15px; text-decoration: none; background: #f1f1f1; border: 1px solid #ccc; border-bottom: none; margin-right: 5px; }
        .custom-seo-tab-nav a:hover, .custom-seo-tab-nav a.active { background: #fff; }
        .custom-seo-tab-content { display: none; padding: 20px 0; }
        .custom-seo-tab-content.active { display: block; }
        #custom_og_image_preview img { max-width: 200px; height: auto; margin-top: 10px; }
    </style>
    
    <script>
        jQuery(document).ready(function($) {
            // Tab functionality
            $('.custom-seo-tab-nav a').on('click', function(e) {
                e.preventDefault();
                $('.custom-seo-tab-nav a').removeClass('active');
                $('.custom-seo-tab-content').removeClass('active');
                $(this).addClass('active');
                $($(this).attr('href')).addClass('active');
            });
            $('.custom-seo-tab-nav a:first').click();
            
            // Media uploader
            var mediaUploader;
            $('#custom_og_image_button').on('click', function(e) {
                e.preventDefault();
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media({
                    title: 'Select Image',
                    button: { text: 'Use Image' },
                    multiple: false
                });
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#custom_og_image_id').val(attachment.id);
                    $('#custom_og_image_preview').html('<img src="' + attachment.url + '" alt="" style="max-width: 200px; height: auto;" />');
                });
                mediaUploader.open();
            });
            
            $('#custom_og_image_remove').on('click', function(e) {
                e.preventDefault();
                $('#custom_og_image_id').val('');
                $('#custom_og_image_preview').html('');
            });
        });
    </script>
    <?php
}

function custom_seo_save_meta( $post_id ) {
    if ( ! isset( $_POST['custom_seo_nonce'] ) || ! wp_verify_nonce( $_POST['custom_seo_nonce'], 'custom_seo_save_meta' ) ) {
        return;
    }
    
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    $fields = [
        'custom_seo_title', 'custom_seo_description', 'custom_focus_keyword', 'custom_canonical_url',
        'custom_robots', 'custom_og_title', 'custom_og_description', 'custom_og_image_id', 'custom_og_type',
        'custom_og_locale', 'custom_twitter_title', 'custom_twitter_description', 'custom_twitter_image_id',
        'custom_twitter_card', 'custom_twitter_site', 'custom_twitter_creator', 'custom_schema_type',
        'custom_schema_data', 'custom_meta_keywords', 'custom_redirect_url', 'custom_redirect_type',
        'custom_noindex', 'custom_nofollow', 'custom_noarchive', 'custom_nosnippet', 'custom_noimageindex'
    ];
    
    foreach ( $fields as $field ) {
        if ( isset( $_POST[$field] ) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
        } else {
            delete_post_meta( $post_id, $field );
        }
    }
}
add_action( 'save_post', 'custom_seo_save_meta' );

function custom_seo_enqueue_admin_scripts( $hook ) {
    if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
        wp_enqueue_media();
    }
}
add_action( 'admin_enqueue_scripts', 'custom_seo_enqueue_admin_scripts' );

// XML Sitemap Functionality
function custom_seo_init_sitemap() {
    add_rewrite_rule( '^sitemap\.xml$', 'index.php?custom_sitemap=main', 'top' );
    add_rewrite_rule( '^sitemap-([^/]+)\.xml$', 'index.php?custom_sitemap=$matches[1]', 'top' );
}
add_action( 'init', 'custom_seo_init_sitemap' );

function custom_seo_add_query_vars( $vars ) {
    $vars[] = 'custom_sitemap';
    return $vars;
}
add_filter( 'query_vars', 'custom_seo_add_query_vars' );

function custom_seo_template_redirect() {
    $sitemap = get_query_var( 'custom_sitemap' );
    
    if ( ! $sitemap ) {
        return;
    }
    
    header( 'Content-Type: application/xml; charset=utf-8' );
    
    if ( $sitemap === 'main' ) {
        custom_seo_generate_sitemap_index();
    } else {
        custom_seo_generate_sitemap( $sitemap );
    }
    
    exit;
}
add_action( 'template_redirect', 'custom_seo_template_redirect' );

function custom_seo_generate_sitemap_index() {
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // Post types sitemap
    $post_types = get_post_types( ['public' => true, 'exclude_from_search' => false], 'objects' );
    foreach ( $post_types as $post_type ) {
        if ( $post_type->name === 'attachment' ) continue;
        
        $count = wp_count_posts( $post_type->name );
        if ( $count->publish > 0 ) {
            echo '<sitemap>' . "\n";
            echo '<loc>' . home_url( '/sitemap-' . $post_type->name . '.xml' ) . '</loc>' . "\n";
            echo '<lastmod>' . mysql2date( 'Y-m-d\TH:i:s+00:00', get_lastpostmodified( 'GMT', $post_type->name ) ) . '</lastmod>' . "\n";
            echo '</sitemap>' . "\n";
        }
    }
    
    // Taxonomies sitemap
    $taxonomies = get_taxonomies( ['public' => true], 'objects' );
    foreach ( $taxonomies as $taxonomy ) {
        $terms = get_terms( ['taxonomy' => $taxonomy->name, 'hide_empty' => true] );
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            echo '<sitemap>' . "\n";
            echo '<loc>' . home_url( '/sitemap-' . $taxonomy->name . '.xml' ) . '</loc>' . "\n";
            echo '<lastmod>' . date( 'Y-m-d\TH:i:s+00:00' ) . '</lastmod>' . "\n";
            echo '</sitemap>' . "\n";
        }
    }
    
    echo '</sitemapindex>';
}

function custom_seo_generate_sitemap( $type ) {
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
    
    // Check if it's a post type
    if ( post_type_exists( $type ) ) {
        custom_seo_generate_post_type_sitemap( $type );
    } elseif ( taxonomy_exists( $type ) ) {
        custom_seo_generate_taxonomy_sitemap( $type );
    }
    
    echo '</urlset>';
}

function custom_seo_generate_post_type_sitemap( $post_type ) {
    $posts = get_posts( [
        'post_type' => $post_type,
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => 'modified',
        'order' => 'DESC'
    ] );
    
    foreach ( $posts as $post ) {
        // Skip if noindex is set
        if ( get_post_meta( $post->ID, 'custom_noindex', true ) ) {
            continue;
        }
        
        $permalink = get_permalink( $post );
        $modified = mysql2date( 'Y-m-d\TH:i:s+00:00', $post->post_modified_gmt );
        
        echo '<url>' . "\n";
        echo '<loc>' . esc_url( $permalink ) . '</loc>' . "\n";
        echo '<lastmod>' . $modified . '</lastmod>' . "\n";
        echo '<changefreq>weekly</changefreq>' . "\n";
        echo '<priority>' . custom_seo_get_priority( $post ) . '</priority>' . "\n";
        
        // Add image if available
        $image_id = get_post_meta( $post->ID, 'custom_og_image_id', true );
        if ( ! $image_id && has_post_thumbnail( $post->ID ) ) {
            $image_id = get_post_thumbnail_id( $post->ID );
        }
        
        if ( $image_id ) {
            $image_url = wp_get_attachment_url( $image_id );
            if ( $image_url ) {
                echo '<image:image>' . "\n";
                echo '<image:loc>' . esc_url( $image_url ) . '</image:loc>' . "\n";
                echo '<image:title>' . esc_html( get_the_title( $post ) ) . '</image:title>' . "\n";
                echo '</image:image>' . "\n";
            }
        }
        
        echo '</url>' . "\n";
    }
}

function custom_seo_generate_taxonomy_sitemap( $taxonomy ) {
    $terms = get_terms( [
        'taxonomy' => $taxonomy,
        'hide_empty' => true,
        'orderby' => 'count',
        'order' => 'DESC'
    ] );
    
    foreach ( $terms as $term ) {
        $term_link = get_term_link( $term );
        if ( is_wp_error( $term_link ) ) {
            continue;
        }
        
        echo '<url>' . "\n";
        echo '<loc>' . esc_url( $term_link ) . '</loc>' . "\n";
        echo '<lastmod>' . date( 'Y-m-d\TH:i:s+00:00' ) . '</lastmod>' . "\n";
        echo '<changefreq>weekly</changefreq>' . "\n";
        echo '<priority>0.6</priority>' . "\n";
        echo '</url>' . "\n";
    }
}

function custom_seo_get_priority( $post ) {
    if ( is_front_page() && $post->ID == get_option( 'page_on_front' ) ) {
        return '1.0';
    }
    
    if ( $post->post_type === 'page' ) {
        return '0.8';
    }
    
    // Calculate priority based on comments and age
    $comments = wp_count_comments( $post->ID );
    $age_days = ( time() - strtotime( $post->post_date ) ) / ( 60 * 60 * 24 );
    
    $priority = 0.5;
    
    if ( $comments->approved > 10 ) {
        $priority += 0.1;
    }
    
    if ( $age_days < 30 ) {
        $priority += 0.2;
    } elseif ( $age_days < 90 ) {
        $priority += 0.1;
    }
    
    return number_format( min( $priority, 1.0 ), 1 );
}

// Flush rewrite rules on activation
function custom_seo_activate() {
    custom_seo_init_sitemap();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'custom_seo_activate' );

function custom_seo_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'custom_seo_deactivate' );

// Breadcrumb Functionality
function custom_seo_breadcrumbs( $args = [] ) {
    $defaults = [
        'separator' => ' > ',
        'home_text' => 'Home',
        'show_current' => true,
        'show_home' => true,
        'structured_data' => true
    ];
    
    $args = wp_parse_args( $args, $defaults );
    
    if ( is_front_page() ) {
        return;
    }
    
    $breadcrumbs = [];
    
    // Home
    if ( $args['show_home'] ) {
        $breadcrumbs[] = [
            'text' => $args['home_text'],
            'url' => home_url( '/' )
        ];
    }
    
    if ( is_category() ) {
        $category = get_queried_object();
        if ( $category->parent ) {
            $parent_cats = [];
            $parent = $category->parent;
            while ( $parent ) {
                $parent_cat = get_category( $parent );
                $parent_cats[] = [
                    'text' => $parent_cat->name,
                    'url' => get_category_link( $parent_cat->term_id )
                ];
                $parent = $parent_cat->parent;
            }
            $breadcrumbs = array_merge( $breadcrumbs, array_reverse( $parent_cats ) );
        }
        if ( $args['show_current'] ) {
            $breadcrumbs[] = [
                'text' => $category->name,
                'url' => ''
            ];
        }
    } elseif ( is_tag() ) {
        $tag = get_queried_object();
        if ( $args['show_current'] ) {
            $breadcrumbs[] = [
                'text' => $tag->name,
                'url' => ''
            ];
        }
    } elseif ( is_tax() ) {
        $term = get_queried_object();
        $taxonomy = get_taxonomy( $term->taxonomy );
        
        if ( $term->parent ) {
            $parent_terms = [];
            $parent = $term->parent;
            while ( $parent ) {
                $parent_term = get_term( $parent, $term->taxonomy );
                $parent_terms[] = [
                    'text' => $parent_term->name,
                    'url' => get_term_link( $parent_term )
                ];
                $parent = $parent_term->parent;
            }
            $breadcrumbs = array_merge( $breadcrumbs, array_reverse( $parent_terms ) );
        }
        
        if ( $args['show_current'] ) {
            $breadcrumbs[] = [
                'text' => $term->name,
                'url' => ''
            ];
        }
    } elseif ( is_single() ) {
        global $post;
        
        // Add post type archive if it exists
        $post_type_object = get_post_type_object( $post->post_type );
        if ( $post_type_object->has_archive ) {
            $breadcrumbs[] = [
                'text' => $post_type_object->labels->name,
                'url' => get_post_type_archive_link( $post->post_type )
            ];
        }
        
        // Add categories for posts
        if ( $post->post_type === 'post' ) {
            $categories = get_the_category( $post->ID );
            if ( $categories ) {
                $main_cat = $categories[0];
                if ( $main_cat->parent ) {
                    $parent_cats = [];
                    $parent = $main_cat->parent;
                    while ( $parent ) {
                        $parent_cat = get_category( $parent );
                        $parent_cats[] = [
                            'text' => $parent_cat->name,
                            'url' => get_category_link( $parent_cat->term_id )
                        ];
                        $parent = $parent_cat->parent;
                    }
                    $breadcrumbs = array_merge( $breadcrumbs, array_reverse( $parent_cats ) );
                }
                $breadcrumbs[] = [
                    'text' => $main_cat->name,
                    'url' => get_category_link( $main_cat->term_id )
                ];
            }
        }
        
        if ( $args['show_current'] ) {
            $breadcrumbs[] = [
                'text' => get_the_title( $post ),
                'url' => ''
            ];
        }
    } elseif ( is_page() ) {
        global $post;
        
        if ( $post->post_parent ) {
            $parent_pages = [];
            $parent_id = $post->post_parent;
            while ( $parent_id ) {
                $parent_page = get_post( $parent_id );
                $parent_pages[] = [
                    'text' => get_the_title( $parent_page ),
                    'url' => get_permalink( $parent_page )
                ];
                $parent_id = $parent_page->post_parent;
            }
            $breadcrumbs = array_merge( $breadcrumbs, array_reverse( $parent_pages ) );
        }
        
        if ( $args['show_current'] ) {
            $breadcrumbs[] = [
                'text' => get_the_title( $post ),
                'url' => ''
            ];
        }
    } elseif ( is_author() ) {
        $author = get_queried_object();
        if ( $args['show_current'] ) {
            $breadcrumbs[] = [
                'text' => 'Author: ' . $author->display_name,
                'url' => ''
            ];
        }
    } elseif ( is_archive() ) {
        if ( $args['show_current'] ) {
            $breadcrumbs[] = [
                'text' => post_type_archive_title( '', false ),
                'url' => ''
            ];
        }
    } elseif ( is_search() ) {
        if ( $args['show_current'] ) {
            $breadcrumbs[] = [
                'text' => 'Search Results for: ' . get_search_query(),
                'url' => ''
            ];
        }
    }
    
    if ( empty( $breadcrumbs ) ) {
        return;
    }
    
    // Output HTML
    echo '<nav class="custom-seo-breadcrumbs" aria-label="Breadcrumb">';
    echo '<ol class="breadcrumb-list">';
    
    foreach ( $breadcrumbs as $index => $breadcrumb ) {
        echo '<li class="breadcrumb-item">';
        if ( ! empty( $breadcrumb['url'] ) ) {
            echo '<a href="' . esc_url( $breadcrumb['url'] ) . '">' . esc_html( $breadcrumb['text'] ) . '</a>';
        } else {
            echo '<span>' . esc_html( $breadcrumb['text'] ) . '</span>';
        }
        echo '</li>';
        
        if ( $index < count( $breadcrumbs ) - 1 ) {
            echo '<li class="breadcrumb-separator">' . esc_html( $args['separator'] ) . '</li>';
        }
    }
    
    echo '</ol>';
    echo '</nav>';
    
    // Output structured data
    if ( $args['structured_data'] ) {
        custom_seo_breadcrumb_schema( $breadcrumbs );
    }
}

function custom_seo_breadcrumb_schema( $breadcrumbs ) {
    $schema_items = [];
    
    foreach ( $breadcrumbs as $index => $breadcrumb ) {
        $schema_items[] = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $breadcrumb['text'],
            'item' => ! empty( $breadcrumb['url'] ) ? $breadcrumb['url'] : null
        ];
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $schema_items
    ];
    
    echo '<script type="application/ld+json">';
    echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
    echo '</script>';
}

// Add breadcrumb CSS
function custom_seo_breadcrumb_styles() {
    echo '<style>
        .custom-seo-breadcrumbs { margin: 1em 0; font-size: 0.9em; }
        .breadcrumb-list { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; align-items: center; }
        .breadcrumb-item { margin: 0; }
        .breadcrumb-separator { margin: 0 0.5em; color: #666; }
        .breadcrumb-item a { color: #0073aa; text-decoration: none; }
        .breadcrumb-item a:hover { text-decoration: underline; }
        .breadcrumb-item span { color: #333; }
    </style>';
}
add_action( 'wp_head', 'custom_seo_breadcrumb_styles' );

// Settings Page
function custom_seo_admin_menu() {
    add_options_page(
        'Custom SEO Settings',
        'Custom SEO',
        'manage_options',
        'custom-seo-settings',
        'custom_seo_settings_page'
    );
}
add_action( 'admin_menu', 'custom_seo_admin_menu' );

function custom_seo_settings_init() {
    register_setting( 'custom_seo_settings', 'custom_seo_google_verification' );
    register_setting( 'custom_seo_settings', 'custom_seo_bing_verification' );
    register_setting( 'custom_seo_settings', 'custom_seo_pinterest_verification' );
    register_setting( 'custom_seo_settings', 'custom_seo_google_analytics' );
    register_setting( 'custom_seo_settings', 'custom_seo_gtag_id' );
    register_setting( 'custom_seo_settings', 'custom_seo_facebook_app_id' );
    register_setting( 'custom_seo_settings', 'custom_seo_default_og_image' );
    register_setting( 'custom_seo_settings', 'custom_seo_twitter_username' );
    register_setting( 'custom_seo_settings', 'custom_seo_organization_name' );
    register_setting( 'custom_seo_settings', 'custom_seo_organization_logo' );
}
add_action( 'admin_init', 'custom_seo_settings_init' );

function custom_seo_settings_page() {
    ?>
    <div class="wrap">
        <h1>Custom SEO Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'custom_seo_settings' ); ?>
            <?php do_settings_sections( 'custom_seo_settings' ); ?>
            
            <h2>Search Engine Verification</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Google Search Console</th>
                    <td>
                        <input type="text" name="custom_seo_google_verification" value="<?php echo esc_attr( get_option( 'custom_seo_google_verification' ) ); ?>" class="regular-text" />
                        <p class="description">Enter your Google Search Console verification code</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Bing Webmaster Tools</th>
                    <td>
                        <input type="text" name="custom_seo_bing_verification" value="<?php echo esc_attr( get_option( 'custom_seo_bing_verification' ) ); ?>" class="regular-text" />
                        <p class="description">Enter your Bing Webmaster Tools verification code</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Pinterest</th>
                    <td>
                        <input type="text" name="custom_seo_pinterest_verification" value="<?php echo esc_attr( get_option( 'custom_seo_pinterest_verification' ) ); ?>" class="regular-text" />
                        <p class="description">Enter your Pinterest verification code</p>
                    </td>
                </tr>
            </table>
            
            <h2>Analytics & Tracking</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Google Analytics 4 ID</th>
                    <td>
                        <input type="text" name="custom_seo_gtag_id" value="<?php echo esc_attr( get_option( 'custom_seo_gtag_id' ) ); ?>" class="regular-text" placeholder="G-XXXXXXXXXX" />
                        <p class="description">Enter your Google Analytics 4 Measurement ID</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Google Analytics Code</th>
                    <td>
                        <textarea name="custom_seo_google_analytics" rows="5" class="large-text code"><?php echo esc_textarea( get_option( 'custom_seo_google_analytics' ) ); ?></textarea>
                        <p class="description">Enter custom Google Analytics code (optional if using GA4 ID above)</p>
                    </td>
                </tr>
            </table>
            
            <h2>Social Media</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Facebook App ID</th>
                    <td>
                        <input type="text" name="custom_seo_facebook_app_id" value="<?php echo esc_attr( get_option( 'custom_seo_facebook_app_id' ) ); ?>" class="regular-text" />
                        <p class="description">Facebook App ID for social sharing</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Twitter Username</th>
                    <td>
                        <input type="text" name="custom_seo_twitter_username" value="<?php echo esc_attr( get_option( 'custom_seo_twitter_username' ) ); ?>" class="regular-text" placeholder="@yourusername" />
                        <p class="description">Default Twitter username for cards</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Default Open Graph Image</th>
                    <td>
                        <input type="hidden" name="custom_seo_default_og_image" value="<?php echo esc_attr( get_option( 'custom_seo_default_og_image' ) ); ?>" />
                        <button type="button" class="button" id="custom_default_og_image_button">Select Default Image</button>
                        <button type="button" class="button" id="custom_default_og_image_remove">Remove</button>
                        <div id="custom_default_og_image_preview">
                            <?php 
                            $default_og_image = get_option( 'custom_seo_default_og_image' );
                            if ( $default_og_image ): ?>
                                <?php echo wp_get_attachment_image( $default_og_image, 'medium' ); ?>
                            <?php endif; ?>
                        </div>
                        <p class="description">Default image for Open Graph when no specific image is set</p>
                    </td>
                </tr>
            </table>
            
            <h2>Organization Schema</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Organization Name</th>
                    <td>
                        <input type="text" name="custom_seo_organization_name" value="<?php echo esc_attr( get_option( 'custom_seo_organization_name' ) ); ?>" class="regular-text" />
                        <p class="description">Your organization/company name for schema markup</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Organization Logo</th>
                    <td>
                        <input type="hidden" name="custom_seo_organization_logo" value="<?php echo esc_attr( get_option( 'custom_seo_organization_logo' ) ); ?>" />
                        <button type="button" class="button" id="custom_org_logo_button">Select Logo</button>
                        <button type="button" class="button" id="custom_org_logo_remove">Remove</button>
                        <div id="custom_org_logo_preview">
                            <?php 
                            $org_logo = get_option( 'custom_seo_organization_logo' );
                            if ( $org_logo ): ?>
                                <?php echo wp_get_attachment_image( $org_logo, 'medium' ); ?>
                            <?php endif; ?>
                        </div>
                        <p class="description">Your organization logo for schema markup</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        
        <h2>Sitemap URLs</h2>
        <p>Your XML sitemaps are available at:</p>
        <ul>
            <li><a href="<?php echo home_url( '/sitemap.xml' ); ?>" target="_blank"><?php echo home_url( '/sitemap.xml' ); ?></a> (Main sitemap index)</li>
        </ul>
        
        <h2>Usage</h2>
        <p>To display breadcrumbs in your theme, add this code to your template files:</p>
        <pre><code>&lt;?php if ( function_exists( 'custom_seo_breadcrumbs' ) ) custom_seo_breadcrumbs(); ?&gt;</code></pre>
    </div>
    
    <script>
        jQuery(document).ready(function($) {
            // Default OG Image uploader
            var defaultOgUploader;
            $('#custom_default_og_image_button').on('click', function(e) {
                e.preventDefault();
                if (defaultOgUploader) {
                    defaultOgUploader.open();
                    return;
                }
                defaultOgUploader = wp.media({
                    title: 'Select Default OG Image',
                    button: { text: 'Use Image' },
                    multiple: false
                });
                defaultOgUploader.on('select', function() {
                    var attachment = defaultOgUploader.state().get('selection').first().toJSON();
                    $('input[name="custom_seo_default_og_image"]').val(attachment.id);
                    $('#custom_default_og_image_preview').html('<img src="' + attachment.url + '" alt="" style="max-width: 200px; height: auto;" />');
                });
                defaultOgUploader.open();
            });
            
            $('#custom_default_og_image_remove').on('click', function(e) {
                e.preventDefault();
                $('input[name="custom_seo_default_og_image"]').val('');
                $('#custom_default_og_image_preview').html('');
            });
            
            // Organization logo uploader
            var orgLogoUploader;
            $('#custom_org_logo_button').on('click', function(e) {
                e.preventDefault();
                if (orgLogoUploader) {
                    orgLogoUploader.open();
                    return;
                }
                orgLogoUploader = wp.media({
                    title: 'Select Organization Logo',
                    button: { text: 'Use Image' },
                    multiple: false
                });
                orgLogoUploader.on('select', function() {
                    var attachment = orgLogoUploader.state().get('selection').first().toJSON();
                    $('input[name="custom_seo_organization_logo"]').val(attachment.id);
                    $('#custom_org_logo_preview').html('<img src="' + attachment.url + '" alt="" style="max-width: 200px; height: auto;" />');
                });
                orgLogoUploader.open();
            });
            
            $('#custom_org_logo_remove').on('click', function(e) {
                e.preventDefault();
                $('input[name="custom_seo_organization_logo"]').val('');
                $('#custom_org_logo_preview').html('');
            });
        });
    </script>
    <?php
}

// Enhanced global meta output with analytics
function custom_seo_output_enhanced_global_meta() {
    // Site verification codes
    $google_verification = get_option( 'custom_seo_google_verification' );
    $bing_verification = get_option( 'custom_seo_bing_verification' );
    $pinterest_verification = get_option( 'custom_seo_pinterest_verification' );
    
    if ( $google_verification ) {
        echo '<meta name="google-site-verification" content="' . esc_attr( $google_verification ) . "\">\n";
    }
    if ( $bing_verification ) {
        echo '<meta name="msvalidate.01" content="' . esc_attr( $bing_verification ) . "\">\n";
    }
    if ( $pinterest_verification ) {
        echo '<meta name="p:domain_verify" content="' . esc_attr( $pinterest_verification ) . "\">\n";
    }
    
    // Facebook App ID
    $facebook_app_id = get_option( 'custom_seo_facebook_app_id' );
    if ( $facebook_app_id ) {
        echo '<meta property="fb:app_id" content="' . esc_attr( $facebook_app_id ) . "\">\n";
    }
    
    // Google Analytics
    $gtag_id = get_option( 'custom_seo_gtag_id' );
    $custom_analytics = get_option( 'custom_seo_google_analytics' );
    
    if ( $gtag_id ) {
        echo "<script async src=\"https://www.googletagmanager.com/gtag/js?id=" . esc_attr( $gtag_id ) . "\"></script>\n";
        echo "<script>\n";
        echo "window.dataLayer = window.dataLayer || [];\n";
        echo "function gtag(){dataLayer.push(arguments);}\n";
        echo "gtag('js', new Date());\n";
        echo "gtag('config', '" . esc_js( $gtag_id ) . "');\n";
        echo "</script>\n";
    } elseif ( $custom_analytics ) {
        echo $custom_analytics . "\n";
    }
    
    // Organization schema
    $org_name = get_option( 'custom_seo_organization_name' );
    $org_logo = get_option( 'custom_seo_organization_logo' );
    
    if ( $org_name ) {
        $org_schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $org_name,
            'url' => home_url()
        ];
        
        if ( $org_logo ) {
            $logo_url = wp_get_attachment_url( $org_logo );
            if ( $logo_url ) {
                $org_schema['logo'] = $logo_url;
            }
        }
        
        echo '<script type="application/ld+json">';
        echo wp_json_encode( $org_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
        echo '</script>' . "\n";
    }
}

// Replace the simple global meta function with the enhanced one
function custom_seo_output_global_meta() {
    custom_seo_output_enhanced_global_meta();
}

// Enqueue media scripts on settings page
function custom_seo_settings_enqueue_scripts( $hook ) {
    if ( 'settings_page_custom-seo-settings' === $hook ) {
        wp_enqueue_media();
    }
}
add_action( 'admin_enqueue_scripts', 'custom_seo_settings_enqueue_scripts' );

// Add default OG image fallback
function custom_seo_get_default_og_image() {
    $default_image_id = get_option( 'custom_seo_default_og_image' );
    if ( $default_image_id ) {
        return wp_get_attachment_url( $default_image_id );
    }
    return '';
}
