<?php
/**
 * Custom SEO Utility Functions
 * 
 * @package Custom_SEO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get SEO meta fields
 */
function custom_seo_get_meta_fields() {
    return [
        'custom_seo_title', 'custom_seo_description', 'custom_focus_keyword', 'custom_canonical_url',
        'custom_robots', 'custom_og_title', 'custom_og_description', 'custom_og_image_id', 'custom_og_type',
        'custom_og_locale', 'custom_twitter_title', 'custom_twitter_description', 'custom_twitter_image_id',
        'custom_twitter_card', 'custom_twitter_site', 'custom_twitter_creator', 'custom_schema_type',
        'custom_schema_data', 'custom_meta_keywords', 'custom_redirect_url', 'custom_redirect_type',
        'custom_noindex', 'custom_nofollow', 'custom_noarchive', 'custom_nosnippet', 'custom_noimageindex',
        'custom_seo_categories', 'custom_seo_tags', 'custom_seo_replace_categories', 'custom_seo_replace_tags',
        'custom_seo_auto_create_terms'
    ];
}

/**
 * Get default OG image
 */
function custom_seo_get_default_og_image() {
    $default_image_id = get_option( 'custom_seo_default_og_image' );
    if ( $default_image_id ) {
        return wp_get_attachment_url( $default_image_id );
    }
    return '';
}

/**
 * Get default Twitter username
 */
function custom_seo_get_default_twitter_username() {
    return get_option( 'custom_seo_twitter_username', '' );
}

/**
 * Sanitize SEO field value
 */
function custom_seo_sanitize_field( $value, $field_type = 'string' ) {
    switch ( $field_type ) {
        case 'string':
        case 'text':
            return sanitize_text_field( $value );
        case 'textarea':
            return sanitize_textarea_field( $value );
        case 'url':
            return esc_url_raw( $value );
        case 'integer':
            return absint( $value );
        case 'boolean':
            return (bool) $value;
        default:
            return sanitize_text_field( $value );
    }
}

/**
 * Get fallback description for posts
 */
function custom_seo_get_fallback_description( $post = null ) {
    if ( ! $post ) {
        global $post;
    }
    
    if ( ! $post ) {
        return '';
    }
    
    $description = '';
    
    // Try excerpt first
    if ( ! empty( $post->post_excerpt ) ) {
        $description = $post->post_excerpt;
    } else {
        // Fallback to trimmed content
        $description = wp_strip_all_tags( $post->post_content );
    }
    
    return wp_trim_words( $description, 25 );
}

/**
 * Check if current request is for sitemap
 */
function custom_seo_is_sitemap_request() {
    return ! empty( get_query_var( 'custom_sitemap' ) );
}

/**
 * Get organization schema data
 */
function custom_seo_get_organization_schema() {
    $org_name = get_option( 'custom_seo_organization_name' );
    $org_logo = get_option( 'custom_seo_organization_logo' );
    
    if ( ! $org_name ) {
        return null;
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $org_name,
        'url' => home_url()
    ];
    
    if ( $org_logo ) {
        $logo_url = wp_get_attachment_url( $org_logo );
        if ( $logo_url ) {
            $schema['logo'] = $logo_url;
        }
    }
    
    return $schema;
}

/**
 * Get verification meta tags
 */
function custom_seo_get_verification_meta() {
    $meta_tags = [];
    
    $google_verification = get_option( 'custom_seo_google_verification' );
    if ( $google_verification ) {
        $meta_tags['google-site-verification'] = $google_verification;
    }
    
    $bing_verification = get_option( 'custom_seo_bing_verification' );
    if ( $bing_verification ) {
        $meta_tags['msvalidate.01'] = $bing_verification;
    }
    
    $pinterest_verification = get_option( 'custom_seo_pinterest_verification' );
    if ( $pinterest_verification ) {
        $meta_tags['p:domain_verify'] = $pinterest_verification;
    }
    
    return $meta_tags;
}

/**
 * Create category if it doesn't exist and return the term ID
 * 
 * @param string $category_name The name of the category
 * @param int $parent_id Optional parent category ID
 * @return int|WP_Error The category term ID or WP_Error on failure
 */
function custom_seo_create_category( $category_name, $parent_id = 0 ) {
    $category_name = trim( $category_name );
    
    if ( empty( $category_name ) ) {
        return new WP_Error( 'empty_category_name', __( 'Category name cannot be empty', 'custom-seo' ) );
    }
    
    // Check if category already exists
    $existing_category = get_category_by_slug( sanitize_title( $category_name ) );
    if ( $existing_category ) {
        return $existing_category->term_id;
    }
    
    // Create new category
    $category_data = wp_insert_category( [
        'cat_name' => $category_name,
        'category_parent' => $parent_id,
        'category_description' => sprintf( __( 'Auto-created category: %s', 'custom-seo' ), $category_name )
    ] );
    
    if ( is_wp_error( $category_data ) ) {
        return $category_data;
    }
    
    return $category_data;
}

