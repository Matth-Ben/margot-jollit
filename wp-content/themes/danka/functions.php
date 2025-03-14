<?php

use Timber\Twig;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

include get_template_directory() . "/core/index.php";
include_once get_template_directory() . "/includes/index.php";

function search_filter( $query ) {
    if ( !is_admin() && $query->is_main_query() ) {
        if ( $query->is_search ) {
            $query->set( 'paged', ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1 );
            $query->set( 'posts_per_page', 10 );
        }
    }
}

add_action( 'pre_get_posts', 'search_filter' );


function get_month_list( $end_date ) {
    if ( ! $end_date ) {
        return [];
    }

    $month_list = array();
    $end_date = new DateTime( $end_date );

    // set first day of the month
    $start_date = new DateTime( 'first day of this month' );
    $start_date->setTime( 0, 0, 0 );

    while ( $start_date <= $end_date ) {
        $month_list[] = $start_date->format( 'Y-m' );
        $start_date->modify( '+1 month' );
    }

    return $month_list;
}


function mapped_implode( $glue, $array, $symbol = '=' ) {
    return implode( $glue, array_map(
            function( $k, $v ) use( $symbol ) {
                return $k . $symbol . $v;
            },
            array_keys($array), 
            array_values($array)
        )
    );
}


function get_url_parameters() {
    $url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $url_parts = parse_url($url);

    // Si l'URL n'a pas de chaîne de requête
    if ( isset( $url_parts['query'] ) ) {
        parse_str( $url_parts['query'], $parameters );
    } else {
        $parameters = array();
    }

    return $parameters;
}



function get_flex_content( $content, $type = null ) {
    $new_content = array();
    $halfscreen_content = array();

    foreach ( $content as $item ) {
        $name = $item['acf_fc_layout'];
        $filename = str_replace( '_', '-', $name ) . '.twig';
        $path = "acf-components/" . $filename;

        if ( file_exists( get_template_directory() . '/views/' . $path ) && isset( $item[$name] ) ) {
            $compile = \Timber\Timber::compile( $path, array( 'data' => $item[$name] ) );
            
            if ( str_contains( $compile, '--halfscreen' ) ) {
                $halfscreen_content[] = $compile;
            } else {
                if ( $halfscreen_content ) {
                    $new_content[] = $halfscreen_content;
                    $halfscreen_content = array();
                }
                $new_content[] = $compile;
            }
        }
    }
    
    if ( $halfscreen_content ) {
        $new_content[] = $halfscreen_content;
    }

    return $new_content;
}
