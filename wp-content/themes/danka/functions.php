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



function get_flex_content( $content ) {
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




// function custom_acf_json_save_paths( $paths, $post ) {
//     if ( $post['title'] === 'Theme Settings' ) {
//         $paths = array( get_stylesheet_directory() . '/options-pages' );
//     }

//     if ( $post['title'] === 'Theme Settings Fields' ) {
//         $paths = array( get_stylesheet_directory() . '/field-groups' );
//     }

//     return $paths;
// }
// add_filter( 'acf/json/save_paths', 'custom_acf_json_save_paths', 10, 2 );

// function my_acf_cpt_save_folder( $path ) {
//     return get_stylesheet_directory() . '/acf-json/post-types'; 
// }
// add_filter( 'acf/settings/save_json/type=acf-post-type', 'my_acf_cpt_save_folder' );

// function my_acf_json_save_point( $path ) {
//     return get_stylesheet_directory() . '/my-custom-folder';
// }
// add_filter( 'acf/settings/save_json', 'my_acf_json_save_point' );