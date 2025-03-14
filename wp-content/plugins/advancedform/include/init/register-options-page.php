<?php

/**
 * Render options pages : Actions
 */
function af_render_options_page__actions()
{
    // css global
    wp_enqueue_style('af--global--fields-array',    plugins_url('/advancedform/assets/css/global/fields-array.css'));
    
    // css
    wp_enqueue_style('af--af-form--actions',   plugins_url('/advancedform/assets/css/options-page-actions.css'));

    include WP_PLUGIN_DIR  . '/advancedform/templates/options-page/actions.php';
}

/**
 * Register options page : Actions
 */
function af_register_options_page__actions()
{
    // AdvancedForm::intercept_options_page__actions();

    add_submenu_page(
        'edit.php?post_type=af_form',           // parent
        'Actions',                              // <title></title>
        'Actions',                              // menu link text
        'manage_options',                       // capability to access the page
        'af-actions',                           // page URL slug
        'af_render_options_page__actions',      // callback function with content
        4                                       // priority
    );
}

add_action('admin_menu', 'af_register_options_page__actions');




/**
 * Register options page "Settings"
 */
function af_register_options_page__settings()
{
    add_submenu_page(
        'edit.php?post_type=af_form',           // parent
        'Paramètres',                           // <title></title>
        'Paramètres',                           // menu link text
        'manage_options',                       // capability to access the page
        'af-settings',                          // page URL slug
        'af_render_options_page__settings',     // callback function with content
        5                                       // priority
    );
}


/**
 * Render options pages "Settings"
 */
function af_render_options_page__settings()
{
    // css
    wp_enqueue_style('af--global--fields-array', plugins_url('/advancedform/assets/css/options-page-settings.css'));

    include WP_PLUGIN_DIR  . '/advancedform/templates/options-page/settings.php';
}

add_action('admin_menu', 'af_register_options_page__settings');
