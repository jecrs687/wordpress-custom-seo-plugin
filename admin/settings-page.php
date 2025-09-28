<?php
/**
 * Custom SEO Settings Page
 * 
 * @package Custom_SEO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom SEO Settings Page Class
 */
class Custom_SEO_Settings_Page {
    
    /**
     * Initialize settings page
     */
    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'add_admin_menu' ] );
        add_action( 'admin_init', [ __CLASS__, 'settings_init' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ] );
        add_action( 'wp_ajax_custom_seo_flush_rewrite_rules', [ __CLASS__, 'ajax_flush_rewrite_rules' ] );
    }
    
    /**
     * Add admin menu
     */
    public static function add_admin_menu() {
        // Add to Settings menu
        $hook = add_options_page(
            __( 'Custom SEO Settings', 'custom-seo' ),
            __( 'Custom SEO', 'custom-seo' ),
            'manage_options',
            'custom-seo-settings',
            [ __CLASS__, 'settings_page' ]
        );
        
        // Also add a top-level menu for easier access (temporary)
        add_menu_page(
            __( 'Custom SEO', 'custom-seo' ),
            __( 'Custom SEO', 'custom-seo' ),
            'manage_options',
            'custom-seo-main',
            [ __CLASS__, 'settings_page' ],
            'dashicons-search',
            80
        );
        
        // Debug: Log the hook suffix
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'Custom SEO Settings page hook: ' . $hook );
        }
    }
    
    /**
     * Initialize settings
     */
    public static function settings_init() {
        $settings = [
            'custom_seo_google_verification',
            'custom_seo_bing_verification',
            'custom_seo_pinterest_verification',
            'custom_seo_google_analytics',
            'custom_seo_gtag_id',
            'custom_seo_facebook_app_id',
            'custom_seo_default_og_image',
            'custom_seo_twitter_username',
            'custom_seo_organization_name',
            'custom_seo_organization_logo'
        ];
        
        foreach ( $settings as $setting ) {
            register_setting( 'custom_seo_settings', $setting );
        }
    }
    
    /**
     * Settings page callback
     */
    public static function settings_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'custom-seo' ) );
        }
        ?>
        <div class="wrap">
            <h1><?php _e( 'Custom SEO Settings', 'custom-seo' ); ?></h1>
            
            <div class="notice notice-success">
                <p><strong><?php _e( 'Success!', 'custom-seo' ); ?></strong> <?php _e( 'You have successfully accessed the Custom SEO settings page.', 'custom-seo' ); ?></p>
            </div>
            <form method="post" action="options.php">
                <?php settings_fields( 'custom_seo_settings' ); ?>
                <?php do_settings_sections( 'custom_seo_settings' ); ?>
                
                <h2><?php _e( 'Search Engine Verification', 'custom-seo' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Google Search Console', 'custom-seo' ); ?></th>
                        <td>
                            <input type="text" name="custom_seo_google_verification" value="<?php echo esc_attr( get_option( 'custom_seo_google_verification' ) ); ?>" class="regular-text" />
                            <p class="description"><?php _e( 'Enter your Google Search Console verification code', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Bing Webmaster Tools', 'custom-seo' ); ?></th>
                        <td>
                            <input type="text" name="custom_seo_bing_verification" value="<?php echo esc_attr( get_option( 'custom_seo_bing_verification' ) ); ?>" class="regular-text" />
                            <p class="description"><?php _e( 'Enter your Bing Webmaster Tools verification code', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Pinterest', 'custom-seo' ); ?></th>
                        <td>
                            <input type="text" name="custom_seo_pinterest_verification" value="<?php echo esc_attr( get_option( 'custom_seo_pinterest_verification' ) ); ?>" class="regular-text" />
                            <p class="description"><?php _e( 'Enter your Pinterest verification code', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                </table>
                
                <h2><?php _e( 'Analytics & Tracking', 'custom-seo' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Google Analytics 4 ID', 'custom-seo' ); ?></th>
                        <td>
                            <input type="text" name="custom_seo_gtag_id" value="<?php echo esc_attr( get_option( 'custom_seo_gtag_id' ) ); ?>" class="regular-text" placeholder="G-XXXXXXXXXX" />
                            <p class="description"><?php _e( 'Enter your Google Analytics 4 Measurement ID', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Google Analytics Code', 'custom-seo' ); ?></th>
                        <td>
                            <textarea name="custom_seo_google_analytics" rows="5" class="large-text code"><?php echo esc_textarea( get_option( 'custom_seo_google_analytics' ) ); ?></textarea>
                            <p class="description"><?php _e( 'Enter custom Google Analytics code (optional if using GA4 ID above)', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                </table>
                
                <h2><?php _e( 'Social Media', 'custom-seo' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Facebook App ID', 'custom-seo' ); ?></th>
                        <td>
                            <input type="text" name="custom_seo_facebook_app_id" value="<?php echo esc_attr( get_option( 'custom_seo_facebook_app_id' ) ); ?>" class="regular-text" />
                            <p class="description"><?php _e( 'Facebook App ID for social sharing', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Twitter Username', 'custom-seo' ); ?></th>
                        <td>
                            <input type="text" name="custom_seo_twitter_username" value="<?php echo esc_attr( get_option( 'custom_seo_twitter_username' ) ); ?>" class="regular-text" placeholder="@yourusername" />
                            <p class="description"><?php _e( 'Default Twitter username for cards', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Default Open Graph Image', 'custom-seo' ); ?></th>
                        <td>
                            <input type="hidden" name="custom_seo_default_og_image" value="<?php echo esc_attr( get_option( 'custom_seo_default_og_image' ) ); ?>" />
                            <button type="button" class="button" id="custom_default_og_image_button"><?php _e( 'Select Default Image', 'custom-seo' ); ?></button>
                            <button type="button" class="button" id="custom_default_og_image_remove"><?php _e( 'Remove', 'custom-seo' ); ?></button>
                            <div id="custom_default_og_image_preview">
                                <?php 
                                $default_og_image = get_option( 'custom_seo_default_og_image' );
                                if ( $default_og_image ): ?>
                                    <?php echo wp_get_attachment_image( $default_og_image, 'medium' ); ?>
                                <?php endif; ?>
                            </div>
                            <p class="description"><?php _e( 'Default image for Open Graph when no specific image is set', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                </table>
                
                <h2><?php _e( 'Organization Schema', 'custom-seo' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Organization Name', 'custom-seo' ); ?></th>
                        <td>
                            <input type="text" name="custom_seo_organization_name" value="<?php echo esc_attr( get_option( 'custom_seo_organization_name' ) ); ?>" class="regular-text" />
                            <p class="description"><?php _e( 'Your organization/company name for schema markup', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Organization Logo', 'custom-seo' ); ?></th>
                        <td>
                            <input type="hidden" name="custom_seo_organization_logo" value="<?php echo esc_attr( get_option( 'custom_seo_organization_logo' ) ); ?>" />
                            <button type="button" class="button" id="custom_org_logo_button"><?php _e( 'Select Logo', 'custom-seo' ); ?></button>
                            <button type="button" class="button" id="custom_org_logo_remove"><?php _e( 'Remove', 'custom-seo' ); ?></button>
                            <div id="custom_org_logo_preview">
                                <?php 
                                $org_logo = get_option( 'custom_seo_organization_logo' );
                                if ( $org_logo ): ?>
                                    <?php echo wp_get_attachment_image( $org_logo, 'medium' ); ?>
                                <?php endif; ?>
                            </div>
                            <p class="description"><?php _e( 'Your organization logo for schema markup', 'custom-seo' ); ?></p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <h2><?php _e( 'Sitemap URLs', 'custom-seo' ); ?></h2>
            <p><?php _e( 'Your XML sitemaps are available at:', 'custom-seo' ); ?></p>
            
            <div class="notice notice-info">
                <p><strong><?php _e( 'Note:', 'custom-seo' ); ?></strong> <?php _e( 'If sitemap links don\'t work, try flushing permalinks by going to Settings → Permalinks and clicking "Save Changes".', 'custom-seo' ); ?></p>
            </div>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e( 'Main Sitemap Index', 'custom-seo' ); ?></th>
                    <td>
                        <a href="<?php echo home_url( '/sitemap.xml' ); ?>" target="_blank" class="button"><?php _e( 'View Sitemap', 'custom-seo' ); ?></a>
                        <code><?php echo home_url( '/sitemap.xml' ); ?></code>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e( 'Individual Sitemaps', 'custom-seo' ); ?></th>
                    <td>
                        <?php
                        $post_types = get_post_types( [ 'public' => true ], 'objects' );
                        foreach ( $post_types as $post_type ) {
                            $sitemap_url = home_url( '/sitemap-' . $post_type->name . '.xml' );
                            echo '<p>';
                            echo '<a href="' . esc_url( $sitemap_url ) . '" target="_blank" class="button button-small">' . __( 'View', 'custom-seo' ) . '</a> ';
                            echo '<code>' . esc_html( $sitemap_url ) . '</code> ';
                            echo '<span class="description">(' . esc_html( $post_type->labels->name ) . ')</span>';
                            echo '</p>';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e( 'Troubleshooting', 'custom-seo' ); ?></th>
                    <td>
                        <button type="button" class="button" id="flush-rewrite-rules"><?php _e( 'Flush Rewrite Rules', 'custom-seo' ); ?></button>
                        <p class="description"><?php _e( 'Click this button if your sitemaps are not working. This will refresh the URL rewrite rules.', 'custom-seo' ); ?></p>
                    </td>
                </tr>
            </table>
            
            <h2><?php _e( 'Usage', 'custom-seo' ); ?></h2>
            <p><?php _e( 'To display breadcrumbs in your theme, add this code to your template files:', 'custom-seo' ); ?></p>
            <pre><code>&lt;?php if ( function_exists( 'custom_seo_breadcrumbs' ) ) custom_seo_breadcrumbs(); ?&gt;</code></pre>
        </div>
        
        <?php self::output_settings_scripts(); ?>
        <?php
    }
    
    /**
     * Output settings page scripts
     */
    private static function output_settings_scripts() {
        ?>
        <script>
            jQuery(document).ready(function($) {
                // Check if wp.media is available
                if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                    console.error('Custom SEO: wp.media is not available. Make sure WordPress media scripts are loaded.');
                    return;
                }
                
                // Default OG Image uploader
                var defaultOgUploader;
                $('#custom_default_og_image_button').on('click', function(e) {
                    e.preventDefault();
                    
                    if (defaultOgUploader) {
                        defaultOgUploader.open();
                        return;
                    }
                    
                    try {
                        defaultOgUploader = wp.media({
                            title: '<?php _e( 'Select Default OG Image', 'custom-seo' ); ?>',
                            button: { text: '<?php _e( 'Use Image', 'custom-seo' ); ?>' },
                            multiple: false,
                            library: { type: 'image' }
                        });
                        
                        defaultOgUploader.on('select', function() {
                            var attachment = defaultOgUploader.state().get('selection').first().toJSON();
                            $('input[name="custom_seo_default_og_image"]').val(attachment.id);
                            $('#custom_default_og_image_preview').html('<img src="' + attachment.url + '" alt="" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px;" />');
                            console.log('Custom SEO: Default OG image selected - ID: ' + attachment.id);
                        });
                        
                        defaultOgUploader.open();
                    } catch (error) {
                        console.error('Custom SEO: Error creating media uploader:', error);
                        alert('<?php _e( 'Error opening media uploader. Please refresh the page and try again.', 'custom-seo' ); ?>');
                    }
                });
                
                $('#custom_default_og_image_remove').on('click', function(e) {
                    e.preventDefault();
                    if (confirm('<?php _e( 'Are you sure you want to remove the default OG image?', 'custom-seo' ); ?>')) {
                        $('input[name="custom_seo_default_og_image"]').val('');
                        $('#custom_default_og_image_preview').html('');
                        console.log('Custom SEO: Default OG image removed');
                    }
                });
                
                // Organization logo uploader
                var orgLogoUploader;
                $('#custom_org_logo_button').on('click', function(e) {
                    e.preventDefault();
                    
                    if (orgLogoUploader) {
                        orgLogoUploader.open();
                        return;
                    }
                    
                    try {
                        orgLogoUploader = wp.media({
                            title: '<?php _e( 'Select Organization Logo', 'custom-seo' ); ?>',
                            button: { text: '<?php _e( 'Use Image', 'custom-seo' ); ?>' },
                            multiple: false,
                            library: { type: 'image' }
                        });
                        
                        orgLogoUploader.on('select', function() {
                            var attachment = orgLogoUploader.state().get('selection').first().toJSON();
                            $('input[name="custom_seo_organization_logo"]').val(attachment.id);
                            $('#custom_org_logo_preview').html('<img src="' + attachment.url + '" alt="" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px;" />');
                            console.log('Custom SEO: Organization logo selected - ID: ' + attachment.id);
                        });
                        
                        orgLogoUploader.open();
                    } catch (error) {
                        console.error('Custom SEO: Error creating logo uploader:', error);
                        alert('<?php _e( 'Error opening media uploader. Please refresh the page and try again.', 'custom-seo' ); ?>');
                    }
                });
                
                $('#custom_org_logo_remove').on('click', function(e) {
                    e.preventDefault();
                    if (confirm('<?php _e( 'Are you sure you want to remove the organization logo?', 'custom-seo' ); ?>')) {
                        $('input[name="custom_seo_organization_logo"]').val('');
                        $('#custom_org_logo_preview').html('');
                        console.log('Custom SEO: Organization logo removed');
                    }
                });
                
                // Flush rewrite rules button
                $('#flush-rewrite-rules').on('click', function(e) {
                    e.preventDefault();
                    var button = $(this);
                    var originalText = button.text();
                    
                    button.text('<?php _e( 'Flushing...', 'custom-seo' ); ?>').prop('disabled', true);
                    
                    $.post(ajaxurl, {
                        action: 'custom_seo_flush_rewrite_rules',
                        nonce: '<?php echo wp_create_nonce( 'custom_seo_nonce' ); ?>'
                    }, function(response) {
                        if (response.success) {
                            button.text('<?php _e( 'Success!', 'custom-seo' ); ?>');
                            setTimeout(function() {
                                button.text(originalText).prop('disabled', false);
                            }, 2000);
                        } else {
                            button.text('<?php _e( 'Error', 'custom-seo' ); ?>');
                            setTimeout(function() {
                                button.text(originalText).prop('disabled', false);
                            }, 2000);
                        }
                    }).fail(function() {
                        button.text('<?php _e( 'Error', 'custom-seo' ); ?>');
                        setTimeout(function() {
                            button.text(originalText).prop('disabled', false);
                        }, 2000);
                    });
                });
                
                // Debug info
                console.log('Custom SEO: Media uploader scripts initialized');
            });
        </script>
        <?php
    }
    
    /**
     * Enqueue admin scripts for settings page
     */
    public static function enqueue_scripts( $hook ) {
        // More flexible hook detection - check for various SEO menu locations
        if ( strpos( $hook, 'custom-seo' ) === false && 
             strpos( $hook, 'seo-options' ) === false &&
             $hook !== 'settings_page_custom_seo_options' &&
             $hook !== 'toplevel_page_custom_seo_options' ) {
            return;
        }

        // Make sure WordPress media library is available
        wp_enqueue_media();
        
        echo '<script>console.log("SEO Settings: admin_enqueue_scripts called on hook:", "' . $hook . '");</script>';
    }

    /**
     * AJAX handler for flushing rewrite rules
     */
    public static function ajax_flush_rewrite_rules() {
        // Check for proper capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized', 'Error', array( 'response' => 403 ) );
        }

        // Verify nonce
        if ( ! check_ajax_referer( 'custom_seo_nonce', 'nonce', false ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Send success response
        wp_send_json_success( 'Rewrite rules flushed successfully' );
    }
}

// Initialize settings page
Custom_SEO_Settings_Page::init();