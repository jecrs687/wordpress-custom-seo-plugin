<?php
/**
 * Custom SEO Meta Boxes
 * 
 * @package Custom_SEO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom SEO Meta Boxes Class
 */
class Custom_SEO_Meta_Boxes {
    
    /**
     * Initialize meta boxes
     */
    public static function init() {
        add_action( 'add_meta_boxes', [ __CLASS__, 'add_meta_boxes' ] );
        add_action( 'save_post', [ __CLASS__, 'save_meta' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ] );
        add_action( 'admin_notices', [ __CLASS__, 'show_term_processing_notices' ] );
    }
    
    /**
     * Add meta boxes
     */
    public static function add_meta_boxes() {
        $post_types = get_post_types( ['public' => true] );
        foreach ( $post_types as $post_type ) {
            add_meta_box(
                'custom-seo-meta',
                __( 'SEO Settings', 'custom-seo' ),
                [ __CLASS__, 'meta_box_callback' ],
                $post_type,
                'normal',
                'high'
            );
        }
    }
    
    /**
     * Meta box callback
     */
    public static function meta_box_callback( $post ) {
        wp_nonce_field( 'custom_seo_save_meta', 'custom_seo_nonce' );
        
        // Get current values
        $values = [];
        $fields = custom_seo_get_meta_fields();
        
        foreach ( $fields as $field ) {
            $values[$field] = get_post_meta( $post->ID, $field, true );
        }
        
        self::render_meta_box_html( $values );
    }
    
