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
        // 'google_analytics' => 'Google Analytics'
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


    $name = $section_name . "_fields";

    add_settings_field(
        $name,                                     // id
        "Autres cookies",                                  // title
        "danka_settings__cookies__render_custom",               // callback
        DANKA_SETTINGS_COOKIES_SLUG,                               // page
        $section_name,                                          // section
        array( 'name' => $name )
    );

    register_setting(
        DANKA_SETTINGS_COOKIES_SLUG,                           // option group
        $name,                                 // option name
        array( 'type' => 'string' )
    );
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

function danka_settings__cookies__render_custom( $args )
{
    $name = $args['name'];
    $value = get_option( $name );
    $value = esc_attr( $value );
    
    echo "<div><input id='$name' type='hidden' name='$name' value='$value' /></div>";
    echo "<div id='container-danka-cookies-fields'>Chargement...<br><br></div>";
    echo "<div><button class='button' type='button' data-add-cookie>Ajouter un cookie</button></div>";

    ?>

    <script>
        const input = document.querySelector('#danka_cookies_fields')
        const container = document.querySelector('#container-danka-cookies-fields')

        const get_field_html = (is_active = true, name = "", script = "") => {
            return `
                <div>
                    <div><label><input type='checkbox' ${is_active ? 'checked' : ''} /> Actif</label></div>
                    <br>
                    <div><input type='text' value='${name}' /></div>
                    <br>
                    <div><textarea class='regular-text'>${script}</textarea></div>
                    <br>
                    <div><button class='button' type='button' data-remove-cookie>Supprimer</button></div>
                    <br>
                    <br>
                </div>
            `
        }

        const create_json = () => {
            const json = []

            document.querySelectorAll('#container-danka-cookies-fields > div').forEach(element => {
                const checkbox = element.querySelector('input[type="checkbox"]')
                const name = element.querySelector('input[type="text"]')
                const script = element.querySelector('textarea')

                json.push({
                    name: name.value,
                    is_active: checkbox.checked,
                    script: script.value
                })
            })
            
            input.value = JSON.stringify(json)
        }
        
        const parse_json = () => {
            const json = input.value ? JSON.parse(input.value) : null

            if (json && json.length) {
                container.innerHTML = ''
                json.forEach(cookie => {
                    container.innerHTML += get_field_html(cookie.is_active, cookie.name, cookie.script)
                })
            } else {
                container.innerHTML = '<p>Aucun cookie n\'est enregistré pour le moment !<br><br></p>'
            }
        }

        document.addEventListener("click", () => {

            // Ajouter un cookie
            if (event.target.tagName === 'BUTTON' && event.target.getAttribute('data-add-cookie') !== null) {
                document.querySelector('#container-danka-cookies-fields p')?.remove()
                document.querySelector('#container-danka-cookies-fields').innerHTML += get_field_html()
            }
            
            // Supprimer un cookie
            if (event.target.tagName === 'BUTTON' && event.target.getAttribute('data-remove-cookie') !== null) {
                event.target.parentElement.parentElement.remove()
                create_json()
            }
        })

        document.addEventListener('change', event => {
            if (event.target.closest('#container-danka-cookies-fields') !== null) {
                create_json()
            }
        })

        document.addEventListener('DOMContentLoaded', parse_json)
    </script>

    <?php
}

add_action( 'admin_init', 'danka_register_settings__cookies' );