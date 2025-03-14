<?php

// Get ACF field from edit/new af_form page
function af_populate_with_acf_fields()
{
    $post_type = $_POST['post_type'];
    $af_form_id = $_POST['af_form_id'];

    $af_form = new AF_Form($af_form_id);
    echo json_encode($af_form->ajax_get_acf_fields($post_type));
    wp_die();
}

add_action('wp_ajax_load_acf_fields', 'af_populate_with_acf_fields');
add_action('wp_ajax_nopriv_load_acf_fields', 'af_populate_with_acf_fields');


function af_export_form()
{
    $post_id = is_numeric( $_POST['advancedform'] ) ? intval( $_POST['advancedform'] ) : null;
    
    if ( $post_id ) {
        $p = get_post( $post_id );

        if ( $p ) {
            get_post_meta( $post_id, 'af_form_data', true );
            
            echo json_encode( array(
                'title' => $p->post_title,
                'settings' => json_decode( get_post_meta( $post_id, 'af_form_data', true ) ),
            ) );
        }
    } else {
        echo json_encode( array(
            'error' => 'No post_id'
        ) );
    }

    wp_die();
}

add_action('wp_ajax_export_form', 'af_export_form');
add_action('wp_ajax_nopriv_export_form', 'af_export_form');


function af_import_form()
{
    if ( isset( $_POST['file_data'] ) ) {
        $data = json_decode( stripslashes( $_POST['file_data'] ), true );
    
        if ( isset( $data['title'] ) && isset( $data['settings'] ) ) {
            $post_id = wp_insert_post( array(
                'post_title' => $data['title'],
                'post_type' => 'af_form',
                'post_status' => 'publish',
            ) );
        
            update_post_meta( $post_id, 'af_form_data', json_encode( $data['settings'], JSON_UNESCAPED_UNICODE ) );
        
            echo json_encode( array(
                'post' => $post_id,
                'data' => $data['settings'],
                't' => json_encode( 'Prénom' )
            ) );

            wp_die();
        }

    }

    echo json_encode( array(
        'error' => 'No file_data'
    ) );

    wp_die();
}

add_action('wp_ajax_import_form', 'af_import_form');
add_action('wp_ajax_nopriv_import_form', 'af_import_form');