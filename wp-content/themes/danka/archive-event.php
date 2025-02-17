<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_filter = null;
$specific_periods = array( 'current_week', 'next_week', 'next_weekend' );
$current_period = isset( $_GET['period'] ) && $_GET['period'] ? $_GET['period'] : null;
$current_taxonomies = isset( $_GET['taxonomies'] ) && $_GET['taxonomies'] ? $_GET['taxonomies'] : null;
$context = \Timber\Timber::context();
$context['post_type'] = get_queried_object();
$context['wp_query'] = $wp_query;
$templates = array( 'archive-' . $context['post_type']->name . '.twig', 'archive.twig', 'index.twig' );
$arguments = array(
    'post_type' => 'event',
    'posts_per_page' => 9,
    'paged' => get_query_var( 'paged' ),
    'meta_key' => 'date',
    'orderby' => 'meta_value_num',
    'order' => 'ASC',
);

// Taxonomies
if ( $current_taxonomies ) {
    $arguments['tax_query'] = array( 'relation' => 'AND' );

    foreach ( explode( '|', $current_taxonomies ) as $taxonomy ) {
        $taxonomy = explode( ',', $taxonomy );

        if ( count( $taxonomy ) === 2 ) {
            $arguments['tax_query'][] = array(
                'taxonomy' => $taxonomy[0],
                'field' => 'slug',
                'terms' => $taxonomy[1],
            );
        }
    }
}

// Période
if ( $current_period ) {
    $start_date = null;
    $end_date = null;

    if ( in_array( $current_period, $specific_periods ) ) {
        switch ( $current_period ) {
            case 'current_week':
                $start_date = new DateTime( 'monday this week' );
                $end_date = new DateTime( 'sunday this week' );
                break;
            case 'next_week':
                $start_date = new DateTime( 'monday next week' );
                $end_date = new DateTime( 'sunday next week' );
                break;
            case 'next_weekend':
                $start_date = new DateTime( 'saturday next week' );
                $end_date = new DateTime( 'sunday next week' );
                break;
        }
    }
    else {
        $current_period = explode( '-', $current_period );

        // Si il y a un mois et une année 
        if ( count( $current_period ) === 2 ) {
            $start_date = new DateTime( "{$current_period[0]}-{$current_period[1]}-1" );
            $start_date->setTime( 0, 0, 0 );
            $start_date->setDate( $current_period[0], $current_period[1], 1 );
            $end_date = new DateTime( "{$current_period[0]}-{$current_period[1]}-1" );
            $end_date->setTime( 0, 0, 0 );
            $end_date->modify( 'last day of this month' );
        }
    }
    
    if ( $start_date && $end_date ) {
        $start_date->setTimezone( new DateTimeZone( 'Europe/Paris' ) );
        $end_date->setTimezone( new DateTimeZone( 'Europe/Paris' ) );

        // Si la date de début ou la date de fin sont pendant la durée de l'événement
        $arguments['meta_query'][] = array(
            'relation' => 'OR',
            array(
                'relation' => 'AND',
                array(
                    'key' => 'dates_start_date',
                    'value' => $start_date->format( 'Ymd' ),
                    'compare' => '>=',
                    'type' => 'DATE',
                ),
                array(
                    'key' => 'dates_start_date',
                    'value' => $end_date->format( 'Ymd' ),
                    'compare' => '<=',
                    'type' => 'DATE',
                ),
            ),
            array(
                'relation' => 'AND',
                array(
                    'key' => 'dates_end_date',
                    'value' => $start_date->format( 'Ymd' ),
                    'compare' => '>=',
                    'type' => 'DATE',
                ),
                array(
                    'key' => 'dates_end_date',
                    'value' => $end_date->format( 'Ymd' ),
                    'compare' => '<=',
                    'type' => 'DATE',
                ),
            ),
        );
    }
}

// Récupérer le post avec la date la plus tardive
$later_post = new \Timber\PostQuery( array(
    'post_type' => 'event',
    'posts_per_page' => 1,
    'orderby' => 'meta_value',
    'meta_key' => 'date',
    'order' => 'DESC',
) );
$last_date = isset( $later_post[0] ) ? $later_post[0]->date : null;

$context['current_filter'] = $current_filter;
$context['posts'] = new \Timber\PostQuery( $arguments );
$context['last_date'] = $last_date;

\Timber\Timber::render( $templates, $context );
