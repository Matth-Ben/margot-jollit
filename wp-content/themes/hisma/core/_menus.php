<?php

// Emplacements de menu
add_action( 'after_setup_theme', function() {
    register_nav_menu( 'header-primary',    'Header (primaire)' );
    register_nav_menu( 'header-secondary',  'Header (secondaire)' );
    register_nav_menu( 'footer-primary',    'Footer (primaire)' );
    register_nav_menu( 'footer-secondary',  'Footer (secondaire)' );
    register_nav_menu( 'footer-tertiary',  'Footer (tertiaire)' );
    register_nav_menu( 'footer-quaternary',  'Footer (quaternaire)' );
    register_nav_menu( 'footer-copyright',  'Pied de page' );
    // register_nav_menu( 'mobile',            'Mobile' );
} );


// Retourne tous les menus par emplacement
function get_menus() {
    $menus = null;

    foreach ( get_nav_menu_locations() as $slug => $menu_id ) {
        if ( $menu_id ) {
            $menu = new \Timber\Menu( $menu_id );

            if ( $menu->items ) {
                $menus[$slug] = $menu;
            }
        }
    }

    return $menus;
}
