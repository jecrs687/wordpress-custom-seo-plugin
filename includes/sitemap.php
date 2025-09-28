<?php
/**
 * Custom SEO Sitemap
 * 
 * @package Custom_SEO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom SEO Sitemap Class
 */
class Custom_SEO_Sitemap {
    
    /**
     * Initialize sitemap functionality
     */
    public static function init() {
        add_action( 'init', [ __CLASS__, 'init_rewrite_rules' ] );
        add_filter( 'query_vars', [ __CLASS__, 'add_query_vars' ] );
        add_action( 'template_redirect', [ __CLASS__, 'template_redirect' ] );
    }
    
    /**
     * Initialize rewrite rules for sitemaps
     */
    public static function init_rewrite_rules() {
        add_rewrite_rule( '^sitemap\.xml$', 'index.php?custom_sitemap=main', 'top' );
        add_rewrite_rule( '^sitemap-([^/]+)\.xml$', 'index.php?custom_sitemap=$matches[1]', 'top' );
        
        // Check if we need to flush rewrite rules
        $rules = get_option( 'rewrite_rules' );
        if ( ! isset( $rules['^sitemap\.xml$'] ) ) {
            flush_rewrite_rules();
        }
    }
    
    /**
     * Add custom query vars
     */
    public static function add_query_vars( $vars ) {
        $vars[] = 'custom_sitemap';
        return $vars;
    }
    
    /**
     * Handle sitemap requests
     */
    public static function template_redirect() {
        $sitemap = get_query_var( 'custom_sitemap' );
        
        if ( ! $sitemap ) {
            return;
        }
        
        header( 'Content-Type: application/xml; charset=utf-8' );
        
        if ( $sitemap === 'main' ) {
            self::generate_sitemap_index();
        } else {
            self::generate_sitemap( $sitemap );
        }
        
        exit;
    }
    
    /**
     * Generate sitemap index
     */
    private static function generate_sitemap_index() {
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
    
    /**
     * Generate individual sitemap
     */
    private static function generate_sitemap( $type ) {
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
        
        // Check if it's a post type
        if ( post_type_exists( $type ) ) {
            self::generate_post_type_sitemap( $type );
        } elseif ( taxonomy_exists( $type ) ) {
            self::generate_taxonomy_sitemap( $type );
        }
        
        echo '</urlset>';
    }
    
    /**
     * Generate sitemap for post type
     */
    private static function generate_post_type_sitemap( $post_type ) {
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
            echo '<priority>' . self::get_priority( $post ) . '</priority>' . "\n";
            
            // Add language information if available
            $language = get_post_meta( $post->ID, 'custom_language', true );
            if ( ! $language ) {
                $language = get_option( 'custom_seo_default_language', '' );
            }
            if ( $language ) {
                echo '<xhtml:link rel="alternate" hreflang="' . esc_attr( $language ) . '" href="' . esc_url( $permalink ) . '" />' . "\n";
            }
            
            // Add image if available
            self::add_sitemap_image( $post );
            
            echo '</url>' . "\n";
        }
    }
    
    /**
     * Generate sitemap for taxonomy
     */
    private static function generate_taxonomy_sitemap( $taxonomy ) {
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
    
    /**
     * Get priority for a post
     */
    private static function get_priority( $post ) {
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
    
    /**
     * Add image to sitemap
     */
    private static function add_sitemap_image( $post ) {
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
    }
}

// Initialize sitemap functionality
Custom_SEO_Sitemap::init();