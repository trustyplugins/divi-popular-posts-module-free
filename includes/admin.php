<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class POPULAR_DIVI_MODULE_ADMIN_ACTION
{
    private $option_name = 'tpdivi_post_types'; // Option name to store data
    public function __construct()
    {
        add_action('admin_menu', array($this, 'TP_POPULAR_DIVI_MENU'));
        // Hook into admin initialization to register settings
        add_action('admin_init', [$this, 'tp_register_settings']);
        // Hook into admin-post.php to handle reset
        add_action('admin_post_tp_divi_reset_records', [$this, 'handle_reset_records']);
        add_action('admin_enqueue_scripts', [$this, 'tp_enqueue_admin_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'tp_enqueue_plugin_scripts']);
    }
    public function tp_enqueue_admin_scripts()
    {

        wp_enqueue_style('tp-analytics-react-datepicker-style', POPULAR_DIVI_URL  . 'styles/react-datepicker.css', array(), POPULAR_DIVI_VERSION, 'all');
        wp_enqueue_style('tp-analytics-react-style', POPULAR_DIVI_URL  . 'styles/admin.css', array(), POPULAR_DIVI_VERSION, 'all');
        wp_enqueue_script('tp-analytics-admin-script', POPULAR_DIVI_URL . 'js/admin.js', array('jquery'), '1.0.0', true);
        wp_localize_script('tp-analytics-admin-script', 'tpdivi_analytics', array(
            'site_url' => get_site_url(),
            'assets_url' => POPULAR_DIVI_URL . 'assets',
            'nonce' => wp_create_nonce('wp_rest') 
        ));
        wp_enqueue_script(
            'tp-analytics-react-script',
            POPULAR_DIVI_URL  . 'js/index.js',
            ['react', 'react-dom'], // Ensure React and ReactDOM are loaded before
            POPULAR_DIVI_VERSION,
            true // Load in the footer
        );
    }

    public function tp_enqueue_plugin_scripts()
    {
        // Enqueue D3.js and your custom script
        wp_enqueue_script('tp-analytics-d3-script', POPULAR_DIVI_URL . 'js/d3.js', array('jquery'), '1.0.0', true);
        // wp_enqueue_style('tp-analytics-react-front-style', POPULAR_DIVI_URL  . 'styles/style.css', array(), POPULAR_DIVI_VERSION, 'all');
        wp_enqueue_script('tp-analytics-front-script', POPULAR_DIVI_URL . 'js/script.js', array('jquery'), '1.0.0', true);
        wp_localize_script('tp-analytics-front-script', 'tpdivi_analytics', array(
            'site_url' => get_site_url(),
            'assets_url' => POPULAR_DIVI_URL . 'assets',
            'nonce' => wp_create_nonce('wp_rest') 
        ));
    }

    public function is_theme_activate($target)
    {
        $theme = wp_get_theme();
        if ($theme->name == $target || stripos($theme->parent_theme, $target) !== false) {
            return true;
        }
        if (apply_filters('divi_ghoster_ghosted_theme', '') == $target) {
            return true;
        }
        return false;
    }

    public function TP_POPULAR_DIVI_MENU()
    {
        // Add a top-level menu
        if ($this->is_theme_activate('Divi')) {
            add_menu_page(
                'Divi Popular Posts',                  // Page title
                'Popular Posts',                       // Menu title
                'manage_options',                      // Capability required
                'tpdivi-popular-posts',               // Menu slug
                [$this, 'render_settings_page'],       // Callback function
                'dashicons-chart-bar',                 // Icon URL or Dashicons class
                60                                     // Position in the menu
            );
            // Add a submenu for 'Analytics'
            add_submenu_page(
                'tpdivi-popular-posts',               // Parent slug
                'Popular Posts Analytics',            // Page title
                'Analytics',                          // Menu title
                'manage_options',                     // Capability required
                'tpdivi-popular-posts-analytics',       // Menu slug
                [$this, 'render_analytics_page']      // Callback function
            );
        }
    }


    // Render the settings page
    public function render_settings_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html('You do not have sufficient permissions to access this page.', 'popular-posts-for-divi-with-charts'));
        }

        // Fetch the saved settings
        $saved_post_types = get_option($this->option_name, []);
        $saved_dropdown_value = get_option('tpdivi_logic_setting', ''); // Default value
?>
        <div class="wrap">
            <h1>Divi Popular Posts - Settings</h1>
            <form method="post" action="options.php">
                <?php
                // Output nonce and action URL
                settings_fields('tpdivi_settings_group');
                ?>
                <div class="tp-divi-settings">
                    <h2>Select Post Types</h2>
                    <div class="tp-divi-field-group">
                        <?php
                        // Render the checkboxes for post types
                        $post_types = get_post_types(['public' => true], 'objects');
                        foreach ($post_types as $post_type) {
                            if ($post_type->name == 'post') {
                                $checked = in_array($post_type->name, $saved_post_types) ? 'checked' : '';
                        ?>
                                <div class="tp-divi-field">
                                    <label>
                                        <input type="checkbox" name="<?php echo esc_attr($this->option_name); ?>[]" value="<?php echo esc_attr($post_type->name); ?>" <?php echo esc_attr($checked); ?>>
                                        <?php echo esc_html($post_type->label); ?>
                                    </label>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                    <h2>View Count Logic</h2>
                    <div class="tp-divi-field">
                        <label for="tpdivi_logic_setting"><b>Select an Option:</b></label><br />
                        <select id="tpdivi_logic_setting" name="tpdivi_logic_setting" style="margin-top: 10px;">
                            <option value="default" <?php selected($saved_dropdown_value, 'default'); ?>>Default</option>
                            <option value="cache" <?php selected($saved_dropdown_value, 'cache'); ?>>Cache mechanism</option>
                        </select>
                        <div class="label-desc" style="margin-top: 10px;">
                            By <b>default</b>, Divi Popular Posts stores the post details data in database every single visit only of your selected post types.<br />
                            Instead of incrementing the view count on every visit, we will use a <b>cache mechanism</b> to store the count and only update it periodically (after 30 minutes). This reduces load on the database for high-traffic sites.


                        </div>
                    </div>
                </div>
                <?php
                // Submit button
                submit_button();
                ?>
            </form>
            <hr />
            <div class="tp-divi-reset-section">
                <h2>Reset Post View Counts</h2>
                <p>This action will erase all post view counts from the database. Are you sure you want to proceed?</p>
                <button id="reset-records" class="button button-danger">Reset Records</button>
            </div>
            <?php
            //phpcs:ignore
            if (isset($_GET['reset_success']) && $_GET['reset_success'] == '1') {
                echo '<div class="updated notice"><p>All post view counts have been reset successfully.</p></div>';
            }
            ?>
        </div>

        </div>

    <?php
    }

    // Render the analytics page
    public function render_analytics_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html('You do not have sufficient permissions to access this page.', 'popular-posts-for-divi-with-charts'));
        }

    ?>
        <div class="wrap">
            <div id="tp-analytics-container"></div>
        </div>
