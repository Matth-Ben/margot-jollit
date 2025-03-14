<?php

add_action( 'init', function() {
    $args = array(
       'label'        => 'Formulaire',
       'public'       => false, // --> false
       'rewrite'      => false,
       'hierarchical' => false,
       'show_admin_column' => true
    );
    
    register_taxonomy( 'the_form', 'af_entry', $args );
}, 0 );