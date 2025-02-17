<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

include get_template_directory() . "/core/index.php";
include_once get_template_directory() . "/includes/index.php";

// ACF is required
if ( !is_admin() && !function_exists( 'get_fields' ) ) {
    echo 'ACF plugin is required';
    die;
}

// Specify results per page for search
function search_filter( $query ) {
    if ( !is_admin() && $query->is_main_query() ) {
        if ( $query->is_search ) {
            $query->set( 'paged', ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1 );
            $query->set( 'posts_per_page', 10 );
        }
    }
}
add_action( 'pre_get_posts', 'search_filter' );
