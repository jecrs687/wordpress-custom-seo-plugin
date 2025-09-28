<?php
/**
 * Custom SEO Schema Output
 * 
 * @package Custom_SEO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom SEO Schema Class
 */
class Custom_SEO_Schema {
    
    /**
     * Output post schema
     */
    public static function output_post_schema( $post ) {
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
            self::output_article_schema( $post );
        } elseif ( $schema_type === 'Product' ) {
            self::output_product_schema( $post );
        } elseif ( $schema_type === 'Event' ) {
            self::output_event_schema( $post );
        } elseif ( $schema_type === 'FAQ' ) {
            self::output_faq_schema( $post );
        }
    }
    
    /**
     * Output Article schema
     */
    private static function output_article_schema( $post ) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title( $post ),
            'description' => custom_seo_get_fallback_description( $post ),
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
        
        // Add image if available
        $og_image_id = get_post_meta( $post->ID, 'custom_og_image_id', true );
        if ( ! $og_image_id && has_post_thumbnail( $post->ID ) ) {
            $og_image_id = get_post_thumbnail_id( $post->ID );
        }
        
        if ( $og_image_id ) {
            $image_url = wp_get_attachment_url( $og_image_id );
            if ( $image_url ) {
                $schema['image'] = $image_url;
            }
        }
        
        self::output_schema_json( $schema );
    }
    
    /**
     * Output Product schema (basic structure)
     */
    private static function output_product_schema( $post ) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => get_the_title( $post ),
            'description' => custom_seo_get_fallback_description( $post ),
            'url' => get_permalink( $post )
        ];
        
        // Add image if available
        $og_image_id = get_post_meta( $post->ID, 'custom_og_image_id', true );
        if ( ! $og_image_id && has_post_thumbnail( $post->ID ) ) {
            $og_image_id = get_post_thumbnail_id( $post->ID );
        }
        
        if ( $og_image_id ) {
            $image_url = wp_get_attachment_url( $og_image_id );
            if ( $image_url ) {
                $schema['image'] = $image_url;
            }
        }
        
        self::output_schema_json( $schema );
    }
    
    /**
     * Output Event schema (basic structure)
     */
    private static function output_event_schema( $post ) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => get_the_title( $post ),
            'description' => custom_seo_get_fallback_description( $post ),
            'url' => get_permalink( $post )
        ];
        
        self::output_schema_json( $schema );
    }
    
    /**
     * Output FAQ schema (basic structure)
     */
    private static function output_faq_schema( $post ) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => []
        ];
        
        // This would need to be enhanced to parse FAQ content from the post
        // For now, just output basic structure
        self::output_schema_json( $schema );
    }
    
    /**
     * Output organization schema
     */
    public static function output_organization_schema() {
        $org_schema = custom_seo_get_organization_schema();
        
        if ( $org_schema ) {
            self::output_schema_json( $org_schema );
        }
    }
    
    /**
     * Output breadcrumb schema
     */
    public static function output_breadcrumb_schema( $breadcrumbs ) {
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
        
        self::output_schema_json( $schema );
    }
    
    /**
     * Helper function to output JSON-LD schema
     */
    private static function output_schema_json( $schema ) {
        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . "\n";
        echo '</script>' . "\n";
    }
}