<?php

/**
 * Render metabox : shortcode on af_form
 */
function af_render_metabox__af_form__shortcode($post)
{
    include WP_PLUGIN_DIR  . '/advancedform/templates/metabox/af-form--shortcode.php';
}

/**
 * Register metabox : shortcode on af_form
 */
function af_register_metabox__af_form__shortcode()
{
    add_meta_box(
        'metabox--af-form--shortcode',          // id
        'Code court',                           // title
        'af_render_metabox__af_form__shortcode',   // callback
        'af_form',                              // screen (on new/edit af_form page)
        'advanced',
        'core'
    );
}

add_action('add_meta_boxes', 'af_register_metabox__af_form__shortcode');




/**
 * Render metabox : settings on af_form
 */
function af_render_metabox__af_form__settings($post)
{
    $af_form = new AF_Form($post->ID);

    include WP_PLUGIN_DIR  . '/advancedform/templates/metabox/af-form--settings.php';
}

/**
 * Register metabox : settings on af_form
 */
function af_register_metabox__af_form__settings()
{
    add_meta_box(
        'metabox--af-form--settings',           // id
        'Paramètres du formulaire',             // title
        'af_render_metabox__af_form__settings',    // callback
        'af_form',                              // screen (on new/edit af_form page)
        'advanced',
        'core'
    );
}

add_action('add_meta_boxes', 'af_register_metabox__af_form__settings');




/**
 * Render metabox : fields on af_form
 */
function af_render_metabox__af_form__fields($post)
{
    // css and js for wordpress attachment loader
    wp_enqueue_media();
        

    // css global
    wp_enqueue_style('af--global--custom-textarea',                 plugins_url('/advancedform/assets/css/global/custom-textarea.css'));
    wp_enqueue_style('af--global--files-manager',                   plugins_url('/advancedform/assets/css/global/files-manager.css'));
    wp_enqueue_style('af--global--fields-array',         plugins_url('/advancedform/assets/css/global/fields-array.css'));

    // css
    wp_enqueue_style('af--af-form',                       plugins_url('/advancedform/assets/css/edit-form.css'));


    // js global
    wp_enqueue_script('af--global--create-global-object',           plugins_url('/advancedform/assets/js/global/create-global-object.js'));
    wp_enqueue_script('af--global--uniqid',                         plugins_url('/advancedform/assets/js/global/uniqid.js'));
    wp_enqueue_script('af--global--custom-textarea',                plugins_url('/advancedform/assets/js/global/custom-textarea.js'));
    wp_enqueue_script('af--global--files-manager',                  plugins_url('/advancedform/assets/js/global/files-manager.js'));
    
    // js templates
    wp_enqueue_script('af--af-form--template-field',    plugins_url('/advancedform/assets/js/edit-form/templates/field.js'));
    
    // js ajax
    wp_enqueue_script('af--af-form--ajax',               plugins_url('/advancedform/assets/js/edit-form/ajax.js'));
    wp_localize_script('af--af-form--ajax',               'parameters', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'af_form_id' => $post->ID
    ]);
    
    // js
    wp_enqueue_script('af--af-form--fields',               plugins_url('/advancedform/assets/js/edit-form/fields.js'));
    wp_enqueue_script('af--af-form--json',                 plugins_url('/advancedform/assets/js/edit-form/json.js'));
    wp_enqueue_script('af--af-form--update-view',          plugins_url('/advancedform/assets/js/edit-form/update-view.js'));
    wp_enqueue_script('af--af-form--parameters',           plugins_url('/advancedform/assets/js/edit-form/parameters.js'));
    wp_enqueue_script('af--af-form--drag-and-drop',        plugins_url('/advancedform/assets/js/edit-form/drag-and-drop.js'));
    wp_enqueue_script('af--af-form--events',               plugins_url('/advancedform/assets/js/edit-form/events.js'));
    wp_enqueue_script('af--af-form--init',                 plugins_url('/advancedform/assets/js/edit-form/init.js'));

    include WP_PLUGIN_DIR  . '/advancedform/templates/metabox/af-form--fields.php';
}

/**
 * Register metabox : fields on af_form
 */
function af_register_metabox__af_form__fields()
{
    add_meta_box(
        'metabox--af-form--fields',           // id
        'Édition du formulaire',              // title
        'af_render_metabox__af_form__fields',    // callback
        'af_form',                            // screen (on new/edit af_form page)
        'advanced',
        'core'
    );
}

add_action('add_meta_boxes', 'af_register_metabox__af_form__fields');




/**
 * Render metabox : alerts on af_form
 */
function af_render_metabox__af_form__alerts($post)
{
    // wp editor
    wp_enqueue_editor();


    // js template
    wp_enqueue_script('af--af-form--template-alert', plugins_url('/advancedform/assets/js/edit-form/templates/alert.js'));

    // js
    wp_enqueue_script('af--af-form--alerts', plugins_url('/advancedform/assets/js/edit-form/alerts.js'));

    include WP_PLUGIN_DIR  . '/advancedform/templates/metabox/af-form--alerts.php';
}

/**
 * Register metabox : alerts on af_form
 */
function af_register_metabox__af_form__alerts()
{
    add_meta_box(
        'metabox--af-form--alerts',         // id
        'Alertes',                          // title
        'af_render_metabox__af_form__alerts',  // callback
        'af_form',                          // screen (on new/edit af_form page)
        'advanced',
        'core'
    );
}

add_action('add_meta_boxes', 'af_register_metabox__af_form__alerts');




/**
 * Register metabox for af_entry
 */
function af_register_metabox__af_entry()
{
    add_meta_box(
        'metabox--af-entry',            // id
        'Entrées',                      // title
        'af_render_metabox__af_entry',  // callback
        'af_entry'                      // screen (on new/edit advancedform page)
    );
}

/**
 * Render form template for entries
 */
function af_render_metabox__af_entry($post)
{
    // css and js for wordpress attachment loader
    wp_enqueue_media();

    // global css
    wp_enqueue_style('af--global--fields-array',            plugins_url('/advancedform/assets/css/global/fields-array.css'));
    wp_enqueue_style('af--global--custom-textarea',         plugins_url('/advancedform/assets/css/global/custom-textarea.css'));
    wp_enqueue_style('af--global--files-manager',           plugins_url('/advancedform/assets/css/global/files-manager.css'));

    // css
    wp_enqueue_style('af--af-entry',                        plugins_url('/advancedform/assets/css/edit-form-entry.css'));

    // global js
    wp_enqueue_script('af--global--create-global-object',   plugins_url('/advancedform/assets/js/global/create-global-object.js'));
    wp_enqueue_script('af--global--custom-textarea',        plugins_url('/advancedform/assets/js/global/custom-textarea.js'));
    wp_enqueue_script('af--global--files-manager',          plugins_url('/advancedform/assets/js/global/files-manager.js'));
    wp_enqueue_script('af--global--uniqid',                 plugins_url('/advancedform/assets/js/global/uniqid.js'));
    
    // js
    wp_enqueue_script('edit-form-entry',        plugins_url('/advancedform/assets/js/edit-form-entry/init.js'));

    include WP_PLUGIN_DIR  . '/advancedform/templates/metabox/af-entry.php';
}

add_action('add_meta_boxes', 'af_register_metabox__af_entry');
