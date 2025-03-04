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


// after header
add_action( 'wp_head', function() {
    $html = "";
    $svg_files = glob( get_template_directory() . '/assets/icons/*.svg' );

    foreach ( $svg_files as $svg_file ) {
        $svg = file_get_contents( $svg_file );
        $filename = pathinfo( $svg_file, PATHINFO_FILENAME );
        $svg = str_replace( '<svg', "<symbol id='icon-{$filename}' aria-hidden='true' focusable='false'", $svg );
        $svg = str_replace( '</svg>', '</symbol>', $svg );
        $html .= $svg;
    }
    echo "<svg style='display: none;'>{$html}</svg>";
}, 10 );


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
            
            if ( str_contains( $compile, 'component--halfscreen' ) ) {
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






////
add_filter( 'danka_breadcrumbs', function( $items, $post_id )
{    
    if ( get_page_template_slug( $post_id ) === "template-collection.php" ) {
        $last_item = array_pop( $items );
        $last_item['text'] = pll__( 'En détail' );
        $data = get_fields( $post_id );
        $collection_data = isset( $data['collection'] ) ? get_fields( $data['collection'] ) : null;
        $museum_data = isset( $collection_data['museum'] ) ? get_fields( $collection_data['museum'] ) : null;
        
        if ( $museum_data )  {
            $items[1] = array(
                'text' => get_the_title( $collection_data['museum'] ),
                'url' => get_permalink( $collection_data['museum'] ),
                'id' => $collection_data['museum'],
            );
        }

        if ( $collection_data ) {
            $items[2] = array(
                'text' => get_the_title( $data['collection'] ),
                'url' => get_permalink( $data['collection'] ),
                'id' => $data['collection'],
            );
        }

        $items[] = $last_item;
        
        return $items;
    }
}, 10, 2 );
////


// Mettre à jour le champ "museum" à -1 si "all_museums" est coché
add_action( 'acf/save_post', function( $post_id ) {
    $post_type = get_post_type( $post_id );

    if ( in_array( $post_type, array( 'exhibition', 'event', 'news' ) ) ) {
        $data = get_fields( $post_id );
        
        if ( $data['all_museums'] ) {
            update_field( 'museum', -1, $post_id );
        }
    }
} );


// Sélectionner les collections qui appartiennent au musée
add_filter('acf/fields/relationship/query/name=collections', function ( $args, $field, $post_id ) {

    if ( get_post_type( $post_id ) === 'museum' ) {
        $args['meta_query'][] = array(
            'key' => 'museum',
            'value' => array( $post_id ),
            'compare' => 'IN',
        );
    }

    return $args;
}, 10, 3);


// Sélectionner les actualités qui appartiennent au musée
add_filter('acf/fields/relationship/query/name=news', function ( $arguments, $field, $post_id ) {

    if ( get_post_type( $post_id ) === 'museum' ) {
        $arguments['meta_query'] = array(
            'relation' => 'AND',
            array(
                'key' => 'museums',
                'value' => '"' . $post_id . '"',
                'compare' => 'LIKE',
            ),
            array(
                'key' => 'is_extramural',
                'value' => '0',
                'compare' => '=',
            ),
        );
    }

    return $arguments;
}, 10, 3);


add_action( 'init', function() {
    if ( !is_admin() && isset( $_GET['update-data'] ) ) {
        $posts = get_posts( array(
            'post_type' => 'news',
            'posts_per_page' => -1,
        ) );
    
        
        foreach ( $posts as $p ) {
            $museum_id = get_field( 'museum', $p->ID );
    
            if ( $museum_id && $museum_id !== -1 ) {
                update_field( 'museums', array($museum_id), $p->ID );
                update_field( 'is_extramural', 0, $p->ID );
            }
        }
        
        $posts = get_posts( array(
            'post_type' => 'event',
            'posts_per_page' => -1,
        ) );
    
        foreach ( $posts as $p ) {
            $museum_id = get_field( 'museum', $p->ID );
    
            if ( $museum_id && $museum_id !== -1 ) {
                update_field( 'museums', array($museum_id), $p->ID );
                update_field( 'is_extramural', 0, $p->ID );
            }
        }
        
        $posts = get_posts( array(
            'post_type' => 'exhibition',
            'posts_per_page' => -1,
        ) );
    
        foreach ( $posts as $p ) {
            $museum_id = get_field( 'museum', $p->ID );
    
            if ( $museum_id && $museum_id !== -1 ) {
                update_field( 'museums', array($museum_id), $p->ID );
                update_field( 'is_extramural', 0, $p->ID );
            }
        }
        die;
    }
} );



