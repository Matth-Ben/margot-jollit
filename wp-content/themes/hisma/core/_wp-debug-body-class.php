<?php

if ( WP_DEBUG || WP_DEBUG_DISPLAY ) {
	add_filter( 'body_class', function( $classes ) {
		$classes[] = 'debug';

		return $classes;
	} );
}