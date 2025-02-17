<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_filter = null;
$context = \Timber\Timber::context();
$context['post_type'] = get_queried_object();
$context['wp_query'] = $wp_query;
$templates = array( 'archive.twig', 'index.twig' );
$arguments = array(
    'post_type' => $context['post_type']->name,
    'posts_per_page' => 6,
    'paged' => get_query_var( 'paged' ),
);

if ( isset( $_GET['filter'] ) && $_GET['filter'] !== '' ) {
    $current_filter = $_GET['filter'];
    $arguments['tax_query'] = array(
        array(
            'taxonomy' => 'type_of_' . $context['post_type']->name,
            'field' => 'slug',
            'terms' => $current_filter,
        ),
    );
}

$context['current_filter'] = $current_filter;
$context['posts'] = new \Timber\PostQuery( $arguments );

\Timber\Timber::render( $templates, $context );
