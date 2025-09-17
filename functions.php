<?php


//currently once the user register via the become a member form, we're charging them a amount, and noteing down that date as a payment date, so in the future we need to update the user meta 'payment-date' to the latest payment date once they made any other payment

// define new badge period
$education_new_badge_period = 7;

define('LESM_USER_LOGIN_URL', '/login');

require_once(get_stylesheet_directory() . "/includes/lesm-membership.php");
$lesm_membership = new Lesm_Membership();

add_action('wp_enqueue_scripts', 'salient_child_enqueue_styles', 100);
function salient_child_enqueue_styles()
{
    $nectar_theme_version = nectar_get_theme_version();
    wp_enqueue_style('slick', get_stylesheet_directory_uri() . '/css/slick.css');
    wp_enqueue_style('salient-child-style', get_stylesheet_directory_uri() . '/style.css', '', $nectar_theme_version);
    wp_enqueue_style('font-abs', get_stylesheet_directory_uri() . '/fonts/abc/stylesheet.css', array(), $nectar_theme_version, 'all');
    if (is_rtl()) {
        wp_enqueue_style('salient-rtl',  get_template_directory_uri() . '/rtl.css', array(), '1', 'screen');
    }


    wp_enqueue_script('slick', get_stylesheet_directory_uri() . '/js/slick.js', array('jquery'), true);
    wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/js/custom-script.js', array('jquery'), true);

    $wp_ajx_array = array('wp_ajax_url' => admin_url('admin-ajax.php'));
    wp_localize_script('custom-script', 'admin_ajax', $wp_ajx_array); // localize ajax url in script
}

function lesm_login_styles() {
    wp_enqueue_style('lesm-login', get_stylesheet_directory_uri() .  '/login.css' , array(), '1.0.0', 'all');
}
add_action('login_enqueue_scripts', 'lesm_login_styles');

function my_custom_login_url() {
    return home_url(); // Or your preferred URL
}
add_filter('login_headerurl', 'my_custom_login_url');

function my_custom_login_title() {
    return get_bloginfo('name');
}
add_filter('login_headertext', 'my_custom_login_title');


require_once(get_stylesheet_directory() . "/includes/custom-function.php");
require_once(get_stylesheet_directory() . "/includes/api-function.php");

//add SVG to allowed file uploads
add_action('upload_mimes', 'add_file_types_to_uploads');
function add_file_types_to_uploads($file_types)
{
    $new_filetypes = array();
    $new_filetypes['svg'] = 'image/svg+xml';
    $file_types = array_merge($file_types, $new_filetypes);
    return $file_types;
}

add_filter("redux/salient_redux/field/typography/custom_fonts", "salient_redux_custom_fonts");
function salient_redux_custom_fonts()
{
    return array(
        'Custom Fonts' => array(
            'Rockness' => 'Rockness',
            'Product Sans' => 'Product Sans',
        )
    );
}


// register post type

add_action('init', 'register_post_type_for_lesm');

