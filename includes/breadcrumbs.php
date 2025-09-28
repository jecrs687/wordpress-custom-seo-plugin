<?php
/**
 * Custom SEO Breadcrumbs
 * 
 * @package Custom_SEO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom SEO Breadcrumbs Class
 */
class Custom_SEO_Breadcrumbs {
    
    /**
     * Initialize breadcrumbs
     */
    public static function init() {
        add_action( 'wp_head', [ __CLASS__, 'output_styles' ] );
    }
    
    /**
     * Display breadcrumbs
     */
    public static function display( $args = [] ) {
        $defaults = [
            'separator' => ' > ',
            'home_text' => __( 'Home', 'custom-seo' ),
            'show_current' => true,
            'show_home' => true,
            'structured_data' => true
        ];
        
        $args = wp_parse_args( $args, $defaults );
        
        if ( is_front_page() ) {
            return;
        }
        
        $breadcrumbs = self::get_breadcrumbs( $args );
        
        if ( empty( $breadcrumbs ) ) {
            return;
        }
        
        self::output_html( $breadcrumbs, $args );
        
        if ( $args['structured_data'] ) {
            Custom_SEO_Schema::output_breadcrumb_schema( $breadcrumbs );
        }
    }
    
    /**
     * Get breadcrumb items
     */
    private static function get_breadcrumbs( $args ) {
        $breadcrumbs = [];
        
        // Home
        if ( $args['show_home'] ) {
            $breadcrumbs[] = [
                'text' => $args['home_text'],
                'url' => home_url( '/' )
            ];
        }
        
        if ( is_category() ) {
            $breadcrumbs = array_merge( $breadcrumbs, self::get_category_breadcrumbs( $args ) );
        } elseif ( is_tag() ) {
            $breadcrumbs = array_merge( $breadcrumbs, self::get_tag_breadcrumbs( $args ) );
        } elseif ( is_tax() ) {
            $breadcrumbs = array_merge( $breadcrumbs, self::get_taxonomy_breadcrumbs( $args ) );
        } elseif ( is_single() ) {
            $breadcrumbs = array_merge( $breadcrumbs, self::get_single_breadcrumbs( $args ) );
        } elseif ( is_page() ) {
            $breadcrumbs = array_merge( $breadcrumbs, self::get_page_breadcrumbs( $args ) );
        } elseif ( is_author() ) {
            $breadcrumbs = array_merge( $breadcrumbs, self::get_author_breadcrumbs( $args ) );
        } elseif ( is_archive() ) {
            $breadcrumbs = array_merge( $breadcrumbs, self::get_archive_breadcrumbs( $args ) );
        } elseif ( is_search() ) {
            $breadcrumbs = array_merge( $breadcrumbs, self::get_search_breadcrumbs( $args ) );
        }
        
        return $breadcrumbs;
    }
    
    /**
     * Get category breadcrumbs
     */
    private static function get_category_breadcrumbs( $args ) {
        $breadcrumbs = [];
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
        
        return $breadcrumbs;
    }
    
    /**
     * Get tag breadcrumbs
     */
    private static function get_tag_breadcrumbs( $args ) {
        $breadcrumbs = [];
        $tag = get_queried_object();
        
        if ( $args['show_current'] ) {
            $breadcrumbs[] = [
                'text' => $tag->name,
                'url' => ''
            ];
        }
        
        return $breadcrumbs;
    }
    
    /**
     * Get taxonomy breadcrumbs
     */
    private static function get_taxonomy_breadcrumbs( $args ) {
        $breadcrumbs = [];
        $term = get_queried_object();
        
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
        
        return $breadcrumbs;
    }
    
    /**
     * Get single post breadcrumbs
     */
    private static function get_single_breadcrumbs( $args ) {
        global $post;
        $breadcrumbs = [];
        
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
        
        return $breadcrumbs;
    }
    
    /**
     * Get page breadcrumbs
     */
    private static function get_page_breadcrumbs( $args ) {
        global $post;
        $breadcrumbs = [];
        
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
        
        return $breadcrumbs;
    }
    
    /**
     * Get author breadcrumbs
     */
    private static function get_author_breadcrumbs( $args ) {
        $breadcrumbs = [];
        $author = get_queried_object();
        
        if ( $args['show_current'] ) {
            $breadcrumbs[] = [
                'text' => sprintf( __( 'Author: %s', 'custom-seo' ), $author->display_name ),
                'url' => ''
            ];
        }
        
        return $breadcrumbs;
    }
    
    /**
     * Get archive breadcrumbs
     */
    private static function get_archive_breadcrumbs( $args ) {
        $breadcrumbs = [];
        
        if ( $args['show_current'] ) {
            $breadcrumbs[] = [
                'text' => post_type_archive_title( '', false ),
                'url' => ''
            ];
        }
        
        return $breadcrumbs;
    }
    
    /**
     * Get search breadcrumbs
     */
    private static function get_search_breadcrumbs( $args ) {
        $breadcrumbs = [];
        
        if ( $args['show_current'] ) {
            $breadcrumbs[] = [
                'text' => sprintf( __( 'Search Results for: %s', 'custom-seo' ), get_search_query() ),
                'url' => ''
            ];
        }
        
        return $breadcrumbs;
    }
    
    /**
     * Output breadcrumb HTML
     */
    private static function output_html( $breadcrumbs, $args ) {
        echo '<nav class="custom-seo-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'custom-seo' ) . '">';
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
    }
    
    /**
     * Output breadcrumb styles
     */
    public static function output_styles() {
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
}

// Initialize breadcrumbs
Custom_SEO_Breadcrumbs::init();

/**
 * Template function to display breadcrumbs
 */
function custom_seo_breadcrumbs( $args = [] ) {
    Custom_SEO_Breadcrumbs::display( $args );
}