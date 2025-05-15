<?php
/*
Plugin Name: Popular Posts for Divi - Lite
Plugin URI:  https://trustyplugins.com
Description: A popular posts module for Divi with analytics dashboard and charts.
Version:     1.0
Author:      Trusty Plugins
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: popular-posts-for-divi-with-charts
Domain Path: /languages

Divi Popular Posts For Divi is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

You should have received a copy of the GNU General Public License
along with Popular Posts for Divi - Lite. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */
if (! defined('ABSPATH')) exit; // Exit if accessed directly 

define('POPULAR_DIVI_VERSION', '1.0');
define('POPULAR_DIVI_PATH', plugin_dir_path(__FILE__));
define('POPULAR_DIVI_URL', plugin_dir_url(__FILE__));
register_activation_hook(__FILE__, array('POPULAR_DIVI_MODULE_ACTION', 'POPULAR_DIVI_activate_plugin'));


if ( ! function_exists( 'POPULAR_DIVI_initialize_extension' ) ):
    /**
     * Creates the extension's main class instance.
     *
     * @since 1.0.0
     */
    function POPULAR_DIVI_initialize_extension() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/DiviPopularPosts.php';
    }
    add_action( 'divi_extensions_init', 'POPULAR_DIVI_initialize_extension' );
    endif;


class POPULAR_DIVI_MODULE_ACTION
{
    public function __construct()
    {
        require_once POPULAR_DIVI_PATH . 'includes/admin.php';
        require_once POPULAR_DIVI_PATH . 'includes/functions.php';
        add_action( 'admin_init', array( $this, 'is_divi_exist' ) );
       
    }
    public static function POPULAR_DIVI_activate_plugin() {
        self::create_post_views_table();
    }
    public function is_divi_exist()
    {
        if (!self::is_theme_activate('Divi')) {
            add_action('admin_notices', array($this, 'admin_notice_divi_not_exist'));
        }
    }
    public static function is_theme_activate($target)
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
    public static function create_post_views_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'post_views_tp';
    
        // Check if the table already exists
        // phpcs:ignore
        if ($wpdb->get_var("SHOW TABLES LIKE $wpdb->prefix . 'post_views_tp'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();
    
            $sql = "CREATE TABLE $table_name (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                post_id BIGINT UNSIGNED NOT NULL,
                view_count INT DEFAULT 1,
                view_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                user_ip VARCHAR(45),
                referrer TEXT,
                user_agent TEXT,
                post_type TEXT,
                UNIQUE(post_id, view_date)
            ) $charset_collate;";
    
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
    
    public function admin_notice_divi_not_exist()
    {
        $message = esc_html__(
            'Popular Posts Module For Divi requires Divi Theme to be installed and activated.',
            'popular-posts-for-divi-with-charts'
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', esc_html($message));
        
    }
}
new POPULAR_DIVI_MODULE_ACTION();
