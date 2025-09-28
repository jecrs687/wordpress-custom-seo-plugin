<?php
/**
 * Custom SEO Analytics
 * 
 * @package Custom_SEO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom SEO Analytics Class
 */
class Custom_SEO_Analytics {
    
    /**
     * Output analytics tracking code
     */
    public static function output_analytics() {
        $gtag_id = get_option( 'custom_seo_gtag_id' );
        $custom_analytics = get_option( 'custom_seo_google_analytics' );
        
        if ( $gtag_id ) {
            self::output_gtag_analytics( $gtag_id );
        } elseif ( $custom_analytics ) {
            echo $custom_analytics . "\n";
        }
    }
    
    /**
     * Output Google Analytics 4 (gtag) code
     */
    private static function output_gtag_analytics( $gtag_id ) {
        echo "<script async src=\"https://www.googletagmanager.com/gtag/js?id=" . esc_attr( $gtag_id ) . "\"></script>\n";
        echo "<script>\n";
        echo "window.dataLayer = window.dataLayer || [];\n";
        echo "function gtag(){dataLayer.push(arguments);}\n";
        echo "gtag('js', new Date());\n";
        echo "gtag('config', '" . esc_js( $gtag_id ) . "');\n";
        echo "</script>\n";
    }
    
    /**
     * Add enhanced ecommerce tracking (for future enhancement)
     */
    public static function track_ecommerce_event( $event_type, $data = [] ) {
        // Placeholder for future ecommerce tracking functionality
        // This could track events like purchase, add_to_cart, etc.
    }
    
    /**
     * Add custom event tracking (for future enhancement)
     */
    public static function track_custom_event( $event_name, $parameters = [] ) {
        // Placeholder for custom event tracking
        // This could be used to track form submissions, downloads, etc.
    }
}