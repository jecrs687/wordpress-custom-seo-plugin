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
    }
    
    /**
     * Add admin menu
     */
    public static function add_admin_menu() {
        add_options_page(
            __( 'Custom SEO Settings', 'custom-seo' ),
            __( 'Custom SEO', 'custom-seo' ),
            'manage_options',
            'custom-seo-settings',
            [ __CLASS__, 'settings_page' ]
        );
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
        ?>
        <div class="wrap">
            <h1><?php _e( 'Custom SEO Settings', 'custom-seo' ); ?></h1>
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
            <ul>
                <li><a href="<?php echo home_url( '/sitemap.xml' ); ?>" target="_blank"><?php echo home_url( '/sitemap.xml' ); ?></a> (<?php _e( 'Main sitemap index', 'custom-seo' ); ?>)</li>
            </ul>
            
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
                // Default OG Image uploader
                var defaultOgUploader;
                $('#custom_default_og_image_button').on('click', function(e) {
                    e.preventDefault();
                    if (defaultOgUploader) {
                        defaultOgUploader.open();
                        return;
                    }
                    defaultOgUploader = wp.media({
                        title: '<?php _e( 'Select Default OG Image', 'custom-seo' ); ?>',
                        button: { text: '<?php _e( 'Use Image', 'custom-seo' ); ?>' },
                        multiple: false
                    });
                    defaultOgUploader.on('select', function() {
                        var attachment = defaultOgUploader.state().get('selection').first().toJSON();
                        $('input[name="custom_seo_default_og_image"]').val(attachment.id);
                        $('#custom_default_og_image_preview').html('<img src="' + attachment.url + '" alt="" style="max-width: 200px; height: auto;" />');
                    });
                    defaultOgUploader.open();
                });
                
                $('#custom_default_og_image_remove').on('click', function(e) {
                    e.preventDefault();
                    $('input[name="custom_seo_default_og_image"]').val('');
                    $('#custom_default_og_image_preview').html('');
                });
                
                // Organization logo uploader
                var orgLogoUploader;
                $('#custom_org_logo_button').on('click', function(e) {
                    e.preventDefault();
                    if (orgLogoUploader) {
                        orgLogoUploader.open();
                        return;
                    }
                    orgLogoUploader = wp.media({
                        title: '<?php _e( 'Select Organization Logo', 'custom-seo' ); ?>',
                        button: { text: '<?php _e( 'Use Image', 'custom-seo' ); ?>' },
                        multiple: false
                    });
                    orgLogoUploader.on('select', function() {
                        var attachment = orgLogoUploader.state().get('selection').first().toJSON();
                        $('input[name="custom_seo_organization_logo"]').val(attachment.id);
                        $('#custom_org_logo_preview').html('<img src="' + attachment.url + '" alt="" style="max-width: 200px; height: auto;" />');
                    });
                    orgLogoUploader.open();
                });
                
                $('#custom_org_logo_remove').on('click', function(e) {
                    e.preventDefault();
                    $('input[name="custom_seo_organization_logo"]').val('');
                    $('#custom_org_logo_preview').html('');
                });
            });
        </script>
        <?php
    }
    
    /**
     * Enqueue settings page scripts
     */
    public static function enqueue_scripts( $hook ) {
        if ( 'settings_page_custom-seo-settings' === $hook ) {
            wp_enqueue_media();
        }
    }
}

// Initialize settings page
Custom_SEO_Settings_Page::init();