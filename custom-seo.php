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
        
        // Register REST API hooks
        add_action( 'rest_api_init', [ __CLASS__, 'register_rest_hooks' ] );
        
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