<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class POPULAR_DIVI_MODULE_FUNCTIONS
{
    public function __construct()
    {
        if ($this->is_theme_activate('Divi')) {
            add_action('wp', array($this, 'track_post_views'));
        }
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
    public function track_post_views()
    {
        if (is_singular()) {
            global $post, $wpdb;

            $saved_post_types = get_option('tpdivi_post_types', []);
            $view_tracking_method = get_option('tpdivi_logic_setting', 'default'); // Default: Every Single Visit
            $post_id = $post->ID;
            // phpcs:ignore
            $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '';
            // phpcs:ignore
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '';
            // phpcs:ignore
            $referrer = isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : '';
            $post_type = $post->post_type;
            $table_name = $wpdb->prefix . 'post_views_tp';
            if (in_array($post_type, $saved_post_types)) {
                if ($view_tracking_method == 'default') {
                    // Default: Count every single visit
                    // phpcs:ignore
                    $wpdb->insert($table_name, [
                        'post_id' => $post_id,
                        'user_ip' => $ip,
                        'referrer' => $referrer,
                        'user_agent' => $user_agent,
                        'post_type' => 'post',
                    ], [
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                    ]);
                } else {
                    // Cache Mechanism
                    $unique_visitor_key = 'tpdivi_post_views_' . $post_id . '_' . md5($ip . $user_agent);
                    $cookie_key = 'tpdivi_viewed_' . $post_id;

                    // Check if this user has already been tracked
                    $already_viewed = get_transient($unique_visitor_key) || isset($_COOKIE[$cookie_key]);

                    if (!$already_viewed) {
                        // phpcs:ignore
                        $wpdb->insert($table_name, [
                            'post_id' => $post_id,
                            'user_ip' => $ip,
                            'referrer' => $referrer,
                            'user_agent' => $user_agent,
                            'post_type' => 'post',
                        ], [
                            '%d',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                        ]);

                        // Store transient for 30 minutes
                        set_transient($unique_visitor_key, true, 30 * MINUTE_IN_SECONDS);

                        // Set a cookie to prevent double-counting within 30 minutes
                        setcookie($cookie_key, 'viewed', time() + 30 * 60, '/'); // 30 minutes
                    }
                }
            }
        }
    }
}

new POPULAR_DIVI_MODULE_FUNCTIONS();

add_action('rest_api_init', function () {
    register_rest_route('tp/v1', '/render/', [
        'methods'  => 'POST',
        'callback' => 'tpdivi_render_popular_posts',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
        'args'     => [
            'attributes' => [
                'required' => true,
                'validate_callback' => function ($param) {
                    return is_array($param);
                },
            ],
        ],
    ]);
});

add_action('rest_api_init', function () {
    register_rest_route('tp/v1', '/render-charts/', [
        'methods'  => 'POST',
        'callback' => 'tpdivi_render_popular_posts_charts',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
        'args'     => [
            'attributes' => [
                'required' => true,
                'validate_callback' => function ($param) {
                    return is_array($param);
                },
            ],
        ],
    ]);
});

function tpdivi_render_popular_posts_charts($request)
{
    // Get attributes and check if they are provided
    $attributes = $request->get_param('attributes');
    
    if (empty($attributes)) {
        return wp_send_json([]);
    }

    $data = [];
    // Default filter and posts number
    $selected_filter = $attributes['filter'] ?? 'all';
    $posts_number = isset($attributes['posts_number']) ? absint($attributes['posts_number']) : 5;
    $chart_posts = isset($attributes['chart_posts']) ? absint($attributes['chart_posts']) : 5;
    $posts_number=max($posts_number,$chart_posts);
    global $wpdb;
    $date_value = '';
    switch ($selected_filter) {
        case 'today':
            $date_value = date('Y-m-d');
            break;
        case 'weekly':
            $date_value = date('Y-m-d', strtotime('-7 days'));
            break;
        case 'monthly':
            $date_value = date('Y-m-d', strtotime('-30 days'));
            break;
        case 'yearly':
            $date_value = date('Y-m-d', strtotime('-1 year'));
            break;
    }
    $args = [];
    $date_sql = '';
    if ($date_value) {
        $date_sql = 'AND DATE(pv.view_date) >= %s';
        $args[] = $date_value;
    }
    $args[] = (int) $posts_number;
    // SQL query
    $sql = "SELECT p.ID AS post_id,p.post_title,p.post_type,SUM(pv.view_count) AS total_views FROM {$wpdb->prefix}post_views_tp pv INNER JOIN {$wpdb->prefix}posts p ON pv.post_id = p.ID WHERE p.post_type ='post' AND p.post_status = 'publish' $date_sql GROUP BY p.ID ORDER BY total_views DESC LIMIT %d";
    // Get results
    //phpcs:ignore
    $popular_posts = $wpdb->get_results($wpdb->prepare($sql, ...$args));
    $total_views_sum = array_sum(array_column($popular_posts, 'total_views'));
    // Define default excerpt length
    $excerpt_length = isset($attributes['excerpt_length']) ? absint($attributes['excerpt_length']) : 20;
  if (!defined('ONLY_ONCE_ap3_divi_do_shortcodes') && function_exists('et_builder_init_global_settings') && function_exists('et_builder_add_main_elements')) {
        define('ONLY_ONCE_ap3_divi_do_shortcodes', true);
        et_builder_add_main_elements();
    }
    foreach ($popular_posts as $postloop) {
        // Get post thumbnail and content
        $post_thumb = get_the_post_thumbnail_url($postloop->post_id, 'thumbnail') ?: '';
        $post_data = get_post($postloop->post_id);
        ET_Builder_Element::clean_internal_modules_styles();
        $excerpt = et_core_intentionally_unescaped(wpautop(et_delete_post_first_video(strip_shortcodes(truncate_post($excerpt_length, false, $post_data, true)))), 'html');
        // Author and metadata
        $post_author_id = get_post_field('post_author', $postloop->post_id);
        $author_name = get_the_author_meta('display_name', $post_author_id);
        $author_link = get_author_posts_url($post_author_id);
        $author_data = '<a href="' . esc_url($author_link) . '">' . esc_html($author_name) . '</a>';

        // Meta information
        $meta_date = esc_html(get_the_date(str_replace('\\\\', '\\', $attributes['meta_date'] ?? ''), $postloop->post_id));
        $meta_comments = get_comments_number($postloop->post_id);

        // Taxonomy terms
        $terms = get_the_terms($postloop->post_id, get_object_taxonomies(get_post_type($postloop->post_id), 'names'));
        $terms_list = $terms ? array_map(fn($term) => '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>', $terms) : [];

        // Prepare response data
        $data[] = [
            'id' => $postloop->post_id,
            'post_type' => $postloop->post_type,
            'title' => $postloop->post_title,
            'thumbnail' => $post_thumb,
            'excerpt' => $excerpt,
            'permalink' => get_the_permalink($postloop->post_id),
            'meta_author' => $author_data,
            'meta_date' => $meta_date,
            'meta_comments' => $meta_comments,
            'meta_views' => $postloop->total_views,
            'terms' => implode(', ', $terms_list),
            'percentage' => round(($postloop->total_views / $total_views_sum) * 100, 2)
        ];
    }

    // Return the data in JSON format
    return wp_send_json($data);
}


function tpdivi_render_popular_posts($request)
{
    // Get attributes and check if they are provided
    $attributes = $request->get_param('attributes');
    if (empty($attributes)) {
        return wp_send_json([]);
    }
    $data = [];
    // Default filter and posts number
    $selected_filter = $attributes['filter'] ?? 'all';
    $posts_number = isset($attributes['posts_number']) ? absint($attributes['posts_number']) : 5;
    global $wpdb;
    $date_value = '';
    switch ($selected_filter) {
        case 'today':
            $date_value = date('Y-m-d');
            break;
        case 'weekly':
            $date_value = date('Y-m-d', strtotime('-7 days'));
            break;
        case 'monthly':
            $date_value = date('Y-m-d', strtotime('-30 days'));
            break;
        case 'yearly':
            $date_value = date('Y-m-d', strtotime('-1 year'));
            break;
    }
    $args = [];
    $date_sql = '';
    if ($date_value) {
        $date_sql = 'AND DATE(pv.view_date) >= %s';
        $args[] = $date_value;
    }
    $args[] = (int) $posts_number;
    // SQL query
    $sql = "SELECT p.ID AS post_id,p.post_title,p.post_type,SUM(pv.view_count) AS total_views FROM {$wpdb->prefix}post_views_tp pv INNER JOIN {$wpdb->prefix}posts p ON pv.post_id = p.ID WHERE p.post_type ='post' AND p.post_status = 'publish' $date_sql GROUP BY p.ID ORDER BY total_views DESC LIMIT %d";
    // Get results
    //phpcs:ignore
    $popular_posts = $wpdb->get_results($wpdb->prepare($sql, ...$args));

    // Define default excerpt length
    $excerpt_length = isset($attributes['excerpt_length']) ? absint($attributes['excerpt_length']) : 20;

    // Ensure Divi builder shortcodes are processed
    if (!defined('ONLY_ONCE_ap3_divi_do_shortcodes') && function_exists('et_builder_init_global_settings') && function_exists('et_builder_add_main_elements')) {
        define('ONLY_ONCE_ap3_divi_do_shortcodes', true);
        et_builder_add_main_elements();
    }

    foreach ($popular_posts as $postloop) {
        // Get post thumbnail and content
        $post_thumb = get_the_post_thumbnail_url($postloop->post_id, 'thumbnail') ?: '';
        $post_data = get_post($postloop->post_id);
        $post_content = et_strip_shortcodes(et_delete_post_first_video($post_data->post_content), true);
        $shortcodes_to_remove = ['tp_popular_posts'];
        $processed_content = tpdivi_remove_specific_shortcodes($post_content, $shortcodes_to_remove);
        ET_Builder_Element::clean_internal_modules_styles();
        $excerpt = et_core_intentionally_unescaped(wpautop(et_delete_post_first_video(strip_shortcodes(truncate_post($excerpt_length, false, $post_data, true)))), 'html');
        $content = et_core_intentionally_unescaped(apply_filters('the_content', $processed_content), 'html');
        // Author and metadata
        $post_author_id = get_post_field('post_author', $postloop->post_id);
        $author_name = get_the_author_meta('display_name', $post_author_id);
        $author_link = get_author_posts_url($post_author_id);
        $author_data = '<a href="' . esc_url($author_link) . '">' . esc_html($author_name) . '</a>';

        // Meta information
        $meta_date = esc_html(get_the_date(str_replace('\\\\', '\\', $attributes['meta_date'] ?? ''), $postloop->post_id));
        $meta_comments = get_comments_number($postloop->post_id);

        // Taxonomy terms
        $terms = get_the_terms($postloop->post_id, get_object_taxonomies(get_post_type($postloop->post_id), 'names'));
        $terms_list = $terms ? array_map(fn($term) => '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>', $terms) : [];

        // Prepare response data
        $data[] = [
            'id' => $postloop->post_id,
            'post_type' => $postloop->post_type,
            'title' => $postloop->post_title,
            'thumbnail' => $post_thumb,
            'content' => $content,
            'excerpt' => $excerpt,
            'permalink' => get_the_permalink($postloop->post_id),
            'meta_author' => $author_data,
            'meta_date' => $meta_date,
            'meta_comments' => $meta_comments,
            'meta_views' => $postloop->total_views,
            'terms' => implode(', ', $terms_list),
        ];
    }

    // Return the data in JSON format
    return wp_send_json($data);
}


add_action('rest_api_init', function () {
    register_rest_route('tp/v1', 'chart/post-views', [
        'methods' => 'POST',
        'callback' => 'tpdivi_get_post_views',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
        'args'     => [
            'attributes' => [
                'required' => true,
                'validate_callback' => function ($param) {
                    return is_array($param);
                },
            ],
        ],
    ]);
});


// Helper function to sanitize and get dates
function tpdivi_get_date_range($attr)
{
    //phpcs:ignore
    $start_date = date('Y-m-d', strtotime('-1 year'));
    //phpcs:ignore
    $end_date = date('Y-m-d');

    if (isset($attr['startDate']) && !empty($attr['startDate'])) {
        $start_date = sanitize_text_field($attr['startDate']);
    }

    if (isset($attr['endDate']) && !empty($attr['endDate'])) {
        $end_date = sanitize_text_field($attr['endDate']);
    }

    return [$start_date, $end_date];
}

// Helper function to get selected post types
function tpdivi_get_selected_post_types($attr)
{
    $selected_post_types = get_option('tpdivi_post_types', ['post']);

    if (isset($attr['postType']) && !empty($attr['postType']) && $attr['postType'] != '0') {
        $selected_post_types = explode(',', sanitize_text_field($attr['postType']));
    }

    return $selected_post_types;
}

// Helper function to prepare placeholders for SQL
function tpdivi_prepare_placeholders($selected_post_types)
{
    return implode(',', array_fill(0, count($selected_post_types), '%s'));
}


// Get SQL results (optimized)
function tpdivi_get_sql_results($attr, $posts_number = 20, $filter = 'no-filter')
{
    global $wpdb;

    // Prepare values
    $selected_post_types = tpdivi_get_selected_post_types($attr);
    $selected_post_types = ['post'];
    list($start_date, $end_date)  = tpdivi_get_date_range($attr);

    // Prepare the placeholders for the post types
    $placeholders = implode(',', array_fill(0, count($selected_post_types), '%s'));

    // Start constructing the SQL query
    $sql = "
        SELECT 
            p.ID AS post_id, 
            p.post_title, 
            p.post_type, 
            SUM(pv.view_count) AS total_views
        FROM {$wpdb->prefix}post_views_tp pv
        INNER JOIN {$wpdb->prefix}posts p ON pv.post_id = p.ID
        WHERE p.post_type IN ($placeholders)
          AND p.post_status = 'publish'
    ";

    // Prepare the arguments for the query
    $args = $selected_post_types;

    // If a filter is set, add the date range condition
    if ($filter !== 'no-filter') {
        $sql .= " AND DATE(pv.view_date) BETWEEN %s AND %s";
        $args[] = $start_date;
        $args[] = $end_date;
    }

    // Finalize the query with grouping, ordering, and limit
    $sql .= " GROUP BY p.ID ORDER BY total_views DESC LIMIT %d";
    $args[] = $posts_number;

    // Prepare the final query using $wpdb->prepare() with all arguments
    // phpcs:ignore
    $prepared_sql = $wpdb->prepare($sql, ...$args);

    // Execute the query and return results
    // phpcs:ignore
    return $wpdb->get_results($prepared_sql);
}




// Get views by date (optimized)
function tpdivi_get_views_by_date($attr)
{
    global $wpdb;

    // Prepare values
    $selected_post_types = tpdivi_get_selected_post_types($attr); // Get post types
    $selected_post_types = ['post'];
    list($start_date, $end_date)  = tpdivi_get_date_range($attr);       // Get date range

    // Prepare placeholders for selected post types
    $placeholders = implode(',', array_fill(0, count($selected_post_types), '%s'));

    // Merge arguments for post types and date range
    $args = array_merge($selected_post_types, [$start_date, $end_date]);
    // Execute the query and return results
    // phpcs:ignore
    return $wpdb->get_results($wpdb->prepare("SELECT DATE(pv.view_date) AS view_date, SUM(pv.view_count) AS total_views FROM {$wpdb->prefix}post_views_tp pv INNER JOIN {$wpdb->prefix}posts p ON pv.post_id = p.ID WHERE p.post_type IN ($placeholders) AND p.post_status = 'publish' AND DATE(pv.view_date) BETWEEN %s AND %s GROUP BY DATE(pv.view_date) ORDER BY view_date ASC", ...$args), ARRAY_A);
}


// Get total views count (optimized)
function tpdivi_get_total_views_count($attr)
{
    global $wpdb;
    // SQL query to count total views
    // phpcs:ignore
    return (int)$wpdb->get_var("
    SELECT SUM(pv.view_count)
    FROM {$wpdb->prefix}post_views_tp pv
    INNER JOIN {$wpdb->prefix}posts p ON pv.post_id = p.ID
    WHERE p.post_status = 'publish'");
}
// Get filtered views count (optimized)
function tpdivi_get_filtered_views_count($attr)
{
    global $wpdb;
    // Prepare values
    $selected_post_types = tpdivi_get_selected_post_types($attr);
    $selected_post_types = ['post'];
    list($start_date, $end_date)  = tpdivi_get_date_range($attr);
    $placeholders = tpdivi_prepare_placeholders($selected_post_types);
    // Build SQL query for filtered views
    $sql = "
        SELECT SUM(pv.view_count)
        FROM {$wpdb->prefix}post_views_tp pv
        INNER JOIN {$wpdb->prefix}posts p ON pv.post_id = p.ID
        WHERE p.post_status = 'publish'
        AND DATE(pv.view_date) >= %s
        AND DATE(pv.view_date) <= %s
    ";
    if (!empty($selected_post_types)) {
        $sql .= " AND p.post_type IN ($placeholders)";
    }
    $args = array_merge([$start_date, $end_date], $selected_post_types);
    // phpcs:ignore
    return (int)$wpdb->get_var($wpdb->prepare($sql, $args));
}

// Get total posts count (optimized)
function tpdivi_get_total_posts_count($attr)
{
    global $wpdb;
    // SQL query to count total posts
    // phpcs:ignore
    return (int)$wpdb->get_var("
    SELECT COUNT(DISTINCT pv.post_id)
    FROM {$wpdb->prefix}post_views_tp pv
    INNER JOIN {$wpdb->prefix}posts p ON pv.post_id = p.ID
    WHERE p.post_status = 'publish'
");
}

function tpdivi_get_filtered_posts_count($attr)
{
    global $wpdb;
    // Get the selected post types (can be passed as a comma-separated string in $attr)
    $selected_post_types = tpdivi_get_selected_post_types($attr);
    $selected_post_types = ['post'];
    list($start_date, $end_date)  = tpdivi_get_date_range($attr);
    $placeholders = tpdivi_prepare_placeholders($selected_post_types);
    // Prepare arguments for the query
    $args = array_merge($selected_post_types, [$start_date, $end_date]);
    // Execute the query and return the count
    // phpcs:ignore
    $total_posts_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(DISTINCT pv.post_id) FROM {$wpdb->prefix}post_views_tp pv INNER JOIN {$wpdb->prefix}posts p ON pv.post_id = p.ID WHERE p.post_status = 'publish' AND p.post_type IN ($placeholders) AND DATE(pv.view_date) BETWEEN %s AND %s", ...$args));

    return (int)$total_posts_count;
}

function tpdivi_get_last_month_views_count($attr)
{
    global $wpdb;
    // Get the first and last day of the previous month
    //phpcs:ignore
    $first_day_last_month = date('Y-m-01', strtotime('first day of last month'));
    //phpcs:ignore
    $last_day_last_month = date('Y-m-t', strtotime('last day of last month'));
    // phpcs:ignore
    $selected_post_types = tpdivi_get_selected_post_types($attr);
    $selected_post_types = ['post'];
    //$placeholders = tdivi_prepare_placeholders($selected_post_types);
    if (count($selected_post_types) === 1) {
        // Single post type condition
        $post_type_condition = $wpdb->prepare("AND p.post_type = %s", $selected_post_types[0]);
    } else {
        // Multiple post types condition
        $placeholders = implode(', ', array_fill(0, count($selected_post_types), '%s'));
        // phpcs:ignore
        $post_type_condition = $wpdb->prepare("AND p.post_type IN ($placeholders)", ...$selected_post_types);
    }
    // phpcs:ignore
    $total_views_count = $wpdb->get_var($wpdb->prepare("SELECT SUM(pv.view_count) FROM {$wpdb->prefix}post_views_tp pv INNER JOIN {$wpdb->prefix}posts p ON pv.post_id = p.ID WHERE p.post_status = 'publish' $post_type_condition AND DATE(pv.view_date) BETWEEN %s AND %s", $first_day_last_month, $last_day_last_month));
    return (int)$total_views_count;
}
function tpdivi_get_todays_views_count($attr)
{
    global $wpdb;
    // Get today's date
    //phpcs:ignore
    $today_date = date('Y-m-d');
    // Get the total views count for today
    $selected_post_types = tpdivi_get_selected_post_types($attr);
    $selected_post_types = ['post'];
    //$placeholders = tpdivi_prepare_placeholders($selected_post_types);
    if (count($selected_post_types) === 1) {
        // Single post type condition
        $post_type_condition = $wpdb->prepare("AND p.post_type = %s", $selected_post_types[0]);
    } else {
        // Multiple post types condition
        $placeholders = implode(', ', array_fill(0, count($selected_post_types), '%s'));
        // phpcs:ignore
        $post_type_condition = $wpdb->prepare("AND p.post_type IN ($placeholders)", ...$selected_post_types);
    }
    // phpcs:ignore
    $todays_views_count = $wpdb->get_var($wpdb->prepare("SELECT SUM(pv.view_count) FROM {$wpdb->prefix}post_views_tp pv INNER JOIN {$wpdb->prefix}posts p ON pv.post_id = p.ID WHERE p.post_status = 'publish' $post_type_condition AND DATE(pv.view_date) = %s", $today_date));
    return (int)$todays_views_count;
}

// Helper function to prepare post data for the response
function tpdivi_prepare_post_data($posts)
{
    $post_data = [];

    foreach ($posts as $key => $postloop) {
        $post_thumb = get_the_post_thumbnail_url($postloop->post_id, 'thumbnail') ?: POPULAR_DIVI_URL . 'assets/default.jpg';
        $title = wp_specialchars_decode(get_the_title($postloop->post_id));
        $views = isset($postloop->total_views) ? (int)$postloop->total_views : 0;
        $id = isset($postloop->post_id) ? (int)$postloop->post_id : 0;

        $post_data[] = [
            'id' => $id,
            'views' => $views,
            'postName' => $title,
            'link' => get_the_permalink($id),
            'thumbnail' => $post_thumb,
            'pdate' => get_the_date('d/m/Y', $id),
            'ptype' => get_post_type($id)
        ];
    }

    return $post_data;
}
// Main function to get post views (optimized)
function tpdivi_get_post_views($request)
{
    $attr = $request->get_param('attributes');

    // Fetch results
    $posts = tpdivi_get_sql_results($attr, 15, 'filter');
    $viewsbydate = tpdivi_get_views_by_date($attr);
    $trending = tpdivi_get_sql_results($attr, 5, 'no-filter');
    $total_views = tpdivi_get_total_views_count($attr);
    $total_posts = tpdivi_get_total_posts_count($attr);
    $filtered_views = tpdivi_get_filtered_views_count($attr);
    $filtered_posts = tpdivi_get_filtered_posts_count($attr);
    // Prepare data arrays
    $postdata = tpdivi_prepare_post_data($posts);
    $top_posts_data = tpdivi_prepare_post_data(array_slice($posts, 0, 5));
    // Prepare final response data
    $data = [
        'postdata' => $postdata,
        'top_posts_data' => $top_posts_data,
        'total_views' => $total_views,
        'filtered_views' => $filtered_views,
        'total_posts' => $total_posts,
        'filtered_posts' => $filtered_posts,
        'last_month_views' => tpdivi_get_last_month_views_count($attr),
        'today_views' => tpdivi_get_todays_views_count($attr),
        'available_post_types' => get_option('tpdivi_post_types', []),
        'views_by_date' => $viewsbydate,
    ];

    return new WP_REST_Response($data, 200);
}
function tpdivi_remove_specific_shortcodes($content, $shortcodes_to_remove = [])
{
    foreach ($shortcodes_to_remove as $shortcode) {
        // Regex to match the shortcode, including attributes and closing tag
        $pattern = sprintf('/\[%s\b[^]]*\](.*?)\[\/%s\]/s', $shortcode, $shortcode);

        // Remove the shortcode and its enclosed content
        $content = preg_replace($pattern, '', $content);

        // Regex to match self-closing shortcodes (e.g., [tp_popular_posts /])
        $self_closing_pattern = sprintf('/\[%s\b[^]]*\/\]/s', $shortcode);

        // Remove the self-closing shortcode
        $content = preg_replace($self_closing_pattern, '', $content);
    }

    return $content;
}
