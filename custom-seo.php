<?php
/**
 * Plugin Name: Custom SEO
 * Plugin URI: https://github.com/jecrs687/wordpress-custom-seo-plugin
 * Description: A comprehensive SEO plugin with REST API fields, sitemaps, breadcrumbs, and advanced social sharing.
 * Version: 1.0.0
 * Author: jecrs687
 * Author URI: https://github.com/jecrs687
 * Text Domain: custom-seo
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.5
 * Requires PHP: 7.4
 * Network: false
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
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
        
        // Register REST API hooks
        add_action( 'rest_api_init', [ __CLASS__, 'register_rest_hooks' ] );
        
        // Add plugin icon and admin styles
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_styles' ] );
        add_filter( 'plugin_row_meta', [ __CLASS__, 'add_plugin_row_meta' ], 10, 2 );
        add_filter( 'plugin_action_links_' . plugin_basename( CUSTOM_SEO_PLUGIN_FILE ), [ __CLASS__, 'add_plugin_action_links' ] );
        
        // Load includes
        self::includes();
        
        // Add admin notice to verify plugin is working
        add_action( 'admin_notices', [ __CLASS__, 'show_plugin_loaded_notice' ] );
        
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
            
            // Categories and Tags Management
            'custom_seo_categories'        => 'string',
            'custom_seo_tags'              => 'string',
            'custom_seo_replace_categories' => 'boolean',
            'custom_seo_replace_tags'      => 'boolean',
            'custom_seo_auto_create_terms' => 'boolean',
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
     * Register REST API hooks
     */
    public static function register_rest_hooks() {
        // Hook into post meta updates to process categories and tags
        add_action( 'updated_post_meta', [ __CLASS__, 'process_rest_categories_tags' ], 10, 4 );
        
        // Register REST endpoint for category/tag processing
        register_rest_route( 'custom-seo/v1', '/process-terms/(?P<id>\d+)', [
            'methods' => 'POST',
            'callback' => [ __CLASS__, 'rest_process_terms' ],
            'permission_callback' => function() {
                return current_user_can( 'edit_posts' );
            },
            'args' => [
                'id' => [
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param );
                    }
                ],
                'categories' => [
                    'type' => 'string',
                    'default' => ''
                ],
                'tags' => [
                    'type' => 'string',
                    'default' => ''
                ],
                'replace_categories' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'replace_tags' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'auto_create' => [
                    'type' => 'boolean',
                    'default' => true
                ]
            ]
        ] );
    }
    
    /**
     * Process categories and tags when updated via REST API
     */
    public static function process_rest_categories_tags( $meta_id, $post_id, $meta_key, $meta_value ) {
        // Only process if one of our category/tag fields was updated
        if ( ! in_array( $meta_key, [ 'custom_seo_categories', 'custom_seo_tags' ] ) ) {
            return;
        }
        
        // Get all the SEO meta for processing
        $categories = get_post_meta( $post_id, 'custom_seo_categories', true );
        $tags = get_post_meta( $post_id, 'custom_seo_tags', true );
        $replace_categories = get_post_meta( $post_id, 'custom_seo_replace_categories', true );
        $replace_tags = get_post_meta( $post_id, 'custom_seo_replace_tags', true );
        $auto_create = get_post_meta( $post_id, 'custom_seo_auto_create_terms', true );
        
        // Process if we have categories or tags to process
        if ( ! empty( $categories ) || ! empty( $tags ) ) {
            custom_seo_process_categories_and_tags( 
                $post_id, 
                $categories, 
                $tags, 
                (bool) $replace_categories, 
                (bool) $replace_tags, 
                (bool) $auto_create 
            );
        }
    }
    
    /**
     * REST endpoint to process categories and tags
     */
    public static function rest_process_terms( $request ) {
        $post_id = $request['id'];
        $categories = $request['categories'];
        $tags = $request['tags'];
        $replace_categories = $request['replace_categories'];
        $replace_tags = $request['replace_tags'];
        $auto_create = $request['auto_create'];
        
        // Verify post exists
        $post = get_post( $post_id );
        if ( ! $post ) {
            return new WP_Error( 'post_not_found', __( 'Post not found', 'custom-seo' ), [ 'status' => 404 ] );
        }
        
        // Process categories and tags
        $results = custom_seo_process_categories_and_tags( 
            $post_id, 
            $categories, 
            $tags, 
            $replace_categories, 
            $replace_tags, 
            $auto_create 
        );
        
        return [
            'success' => true,
            'post_id' => $post_id,
            'results' => $results,
            'message' => sprintf( 
                __( 'Processed %d categories and %d tags for post %d', 'custom-seo' ),
                count( $results['categories']['success'] ),
                count( $results['tags']['success'] ),
                $post_id
            )
        ];
    }
    
    /**
     * Enqueue admin styles for plugin icon
     */
    public static function enqueue_admin_styles( $hook ) {
        // Only load on plugins page
        if ( 'plugins.php' === $hook ) {
            $css = '
                .wp-list-table.plugins #the-list tr[data-plugin="' . plugin_basename( CUSTOM_SEO_PLUGIN_FILE ) . '"] .plugin-title strong:before {
                    content: "";
                    background-image: url("' . CUSTOM_SEO_PLUGIN_URL . 'assets/icon.svg"), url("' . CUSTOM_SEO_PLUGIN_URL . 'assets/icon.png");
                    background-size: 20px 20px;
                    background-repeat: no-repeat;
                    display: inline-block;
                    width: 20px;
                    height: 20px;
                    margin-right: 8px;
                    vertical-align: middle;
                }
                .wp-list-table.plugins #the-list tr[data-plugin="' . plugin_basename( CUSTOM_SEO_PLUGIN_FILE ) . '"] .plugin-icon {
                    background-image: url("' . CUSTOM_SEO_PLUGIN_URL . 'assets/icon.svg"), url("' . CUSTOM_SEO_PLUGIN_URL . 'assets/icon-128x128.png");
                    background-size: cover;
                    background-repeat: no-repeat;
                    background-position: center;
                    width: 64px;
                    height: 64px;
                }
                @media only screen and (min-width: 1200px) {
                    .wp-list-table.plugins #the-list tr[data-plugin="' . plugin_basename( CUSTOM_SEO_PLUGIN_FILE ) . '"] .plugin-icon {
                        background-image: url("' . CUSTOM_SEO_PLUGIN_URL . 'assets/icon.svg"), url("' . CUSTOM_SEO_PLUGIN_URL . 'assets/icon-256x256.png");
                    }
                }
                /* Custom SEO Plugin branding */
                .wp-list-table.plugins #the-list tr[data-plugin="' . plugin_basename( CUSTOM_SEO_PLUGIN_FILE ) . '"] {
                    background: linear-gradient(90deg, rgba(0,115,170,0.05) 0%, transparent 100%);
                }
                .wp-list-table.plugins #the-list tr[data-plugin="' . plugin_basename( CUSTOM_SEO_PLUGIN_FILE ) . '"] .plugin-title strong {
                    color: #0073aa;
                }
            ';
            
            wp_add_inline_style( 'wp-admin', $css );
        }
    }
    
    /**
     * Add plugin row meta with icon support
     */
    public static function add_plugin_row_meta( $links, $file ) {
        if ( $file === plugin_basename( CUSTOM_SEO_PLUGIN_FILE ) ) {
            $new_links = [
                'docs' => '<a href="https://github.com/jecrs687/wordpress-custom-seo-plugin#readme" target="_blank">📚 ' . __( 'Documentation', 'custom-seo' ) . '</a>',
                'support' => '<a href="https://github.com/jecrs687/wordpress-custom-seo-plugin/issues" target="_blank">🐛 ' . __( 'Support', 'custom-seo' ) . '</a>',
                'github' => '<a href="https://github.com/jecrs687/wordpress-custom-seo-plugin" target="_blank">⭐ ' . __( 'GitHub', 'custom-seo' ) . '</a>'
            ];
            return array_merge( $links, $new_links );
        }
        return $links;
    }
    
    /**
     * Add plugin action links
     */
    public static function add_plugin_action_links( $links ) {
        $settings_link = '<a href="' . admin_url( 'admin.php?page=custom-seo-main' ) . '">' . __( 'Settings', 'custom-seo' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }
    
    /**
     * Show plugin loaded notice (temporary for debugging)
     */
    public static function show_plugin_loaded_notice() {
        $screen = get_current_screen();
        if ( $screen->id === 'plugins' ) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p><strong>Custom SEO Plugin:</strong> Plugin loaded successfully! Settings available at <a href="' . admin_url( 'admin.php?page=custom-seo-main' ) . '">Custom SEO</a> in the admin menu.</p>';
            echo '</div>';
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