    /**
     * Render meta box HTML
     */
    private static function render_meta_box_html( $values ) {
        ?>
        <div id="custom-seo-tabs">
                        <ul class="custom-seo-tabs">
                <li><a href="#seo-general" class="active"><?php _e( 'General', 'custom-seo' ); ?></a></li>
                <li><a href="#seo-content"><?php _e( 'Content', 'custom-seo' ); ?></a></li>
                <li><a href="#seo-social"><?php _e( 'Social', 'custom-seo' ); ?></a></li>
                <li><a href="#seo-advanced"><?php _e( 'Advanced', 'custom-seo' ); ?></a></li>
                <li><a href="#seo-schema"><?php _e( 'Schema', 'custom-seo' ); ?></a></li>
            </ul>
            
            <div id="seo-general" class="custom-seo-tab-content">
                <table class="form-table">
                    <tr>
                        <th><label for="custom_seo_title"><?php _e( 'SEO Title', 'custom-seo' ); ?></label></th>
                        <td>
                            <input type="text" id="custom_seo_title" name="custom_seo_title" value="<?php echo esc_attr( $values['custom_seo_title'] ); ?>" class="large-text" />
                            <p class="description"><?php _e( 'Recommended length: 50-60 characters. Leave blank to use post title.', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="custom_seo_description"><?php _e( 'Meta Description', 'custom-seo' ); ?></label></th>
                        <td>
                            <textarea id="custom_seo_description" name="custom_seo_description" rows="3" class="large-text"><?php echo esc_textarea( $values['custom_seo_description'] ); ?></textarea>
                            <p class="description"><?php _e( 'Recommended length: 150-160 characters. Leave blank to use excerpt.', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="custom_focus_keyword"><?php _e( 'Focus Keyword', 'custom-seo' ); ?></label></th>
                        <td>
                            <input type="text" id="custom_focus_keyword" name="custom_focus_keyword" value="<?php echo esc_attr( $values['custom_focus_keyword'] ); ?>" class="large-text" />
                            <p class="description"><?php _e( 'Main keyword you want this page to rank for.', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="custom_canonical_url"><?php _e( 'Canonical URL', 'custom-seo' ); ?></label></th>
                        <td>
                            <input type="url" id="custom_canonical_url" name="custom_canonical_url" value="<?php echo esc_attr( $values['custom_canonical_url'] ); ?>" class="large-text" />
                            <p class="description"><?php _e( 'Leave blank to use default permalink.', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="custom_meta_keywords"><?php _e( 'Meta Keywords', 'custom-seo' ); ?></label></th>
                        <td>
                            <input type="text" id="custom_meta_keywords" name="custom_meta_keywords" value="<?php echo esc_attr( $values['custom_meta_keywords'] ); ?>" class="large-text" />
                            <p class="description"><?php _e( 'Comma-separated keywords (mostly ignored by search engines).', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div id="seo-content" class="custom-seo-tab-content">
                <h3><?php _e( 'Categories & Tags Management', 'custom-seo' ); ?></h3>
                <table class="form-table">
                    <tr>
                        <th><label for="custom_seo_categories"><?php _e( 'Categories', 'custom-seo' ); ?></label></th>
                        <td>
                            <input type="text" id="custom_seo_categories" name="custom_seo_categories" value="<?php echo esc_attr( $values['custom_seo_categories'] ); ?>" class="large-text" />
                            <p class="description"><?php _e( 'Comma-separated category names. Categories will be created automatically if they don\'t exist.', 'custom-seo' ); ?></p>
                            <div id="current-categories">
                                <strong><?php _e( 'Current Categories:', 'custom-seo' ); ?></strong>
                                <?php
                                $current_categories = get_the_category( $post->ID );
                                if ( ! empty( $current_categories ) ) {
                                    $cat_names = array_map( function( $cat ) { return $cat->name; }, $current_categories );
                                    echo '<span class="current-terms">' . esc_html( implode( ', ', $cat_names ) ) . '</span>';
                                } else {
                                    echo '<span class="no-terms">' . __( 'No categories assigned', 'custom-seo' ) . '</span>';
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="custom_seo_tags"><?php _e( 'Tags', 'custom-seo' ); ?></label></th>
                        <td>
                            <input type="text" id="custom_seo_tags" name="custom_seo_tags" value="<?php echo esc_attr( $values['custom_seo_tags'] ); ?>" class="large-text" />
                            <p class="description"><?php _e( 'Comma-separated tag names. Tags will be created automatically if they don\'t exist.', 'custom-seo' ); ?></p>
                            <div id="current-tags">
                                <strong><?php _e( 'Current Tags:', 'custom-seo' ); ?></strong>
                                <?php
                                $current_tags = get_the_tags( $post->ID );
                                if ( ! empty( $current_tags ) ) {
                                    $tag_names = array_map( function( $tag ) { return $tag->name; }, $current_tags );
                                    echo '<span class="current-terms">' . esc_html( implode( ', ', $tag_names ) ) . '</span>';
                                } else {
                                    echo '<span class="no-terms">' . __( 'No tags assigned', 'custom-seo' ) . '</span>';
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Management Options', 'custom-seo' ); ?></label></th>
                        <td>
                            <label>
                                <input type="checkbox" name="custom_seo_replace_categories" value="1" <?php checked( $values['custom_seo_replace_categories'], 1 ); ?> />
                                <?php _e( 'Replace existing categories (instead of adding to them)', 'custom-seo' ); ?>
                            </label>
                            <br />
                            <label>
                                <input type="checkbox" name="custom_seo_replace_tags" value="1" <?php checked( $values['custom_seo_replace_tags'], 1 ); ?> />
                                <?php _e( 'Replace existing tags (instead of adding to them)', 'custom-seo' ); ?>
                            </label>
                            <br />
                            <label>
                                <input type="checkbox" name="custom_seo_auto_create_terms" value="1" <?php checked( $values['custom_seo_auto_create_terms'], 1 ); ?> />
                                <?php _e( 'Automatically create categories and tags if they don\'t exist', 'custom-seo' ); ?>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div id="seo-social" class="custom-seo-tab-content">
                <h3><?php _e( 'Open Graph (Facebook)', 'custom-seo' ); ?></h3>
                <table class="form-table">
                    <tr>
                        <th><label for="custom_og_title"><?php _e( 'OG Title', 'custom-seo' ); ?></label></th>
                        <td>
                            <input type="text" id="custom_og_title" name="custom_og_title" value="<?php echo esc_attr( $values['custom_og_title'] ); ?>" class="large-text" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="custom_og_description"><?php _e( 'OG Description', 'custom-seo' ); ?></label></th>
                        <td>
                            <textarea id="custom_og_description" name="custom_og_description" rows="3" class="large-text"><?php echo esc_textarea( $values['custom_og_description'] ); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="custom_og_image_id"><?php _e( 'OG Image', 'custom-seo' ); ?></label></th>
                        <td>
                            <input type="hidden" id="custom_og_image_id" name="custom_og_image_id" value="<?php echo esc_attr( $values['custom_og_image_id'] ); ?>" />
                            <button type="button" class="button" id="custom_og_image_button"><?php _e( 'Select Image', 'custom-seo' ); ?></button>
                            <button type="button" class="button" id="custom_og_image_remove"><?php _e( 'Remove', 'custom-seo' ); ?></button>
                            <div id="custom_og_image_preview">
                                <?php if ( $values['custom_og_image_id'] ): ?>
                                    <?php echo wp_get_attachment_image( $values['custom_og_image_id'], 'medium' ); ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="custom_og_type"><?php _e( 'OG Type', 'custom-seo' ); ?></label></th>
                        <td>
                            <select id="custom_og_type" name="custom_og_type">
                                <option value="article" <?php selected( $values['custom_og_type'], 'article' ); ?>><?php _e( 'Article', 'custom-seo' ); ?></option>
                                <option value="website" <?php selected( $values['custom_og_type'], 'website' ); ?>><?php _e( 'Website', 'custom-seo' ); ?></option>
                                <option value="product" <?php selected( $values['custom_og_type'], 'product' ); ?>><?php _e( 'Product', 'custom-seo' ); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e( 'Twitter Card', 'custom-seo' ); ?></h3>
                <table class="form-table">
                    <tr>
                        <th><label for="custom_twitter_card"><?php _e( 'Card Type', 'custom-seo' ); ?></label></th>
                        <td>
                            <select id="custom_twitter_card" name="custom_twitter_card">
                                <option value="summary" <?php selected( $values['custom_twitter_card'], 'summary' ); ?>><?php _e( 'Summary', 'custom-seo' ); ?></option>
                                <option value="summary_large_image" <?php selected( $values['custom_twitter_card'], 'summary_large_image' ); ?>><?php _e( 'Summary Large Image', 'custom-seo' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="custom_twitter_title"><?php _e( 'Twitter Title', 'custom-seo' ); ?></label></th>
                        <td>
                            <input type="text" id="custom_twitter_title" name="custom_twitter_title" value="<?php echo esc_attr( $values['custom_twitter_title'] ); ?>" class="large-text" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="custom_twitter_description"><?php _e( 'Twitter Description', 'custom-seo' ); ?></label></th>
                        <td>
                            <textarea id="custom_twitter_description" name="custom_twitter_description" rows="3" class="large-text"><?php echo esc_textarea( $values['custom_twitter_description'] ); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="custom_twitter_site"><?php _e( 'Twitter Site', 'custom-seo' ); ?></label></th>
                        <td>
                            <input type="text" id="custom_twitter_site" name="custom_twitter_site" value="<?php echo esc_attr( $values['custom_twitter_site'] ); ?>" placeholder="@yoursite" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="custom_twitter_creator"><?php _e( 'Twitter Creator', 'custom-seo' ); ?></label></th>
                        <td>
                            <input type="text" id="custom_twitter_creator" name="custom_twitter_creator" value="<?php echo esc_attr( $values['custom_twitter_creator'] ); ?>" placeholder="@yourcreator" />
                        </td>
                    </tr>
                </table>
            </div>
            
            <div id="seo-advanced" class="custom-seo-tab-content">
                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Robots Meta', 'custom-seo' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="custom_noindex" value="1" <?php checked( $values['custom_noindex'], 1 ); ?> /> <?php _e( 'No Index', 'custom-seo' ); ?></label><br>
                            <label><input type="checkbox" name="custom_nofollow" value="1" <?php checked( $values['custom_nofollow'], 1 ); ?> /> <?php _e( 'No Follow', 'custom-seo' ); ?></label><br>
                            <label><input type="checkbox" name="custom_noarchive" value="1" <?php checked( $values['custom_noarchive'], 1 ); ?> /> <?php _e( 'No Archive', 'custom-seo' ); ?></label><br>
                            <label><input type="checkbox" name="custom_nosnippet" value="1" <?php checked( $values['custom_nosnippet'], 1 ); ?> /> <?php _e( 'No Snippet', 'custom-seo' ); ?></label><br>
                            <label><input type="checkbox" name="custom_noimageindex" value="1" <?php checked( $values['custom_noimageindex'], 1 ); ?> /> <?php _e( 'No Image Index', 'custom-seo' ); ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="custom_robots"><?php _e( 'Custom Robots', 'custom-seo' ); ?></label></th>
                        <td>
                            <input type="text" id="custom_robots" name="custom_robots" value="<?php echo esc_attr( $values['custom_robots'] ); ?>" class="large-text" />
                            <p class="description"><?php _e( 'Additional robots directives (e.g., max-snippet:160, max-image-preview:large)', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="custom_redirect_url"><?php _e( '301 Redirect', 'custom-seo' ); ?></label></th>
                        <td>
                            <input type="url" id="custom_redirect_url" name="custom_redirect_url" value="<?php echo esc_attr( $values['custom_redirect_url'] ); ?>" class="large-text" />
                            <select name="custom_redirect_type">
                                <option value="301" <?php selected( $values['custom_redirect_type'], 301 ); ?>><?php _e( '301 Permanent', 'custom-seo' ); ?></option>
                                <option value="302" <?php selected( $values['custom_redirect_type'], 302 ); ?>><?php _e( '302 Temporary', 'custom-seo' ); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div id="seo-schema" class="custom-seo-tab-content">
                <table class="form-table">
                    <tr>
                        <th><label for="custom_schema_type"><?php _e( 'Schema Type', 'custom-seo' ); ?></label></th>
                        <td>
                            <select id="custom_schema_type" name="custom_schema_type">
                                <option value="Article" <?php selected( $values['custom_schema_type'], 'Article' ); ?>><?php _e( 'Article', 'custom-seo' ); ?></option>
                                <option value="Product" <?php selected( $values['custom_schema_type'], 'Product' ); ?>><?php _e( 'Product', 'custom-seo' ); ?></option>
                                <option value="Event" <?php selected( $values['custom_schema_type'], 'Event' ); ?>><?php _e( 'Event', 'custom-seo' ); ?></option>
                                <option value="FAQ" <?php selected( $values['custom_schema_type'], 'FAQ' ); ?>><?php _e( 'FAQ', 'custom-seo' ); ?></option>
                                <option value="custom" <?php selected( $values['custom_schema_type'], 'custom' ); ?>><?php _e( 'Custom JSON-LD', 'custom-seo' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="custom_schema_data"><?php _e( 'Custom Schema JSON-LD', 'custom-seo' ); ?></label></th>
                        <td>
                            <textarea id="custom_schema_data" name="custom_schema_data" rows="10" class="large-text code"><?php echo esc_textarea( $values['custom_schema_data'] ); ?></textarea>
                            <p class="description"><?php _e( 'Enter valid JSON-LD markup. Only used if Schema Type is set to "Custom JSON-LD".', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <?php self::output_meta_box_styles(); ?>
        <?php self::output_meta_box_scripts(); ?>
        <?php
    }
    
    /**
     * Output meta box styles
     */
    private static function output_meta_box_styles() {
        ?>
        <style>
            .custom-seo-tab-nav { list-style: none; margin: 0; padding: 0; border-bottom: 1px solid #ccc; }
            .custom-seo-tab-nav li { display: inline-block; margin: 0; }
            .custom-seo-tab-nav a { display: block; padding: 10px 15px; text-decoration: none; background: #f1f1f1; border: 1px solid #ccc; border-bottom: none; margin-right: 5px; }
            .custom-seo-tab-nav a:hover, .custom-seo-tab-nav a.active { background: #fff; }
            .custom-seo-tab-content { display: none; padding: 20px 0; }
            .custom-seo-tab-content.active { display: block; }
            #custom_og_image_preview img { max-width: 200px; height: auto; margin-top: 10px; }
            #current-categories, #current-tags { margin-top: 10px; padding: 10px; background: #f9f9f9; border-left: 4px solid #0073aa; }
            .current-terms { color: #0073aa; font-weight: bold; }
            .no-terms { color: #666; font-style: italic; }
        </style>
        <?php
    }
    
    /**
     * Output meta box scripts
     */
    private static function output_meta_box_scripts() {
        ?>
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
                        title: '<?php _e( 'Select Image', 'custom-seo' ); ?>',
                        button: { text: '<?php _e( 'Use Image', 'custom-seo' ); ?>' },
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
    
    /**
     * Save meta box data
     */
    public static function save_meta( $post_id ) {
        if ( ! isset( $_POST['custom_seo_nonce'] ) || ! wp_verify_nonce( $_POST['custom_seo_nonce'], 'custom_seo_save_meta' ) ) {
            return;
        }
        
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        
        $fields = custom_seo_get_meta_fields();
        
        // Handle regular meta fields
        foreach ( $fields as $field ) {
            if ( isset( $_POST[$field] ) ) {
                $value = custom_seo_sanitize_field( $_POST[$field] );
                update_post_meta( $post_id, $field, $value );
            } else {
                delete_post_meta( $post_id, $field );
            }
        }
        
        // Handle categories and tags processing
        if ( isset( $_POST['custom_seo_categories'] ) || isset( $_POST['custom_seo_tags'] ) ) {
            $categories_string = isset( $_POST['custom_seo_categories'] ) ? sanitize_text_field( $_POST['custom_seo_categories'] ) : '';
            $tags_string = isset( $_POST['custom_seo_tags'] ) ? sanitize_text_field( $_POST['custom_seo_tags'] ) : '';
            $replace_categories = isset( $_POST['custom_seo_replace_categories'] ) && $_POST['custom_seo_replace_categories'] == '1';
            $replace_tags = isset( $_POST['custom_seo_replace_tags'] ) && $_POST['custom_seo_replace_tags'] == '1';
            $auto_create = isset( $_POST['custom_seo_auto_create_terms'] ) && $_POST['custom_seo_auto_create_terms'] == '1';
            
            // Process categories and tags
            $results = custom_seo_process_categories_and_tags( 
                $post_id, 
                $categories_string, 
                $tags_string, 
                $replace_categories, 
                $replace_tags, 
                $auto_create 
            );
            
            // Store processing results for admin notices
            if ( ! empty( $results['categories']['errors'] ) || ! empty( $results['tags']['errors'] ) ) {
                update_post_meta( $post_id, '_custom_seo_term_errors', $results );
            } else {
                delete_post_meta( $post_id, '_custom_seo_term_errors' );
            }
            
            // Store success count for admin notices
            $success_count = count( $results['categories']['success'] ) + count( $results['tags']['success'] );
            if ( $success_count > 0 ) {
                update_post_meta( $post_id, '_custom_seo_term_success_count', $success_count );
            }
        }
    }
    
    /**
     * Enqueue admin scripts
     */
    public static function enqueue_scripts( $hook ) {
        if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
            wp_enqueue_media();
        }
    }
    
    /**
     * Show admin notices for category/tag processing results
     */
    public static function show_term_processing_notices() {
        global $post;
        
        if ( ! $post || ! is_admin() ) {
            return;
        }
        
        $screen = get_current_screen();
        if ( ! $screen || $screen->base !== 'post' ) {
            return;
        }
        
        // Check for success messages
        $success_count = get_post_meta( $post->ID, '_custom_seo_term_success_count', true );
        if ( $success_count ) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>' . sprintf( 
                _n( 
                    'Successfully processed %d category/tag.', 
                    'Successfully processed %d categories/tags.', 
                    $success_count, 
                    'custom-seo' 
                ), 
                $success_count 
            ) . '</p>';
            echo '</div>';
            delete_post_meta( $post->ID, '_custom_seo_term_success_count' );
        }
        
        // Check for error messages
        $errors = get_post_meta( $post->ID, '_custom_seo_term_errors', true );
        if ( $errors && ( ! empty( $errors['categories']['errors'] ) || ! empty( $errors['tags']['errors'] ) ) ) {
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p><strong>' . __( 'Category/Tag Processing Errors:', 'custom-seo' ) . '</strong></p>';
            echo '<ul>';
            
            foreach ( $errors['categories']['errors'] as $error ) {
                echo '<li>' . esc_html( $error ) . '</li>';
            }
            
            foreach ( $errors['tags']['errors'] as $error ) {
                echo '<li>' . esc_html( $error ) . '</li>';
            }
            
            echo '</ul>';
            echo '</div>';
            delete_post_meta( $post->ID, '_custom_seo_term_errors' );
        }
    }
}

// Initialize meta boxes
Custom_SEO_Meta_Boxes::init();