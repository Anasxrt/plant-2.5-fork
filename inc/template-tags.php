<?php

/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package seed
 */

if (! function_exists('seed_posted_on')) :
    /**
     * Prints HTML with meta information for the current post-date/time.
     */
    function seed_posted_on($show_icon = true)
    {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated hide" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr(get_the_date(DATE_W3C)),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date(DATE_W3C)),
            esc_html(get_the_modified_date())
        );

        echo '<span class="posted-on _heading">';
        if ($show_icon) {
            seed_icon('clock');
        }
        echo ' <a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>';
        echo '</span>';
    }
endif;

if (! function_exists('seed_posted_by')) :
    /**
     * Prints HTML with meta information for the current author.
     */
    function seed_posted_by($show_icon = true)
    {
        echo '<span class="byline _heading">';
        if ($show_icon) {
            seed_icon('s-user');
        }
        echo ' <span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>';
        echo '</span>';
    }
endif;

if (! function_exists('seed_posted_cats')) :
    /**
     * Show Categories
     */
    function seed_posted_cats($show_icon = true)
    {
        if ('post' === get_post_type()) {
            $categories_list = get_the_category_list(esc_html__(', ', 'plant'));
            if ($categories_list) {
                echo '<span class="cat-links _heading">';
                if ($show_icon) {
                    seed_icon('folder');
                }
                echo ' ' . $categories_list;
                echo '</span>';
            }
        }
    }
endif;



if (! function_exists('seed_posted_tags')) :
    /**
     * Show Tags
     */
    function seed_posted_tags()
    {
        if ('post' === get_post_type()) {
            $tags_list = get_the_tag_list('', ' ');
            if ($tags_list) {
                echo '<p class="tags-links _heading">' . $tags_list . '</p>';
            }
        }
    }
endif;


