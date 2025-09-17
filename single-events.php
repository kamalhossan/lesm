<?php


get_header();

$forminator_form_id = 408;
$args = array(
    'form_id' => $forminator_form_id,
);

$entries = Forminator_Form_Entry_Model::query_entries($args);
$total_entries = 0;

foreach ( $entries as $entry ) {
    if ( isset($entry->meta_data['hidden-1']['value']) ) {
        $form_post_id = $entry->meta_data['hidden-1']['value'];
        if ( get_the_ID() == $form_post_id ) {
            $total_entries++;
        }
    }
}


// echo '<pre>';
// var_dump($entries);
// echo '</pre>';

$submission_form_url = get_option('lesm_submission_google_form_url');


echo '<div class="lesm-cpt-container events">';
echo '<div class="lesm-cpt-breadcrumbs">';
echo '<a href="' . esc_url(home_url('/events')) . '">' . esc_html__('< Back to Events Page', 'textdomain') . '</a>; ';
echo '</div>'; // Breadcrumbs

$event_date = get_field('event_date');
$location = get_field('location');
$highlights = get_field('highlights');

if (has_post_thumbnail()) {
    echo '<div class="event-features-image">';
    echo get_the_post_thumbnail(get_the_ID(), 'full');
    echo '</div>';
}

echo '<div class="lesm-cpt-event-wrapper">';
echo '<div class="location-date">';
if ($event_date) {
    echo '<span class="event-date">' . esc_html(date_i18n(get_option('date_format'), strtotime($event_date))) . ',</span>';
}
if ($location) {
    echo '<span class="event-location">' . esc_html($location) . '</span>';
}
echo '</div>'; // location-date
echo '<h1 class="post-title">' . get_the_title() . '</h1>';
echo '<div class="post-excerpt">' . get_the_excerpt() . '</div>';

echo '<div class="event-action">';
if ($submission_form_url) {
    echo '<a href="' . esc_url($submission_form_url) . '" class="btn btn-outlined">' . ($total_entries >= 1 ? $total_entries . ' ' . esc_html__('Submissions', 'textdomain') : esc_html__('Submission', 'textdomain')) . '</a>';
}
echo '<a href="#" class="btn btn-filled btn-event-registration">' . esc_html__('Registration', 'textdomain') . '</a>';
echo '</div>'; // event-action
echo '</div>'; // lesm-cpt-post-wrapper
echo '<div class="lesm-cpt-event-content">';
the_content();
echo '</div>';
echo '</div>';


render_salient_g_section_post(150);
render_salient_g_section_post(106);

// popup section

?>

<div class="lesm-events-submission-popup">
    <div class="lesm-events-submission-popup-content">
        <?php echo do_shortcode('[forminator_form id="408"]'); ?>
        <span class="popup-close"></span>
    </div>
</div>



<?php



get_footer();
