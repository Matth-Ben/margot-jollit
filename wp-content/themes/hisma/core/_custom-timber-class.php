<?php

function get_custom_timber_post( $post_id = null, $post_type = null ) {
    $post_class = $post_id ? ucfirst( get_post_type( $post_id ) ) : ucfirst( get_post_type() );

    if ( $post_type ) {
        $post_class = ucfirst( $post_type );
    }

    $path = get_template_directory() . '/class/' . $post_class . '.php';

    if ( file_exists( $path ) ) {
        require_once $path;
        
        if ( class_exists( $post_class ) ) {
            return $post_class;
        }
    }

    return '\Timber\Post';
}

add_filter( 'Timber\PostClassMap', function( $post_class ) {
    global $post;

    $custom_post_class = ucfirst( $post->post_type );
    $path = get_template_directory() . '/class/' . $custom_post_class . '.php';

    if ( file_exists( $path ) ) {
        require_once $path;
        
        if ( class_exists( $custom_post_class ) ) {
            return $custom_post_class;
        }
    }
    
    return $post_class;
}, 100 );
