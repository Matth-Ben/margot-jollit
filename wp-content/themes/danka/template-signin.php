<?php

/**
 * Template Name: Inscription
 */

global $wp_query;

$context = \Timber\Timber::context();
$templates = array( 'template-signin.twig' );
$timber_post = \Timber\Timber::get_post( false, get_custom_timber_post() );
$context['post'] = $timber_post;
 
\Timber\Timber::render( $templates, $context );