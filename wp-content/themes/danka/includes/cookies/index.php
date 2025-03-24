<?php

define( 'DANKA_SETTINGS_COOKIES_SLUG', 'danka-settings-cookies' );

$youtube_is_active = get_option( 'danka_cookies_youtube' ) ? true : false;
$google_analytics_is_active = get_option( 'danka_cookies_google-analytics' ) ? true : false;

if ( $youtube_is_active || $google_analytics_is_active || true ) {

    add_action( 'wp_enqueue_scripts', function() {
        $path = get_template_directory_uri() . '/includes/cookies';
        wp_enqueue_script( 'app_cookies_init', $path . '/assets/scripts/index.js' );
        wp_localize_script( 'app_cookies_init', 'app_cookies_data', array(
            'text' => array(
                'title' => get_option( 'danka__cookies-banner__title' ),
                'description' => get_option( 'danka__cookies-banner__description' ),
                'link' => get_option( 'danka__cookies-banner__link' ),
            ),
            'iframes' => array(
                'youtube' => array(
                    'name' => 'Youtube',
                    'is_active' => get_option( 'danka_cookies_youtube' ) ? true : false
                ),
            ),
            'json_scripts' => get_option( 'danka_cookies_fields' )
        ) );
    } );
    
    add_filter( 'script_loader_tag', function( $tag, $handle, $source ) {

        if ( 'app_cookies_init' === $handle ) {
            $tag = '<script type="module" src="'. $source .'"></script>';
        }
    
        return $tag;
    }, 10, 3 );
    
    add_action( 'wp_head', function() { \Timber\Timber::render( array( __DIR__ . '/default.twig' ) ); } );
}

include __DIR__ . '/_register-options-page.php';
include __DIR__ . '/_register-settings.php';