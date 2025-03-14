<?php

// advancedform shortcode
function advancedform_shortcode($atts) {

    $form_id = isset($atts['id']) ? $atts['id'] : false;
    $form_name = isset($atts['name']) ? $atts['name'] : false;
    
    if ($form_id == false && $form_name) {
        $title = $form_name;
        $args = array("post_type" => "af_form", "s" => $title);
        $query = get_posts($args);
        $form_id = $query[0] ? $query[0]->ID : null;
    }

    if ($form_id) {
        $af_form = new AF_Form($form_id);
        return $af_form->generate_form_html(true);
    }
}

add_action('init', function() {
    add_shortcode('advancedform', 'advancedform_shortcode');
});
