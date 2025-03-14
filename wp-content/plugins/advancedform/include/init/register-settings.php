<?php

/**
 * Register settings : reCAPTCHA v2
 */
function af_register_settings__recaptcha_v2()
{
    $section_name = 'af__recaptcha_v2';

    add_settings_section(
        $section_name,                                      // id
        'reCaptcha v2',                                     // title
        '',                                                 // callback
        'af-settings'                                       // page
    );


    // public key
    add_settings_field(
        "{$section_name}__public_key",                      // id
        'Clé publique',                                     // title
        "af_render_settings__recaptcha_v2__public_key",     // callback
        'af-settings',                                      // page
        $section_name                                       // section
    );

    register_setting(
        'af-settings',                                      // option group
        "{$section_name}__public_key"                       // option name
    );

    
    // secret key
    add_settings_field(
        "{$section_name}__secret_key",                      // id
        'Clé secrète',                                      // title
        "af_render_settings__recaptcha_v2__secret_key",     // callback
        'af-settings',                                      // page
        $section_name                                       // section
    );

    register_setting(
        'af-settings',                                      // option group
        "{$section_name}__secret_key"                       // option name
    );
}

add_action('admin_init', 'af_register_settings__recaptcha_v2');

/**
 * Render settings : reCAPTCHA v2 public key
 */
function af_render_settings__recaptcha_v2__public_key()
{
    $option_name = 'af__recaptcha_v2__public_key';
    $option = get_option($option_name);

    echo "<input type='text' id='{$option_name}' name='{$option_name}' value='{$option}' />";
}

/**
 * Render settings : reCAPTCHA v2 secret key
 */
function af_render_settings__recaptcha_v2__secret_key()
{
    $option_name = 'af__recaptcha_v2__secret_key';
    $option = get_option($option_name);

    echo "<input type='text' id='{$option_name}' name='{$option_name}' value='{$option}' />";
}




/**
 * Register settings : reCAPTCHA v3
 */
function af_register_settings__recaptcha_v3()
{
    $section_name = 'af__recaptcha_v3';

    add_settings_section(
        $section_name,                                      // id
        'reCaptcha v3',                                     // title
        '',                                                 // callback
        'af-settings'                                       // page
    );


    // public key
    add_settings_field(
        "{$section_name}__public_key",                      // id
        'Clé publique',                                     // title
        "af_render_settings__recaptcha_v3__public_key",     // callback
        'af-settings',                                      // page
        $section_name                                       // section
    );

    register_setting(
        'af-settings',                                      // option group
        "{$section_name}__public_key"                       // option name
    );

    
    // secret key
    add_settings_field(
        "{$section_name}__secret_key",                      // id
        'Clé secrète',                                      // title
        "af_render_settings__recaptcha_v3__secret_key",     // callback
        'af-settings',                                      // page
        $section_name                                       // section
    );

    register_setting(
        'af-settings',                                      // option group
        "{$section_name}__secret_key"                       // option name
    );
}

add_action('admin_init', 'af_register_settings__recaptcha_v3');

/**
 * Render settings : reCAPTCHA v3 public key
 */
function af_render_settings__recaptcha_v3__public_key()
{
    $option_name = 'af__recaptcha_v3__public_key';
    $option = get_option($option_name);

    echo "<input type='text' id='{$option_name}' name='{$option_name}' value='{$option}' />";
}

/**
 * Render settings : reCAPTCHA v3 secret key
 */
function af_render_settings__recaptcha_v3__secret_key()
{
    $option_name = 'af__recaptcha_v3__secret_key';
    $option = get_option($option_name);

    echo "<input type='text' id='{$option_name}' name='{$option_name}' value='{$option}' />";
}
