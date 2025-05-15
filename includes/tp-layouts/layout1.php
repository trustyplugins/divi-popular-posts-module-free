<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ($thumbnail == 'on') {
    $post_thumb = get_the_post_thumbnail_url($postloop->post_id, 'thumbnail');
}
?>
<article class='tp-divi-popular-post post-<?php echo esc_attr($postloop->post_id); ?> <?php echo esc_attr($post_layout); ?>' data-post-type='<?php echo esc_attr($postloop->post_type); ?>'>
    <?php
    if ($show_excerpt == 'on') {
        $post_data = get_post($postloop->post_id);
        $post_content = et_strip_shortcodes(et_delete_post_first_video($post_data->post_content), true);
        $shortcodes_to_remove = ['tp_popular_posts'];
        $processed_content = tpdivi_remove_specific_shortcodes($post_content, $shortcodes_to_remove);
         //var_dump($post_content);
        ET_Builder_Element::clean_internal_modules_styles();
        if ($show_content == 'off') {
           $content= et_core_intentionally_unescaped(wpautop(et_delete_post_first_video(strip_shortcodes(truncate_post($excerpt_length, false, $post_data, true)))), 'html');
        } else {
            $content=et_core_intentionally_unescaped(apply_filters('the_content', $processed_content), 'html');
        }

        // $post_data = get_post($postloop->post_id);
        // if ($show_content == 'off') {
        //     // Process shortcodes and strip HTML tags, then trim to the excerpt length
        //     $processed_content = do_shortcode($post_data->post_content); // Render Divi Builder shortcodes
        //     $stripped_content = wp_strip_all_tags($processed_content); // Remove HTML tags
        //     $content = wp_trim_words($stripped_content, $excerpt_length, '...');
        // } else {
        //     if ($post_data) {
        //         $content = do_shortcode($post_data->post_content);
        //     }
        // }
    }
    if ($thumbnail == 'on') {
        $post_thumb = get_the_post_thumbnail_url($postloop->post_id, 'thumbnail');
        if ($post_thumb) {
            //phpcs:ignore
            echo '<div class="tp-left-wrapper"><div class="tp-post-thumb"><img src="' . esc_attr($post_thumb) . '"></div></div>';
        }
    }
    echo "<div class='tp-right-wrapper'>";
    echo "<div class='tp-post-title'><h2><a href='" . esc_attr(get_the_permalink($postloop->post_id)) . "'>" . esc_html($postloop->post_title) . "</a></h2></div>";

    echo "<div class='tp-meta-data'>";
    $post_author_id = get_post_field('post_author', $postloop->post_id);
    $author_name = get_the_author_meta('display_name', $post_author_id);
    $author_link = get_author_posts_url($post_author_id);
    $author_data = '<a href="' . esc_url($author_link) . '">' . esc_html($author_name) . '</a>';
    if ('on' === $show_author || 'on' === $show_date || 'on' === $show_categories || 'on' === $show_comments || 'on' === $show_views) {
        $author = 'on' === $show_author
            /* translators: %s: Author */
            ? et_get_safe_localization(sprintf(__('by %s', 'popular-posts-for-divi-with-charts'), '<span class="author vcard">' . $author_data . '</span>'))
            : '';

        $author_separator = 'on' === $show_author && 'on' === $show_date
            ? ' | '
            : '';

        // phpcs:disable WordPress.WP.I18n.NoEmptyStrings -- intentionally used.
        $date = 'on' === $show_date
            /* translators: %s: Date */
            ? et_get_safe_localization(sprintf(__('%s', 'popular-posts-for-divi-with-charts'), '<span class="published">' . esc_html(get_the_date(str_replace('\\\\', '\\', $meta_date), $postloop->post_id)) . '</span>'))
            : '';
        // phpcs:enable

        $date_separator = (('on' === $show_author || 'on' === $show_date) && ('on' === $show_comments))
            ? ' | '
            : '';
        $comments_separator = (('on' === $show_views) && ('on' === $show_author || 'on' === $show_date || 'on' === $show_comments))
            ? ' | '
            : '';
        $views_div = 'on' === $show_views
            ? et_get_safe_localization(sprintf('<span class="post_views">' . esc_attr($postloop->total_views) . ' views</span>'))
            : '';

        $comments_data = 'on' === $show_comments
            /* translators: %s: Comments */
            ? et_core_maybe_convert_to_utf_8(sprintf(esc_html(_nx('%s Comment', '%s Comments', get_comments_number($postloop->post_id), 'number of comments', 'popular-posts-for-divi-with-charts')), number_format_i18n(get_comments_number($postloop->post_id))))
            : '';

        // printf(
        //     '<p class="post-meta">%1$s %2$s %3$s %4$s %5$s %6$s %7$s</p>',
        //     //phpcs:ignore
        //     et_core_esc_previously($author),
        //     //phpcs:ignore
        //     et_core_intentionally_unescaped($author_separator, 'fixed_string'),
        //     //phpcs:ignore
        //     et_core_esc_previously($date),
        //     //phpcs:ignore
        //     et_core_intentionally_unescaped($date_separator, 'fixed_string'),
        //     //phpcs:ignore
        //     et_core_esc_previously($comments_data),
        //     //phpcs:ignore
        //     et_core_intentionally_unescaped($comments_separator, 'fixed_string'),
        //     //phpcs:ignore
        //     et_core_esc_previously($views_div)
        // );
        printf(
            '<p class="post-meta">%1$s %2$s %3$s %4$s %5$s %6$s %7$s</p>',
            wp_kses_post($author),
            esc_html($author_separator),
            wp_kses_post($date),
            esc_html($date_separator),
            esc_html($comments_data),
            esc_html($comments_separator),
            wp_kses_post($views_div) // Assuming this contains some safe HTML like an icon
        );
    }
    echo "</div>";
    if ($show_categories == 'on') {
        echo "<div class='tp-post-cats'>";
        //echo "<ul>";
        $taxonomies = get_object_taxonomies(get_post_type($postloop->post_id), 'objects');
        if ($taxonomies) {
            $li_items = []; // Array to collect <li> elements
            foreach ($taxonomies as $taxonomy_slug => $taxonomy_data) {
                $terms = get_the_terms($postloop->post_id, $taxonomy_slug);
                if ($terms && !is_wp_error($terms)) {
                    $term_links = [];
                    foreach ($terms as $term_data) {
                        $term_links[] = '<a href="' . esc_url(get_term_link($term_data)) . '">' . esc_html(trim($term_data->name)) . '</a>';
                    }
                    $li_items[] = implode(', ', $term_links);
                }
            }
            // Output all <li> elements separated by commas
            echo wp_kses_post(implode(', ', $li_items));
        }
        //echo "</ul>";
        echo "</div>";
    }
    if ($show_excerpt == 'on') {
        echo "<div class='tp-post-content'>";
        echo wp_kses_post($content);
        echo "</div>";
    }
    if ($show_more == 'on') {
        echo "<div class='tp-read-more'>";
        echo "<a href='" . esc_html(get_the_permalink($postloop->post_id)) . "'>Read More</a>";
        echo "</div>";
    }
    echo "</div>";

    ?>
</article>