<?php
/**
 * Custom SEO Meta Output
 * 
 * @package Custom_SEO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom SEO Meta Output Class
 */
class Custom_SEO_Meta_Output {
    
    /**
     * Initialize meta output
     */
    public static function init() {
        add_action( 'wp_head', [ __CLASS__, 'output_meta' ], 5 );
    }
    
    /**
     * Main meta output function
     */
    public static function output_meta() {
        global $post;
        
        echo "\n<!-- Custom SEO Plugin -->\n";
        
        // Viewport meta tag
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
        
        if ( is_singular() && $post ) {
            self::output_singular_meta();
        } else {
            self::output_archive_meta();
        }
        
        // Global site verification and analytics
        self::output_global_meta();
        
        echo "<!-- /Custom SEO Plugin -->\n";
    }
    
    /**
     * Output meta for singular posts/pages
     */
    private static function output_singular_meta() {
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
        $description = get_post_meta( $post->ID, 'custom_seo_description', true ) ?: custom_seo_get_fallback_description( $post );
        $keywords = get_post_meta( $post->ID, 'custom_meta_keywords', true );
        $canonical = get_post_meta( $post->ID, 'custom_canonical_url', true ) ?: get_permalink( $post );
        
        // Robots directives
        $robots = self::get_robots_directives( $post->ID );
        
        // Output basic meta tags
        echo '<title>' . esc_html( $title ) . "</title>\n";
        echo '<meta name="description" content="' . esc_attr( $description ) . "\">\n";
        if ( $keywords ) {
            echo '<meta name="keywords" content="' . esc_attr( $keywords ) . "\">\n";
        }
        echo '<meta name="robots" content="' . esc_attr( implode( ',', $robots ) ) . "\">\n";
        echo '<link rel="canonical" href="' . esc_url( $canonical ) . "\">\n";
        
        // Open Graph
        self::output_open_graph_meta( $post, $title, $description );
        
        // Twitter Card
        self::output_twitter_meta( $post, $title, $description );
        
        // JSON-LD Schema
        Custom_SEO_Schema::output_post_schema( $post );
    }
    
    /**
     * Output meta for archive pages
     */
    private static function output_archive_meta() {
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
    
    /**
     * Get robots directives for a post
     */
    private static function get_robots_directives( $post_id ) {
        $robots = [];
        
        if ( get_post_meta( $post_id, 'custom_noindex', true ) ) $robots[] = 'noindex';
        if ( get_post_meta( $post_id, 'custom_nofollow', true ) ) $robots[] = 'nofollow';
        if ( get_post_meta( $post_id, 'custom_noarchive', true ) ) $robots[] = 'noarchive';
        if ( get_post_meta( $post_id, 'custom_nosnippet', true ) ) $robots[] = 'nosnippet';
        if ( get_post_meta( $post_id, 'custom_noimageindex', true ) ) $robots[] = 'noimageindex';
        
        $robots_content = get_post_meta( $post_id, 'custom_robots', true );
        if ( $robots_content ) {
            $robots[] = $robots_content;
        }
        
        if ( empty( $robots ) ) {
            $robots[] = 'index,follow';
        }
        
        return $robots;
    }
    
    /**
     * Output Open Graph meta tags
     */
    private static function output_open_graph_meta( $post, $title, $description ) {
        $og_title = get_post_meta( $post->ID, 'custom_og_title', true ) ?: $title;
        $og_description = get_post_meta( $post->ID, 'custom_og_description', true ) ?: $description;
        $og_type = get_post_meta( $post->ID, 'custom_og_type', true ) ?: 'article';
        $og_locale = get_post_meta( $post->ID, 'custom_og_locale', true ) ?: get_locale();
        $og_image_id = get_post_meta( $post->ID, 'custom_og_image_id', true );
        $og_image_url = $og_image_id ? wp_get_attachment_url( $og_image_id ) : '';
        
        // Use default OG image if no custom image is set
        if ( ! $og_image_url ) {
            $og_image_url = custom_seo_get_default_og_image();
        }
        
        echo '<meta property="og:title" content="' . esc_attr( $og_title ) . "\">\n";
        echo '<meta property="og:description" content="' . esc_attr( $og_description ) . "\">\n";
        echo '<meta property="og:type" content="' . esc_attr( $og_type ) . "\">\n";
        echo '<meta property="og:url" content="' . esc_url( get_permalink( $post ) ) . "\">\n";
        echo '<meta property="og:locale" content="' . esc_attr( $og_locale ) . "\">\n";
        echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . "\">\n";
        
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
            self::output_article_meta( $post );
        }
    }
    
    /**
     * Output article-specific Open Graph meta
     */
    private static function output_article_meta( $post ) {
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
    
    /**
     * Output Twitter Card meta tags
     */
    private static function output_twitter_meta( $post, $title, $description ) {
        $tw_card = get_post_meta( $post->ID, 'custom_twitter_card', true ) ?: 'summary_large_image';
        $tw_title = get_post_meta( $post->ID, 'custom_twitter_title', true ) ?: $title;
        $tw_description = get_post_meta( $post->ID, 'custom_twitter_description', true ) ?: $description;
        $tw_image_id = get_post_meta( $post->ID, 'custom_twitter_image_id', true );
        $tw_image_url = $tw_image_id ? wp_get_attachment_url( $tw_image_id ) : custom_seo_get_default_og_image();
        $tw_site = get_post_meta( $post->ID, 'custom_twitter_site', true ) ?: custom_seo_get_default_twitter_username();
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
    }
    
    /**
     * Output global meta tags
     */
    private static function output_global_meta() {
        // Site verification codes
        $verification_meta = custom_seo_get_verification_meta();
        foreach ( $verification_meta as $name => $content ) {
            echo '<meta name="' . esc_attr( $name ) . '" content="' . esc_attr( $content ) . "\">\n";
        }
        
        // Facebook App ID
        $facebook_app_id = get_option( 'custom_seo_facebook_app_id' );
        if ( $facebook_app_id ) {
            echo '<meta property="fb:app_id" content="' . esc_attr( $facebook_app_id ) . "\">\n";
        }
        
        // Analytics and organization schema
        Custom_SEO_Analytics::output_analytics();
        Custom_SEO_Schema::output_organization_schema();
    }
}

// Initialize meta output
Custom_SEO_Meta_Output::init();