/**
 * Create tag if it doesn't exist and return the term ID
 * 
 * @param string $tag_name The name of the tag
 * @return int|WP_Error The tag term ID or WP_Error on failure
 */
function custom_seo_create_tag( $tag_name ) {
    $tag_name = trim( $tag_name );
    
    if ( empty( $tag_name ) ) {
        return new WP_Error( 'empty_tag_name', __( 'Tag name cannot be empty', 'custom-seo' ) );
    }
    
    // Check if tag already exists
    $existing_tag = get_term_by( 'name', $tag_name, 'post_tag' );
    if ( $existing_tag ) {
        return $existing_tag->term_id;
    }
    
    // Create new tag
    $tag_data = wp_insert_term( $tag_name, 'post_tag', [
        'description' => sprintf( __( 'Auto-created tag: %s', 'custom-seo' ), $tag_name )
    ] );
    
    if ( is_wp_error( $tag_data ) ) {
        return $tag_data;
    }
    
    return $tag_data['term_id'];
}

/**
 * Process categories and tags from comma-separated string
 * 
 * @param int $post_id The post ID
 * @param string $categories_string Comma-separated category names
 * @param string $tags_string Comma-separated tag names
 * @param bool $replace_categories Whether to replace existing categories
 * @param bool $replace_tags Whether to replace existing tags
 * @param bool $auto_create Whether to auto-create non-existing terms
 * @return array Results array with success/error information
 */
function custom_seo_process_categories_and_tags( $post_id, $categories_string = '', $tags_string = '', $replace_categories = false, $replace_tags = false, $auto_create = true ) {
    $results = [
        'categories' => [ 'success' => [], 'errors' => [] ],
        'tags' => [ 'success' => [], 'errors' => [] ]
    ];
    
    // Process categories
    if ( ! empty( $categories_string ) ) {
        $category_names = array_map( 'trim', explode( ',', $categories_string ) );
        $category_ids = [];
        
        foreach ( $category_names as $category_name ) {
            if ( empty( $category_name ) ) continue;
            
            if ( $auto_create ) {
                $category_id = custom_seo_create_category( $category_name );
                if ( is_wp_error( $category_id ) ) {
                    $results['categories']['errors'][] = sprintf( 
                        __( 'Failed to create category "%s": %s', 'custom-seo' ), 
                        $category_name, 
                        $category_id->get_error_message() 
                    );
                } else {
                    $category_ids[] = $category_id;
                    $results['categories']['success'][] = $category_name;
                }
            } else {
                // Only assign existing categories
                $existing_category = get_category_by_slug( sanitize_title( $category_name ) );
                if ( $existing_category ) {
                    $category_ids[] = $existing_category->term_id;
                    $results['categories']['success'][] = $category_name;
                } else {
                    $results['categories']['errors'][] = sprintf( 
                        __( 'Category "%s" does not exist and auto-creation is disabled', 'custom-seo' ),
                        $category_name 
                    );
                }
            }
        }
        
        if ( ! empty( $category_ids ) ) {
            if ( $replace_categories ) {
                wp_set_post_categories( $post_id, $category_ids );
            } else {
                // Get current categories and merge
                $current_categories = wp_get_post_categories( $post_id );
                $all_categories = array_unique( array_merge( $current_categories, $category_ids ) );
                wp_set_post_categories( $post_id, $all_categories );
            }
        }
    }
    
    // Process tags
    if ( ! empty( $tags_string ) ) {
        $tag_names = array_map( 'trim', explode( ',', $tags_string ) );
        $tag_ids = [];
        
        foreach ( $tag_names as $tag_name ) {
            if ( empty( $tag_name ) ) continue;
            
            if ( $auto_create ) {
                $tag_id = custom_seo_create_tag( $tag_name );
                if ( is_wp_error( $tag_id ) ) {
                    $results['tags']['errors'][] = sprintf( 
                        __( 'Failed to create tag "%s": %s', 'custom-seo' ), 
                        $tag_name, 
                        $tag_id->get_error_message() 
                    );
                } else {
                    $tag_ids[] = $tag_id;
                    $results['tags']['success'][] = $tag_name;
                }
            } else {
                // Only assign existing tags
                $existing_tag = get_term_by( 'name', $tag_name, 'post_tag' );
                if ( $existing_tag ) {
                    $tag_ids[] = $existing_tag->term_id;
                    $results['tags']['success'][] = $tag_name;
                } else {
                    $results['tags']['errors'][] = sprintf( 
                        __( 'Tag "%s" does not exist and auto-creation is disabled', 'custom-seo' ),
                        $tag_name 
                    );
                }
            }
        }
        
        if ( ! empty( $tag_ids ) ) {
            if ( $replace_tags ) {
                wp_set_post_tags( $post_id, $tag_ids );
            } else {
                // Get current tags and merge
                $current_tags = wp_get_post_tags( $post_id, [ 'fields' => 'ids' ] );
                $all_tags = array_unique( array_merge( $current_tags, $tag_ids ) );
                wp_set_post_tags( $post_id, $all_tags );
            }
        }
    }
    
    return $results;
}