<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

define( 'DANKA_SETTINGS_SLUG', 'danka-settings' );


function danka_add_menu_pages() {
    $icon = '<svg width="20" height="20" viewBox="0 0 428 448" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M203.132 0H0V448H203.132C262.53 448 319.495 424.4 361.496 382.392C403.497 340.384 427.092 283.408 427.092 224C427.092 164.592 403.497 107.616 361.496 65.6081C319.495 23.5999 262.53 0 203.132 0ZM193.502 336H139.079L193.502 224H247.924L193.502 336ZM193.502 224H139.079L193.502 112H247.924L193.502 224Z" fill="black"/></svg>';

    add_menu_page(
        'Paramètres Danka', // page title
        'Danka', // menu title
        'manage_options', // capability
        DANKA_SETTINGS_SLUG, // menu slug
        'danka_settings_page_callback', // callback
        'data:image/svg+xml;base64,' . base64_encode( $icon ), // icon url
        // 4 // position
    );
}

add_action( 'admin_menu', 'danka_add_menu_pages' );


function danka_settings_page_register_fields(){
    $id = DANKA_SETTINGS_SLUG . '-id';

	// Create section
	add_settings_section(
		$id, // section ID
		'Prises en charge', // title (optional)
		'', // callback function to display the section (optional)
		DANKA_SETTINGS_SLUG
	);

    $setting_names = array(
        'images' => 'Images',
        'cookies' => 'Cookies',
        'breadcrumbs' => 'Fil d\'Ariane',
    );

    foreach ( $setting_names as $setting_name => $setting_title ) {

        // Register fields
        // register_setting( DANKA_SETTINGS_SLUG, "danka_{$setting_name}", array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( DANKA_SETTINGS_SLUG, "danka_{$setting_name}" );
    
        // Add fields
        add_settings_field(
            "danka_{$setting_name}",
            $setting_title,
            'danka_settings_page_render_checkbox',
            DANKA_SETTINGS_SLUG,
            $id,
            array( 'name' => "danka_{$setting_name}" )
        );
    }
}

add_action( 'admin_init',  'danka_settings_page_register_fields' );


function danka_settings_page_render_checkbox( $args ) {
    printf(
        '<label><input type="checkbox" id="%s" name="%s" %s /></label>',
        $args[ 'name' ],
        $args[ 'name' ],
        get_option( $args['name'] ) ? 'checked' : ''
	);
}


function danka_settings_page_callback() {
	?>
		<div class="wrap">
			<h1><?php echo get_admin_page_title() ?></h1>
			<form method="post" action="options.php">
				<?php
					settings_fields( DANKA_SETTINGS_SLUG ); // settings group name
					do_settings_sections( DANKA_SETTINGS_SLUG ); // just a page slug
					submit_button(); // "Save Changes" button
				?>
			</form>
		</div>
	<?php
}


function danka_settings_main_notices() {
	if (
		isset( $_GET[ 'page' ] ) 
		&& DANKA_SETTINGS_SLUG == $_GET[ 'page' ]
		&& isset( $_GET[ 'settings-updated' ] ) 
		&& true == $_GET[ 'settings-updated' ]
	) {
		?>
			<div class="notice notice-success is-dismissible">
				<p>
					<strong>Vos préférences en été enregistrées.</strong>
				</p>
			</div>
		<?php
	}
}

add_action( 'admin_notices', 'danka_settings_main_notices' );


if ( get_option( 'danka_images' ) ) {
    include_once __DIR__ . '/images/index.php';
}

if ( get_option( 'danka_cookies' ) ) {
    include_once __DIR__ . '/cookies/index.php';
}

if ( get_option( 'danka_breadcrumbs' ) ) {
    include_once __DIR__ . '/breadcrumbs/index.php';
}

include_once __DIR__ . '/acf-advanced-wysiwyg/init.php';
