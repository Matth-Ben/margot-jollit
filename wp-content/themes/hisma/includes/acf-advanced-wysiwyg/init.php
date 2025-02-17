<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function app_include_acf_field_advanced_wysiwyg() {

	if ( ! function_exists( 'acf_register_field_type' ) ) {
		return;
	}

	require_once __DIR__ . '/class-app-acf-field-advanced-wysiwyg.php';

	acf_register_field_type( 'app_acf_field_advanced_wysiwyg' );
}
add_action( 'init', 'app_include_acf_field_advanced_wysiwyg' );

add_action( 'admin_enqueue_scripts', function() {
	wp_enqueue_editor();
} );
