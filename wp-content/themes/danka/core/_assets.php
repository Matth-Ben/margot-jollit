<?php

function is_webpack_dev_server_running($url = 'http://localhost:8080/wp-content/themes/danka/assets/build/app.js') {
    $headers = @get_headers($url);
    return $headers && strpos($headers[0], '200') !== false;
}

add_action( 'wp_enqueue_scripts', function() {
	$theme = wp_get_theme();
    $path = get_template_directory() . '/mix-manifest.json';
    $app_css = '/assets/build/app.css';
    $app_js = '/assets/build/app.js';

    if ( file_exists( $path ) ) {
        $content = file_get_contents( $path );
        $manifest = $content ? json_decode( $content, true ) : null;
        $app_css = isset( $manifest[$app_css] ) ? $manifest[$app_css] : $app_css;
        $app_js = isset( $manifest[$app_js] ) ? $manifest[$app_js] : $app_js;
    }

	wp_deregister_script( 'jquery' );
	wp_enqueue_style( 'app', get_template_directory_uri() . $app_css, array(), $theme->get( 'Version' ) );
	wp_enqueue_script( 'app', get_template_directory_uri() . $app_js, array(), $theme->get( 'Version' ) );
} );

add_action( 'wp_enqueue_scripts', function($i) {
    if ( !is_admin() ) {
        wp_dequeue_script( 'editor' );
        wp_dequeue_script( 'quicktags' );
        wp_dequeue_script( 'wplink' );
        wp_dequeue_script( 'jquery-ui-autocomplete' );
        wp_dequeue_script( 'media-upload' );
    
        wp_dequeue_style( 'buttons' );
        wp_dequeue_style( 'editor-buttons' );
        wp_dequeue_style( 'wp-emoji-styles' );
        wp_dequeue_style( 'wp-block-library' );
        wp_dequeue_style( 'classic-theme-styles' );
        wp_dequeue_style( 'global-styles' );
    }
}, 100 );


if ( !is_admin() ) {
    add_filter( 'user_can_richedit', '__return_false', 50 );
    add_filter( 'quicktags_settings', '__return_false', 50 );
}


// Remplacer les URLS de production par celles du serveur de développement
add_filter('final_output', function($buffer) {
    $search = get_site_url();
    $replace = 'http://localhost:8080';

    return str_replace($search, $replace, $buffer);
});


/**
 * Autorise l'upload de fichiers SVG dans la médiathèque WordPress
 */
function prefix_allow_svg_uploads( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'prefix_allow_svg_uploads' );