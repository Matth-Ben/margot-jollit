<?php

// Emplacements de menu
add_action( 'after_setup_theme', function() {
    register_nav_menu( 'header-primary',    'En tête (primaire)' );
    register_nav_menu( 'header-secondary',  'En tête (secondaire)' );
    register_nav_menu( 'footer-primary',    'Pied de page (primaire)' );
    register_nav_menu( 'footer-secondary',  'Pied de page (secondaire)' );
    register_nav_menu( 'footer-tertiary',  'Pied de page (tertiaire)' );
    register_nav_menu( 'footer-quaternary',  'Pied de page (quaternaire)' );
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
