<?php

function danka_custom_menu_order( $menu_order )
{
    if ( !$menu_order ) return true;

    $custom_menu_order = array(
        'index.php',                        // Dashboard
        'separator1',                       // First separator
        'upload.php',                       // Media
        'edit.php?post_type=insula-media',  // Media Insula
        'edit.php?post_type=page',          // Pages
        'edit-comments.php',                // Comments
    );

    foreach ( $menu_order as $item ) {

        if ( !in_array( $item, $custom_menu_order ) ) {
            $custom_menu_order[ count( $custom_menu_order ) ] = $item;
        }
    }

    return $custom_menu_order;
}
// add_filter( 'custom_menu_order', 'danka_custom_menu_order', 10, 1 );
// add_filter( 'menu_order', 'danka_custom_menu_order', 10, 1 );