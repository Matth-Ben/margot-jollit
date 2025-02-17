<?php

/**
 * Render options pages "Settings"
 */
function danka_settings__render_options_page__cookies()
{
    ?>
        <div class="wrap">
            <h1>Cookies</h1>
            <form method="POST" action="options.php">
                <?php
                    do_settings_sections( 'danka-settings-cookies' );
                    settings_fields( 'danka-settings-cookies' );
                    submit_button();
                ?>
            </form>
        </div>
    <?php
}


/**
 * Register options page "Settings"
 */
function danka__cookies__register_options_page()
{
    add_submenu_page(
        DANKA_SETTINGS_SLUG,                              // parent
        'Cookies',                                          // <title></title>
        'Cookies',                                          // menu link text
        'manage_options',                                   // capability to access the page
        DANKA_SETTINGS_COOKIES_SLUG,                           // page URL slug
        'danka_settings__render_options_page__cookies',     // callback function with content
        5                                                   // priority
    );
}

add_action('admin_menu', 'danka__cookies__register_options_page');