<?php
    }


    // Handle the reset action
    public function handle_reset_records()
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to perform this action.', 'popular-posts-for-divi-with-charts'));
        }

        // Check the nonce for security (optional)
        check_admin_referer();

        global $wpdb;
        $post_views_table = $wpdb->prefix . 'post_views_tp'; // Replace with your actual table name

        // Safely escape the table name and execute the query
        $wpdb->query("TRUNCATE TABLE " . esc_sql($post_views_table));

        // Redirect back to the settings page with a success message
        wp_redirect(add_query_arg('reset_success', '1', admin_url('admin.php?page=divi-popular-posts-tp')));
        exit;
    }


   /**
 * Register plugin settings.
 */
public function tp_register_settings() {
    if ( $this->is_theme_activate( 'Divi' ) ) {
        // Register the main option with sanitization.
        register_setting(
            'tpdivi_settings_group',           // Settings group.
            $this->option_name,                // Option name.
            array(
                'type'              => 'array',
                'sanitize_callback' => array( $this, 'sanitize_settings' ),
            )
        );

        // Register another setting with default sanitization.
        register_setting(
            'tpdivi_settings_group',
            'tpdivi_logic_setting',
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            )
        );
    }
}

/**
 * Sanitize the plugin settings.
 *
 * @param mixed $input The input value.
 * @return array Sanitized array.
 */
public function sanitize_settings( $input ) {
    if ( ! is_array( $input ) ) {
        return array();
    }

    return array_map( 'sanitize_text_field', $input );
}


    // Render the checkbox field for post types
    public function render_post_types_field()
    {
        // Get all post types
        $post_types = get_post_types(['public' => true], 'objects');
        // Get saved options
        $saved_post_types = get_option($this->option_name, []);

        foreach ($post_types as $post_type) {
            $checked = in_array($post_type->name, $saved_post_types) ? 'checked' : '';
            echo '<label>';
            echo '<input type="checkbox" name="' . esc_attr($this->option_name) . '[]" value="' . esc_attr($post_type->name) . '" ' . esc_attr($checked) . '>';
            echo esc_html($post_type->label);
            echo '</label><br>';
        }
    }
}


new POPULAR_DIVI_MODULE_ADMIN_ACTION();
