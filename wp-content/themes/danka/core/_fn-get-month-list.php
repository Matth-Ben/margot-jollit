<?php

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