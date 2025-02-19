<?php

/**
 * Template Name: Page Login
 */

 global $wp_query;

 $context = \Timber\Timber::context();
 $templates = array( 'index.twig' );
 
 $timber_post = \Timber\Timber::get_post( false, get_custom_timber_post() );
 $context['post'] = $timber_post;
 $context['is_template_collection'] = $is_template_collection ?? false;
 
 if ( post_password_required( $timber_post->ID ) ) {
     $templates = array( 'single-password.twig' );
 } else {
     $templates = array( 'page-' . $timber_post->post_name . '.twig', 'template-login.twig' );
 }
 
 \Timber\Timber::render( $templates, $context );