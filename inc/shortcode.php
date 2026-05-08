<?php
/**
 * s_loop shortcode
 * REF: https://github.com/billerickson/display-posts-shortcode/blob/master/display-posts-shortcode.php
 * EXAMPLE USAGE:
 * [s_loop args="posts_per_page=10&post_type=page&post_parent=1" template="card" css_class=""]
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * s_loop shortcode callback.
 *
 * @param array<string, mixed> $atts Shortcode attributes.
 * @return string HTML output.
 */
function s_loop_shortcode(array $atts): string {
    $atts = shortcode_atts(
        array(
            'author'                => '',
            'author_name'           => '',
            'cat'                   => '',
            'category_name'         => '',
            'ignore_sticky_posts'   => false,
            'meta_key'              => '',
            'meta_value'            => '',
            'offset'                => 0,
            'order'                 => 'DESC',
            'orderby'               => 'date',
            'post_parent'           => false,
            'post_parent__in'       => false,
            'post_parent__not_in'  => false,
            'post_status'           => 'publish',
            'post_type'             => 'post',
            'post__in'              => false,
            'post__not_in'          => false,
            'posts_per_page'        => '9',
            'tag'                   => '',
            'tax_operator'          => 'IN',
            'tax_include_children'  => true,
            'tax_term'              => false,
            'taxonomy'              => false,
            'exclude_current'       => false,
            'pagination'            => false,
            'css'                   => 's-grid -d3',
            'template'              => 'card',
        ),
        $atts,
        's_loop'
    );
    
    // Sanitize all inputs
    $sanitized = array(
        'author'                => sanitize_text_field($atts['author']),
        'author_name'           => sanitize_text_field($atts['author_name']),
        'cat'                   => sanitize_text_field($atts['cat']),
        'category_name'         => sanitize_text_field($atts['category_name']),
        'exclude_current'       => filter_var($atts['exclude_current'], FILTER_VALIDATE_BOOLEAN),
        'ignore_sticky_posts'   => filter_var($atts['ignore_sticky_posts'], FILTER_VALIDATE_BOOLEAN),
        'meta_key'              => sanitize_text_field($atts['meta_key']),
        'meta_value'            => sanitize_text_field($atts['meta_value']),
        'offset'                => (int) $atts['offset'],
        'order'                 => sanitize_key($atts['order']),
        'orderby'               => sanitize_key($atts['orderby']),
        'post_parent'           => $atts['post_parent'],
        'post_parent__in'       => $atts['post_parent__in'],
        'post_parent__not_in'   => $atts['post_parent__not_in'],
        'post_status'           => $atts['post_status'],
        'post_type'             => sanitize_text_field($atts['post_type']),
        'post__in'              => $atts['post__in'],
        'post__not_in'          => $atts['post__not_in'],
        'posts_per_page'        => (int) $atts['posts_per_page'],
        'tag'                   => sanitize_text_field($atts['tag']),
        'tax_operator'          => $atts['tax_operator'],
        'tax_include_children'  => filter_var($atts['tax_include_children'], FILTER_VALIDATE_BOOLEAN),
        'tax_term'              => sanitize_text_field($atts['tax_term']),
        'taxonomy'              => sanitize_key($atts['taxonomy']),
        'pagination'            => sanitize_key($atts['pagination']),
        'css'                   => sanitize_text_field($atts['css']),
        'template'              => sanitize_text_field($atts['template']),
    );
    
    $args = s_build_query_args($sanitized);
    
    $slider = (strpos($sanitized['css'], 's-slider') === 0);
    
    $the_query = new WP_Query($args);
    
    if ($the_query->have_posts()) {
        return s_render_loop($the_query, $sanitized['css'], $sanitized['template'], $slider, $sanitized['pagination']);
    }
    
    return esc_html__('No posts found', 'plant');
}
add_shortcode('s_loop', 's_loop_shortcode');

/**
 * Build WP_Query arguments from sanitized attributes.
 *
 * @param array<string, mixed> $atts Sanitized shortcode attributes.
 * @return array<string, mixed> WP_Query arguments.
 */
