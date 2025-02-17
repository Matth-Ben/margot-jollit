<?php

function danka_register_settings__cookies()
{  
    $section_name = 'danka_cookies_text';

    add_settings_section(
        $section_name,              // id
        'Texte de la bannière',                  // title
        '',                         // callback
        DANKA_SETTINGS_COOKIES_SLUG    // page
    );


    add_settings_field(
        $section_name . '_title',                              // id
        "Titre",                                                // title
        function( $args ) {
            $name = $args['name'];
            $value = get_option( $name );
            echo "<input id='$name' class='regular-text' type='text' name='$name' value='$value' />";
        },                                                      // callback
        DANKA_SETTINGS_COOKIES_SLUG,                               // page
        $section_name,                                          // section
        array( 'name' => $section_name . '_title' )
    );
    register_setting(
        DANKA_SETTINGS_COOKIES_SLUG,                           // option group
        $section_name . '_title',                          // option name
        array( 'type' => 'string' )
    );
    

    add_settings_field(
        $section_name . '_description',                              // id
        "Description",                                          // title
        function( $args ) {
            $name = $args['name'];
            $value = get_option( $name );
            echo "<textarea id='$name' class='regular-text' name='$name'>$value</textarea>";
        },               // callback
        DANKA_SETTINGS_COOKIES_SLUG,                               // page
        $section_name,                                          // section
        array( 'name' => $section_name . '_description' )
    );
    register_setting(
        DANKA_SETTINGS_COOKIES_SLUG,                           // option group
        $section_name . '_description',                          // option name
        array( 'type' => 'string' )
    );


    add_settings_field(
        $section_name . '_link',                              // id
        "Lien \"Page de confidentialités\"",                                                // title
        function( $args ) {
            $name = $args['name'];
            $value = get_option( $name );
            echo "<input id='$name' class='regular-text' type='text' name='$name' value='$value' />";
        },                                                      // callback
        DANKA_SETTINGS_COOKIES_SLUG,                               // page
        $section_name,                                          // section
        array( 'name' => $section_name . '_link' )
    );
    register_setting(
        DANKA_SETTINGS_COOKIES_SLUG,                           // option group
        $section_name . '_link',                          // option name
        array( 'type' => 'string' )
    );


    $section_name = 'danka_cookies';

    add_settings_section(
        $section_name,              // id
        'Activations',                  // title
        '',                         // callback
        DANKA_SETTINGS_COOKIES_SLUG    // page
    );

    register_setting(
        DANKA_SETTINGS_COOKIES_SLUG,                           // option group
        $section_name . '_link',                           // option name
        array( 'type' => 'string' )
    );

    $cookies = array(
        'youtube' => 'Youtube',
        'google_analytics' => 'Google Analytics'
    );

    foreach ( $cookies as $slug => $cookie ) {
        $slug = str_replace( '-', '_', $slug );
        $name = "{$section_name}_{$slug}";

        add_settings_field(
            $name,                                          // id
            $cookie,                                        // title
            "danka_settings__cookies__render",              // callback
            DANKA_SETTINGS_COOKIES_SLUG,                       // page
            $section_name,                                  // section
            array( 'name' => $name )
        );
    
        register_setting(
            DANKA_SETTINGS_COOKIES_SLUG,                           // option group
            $name,                                              // option name
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            )
        );

        if ( $slug === "google_analytics" ) {
            add_settings_field(
                $name . '_script',                                     // id
                $cookie . " (script)",                                  // title
                "danka_settings__cookies__render_script",               // callback
                DANKA_SETTINGS_COOKIES_SLUG,                               // page
                $section_name,                                          // section
                array( 'name' => $name . '_script' )
            );
        
            register_setting(
                DANKA_SETTINGS_COOKIES_SLUG,                           // option group
                $name . '_script',                                 // option name
                array( 'type' => 'string' )
            );
        }
    }
}


function danka_settings__cookies__render( $args )
{
    $name = $args['name'];
    $value = get_option( $name );
    $checked = $value ? 'checked' : '';

    echo "<input id='$name' type='checkbox' name='$name' $checked />";
}

function danka_settings__cookies__render_script( $args )
{
    $name = $args['name'];
    $value = get_option( $name );
    
    echo "<textarea id='$name' class='regular-text' name='$name'>$value</textarea>";
}

add_action( 'admin_init', 'danka_register_settings__cookies' );