function register_post_type_for_lesm()
{

    $events_args = array(
        'labels' => array(
            'name' => __('Events', 'salient'),
            'singular_name' => __('Event', 'salient'),
            'add_new' => __('Add New Event', 'salient'),
            'add_new_item' => __('Add New Event', 'salient'),
            'edit_item' => __('Edit Event', 'salient'),
            'new_item' => __('New Event', 'salient'),
            'view_item' => __('View Event', 'salient'),
            'search_items' => __('Search Events', 'salient'),
            'not_found' => __('No events found', 'salient'),
            'not_found_in_trash' => __('No events found in Trash', 'salient'),
        ),
        'public' => true,
        'has_archive' => false,
        'rewrite' => array('slug' => 'events'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'menu_icon' => 'dashicons-calendar',
    );

    register_post_type('events', $events_args);

    $education_args = array(
        'labels' => array(
            'name' => __('Education', 'salient'),
            'singular_name' => __('Education', 'salient'),
            'add_new' => __('Add New Education', 'salient'),
            'add_new_item' => __('Add New Education', 'salient'),
            'edit_item' => __('Edit Education', 'salient'),
            'new_item' => __('New Education', 'salient'),
            'view_item' => __('View Education', 'salient'),
            'search_items' => __('Search Education', 'salient'),
            'not_found' => __('No education found', 'salient'),
            'not_found_in_trash' => __('No education found in Trash', 'salient'),
        ),
        'public' => true,
        'has_archive' => false,
        'rewrite' => array('slug' => 'education'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'menu_icon' => 'dashicons-welcome-learn-more',
        'show_in_rest' => true, // Enable Gutenberg editor
        'capability_type' => 'post',
        // 'taxonomies' => array('category', 'post_tag'), // Add categories and tags support
        //'map_meta_cap' => true, // Use custom capabilities
        // 'capabilities' => array(
        //     'edit_post' => 'edit_education',
        // )
    );

    register_post_type('education', $education_args);

    register_taxonomy('education_category', 'education', array(
        'labels' => array(
            'name' => __('Education Categories', 'salient'),
            'singular_name' => __('Education Category', 'salient'),
            'search_items' => __('Search Education Categories', 'salient'),
            'all_items' => __('All Education Categories', 'salient'),
            'edit_item' => __('Edit Education Category', 'salient'),
            'update_item' => __('Update Education Category', 'salient'),
            'add_new_item' => __('Add New Education Category', 'salient'),
            'new_item_name' => __('New Education Category Name', 'salient'),
        ),
        'hierarchical' => true,
        'public' => true,
        'show_in_rest' => true, // Enable Gutenberg editor
    ));

    $team_args = array(
        'labels' => array(
            'name' => __('Team', 'salient'),
            'singular_name' => __('Team Member', 'salient'),
            'add_new' => __('Add New Team Member', 'salient'),
            'add_new_item' => __('Add New Team Member', 'salient'),
            'edit_item' => __('Edit Team Member', 'salient'),
            'new_item' => __('New Team Member', 'salient'),
            'view_item' => __('View Team Member', 'salient'),
            'search_items' => __('Search Team Members', 'salient'),
            'not_found' => __('No team members found', 'salient'),
            'not_found_in_trash' => __('No team members found in Trash', 'salient'),
        ),
        'public' => true,
        'has_archive' => false,
        'rewrite' => array('slug' => 'team'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'menu_icon' => 'dashicons-groups',
        'show_in_rest' => true, // Enable Gutenberg editor
    );

    register_post_type('team', $team_args);

    register_taxonomy('team_category', 'team', array(
        'labels' => array(
            'name' => __('Team Categories', 'salient'),
            'singular_name' => __('Team Category', 'salient'),
            'search_items' => __('Search Team Categories', 'salient'),
            'all_items' => __('All Team Categories', 'salient'),
            'edit_item' => __('Edit Team Category', 'salient'),
            'update_item' => __('Update Team Category', 'salient'),
            'add_new_item' => __('Add New Team Category', 'salient'),
            'new_item_name' => __('New Team Category Name', 'salient'),
        ),
        'hierarchical' => true,
        'public' => true,
        'show_in_rest' => true, // Enable Gutenberg editor
    ));
}

add_action('vc_before_init', 'vc_before_init_actions');
function vc_before_init_actions()
{

    vc_map(array(
        'name' => __('Events', 'salient'),
        'base' => 'events_shortcode',
        'category' => __('LESM', 'salient'),
        'description' => __('Display events in a grid or list format.', 'salient'),
        'icon' => get_stylesheet_directory_uri() . '/images/events-icon.png',
        'params' => array(
            array(
                'type' => 'textfield',
                'heading' => __('Title', 'salient'),
                'param_name' => 'title',
                'value' => '',
            ),
        ),
    ));


    vc_map(array(
        'name' => __('Education', 'salient'),
        'base' => 'education_shortcode',
        'category' => __('LESM', 'salient'),
        'description' => __('Display education posts in a grid or list format.', 'salient'),
        'icon' => get_stylesheet_directory_uri() . '/images/education-icon.png',
        'params' => array(
            // array(
            //     'type' => 'textfield',
            //     'heading' => __('Title', 'salient'),
            //     'param_name' => 'title',
            //     'value' => '',
            // ),
            array(
                'type' => 'textfield',
                'heading' => __('Number of Posts', 'salient'),
                'param_name' => 'posts_per_page',
                'value' => 3,
            ),
            // array(
            //     'type' => 'dropdown',
            //     'heading' => __('Color', 'salient'),
            //     'param_name' => 'color',
            //     'value' => array(
            //         __('Default', 'salient') => '',
            //         __('Light', 'salient') => 'light',
            //         __('Dark', 'salient') => 'dark',
            //     ),
            // ),
        ),
    ));

    // get all team categories
    $team_categories = get_terms(array(
        'taxonomy' => 'team_category',
        'hide_empty' => false,
    ));

    $team_category_options = array('All' => ''); // Optional: default to "All"

    if (!is_wp_error($team_categories) && !empty($team_categories)) {
        foreach ($team_categories as $cat) {
            $team_category_options[$cat->name] = $cat->slug;
        }
    }

    vc_map(array(
        'name' => __('Team', 'salient'),
        'base' => 'team_shortcode',
        'category' => __('LESM', 'salient'),
        'description' => __('Display team members in a grid or list format.', 'salient'),
        'icon' => get_stylesheet_directory_uri() . '/images/team-icon.png',
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => __('Team Type', 'salient'),
                'param_name' => 'terms',
                'value' => $team_category_options,
                'description' => __('Select a team category to filter members.', 'salient'),
            ),
        ),
    ));

    vc_map(array(
        'name' => __('Most Popular Education', 'salient'),
        'base' => 'popular_education_shortcode',
        'category' => __('LESM', 'salient'),
        'description' => __('Display most popular education posts in a grid or list format.', 'salient'),
        'icon' => get_stylesheet_directory_uri() . '/images/popular-education-icon.png',
        'params' => array(
            // array(
            //     'type' => 'textfield',
            //     'heading' => __('Title', 'salient'),
            //     'param_name' => 'title',
            //     'value' => '',
            // ),
            array(
                'type' => 'textfield',
                'heading' => __('Number of Posts', 'salient'),
                'param_name' => 'posts_per_page',
                'value' => 3,
            ),
        ),
    ));


    vc_map(array(
        'name' => __('LESM Education Library', 'salient'),
        'base' => 'lesm_education_library_shortcode',
        'category' => __('LESM', 'salient'),
        'description' => __('Display most popular education posts in a grid or list format.', 'salient'),
        'icon' => get_stylesheet_directory_uri() . '/images/popular-education-icon.png',
        'params' => array(
            // array(
            //     'type' => 'textfield',
            //     'heading' => __('Title', 'salient'),
            //     'param_name' => 'title',
            //     'value' => '',
            // ),
            array(
                'type' => 'checkbox',
                'heading' => __('Show Sidebar', 'salient'),
                'param_name' => 'show_sidebar',
                'value' => array(__('Yes', 'salient') => 'yes'),
                'description' => __('Check this to display the sidebar on the education library page.', 'salient'),
            ),
            array(
                'type' => 'textfield',
                'heading' => __('Number of Posts', 'salient'),
                'param_name' => 'posts_per_page',
                'value' => 6,
            )
        ),
    ));

    vc_map(
        array(
            'name' => __('LESM Events', 'salient'),
            'base' => 'lesm_events_shortcode',
            'category' => __('LESM', 'salient'),
            'description' => __('Display events in a grid or list format.', 'salient'),
            'icon' => get_stylesheet_directory_uri() . '/images/events-icon.png',
            'params' => array(
                array(
                    'type' => 'dropdown',
                    'heading' => __('Past Events', 'salient'),
                    'param_name' => 'past_events',
                    'value' => array(
                        __('No', 'salient') => 'no',
                        __('Yes', 'salient') => 'yes',
                    ),
                    'std' => 'no',
                ),
            )
        )
    );


    vc_map(
        array(
            'name' => __('LESM News', 'salient'),
            'base' => 'lesm_news_shortcode',
            'category' => __('LESM', 'salient'),
            'description' => __('Display news in a grid or list format.', 'salient'),
            'icon' => get_stylesheet_directory_uri() . '/images/events-icon.png',
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => __('Number of Posts', 'salient'),
                    'param_name' => 'posts_per_page',
                    'value' => '9',
                ),
            )
        )
    );
}