if (! function_exists('seed_entry_footer')) :
    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function seed_entry_footer()
    {
        edit_post_link(
            sprintf(
                wp_kses(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                    __('Edit <span class="screen-reader-text">%s</span>', 'plant'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                wp_kses_post(get_the_title())
            ),
            '<span class="edit-link">',
            '</span>'
        );
    }
endif;

/**
 * Output Numbered Pagination
 * https://codex.wordpress.org/Function_Reference/paginate_links
 */
function seed_posts_navigation($wp_query = null)
{
    if (!$wp_query) {
        global $wp_query;
    }
    $posts_per_page = isset($wp_query->query_vars['posts_per_page']) ? $wp_query->query_vars['posts_per_page'] : 12;
    $offset_start = isset($wp_query->query_vars['offset_start']) ? $wp_query->query_vars['offset_start'] : 0;
    if (!$offset_start) {
        $offset_start = 1;
    }
    $total_rows = max(0, $wp_query->found_posts - $offset_start);
    $total_pages = ceil($total_rows / $posts_per_page);
    printf('<div class="content-pagination">');
    $big = 9999999;
    echo paginate_links(
        array(
                'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format'    => '?paged=%#%',
                'current'   => max(1, get_query_var('paged')),
                'mid_size'  => 1,
                'total'     => $total_pages,
                'prev_text'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"></polyline></svg>',
                'next_text'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>',
        )
    );
    printf('</div>');
}

/**
 * Output Logo (from functions.php or Custom Logo)
 */
function seed_logo()
{
    if ($GLOBALS['s_logo_path'] != 'none') {
        echo '<a href="' . esc_url(home_url('/')) . '" rel="home">';
        echo '<img src="' . get_stylesheet_directory_uri() . '/' . $GLOBALS['s_logo_path'] . '" width="' . $GLOBALS['s_logo_width'] . '"  height="' . $GLOBALS['s_logo_height'] . '" alt="Logo">';
        echo '</a>';
    } else {
        if (get_theme_mod('head_logo_img_m', 0)) {
            echo '<div class="site-logo -multi">';
            echo '<a href="' . esc_url(home_url('/')) . '" rel="home" class="custom-logo-link-m">';
            echo wp_get_attachment_image(get_theme_mod('head_logo_img_m', 0), 'full');
            echo '</a>';
        } else {
            echo '<div class="site-logo">';
        }
        the_custom_logo();
        echo '</div>';
    }
}

/**
 * Output Title (h1/p)
 */
function seed_title()
{
    if (is_front_page() && is_home()) {
        $tag = 'h1';
    } else {
        $tag = 'p';
    }
    echo '<' . $tag . ' class="site-title"><a href="' . esc_url(home_url('/')) . '" rel="home">' . get_bloginfo('name') . '</a></' . $tag . '>';
}

/*
 * Output Member Menu
 */
function seed_member_menu()
{
    ?>
<div class="site-member">
    <a href="<?php echo sanitize_url($GLOBALS['s_member_url']); ?>" <?php
    if (!is_user_logged_in()) {
        echo 'class="s-modal-trigger m-user"';
    } else {
        echo 'class="m-user"';
    }
    ?>>
        <span class="pic">
            <?php
                $current_user = wp_get_current_user();
            if (0 != $current_user->ID) {
                echo get_avatar($current_user->ID, 64);
            } else {
                seed_icon('s-user');
            }
            ?>
        </span>
        <span class="info">
            <?php
            if ($GLOBALS['s_member_label'] == 'Member') {
                _e('Member', 'plant');
            } else {
                echo $GLOBALS['s_member_label'];
            }
            ?>
        </span>
    </a>
</div>
    <?php
}
/*
 * Get Post Thumbnail URL from Post ID.
 *
 * @param int $post_id Post ID to get thumbnail for.
 * @return string|false URL string or false if no thumbnail.
 */
function seed_get_thumbnail(int $post_id) {
    $thumb_id = get_post_thumbnail_id($post_id);
    if ($thumb_id) {
        $thumb_url = wp_get_attachment_image_src($thumb_id, 'full', true);
        return $thumb_url[0];
    }
    return false;
}
/*
 * Output Main Header with Title - Refactored into smaller functions
 */
function seed_banner_title(int $post_id): void
{
    $title_style = seed_get_title_style($post_id);
    $banner_bg = '';
    
    if ($title_style === 'banner') {
        $banner_bg = seed_build_banner_background($post_id, $title_style);
    }
    
    $permalink = get_the_permalink($post_id);
    $title = seed_get_banner_title($post_id, $title_style);
    $breadcrumb = seed_get_breadcrumb();
    
    echo seed_render_banner($title_style, $banner_bg, $permalink, $title, $breadcrumb);
}

/**
 * Get the title style for banner display.
 *
 * @param int $post_id Post ID to check.
 * @return string Title style ('banner', 'minimal', or 'hidden').
 */
function seed_get_title_style($post_id) {
    $default_title_style = get_theme_mod('body_title_style', $GLOBALS['s_title_style']);
    $post_title_style = get_field('title_style', $post_id);
    
    return ($post_title_style && $post_title_style !== 'default')
        ? $post_title_style
        : $default_title_style;
}

/**
 * Build the banner background CSS.
 *
 * @param int $post_id Post ID to get banner for.
 * @param string $title_style Title style affecting fallback behavior.
 * @return string HTML div with background styling.
 */
function seed_build_banner_background($post_id, $title_style): string {
    $banner_url = seed_get_thumbnail($post_id);
    $img_banner_blur = '';
    $img_banner_opacity = '';
    
    if ($title_style === 'banner' && function_exists('get_field')) {
        $img_banner = get_field('banner', $post_id);
        if ($img_banner) {
            $banner_url = $img_banner;
            $img_banner_blur = get_field('banner_blur', $post_id);
            $img_banner_opacity = get_field('banner_opacity', $post_id);
        }
    }
    
    if (!$banner_url && $title_style !== 'banner') {
        $banner_url = get_theme_mod('body_title_banner', '');
        $is_shop = function_exists('is_shop') && is_shop();
        
        if (get_theme_mod('body_title_single', '0') && (is_single($post_id) || is_archive()) && !$is_shop) {
            $img_banner_blur = get_theme_mod('body_title_single_banner_blur', '20');
            $img_banner_opacity = get_theme_mod('body_title_single_banner_opacity', '0.7');
        } else {
            $img_banner_blur = get_theme_mod('body_title_banner_blur', '20');
            $img_banner_opacity = get_theme_mod('body_title_banner_opacity', '0.7');
        }
    }
    
    if (!$banner_url) {
        return '<div class="bg -blank"></div>';
    }
    
    $style = 'background-image: url(' . esc_url($banner_url) . ');';
    if ($img_banner_blur) {
        $style .= ' filter: blur(' . intval($img_banner_blur) . 'px);';
    }
    $style .= ' opacity: ' . floatval($img_banner_opacity) . ';';
    
    return '<div class="bg" style="' . $style . '"></div>';
}

/**
 * Get the banner title text.
 *
 * @param int $post_id Post ID to get title for.
 * @param string $title_style Title style affecting output.
 * @return string Banner title HTML.
 */
function seed_get_banner_title($post_id, $title_style): string {
    if (is_front_page()) {
        return get_bloginfo('name') . '<small>' . get_bloginfo('description') . '</small>';
    }
    
    if (is_archive()) {
        return get_the_archive_title();
    }
    
    if (is_404()) {
        return __('Page not found', 'plant');
    }
    
    if ($title_style === 'banner' && function_exists('get_field')) {
        $headline_title = get_field('headline_title', $post_id);
        $headline_subtitle = get_field('headline_subtitle', $post_id);
        
        if ($headline_title) {
            $title = $headline_title;
            if ($headline_subtitle) {
                $title .= '<small>' . $headline_subtitle . '</small>';
            }
            return $title;
        }
    }
    
    return get_the_title($post_id);
}

/**
 * Get breadcrumb HTML.
 *
 * @return string Breadcrumb markup or empty string.
 */
function seed_get_breadcrumb(): string {
    if (!is_front_page() && !is_archive() && !is_404()) {
        if (function_exists('yoast_breadcrumb')) {
            return yoast_breadcrumb('<div id="breadcrumbs" class="bc">', '</div>', false);
        }
        if (function_exists('rank_math_the_breadcrumbs')) {
            return rank_math_get_breadcrumbs();
        }
    }
    return apply_filters('plant_breadcrumb', '');
}

/**
 * Render the complete banner HTML.
 *
 * @param string $title_style Title style for CSS class.
 * @param string $banner_bg Background HTML.
 * @param string $permalink URL for title link.
 * @param string $title Title text/html.
 * @param string $breadcrumb Breadcrumb HTML.
 * @return string Complete banner markup.
 */
function seed_render_banner(string $title_style, string $banner_bg, string $permalink, string $title, string $breadcrumb): string {
    $output = '<div class="main-header -' . esc_attr($title_style) . '">';
    $output .= $banner_bg;
    $output .= '<div class="s-container">';
    $output .= '<div class="main-title _heading">';
    $output .= '<div class="title"><a href="' . esc_url($permalink) . '">' . $title . '</a></div>';
    $output .= $breadcrumb;
    $output .= '</div></div></div>';
    
    return $output;
}
/*
 * Output Author Avatar & Profile in .content-item
 */
function seed_author(int $author_id): void {
    $output = '<a class="author" href="' . esc_url(get_author_posts_url($author_id)) . '">';
    $output .= get_avatar($author_id, 40);
    $output .= '<div class="name">';
    $output .= '<h2>' . esc_html(get_the_author_meta('display_name', $author_id)) . '</h2>';
    $output .= '<small>' . esc_html(get_the_date()) . '</small>';
    $output .= '</div>';
    $output .= '</a>';
    echo $output;
}
/*
* Output SVG icons from /img/i/[ICON-NAME].svg
*/
if (! function_exists('seed_icon')) :
    function seed_icon($i = null)
    {
        if (!$i) {
            return;
        }
        $file = get_theme_file_path('/img/i/' . $i . '.svg');
        if (file_exists($file)) {
            include get_theme_file_path('/img/i/' . $i . '.svg');
        }
    }
endif;
/*
* Output Action in Header for Mobile
*/
function plant_header_action($action, $phone, $custom, $menu_text)
{
    switch ($action) {
        case "menu":
            echo '<div class="site-toggle"><em></em></div>';
            break;
        case "menu_text":
            echo '<div class="site-toggle -text"><em></em><span>' . $menu_text . '</span></div>';
            break;
        case "search":
            echo '<span class="site-search _mobile s-modal-trigger m-user" onclick="return false;" data-popup-trigger="site-search">';
            seed_icon('search');
            echo '</span>';
            break;
        case "phone":
            echo '<a class="site-phone" href="tel:' . $phone . '">';
            seed_icon('phone');
            echo '</a>';
            break;
        case "member":
            seed_member_menu();
            break;
        case 'cart':
            $cart_url = '';
            if (function_exists('wc_get_cart_url')) {
                $cart_url = wc_get_cart_url();
            }
            echo '<a class="site-cart" href="' .  $cart_url . '" title="' . __('View your shopping cart', 'plant') . '">';
            seed_icon($GLOBALS['s_cart_icon']);
            echo '<b id="cart-count-m" class="cart-count hide"></b>';
            echo '</a>';
            break;
        case "custom":
            echo '<div class="site-custom">' . $custom . '</div>';
            break;
    }
}