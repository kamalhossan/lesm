<?php

/**
 * The template for displaying single education posts.
 */

get_header();

$membership_required  = get_field('membership', get_the_ID());

if ($membership_required && !lesm_user_has_membership_access()) {
    echo '<div class="lesm-cpt-container">';
    echo 'You dont have access to this content.';
    get_footer();
    exit;
}
echo '<div class="lesm-cpt-main-content">';
$terms  = get_the_terms(get_the_ID(), 'education_category');
$term   = $terms ? array_shift($terms) : null;
$term_name = strtolower($term->name);

echo '<div class="lesm-cpt-container ' . esc_html($term_name) . '">';
echo '<div class="lesm-cpt-breadcrumbs">';
echo '<a href="' . esc_url(home_url('/education')) . '">' . esc_html__('< Back to Education Page', 'textdomain') . '</a>; ';

if ($term) {
    echo '<div class="post-meta">';
    echo '<span class="education-category">' . esc_html($term->name) . '</span>';
    echo '</div>';
}

echo '</div>';
echo '<div class="lesm-cpt-post-wrapper">';
echo '<div class="lesm-cpt-post-content">';
echo '<div class="post-details">';
echo '<h1 class="post-title">' . get_the_title() . '</h1>';
echo '<p class="post-excerpt">' . get_the_excerpt() . '</p>';
if ($term_name !== 'video') {
    lesm_author_card(get_the_author_ID());
}
echo '</div>';
echo '</div>';
echo '<div class="lesm-cpt-post-features">';

if ($term_name !== 'video') {
    if (has_post_thumbnail()) {
        echo '<div class="post-thumbnail">';
        echo get_the_post_thumbnail(get_the_ID(), 'full');
        echo '</div>';
    } else {
        echo '<div class="post-thumbnail">';
        echo '<img src="' . get_stylesheet_directory_uri() . '/images/post-placeholder.png" alt="Placeholder Image">';
        echo '</div>';
    }
}
if ($term_name === 'video') {
    $video_url = get_field('video_url', get_the_ID());
    if (!empty($video_url)) {
        echo '<div class="post-video">';
        echo '<iframe width="100%" height="695" src="' . esc_url($video_url) . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>';
        echo '</div>';
    }
}
echo '</div>';
echo '</div>'; // lesm-cpt-post-wrapper
echo '<div class="lesm-cpt-post-main-content">';

if ($term_name === 'video') {
    echo '<div class="video-author-meta">';
    lesm_author_card(get_the_author_ID());
    echo '</div>';
}

the_content();
echo '</div>';

echo '</div>';
echo '</div>';

get_footer();
