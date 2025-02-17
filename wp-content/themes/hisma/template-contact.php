<?php

/**
 * Template Name: Contact
 */

global $wp_query;

$context = \Timber\Timber::context();
$templates = array( 'index.twig' );

// styleguide
$path = get_template_directory() . '/data.json';

if ( file_exists( $path ) ) {
    $content = file_get_contents( $path );
    $app_data = $content ? json_decode( $content, true ) : null;

    if ( $app_data ) {
        $context['app_data'] = $app_data;
    }
}

$timber_post = \Timber\Timber::get_post( false, get_custom_timber_post() );
$context['post'] = $timber_post;
$context['data'] = get_fields();

\Timber\Timber::render( array( 'template-contact.twig' ), $context );
