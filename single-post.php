<?php

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}

get_header();

echo '<div class="lesm-cpt-container news">';
echo '<div class="lesm-cpt-breadcrumbs">';
echo '<a href="' . esc_url(home_url('/news')) . '">' . esc_html__('< Back to News Page', 'textdomain') . '</a>; ';
echo '</div>'; // Breadcrumbs


if (has_post_thumbnail()) {
    echo '<div class="event-features-image">';
    echo get_the_post_thumbnail(get_the_ID(), 'full');
    echo '</div>';
}

echo '<div class="lesm-cpt-event-wrapper">';
echo '<h1 class="post-title">' . get_the_title() . '</h1>';
echo '<div class="post-excerpt">' . get_the_excerpt() . '</div>';

echo '<div class="event-action">';
lesm_author_card(get_the_author_ID());
echo '</div>'; // event-action
echo '</div>'; // lesm-cpt-post-wrapper
echo '<div class="lesm-cpt-event-content">';
the_content();
echo '</div>';
echo '</div>';

render_salient_g_section_post(287);

get_footer();