function s_build_query_args(array $atts): array {
    $args = array();
    
    if (!empty($atts['cat'])) {
        $args['cat'] = $atts['cat'];
    }
    if (!empty($atts['category_name'])) {
        $args['category_name'] = $atts['category_name'];
    }
    if (!empty($atts['order'])) {
        $args['order'] = $atts['order'];
    }
    if (!empty($atts['orderby'])) {
        $args['orderby'] = $atts['orderby'];
    }
    if (!empty($atts['post_type'])) {
        $args['post_type'] = s_explode($atts['post_type']);
    }
    if (!empty($atts['posts_per_page'])) {
        $args['posts_per_page'] = $atts['posts_per_page'];
    }
    if (!empty($atts['tag'])) {
        $args['tag'] = $atts['tag'];
    }
    if ($atts['ignore_sticky_posts']) {
        $args['ignore_sticky_posts'] = true;
    }
    if (!empty($atts['meta_key'])) {
        $args['meta_key'] = $atts['meta_key'];
    }
    if (!empty($atts['meta_value'])) {
        $args['meta_value'] = $atts['meta_value'];
    }
    
    // Post IDs
    if (!empty($atts['post__in'])) {
        $args['post__in'] = array_map('intval', s_explode($atts['post__in']));
    }
    
    $posts_not_in = array();
    if (!empty($atts['post__not_in'])) {
        $posts_not_in = array_map('intval', s_explode($atts['post__not_in']));
    }
    if (is_singular() && $atts['exclude_current']) {
        $posts_not_in[] = get_the_ID();
    }
    if (!empty($posts_not_in)) {
        $args['post__not_in'] = $posts_not_in;
    }
    
    // Author
    if (!empty($atts['author'])) {
        $args['author'] = $atts['author'];
    }
    if (!empty($atts['author_name'])) {
        $args['author_name'] = $atts['author_name'];
    }
    
    // Pagination
    if (!empty($atts['offset'])) {
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args['offset_start'] = $atts['offset'];
        $args['offset'] = ($paged - 1) * $atts['posts_per_page'] + $atts['offset'];
    }
    
    if ($atts['pagination']) {
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args['paged'] = $paged;
    }
    
    // Post status
    $post_status = s_explode($atts['post_status']);
    $validated = array();
    $available = array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash', 'any');
    foreach ($post_status as $unvalidated) {
        if (in_array($unvalidated, $available, true)) {
            $validated[] = $unvalidated;
        }
    }
    if (!empty($validated)) {
        $args['post_status'] = $validated;
    }
    
    // Taxonomy
    if (!empty($atts['taxonomy']) && !empty($atts['tax_term'])) {
        $tax_term = 'current' === $atts['tax_term'] 
            ? wp_list_pluck(wp_get_post_terms(get_the_ID(), $atts['taxonomy']), 'slug')
            : s_explode($atts['tax_term']);
        
        $tax_operator = in_array($atts['tax_operator'], array('IN', 'NOT IN', 'AND'), true) 
            ? $atts['tax_operator'] : 'IN';
        
        $args['tax_query'] = array(
            array(
                'taxonomy'         => $atts['taxonomy'],
                'field'            => 'slug',
                'terms'            => $tax_term,
                'operator'         => $tax_operator,
                'include_children' => $atts['tax_include_children'],
            ),
        );
    }
    
    // Parent
    if (false !== $atts['post_parent']) {
        $post_parent = 'current' === $atts['post_parent'] ? get_the_ID() : (int) $atts['post_parent'];
        $args['post_parent'] = $post_parent;
    }
    if (false !== $atts['post_parent__in']) {
        $args['post_parent__in'] = array_map('intval', s_explode($atts['post_parent__in']));
    }
    if (false !== $atts['post_parent__not_in']) {
        $args['post_parent__not_in'] = array_map('intval', s_explode($atts['post_parent__not_in']));
    }
    
    return $args;
}

/**
 * Render the loop output.
 *
 * @param WP_Query $the_query Query object.
 * @param string $css CSS classes for container.
 * @param string $template Template name.
 * @param bool $slider Whether in slider mode.
 * @param bool $pagination Whether to show pagination.
 * @return string HTML output.
 */
function s_render_loop(WP_Query $the_query, string $css, string $template, bool $slider, bool $pagination): string {
    $output = '<div class="' . esc_attr($css) . '">';
    
    while ($the_query->have_posts()) {
        $the_query->the_post();
        if ($slider) {
            $output .= '<div class="slider">';
        }
        ob_start();
        get_template_part('template-parts/content', esc_attr($template));
        $output .= ob_get_clean();
        if ($slider) {
            $output .= '</div>';
        }
    }
    
    $output .= '</div>';
    
    if ($pagination) {
        $output .= seed_posts_navigation($the_query);
    }
    
    wp_reset_postdata();
    
    return $output;
}

/**
 * Explode list using "," and ", ".
 *
 * @param string $string String to split up.
 * @return array Array of string parts.
 */
function s_explode($string = '') {
    $string = str_replace(', ', ',', $string);
    return array_filter(explode(',', $string), 'strlen');
}

/**
 * Shortcode [s_icon i="ICON_NAME"]
 */
function s_icon_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'i'      => '',
            'width'  => '24',
            'height' => '',
        ),
        $atts,
        's_icon'
    );
    
    $icon_name = sanitize_text_field($atts['i']);
    $width = intval($atts['width']);
    $height = !empty($atts['height']) ? intval($atts['height']) : $width;
    
    if (empty($icon_name)) {
        return '';
    }
    
    // Validate icon name to prevent path traversal
    if (preg_match('/[^a-zA-Z0-9_-]/', $icon_name)) {
        return '';
    }
    
    $file = get_theme_file_path('/img/i/' . $icon_name . '.svg');
    if (file_exists($file)) {
        ob_start();
        include $file;
        $output = ob_get_clean();
        $output = str_replace('width="24"', 'width="' . $width . '"', $output);
        $output = str_replace('height="24"', 'height="' . $height . '"', $output);
        return $output;
    }
    
    return '';
}
add_shortcode('s_icon', 's_icon_shortcode');