add_shortcode('events_shortcode', 'events_shortcode_callback');
function events_shortcode_callback($atts)
{
    $atts = shortcode_atts(array(
        'title' => '',
    ), $atts, 'events');

    ob_start();

    // Query for events
    $args = array(
        'post_type' => 'events',
        'posts_per_page' => 2,
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_key' => 'event_start_date',
        'meta_query' => array(
            array(
                'key' => 'event_start_date',
                'value' => date('Y-m-d'),
                'compare' => '>=',
                'type' => 'DATE'
            )
        )
    );
    $events_query = new WP_Query($args);

    if ($events_query->have_posts()) {
        echo '<div class="lesm-events-list">';
        if (!empty($atts['title'])) {
            echo '<h2>' . esc_html($atts['title']) . '</h2>';
        }
        while ($events_query->have_posts()) {
            $events_query->the_post();

            $multiple_dates_event = get_field('multiple_dates_event');
            $event_start_date = get_field('event_start_date');
            $event_end_date = get_field('event_end_date');
            
            if($multiple_dates_event){
                $start_date = date('d', strtotime($event_start_date));
                $end_date = date('d', strtotime($event_end_date));
                $event_date = $start_date . ' - ' . $end_date . ' ' . date('M Y', strtotime($event_start_date));
            } else {
                $event_date = $event_start_date;
            }



            $location = get_field('location');
            $highlights = get_field('highlights');
            $feature_image = get_the_post_thumbnail_url(get_the_ID(), 'full');

            echo '<div class="event-item">';
            echo '<div class="card-top" style="background-image: url(' . esc_url($feature_image) . ');">';
            if ($event_date) {
                echo '<span class="event-date">' . esc_html($event_date) . '</span>';
            }
            echo '<div class="top-content">';

            echo '<h3>' . get_the_title() . '</h3>';
            if ($location) {
                echo '<span class="event-location">' . esc_html($location) . '</span>';
            }

            echo '</div>';
            echo '</div>';
            echo '<div class="card-bottom">';
            echo '<div class="event-content">';
            echo '<p>' . get_the_content() . '</p>';
            if ($highlights) {
                echo '<div class="event-highlights">';
                echo '<span>' . $highlights . '</span>';
                echo '<a href="' . get_permalink() . '" class="read-more register-btn">Register</a>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>'; // Close event-item
        }
        echo '</div>'; // Close events-list
    } else {
        echo '<p>' . __('No events found.', 'salient') . '</p>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}


add_shortcode('education_shortcode', 'education_shortcode_callback');

function education_shortcode_callback($atts)
{

    $atts = shortcode_atts(
        array(
            'posts_per_page' => 3,
        ),
        $atts,
        'education'
    );

    $args = array(
        'post_type' => 'education',
        'posts_per_page' => intval($atts['posts_per_page']),
        'orderby' => 'date',
        'order' => 'ASC',
        'post_status' => 'publish',
    );
    $education_query = new WP_Query($args);

    ob_start();

    if (!$education_query->have_posts()) {
        return '<p>' . __('No education posts found.', 'salient') . '</p>';
    }

    echo '<div class="lesm-education-list">';
    while ($education_query->have_posts()) {
        $education_query->the_post();
        $feature_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
        $categories = get_the_terms(get_the_ID(), 'education_category');
        $category_names = array();
        $membership = get_field('membership');

        if ($categories && !is_wp_error($categories)) {
            foreach ($categories as $category) {
                $category_names[] = esc_html($category->name);
            }
        }

        echo '<div class="education-item">';
        echo '<div class="education-image">';
        echo '<img src="' . esc_url($feature_image) . '" alt="' . esc_attr(get_the_title()) . '" class="education-image" />';
        echo '</div>';
        echo '<div class="education-card-bottom">';

        echo '<div class="content-top">';
        if (!empty($category_names)) {
            echo '<span class="education-category">' . implode(', ', $category_names) . '</span>';
        }
        print_membership_badge();
        echo '</div>';
        echo '<h3>' . get_the_title() . '</h3>';
        echo '<p>' . get_the_excerpt() . '</p>';
        if ($membership) {
            if (lesm_user_has_membership_access()) {
                echo '<a href="' . get_permalink() . '" class="view-education-btn read-more">Read More</a>';
            } else {
                echo '<a href="' . wp_login_url(get_permalink()) . '" class="view-education-btn read-more">Login to View</a>';
            }
        } else {
            echo '<a href="' . get_permalink() . '" class="view-education-btn read-more">Read More</a>';
        }
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    wp_reset_postdata();
    return ob_get_clean();
}


function print_membership_badge($post_id = null)
{
    if (is_null($post_id)) {
        $post_id = get_the_ID();
    }

    if (!$post_id) {
        return;
    }

    $membership = get_field('membership', $post_id);
    if ($membership) {
        echo '<div class="education-membership">';
        echo '<img src="' . get_stylesheet_directory_uri() . '/images/lock-icon.png"' . ' alt="Membership Icon"/>';
        echo '<span>' . esc_html("Membership Access") . '</span>';
        echo '</div>';
    }
}



add_shortcode('team_shortcode', 'team_shortcode_callback');
function team_shortcode_callback($atts)
{
    $atts = shortcode_atts(
        array(
            'terms' => '',
        ),
        $atts,
        'team'
    );

    $args = array(
        'post_type' => 'team',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'ASC',
        'post_status' => 'publish',
    );

    // Only add tax_query if 'terms' is passed
    if (!empty($atts['terms'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'team_category',
                'field'    => 'slug',
                'terms'    => explode(',', $atts['terms']),
            )
        );
    }

    $team_query = new WP_Query($args);

    ob_start();

    if (!$team_query->have_posts()) {
        return '<p>' . __('No team members found for this category.', 'salient') . '</p>';
    }
    echo '<div class="lesm-team-list">';

    while ($team_query->have_posts()) {
        $team_query->the_post();
        $team_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
        $position = get_field('position');
        $location = get_field('location');
        $email = get_field('email');
        $social_links = get_field('linkedin');
        echo '<div class="team-item-wrapper">';
        echo '<div class="team-item">';
        echo '<div class="team-image">';
        if ($team_image) {
            echo '<img src="' . esc_url($team_image) . '" alt="' . esc_attr(get_the_title()) . '" class="team-image" />';
        }
        echo '</div>';
        echo '<div class="team-details">';
        if ($position) {
            echo '<span class="team-position">' . esc_html($position) . '</span>';
        }
        if ($location) {
            echo '<span class="team-location">' . $location . '</span>';
        }
        echo '<h3>' . get_the_title() . '</h3>';
        echo '<div class="team-social">';
        if ($social_links) {
            echo '<a href="' . esc_url($social_links) . '" class="team-linkedin"><img  src="' . get_stylesheet_directory_uri() . '/images/linkedins.png"' . ' alt="LinkedIn Icon"/></a>';
        }
        if ($email) {
            echo '<a href="mailto:' . esc_attr($email) . '" class="team-email">Email</a>';
        }

        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    echo '</div>';

    return ob_get_clean();
}


add_action('wp_head', 'lesm_count_post_views');

function lesm_count_post_views()
{
    if (!is_singular('education')) {
        return; // Only count for single blog posts
    }

    global $post;

    if (empty($post) || !isset($post->ID)) {
        return; // Safety check
    }

    $post_id = $post->ID;
    $count_key = 'lesm_post_views';
    $count = get_post_meta($post_id, $count_key, true);

    if ($count == '') {
        $count = 0;
        update_post_meta($post_id, $count_key, 1); // initialize with 1
    } else {
        $count++;
        update_post_meta($post_id, $count_key, $count);
    }
}


add_shortcode('popular_education_shortcode', 'popular_education_shortcode_callback');

function popular_education_shortcode_callback($atts)
{

    $atts = shortcode_atts(
        array(
            'posts_per_page' => -1,
        ),
        $atts,
        'popular_education'
    );

    $args = array(
        'post_type' => 'education',
        'posts_per_page' => intval($atts['posts_per_page']),
        'orderby' => 'meta_value_num',
        'meta_key' => 'lesm_post_views',
        'order' => 'DESC',
        'post_status' => 'publish',
    );


    $education_query = new WP_Query($args);

    ob_start();

    if (!$education_query->have_posts()) {
        return '<p>' . __('No popular education posts found.', 'salient') . '</p>';
    }

    echo '<div class="lesm-popular-education-list">';

    while ($education_query->have_posts()) {
        $education_query->the_post();
        $feature_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
        $categories = get_the_terms(get_the_ID(), 'education_category');
        $category_names = array();
        $membership = get_field('membership');

        if ($categories && !is_wp_error($categories)) {
            foreach ($categories as $category) {
                $category_names[] = esc_html($category->name);
            }
        }

        render_education_card(get_the_ID());
    }

    echo '</div>';

    return ob_get_clean();
}

function render_education_card($post_id = null)
{
    if (is_null($post_id)) {
        $post_id = get_the_ID();
    }

    if (!$post_id) {
        return;
    }

    $feature_image = get_the_post_thumbnail_url($post_id, 'education_thumbnail');
    $categories = get_the_terms($post_id, 'education_category');
    $category_names = array();
    $excerpt = get_post_field('post_content', $post_id);

    if ($categories && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            $category_names[] = esc_html($category->name);
        }
    }
    echo '<div class="lesm-education-card-item">';
    echo '<div class="lesm-education-card">';
    echo '<div class="education-top" style="background-image: url(' . esc_url($feature_image) . ');">';
    lesm_render_new_badge($post_id);
    print_membership_badge($post_id);
    echo '</div>';
    echo '<div class="lesm-education-card-content">';
    echo '<div class="content-top">';
    echo '<h3>' . get_the_title($post_id) . '</h3>';
    echo '<p class="education-description">' . lesm_trim_excerpt_words($excerpt, 20) . '</p>';
    echo '</div>';
    echo '<div class="content-bottom">';
    if (!empty($category_names)) {
        echo '<span class="education-category">' . implode(', ', $category_names) . '</span>';
    }
    lesm_render_education_view_button($post_id);
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

function lesm_render_education_view_button($post_id = null)
{
    if (is_null($post_id)) {
        $post_id = get_the_ID();
    }

    if (!$post_id) {
        return;
    }

    $membership_required  = get_field('membership', $post_id);
    $permalink = get_permalink($post_id);

    if ($membership_required) {
        // check user is logged in
        if (!is_user_logged_in()) {
            echo '<a href="' . wp_login_url(get_permalink()) . '" class="view-education-btn read-more">Login to View</a>';
            return;
        }

        // add the membership logic here
        if (lesm_user_has_membership_access()) {
            echo '<a href="' . esc_url($permalink) . '" class="view-education-btn read-more">View</a>';
            return;
        } else {
            echo '<a href="' . esc_url(home_url('/membership')) . ' " class="view-education-btn read-more">Member Access</a>';
        }
    } else {
        // if membership is not set, show the view button
        echo '<a href="' . esc_url($permalink) . '" class="view-education-btn read-more">View</a>';
        return;
    }
}

function lesm_user_has_membership_access()
{
    if (!is_user_logged_in()) {
        return false;
    }

    $current_user = wp_get_current_user();

    $allowed_roles = array('administrator', 'member'); // Define allowed roles

    return array_intersect($allowed_roles, $current_user->roles) ? true : false;
}


function lesm_trim_excerpt_words($text, $limit = 150)
{
    $words = explode(' ', wp_strip_all_tags($text));
    if (count($words) > $limit) {
        $words = array_slice($words, 0, $limit);
        return implode(' ', $words) . '...';
    }
    return $text;
}


function lesm_render_new_badge($post_id = null)
{
    if (is_null($post_id)) {
        $post_id = get_the_ID();
    }

    if (!$post_id) {
        return;
    }

    $date = get_the_date('Y-m-d', $post_id);
    $current_date = current_time('Y-m-d');
    $date_diff = strtotime($current_date) - strtotime($date);

    // Check if the post is published within the last 7 days
    if ($date_diff <= $education_new_badge_period * DAY_IN_SECONDS) {
        echo '<span class="new-badge">New</span>';
    }
}



add_shortcode('lesm_education_library_shortcode', 'lesm_education_library_shortcode_callback');
function lesm_education_library_shortcode_callback($atts)
{
    $atts = shortcode_atts(
        array(
            'show_sidebar' => 'no',
            'posts_per_page' => 6,
        ),
        $atts,
        'lesm_education_library'
    );

    $education_categories = get_terms(array(
        'taxonomy' => 'education_category',
        'hide_empty' => false,
    ));

    $paged = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);

    $args = array(
        'post_type' => 'education',
        'posts_per_page' => $atts['posts_per_page'],
        'paged' => $paged,
        'orderby' => 'date',
        'order' => 'ASC',
        'post_status' => 'publish',
    );

    $education_query = new WP_Query($args);

    $education_query_count  = $education_query->post_count;

    ob_start();

    echo '<div class="lesm-education-library">';

    if ($atts['show_sidebar'] === 'yes') {
        echo '<div class="lesm-education-library-sidebar">';
        echo '<form id="lesm-library-form" class="lesm-library-form">';
        echo '<input type="text" id="education-title" name="education-title" placeholder="Search..." />';

        echo '<div class="education-category-checkboxes">';
        echo '<h3 class="search-title">' . esc_html__('View by type', 'salient') . '</h3>';
        if (!empty($education_categories) && !is_wp_error($education_categories)) {
            foreach ($education_categories as $category) {
                echo '<label class="education-category-checkbox" for="' . esc_attr($category->slug) . '">';
                echo '<input type="checkbox" id="' . esc_attr($category->slug) . '" name="education_categories[]" value="' . esc_attr($category->slug) . '" />';
                echo esc_html($category->name);
                echo '</label>';
            }
        } else {
            echo '<p>' . esc_html__('No education types found.', 'salient') . '</p>';
        }
        echo '</div>';
        echo '</form>';
        echo '</div>';
    }

    echo '<div class="lesm-education-library-content">';
    echo '<div class="library-info">';
    echo '<h2 class="library-title">Discover the library</h2>';
    if ($education_query_count > 0) {
        echo '<span class="result-count"> ' . $education_query_count . ' </span>';
    }
    echo '</div>';
    echo '<div class="education-library-wrapper">';
    if (!$education_query->have_posts()) {
        echo '<p>' .  esc_html_e('No education posts found.', 'salient');
        '</p>';
    }

    while ($education_query->have_posts()) {
        $education_query->the_post();
        render_education_card(get_the_ID());
    }
    echo '</div>'; // Close education-library_wrapper
    if ($education_query->max_num_pages > 1) {
        echo '<div class="library-pagination">';
        echo '<button id="load-more-education" data-post-per-page="' . esc_attr($atts['posts_per_page']) . '" data-current-page="' . esc_attr($paged) . '" data-max-pages="' . esc_attr($education_query->max_num_pages) . '" class="load-more-button">' . esc_html__('Load More', 'salient') . '</button>';
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';

    echo '</div>';

    return ob_get_clean();
}

add_action('wp_ajax_lesm_load_more_education', 'lesm_load_more_education_callback');
add_action('wp_ajax_nopriv_lesm_load_more_education', 'lesm_load_more_education_callback');

function lesm_load_more_education_callback()
{
    // Get and validate page number
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 0;
    $posts_per_page = isset($_POST['quantity']) ? intval($_POST['quantity']) : 6;

    if ($posts_per_page < 1) {
        wp_send_json_error(__('Invalid posts per page value.', 'salient'));
    }

    if ($paged < 1) {
        wp_send_json_error(__('Invalid page number.', 'salient'));
    }

    // Define query
    $args = array(
        'post_type'      => 'education',
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'ASC',
        'post_status'    => 'publish',
    );

    $education_query = new WP_Query($args);

    ob_start();

    if ($education_query->have_posts()) {
        while ($education_query->have_posts()) {
            $education_query->the_post();
            render_education_card(get_the_ID());
        }
    }

    $html = ob_get_clean();
    $count = $education_query->post_count;

    wp_reset_postdata();

    wp_send_json_success(array(
        'html'   => $html,
        'count'  => $count,
    ));
}


add_action('after_setup_theme', 'lesm_setup_theme');

function lesm_setup_theme()
{
    add_image_size('education_thumbnail', 450, 300, true);
}


add_action('wp_ajax_lesm_filter_library', 'lesm_filter_library_callback');
add_action('wp_ajax_nopriv_lesm_filter_library', 'lesm_filter_library_callback');

function lesm_filter_library_callback()
{
    if (!isset($_POST['form_data'])) {
        wp_send_json_error(__('Invalid request.', 'salient'));
    }

    parse_str($_POST['form_data'], $form_data);

    $education_title = sanitize_text_field($form_data['education-title'] ?? '');
    $education_categories = isset($form_data['education_categories']) ? array_map('sanitize_text_field', (array) $form_data['education_categories']) : [];

    $args = array(
        'post_type'      => 'education',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'ASC',
        'post_status'    => 'publish',
        's'              => $education_title,
    );

    if (!empty($education_categories)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'education_category',
                'field'    => 'slug',
                'terms'    => $education_categories,
            ),
        );
    }

    $education_query = new WP_Query($args);
    $education_query_count = $education_query->post_count;

    ob_start();

    if (!$education_query->have_posts()) {
        echo '<p>' . esc_html__('No education posts found.', 'salient') . '</p>';
    } else {
        while ($education_query->have_posts()) {
            $education_query->the_post();
            render_education_card(get_the_ID());
        }
    }

    wp_reset_postdata();

    $response = ob_get_clean();
    wp_send_json_success(
        array(
            'content' => $response,
            'count' => $education_query_count,
        )
    );
}

function lesm_author_card($author_id)
{
    $author = get_userdata($author_id);
    if (!$author) {
        return '';
    }

    $post_id = get_the_ID();
    $post_date = get_the_date('F j, Y', $post_id);
?>
    <div class="lesm-author-card">
        <div class="author-avatar">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/author.png" alt="author-name" class="author-image">
        </div>
        <div class="author-info">
            <h4 class="author-name">Prof. Joseph Vella</h4>
            <p class="author-bio">Head of Upper-GI Surgery, Mater Dei Hospital
                <span class="author-date"> <?php echo $post_date; ?></span>
            </p>
        </div>
    </div>
<?php
}


add_shortcode('lesm_events_shortcode', 'lesm_events_shortcode_callback');
function lesm_events_shortcode_callback($atts)
{
    $atts = shortcode_atts(
        array(
            'past_events' => 'no',
        ),
        $atts,
        'lesm_events'
    );

    $past_events = $atts['past_events'];


    ob_start();

    // Query for events
    $args = array(
        'post_type' => 'events',
        'posts_per_page' => -1,
    );

    if($past_events === 'no'){
        $args['meta_key'] = 'event_start_date';
        $args['orderby'] = 'meta_value';
        $args['order'] = 'ASC';
        $args['meta_query'] = array(
            array(
                'key' => 'event_start_date',
                'value' => date('Y-m-d'),
                'compare' => '>=',
                'type' => 'DATE',
            ),
        );
    } else {
        $args['meta_key'] = 'event_start_date';
        $args['orderby'] = 'meta_value';
        $args['order'] = 'DESC';
        $args['meta_query'] = array(
            array(
                'key' => 'event_start_date',
                'value' => date('Y-m-d'),
                'compare' => '<=',
                'type' => 'DATE',
            ),
        );
    }
    

    $events_query = new WP_Query($args);
    echo '<div class="lesm-events-gird arvhive-events">';
    if ($events_query->have_posts()) {
        while ($events_query->have_posts()) {
            $events_query->the_post();
            lesm_cpt_post_card_layout_one(get_the_ID());
        }
    } else {
        echo '<p>' . __('No events found.', 'salient') . '</p>';
    }
    echo '</div>'; // Close events-list

    wp_reset_postdata();

    return ob_get_clean();
}


add_shortcode('lesm_news_shortcode', 'lesm_news_shortcode_callback');

function lesm_news_shortcode_callback($atts)
{
    $atts = shortcode_atts(
        array(
            'posts_per_page' => '9',
        ),
        $atts,
        'lesm_news'
    );

    ob_start();

    // Query for events
    $news_query = array(
        'post_type' => 'post',
        'posts_per_page' => $atts['posts_per_page'],
    );
    $news_query = new WP_Query($news_query);
    echo '<div class="lesm-events-gird arvhive-events news">';
    $count = 0;
    if ($news_query->have_posts()) {
        while ($news_query->have_posts()) {
            $news_query->the_post();
            $count++;
            if ($count == 1) {
                lesm_cpt_post_card_layout_one(get_the_ID());
            } else {
                lesm_cpt_post_card_layout_two(get_the_ID());
            }
        }
    } else {
        echo '<p>' . __('No news found.', 'salient') . '</p>';
    }
    echo '</div>'; // Close events-list
    if ($news_query->max_num_pages > 1) {
        echo '<div class="news-load-more-btn">';
        echo  '<a href="#" id="load-more-news" data-post-per-page="' . $atts['posts_per_page'] . '" data-current-page="1" data-max-page="' . $news_query->max_num_pages . '">Load More</a>';
        echo '</div>';
    }

    wp_reset_postdata();

    return ob_get_clean();
}

function lesm_cpt_post_card_layout_one($post_id = null)
{
    
    $multiple_dates_event = get_field('event_type', $post_id);
    $event_start_date = get_field('event_start_date', $post_id);
    $event_end_date = get_field('event_end_date', $post_id);

    if($multiple_dates_event){
        $start_date = date('d', strtotime($event_start_date));
        $end_date = date('d', strtotime($event_end_date));
        $event_date = $start_date . ' - ' . $end_date . ' ' . date('M Y', strtotime($event_start_date));
    } else {
        $event_date = $event_start_date;
    }

    $location = get_field('location', $post_id);
    $highlights = get_field('highlights', $post_id);
    $feature_image = get_the_post_thumbnail_url($post_id, 'full');
    $post_type = get_post_type($post_id);
    $learn_more_text = $post_type === 'events' ? 'View Event' : 'Read More';

    echo '<div class="event-item" style="background-image: url(' . esc_url($feature_image) . ');">';
    echo '<div class="event-content">';
    echo '<h3 class="event-title">' . get_the_title($post_id) . '</h3>';
    echo '<p class="event-excerpt">' . lesm_trim_excerpt_words(get_the_excerpt($post_id), 10) . '</p>';
    echo '<div class="event-details">';
    if ($event_date) {
        echo '<span class="event-date">' . esc_html($event_date) . '</span>';
    }
    if ($location) {
        echo '<span class="event-location">' . esc_html($location) . '</span>';
    }
    echo '</div>'; // Close event-details
    echo '<a href="' . get_permalink() . '" class="read-more view-event-btn">' . $learn_more_text . '</a>';
    echo '</div>';
    echo '</div>';
}
function lesm_cpt_post_card_layout_two($post_id = null)
{
    $feature_image = get_the_post_thumbnail_url($post_id, 'full');
    echo '<div class="event-item">';
    if ($feature_image) {
        echo '<div class="post-image">';
        echo '<img src="' . esc_url($feature_image) . '" alt="Event Image">';
        echo '</div>';
    } else {
        echo '<div class="post-image">';
        echo '<img src="' . get_stylesheet_directory() . '/images/placeholder.jpg" alt="Event Image">';
        echo '</div>';
    }

    echo '<div class="event-content">';
    echo '<h3 class="event-title">' . get_the_title($post_id) . '</h3>';
    echo '<p class="event-excerpt">' . lesm_trim_excerpt_words(get_the_excerpt($post_id), 10) . '</p>';
    echo '<div class="post-action">';
    echo '<a href="' . get_permalink() . '" class="read-more view-event-btn">Read more</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

function render_salient_g_section_post($post_id = null)
{
    $post = get_post($post_id);
    if ($post && $post->post_type === 'salient_g_sections') {
        setup_postdata($post);
        echo '<div class="custom-salient-g-section">';
        echo apply_filters('the_content', $post->post_content);
        wp_reset_postdata();
        echo '</div>';
    } else {
        echo '<p>' . __('No salient_g_sections found.', 'salient') . '</p>';
    }
}



add_action('wp_ajax_lesm_load_more_news', 'lesm_load_more_news_callback');
add_action('wp_ajax_nopriv_lesm_load_more_news', 'lesm_load_more_news_callback');

function lesm_load_more_news_callback()
{
    $next_page = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $post_per_page = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $post_per_page,
        'paged' => $next_page,
    );

    $news_query = new WP_Query($args);
    $max_page = $news_query->max_num_pages;

    ob_start();

    if ($news_query->have_posts()) {
        while ($news_query->have_posts()) {
            $news_query->the_post();
            lesm_cpt_post_card_layout_two(get_the_ID());
        }
    } else {
        echo '<p>' . __('No news found.', 'salient') . '</p>';
    }

    wp_reset_postdata();

    $response = ob_get_clean();
    wp_send_json_success(
        array(
            'html' => $response,
            'current_page' => $next_page,
            'max_page' => $max_page,
        )
    );
}

add_filter('show_admin_bar', 'hide_admin_bar_for_non_admins');

function hide_admin_bar_for_non_admins($show) {
    if (!current_user_can('administrator')) {
        return false;
    }
    return $show;
}


// LESM SETTING PAGE
add_action('admin_menu', 'lesm_add_settings_page');
add_action('admin_init', 'lesm_settings_init');

function lesm_add_settings_page() {
    add_menu_page(
        'LESM Settings',
        'LESM Settings',
        'manage_options',
        'lesm-settings',
        'lesm_settings_page_callback'
    );
}

function lesm_settings_init() {
    // Register the settings group
    register_setting('lesm_settings_group', 'lesm_submission_google_form_url');
    
    // Add settings section
    add_settings_section(
        'lesm_main_section',
        'Main Settings',
        'lesm_section_callback',
        'lesm-settings'
    );
    
    // Add settings field
    add_settings_field(
        'lesm_submission_google_form_url',
        'Submission Google Form URL',
        'lesm_field_callback',
        'lesm-settings',
        'lesm_main_section'
    );
}

function lesm_section_callback() {
    echo '<p>Configure your settings below:</p>';
}

function lesm_field_callback() {
    $value = get_option('lesm_submission_google_form_url', '');
    echo '<input type="url" name="lesm_submission_google_form_url" value="' . esc_attr($value) . '" style="width: 400px;" />';
}

function lesm_settings_page_callback() {
    ?>
    <div class="wrap">
        <h1>LESM Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('lesm_settings_group');
            do_settings_sections('lesm-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}



function restrict_member_area() {
    if ( is_page('member-area') && ! is_user_logged_in() ) {
        wp_redirect( wp_login_url( get_permalink() ) );
        exit;
    }
}
add_action( 'template_redirect', 'restrict_member_area' );


function redirect_user_away_from_admin() {
    if (is_admin() && !wp_doing_ajax()) {
        // check user roles
        $user = wp_get_current_user();

        // // Only allow administrators to access admin area
        // if (in_array('administrator', (array) $user->roles)) {
        //     return;
        // }

        // // Redirect all other roles to the homepage
        // $roles_to_redirect = array('subscriber', 'student', 'member', 'initiate', 'associate');
        // if (array_intersect($roles_to_redirect, (array) $user->roles)) {
        //     wp_redirect(home_url());
        //     exit;
        // }

        if ( ! current_user_can('administrator') ) {
            wp_redirect( home_url('/member-area') );
            exit;
        }
    }
}


add_action('admin_init', 'redirect_user_away_from_admin');


// Redirect non-admins after login
function lesm_custom_login_redirect( $redirect_to, $request, $user ) {
    if ( isset($user->roles) && is_array($user->roles) && ! in_array( 'administrator', $user->roles ) ) {
        return home_url('/member-area');
    }
    return $redirect_to; // default for admins
}
add_filter( 'login_redirect', 'lesm_custom_login_redirect', 10, 3 );

// error log to see the next cron jobs time
error_log('Next LESM cron: ' . date_i18n('Y-m-d H:i:s', wp_next_scheduled('lesm_check_member_expiration')));
