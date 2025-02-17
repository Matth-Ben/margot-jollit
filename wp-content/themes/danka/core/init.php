<?php

$composer_autoload = __DIR__ . '/../vendor/autoload.php';

if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
	$timber = new \Timber\Timber();
	\Timber\Timber::$dirname = array( 'views' );
}

if ( !class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Timber non activé. Assurez-vous d’activer le plugin dans <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
	} );
	add_filter( 'template_include', function( $template ) {
		echo "<!doctype html><html lang=\"en\"><head></head><body><p>Timber doit être activé</p></body></html>";
		return;
	} );

	return;
}

function danka_get_app_data() {
	$path = get_template_directory() . '/data.json';
	
	if ( file_exists( $path ) ) {
		$content = file_get_contents( $path );
		$app_data = $content ? json_decode( $content, true ) : null;
	
		return $app_data;
	}

	return null;
}
