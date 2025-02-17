<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Enregistre les paramètres pour les commentaires
function danka_comments_register_settings() {
    // Créer une section pour les commentaires
    $comments_section_id = DANKA_SETTINGS_SLUG . '-comments-id';

    add_settings_section(
        $comments_section_id, // section ID
        'Commentaires', // titre de la section
        '', // callback function (optionnel)
        DANKA_SETTINGS_SLUG // même slug de page
    );

    // Ajouter un champ pour activer/désactiver les commentaires
    register_setting( DANKA_SETTINGS_SLUG, 'danka_comments', array(
        'type' => 'string', // définir comme une chaîne
        'sanitize_callback' => 'sanitize_text_field',
        'default' => '0', // valeur par défaut à '0' (non coché)
    ));

    add_settings_field(
        'danka_comments',
        'Activer/Désactiver les Commentaires',
        'danka_settings_page_comments_render_checkbox',
        DANKA_SETTINGS_SLUG,
        $comments_section_id,
        array( 'name' => 'danka_comments' )
    );
}

add_action( 'admin_init', 'danka_comments_register_settings' );

// Fonction pour rendre le bouton switch
function danka_settings_page_comments_render_checkbox( $args ) {
    $option_value = get_option( $args['name'], '0' ); // obtenir la valeur de l'option, '0' par défaut
    printf(
        '<label><input type="checkbox" id="%s" name="%s" value="1" %s /></label>',
        esc_attr( $args['name'] ),
        esc_attr( $args['name'] ),
        checked( $option_value, '1', false ) // vérifier si l'option est '1'
    );
}

// Appliquer les filtres si l'option est activée
if ( get_option( 'danka_comments', '0' ) === '1' ) {
    add_filter('comments_open', '__return_false', 20, 2);
    add_filter('pings_open', '__return_false', 20, 2);
    
    // Cacher les commentaires existants
    add_filter('comments_array', '__return_empty_array', 10, 2);
    
    // Supprimer la page des commentaires du menu
    add_action('admin_menu', function () {
        remove_menu_page('edit-comments.php');
    });
    
    // Supprimer les liens de commentaires de la barre d'administration
    add_action('init', function () {
        if (is_admin_bar_showing()) {
            remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
        }
    });
}
