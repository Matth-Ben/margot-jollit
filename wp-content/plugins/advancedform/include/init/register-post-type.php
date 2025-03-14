<?php

/**
 * Register post type : af_form
 */
function af_register_post_type__af_form()
{
    $labels = [
        'name'                  => 'Formulaires',
        'singular_name'         => 'Formulaire',
        'menu_name'             => 'AdvancedForm',
        'name_admin_bar'        => 'Formulaire',
        'add_new'               => 'Ajouter un formulaire',
        'add_new_item'          => 'Ajouter un nouveau formulaire',
        'new_item'              => 'Nouveau formulaire',
        'edit_item'             => 'Éditer le formulaire',
        'view_item'             => 'Voir le formulaire',
        'all_items'             => 'Formulaires',
        'search_items'          => 'Rechercher des formulaires',
        'parent_item_colon'     => 'Parents du formulaire',
        'not_found'             => 'Aucun formulaire trouvé',
        'not_found_in_trash'    => 'Pas de formulaire trouvé dans la poubelle',
        'insert_into_item'      => 'Insérer dans le formulaire',
        'uploaded_to_this_item' => 'Téléchargé sur ce formulaire',
        'filter_items_list'     => 'Filtrer les formulaire',
        'items_list_navigation' => 'Navigation dans la liste de formulaires',
        'items_list'            => 'Liste de formulaires',
    ];

    $args = [
        'labels'                => $labels,
        'public'                => false,
        'publicly_queryable'    => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite'               => ['slug' => 'af_form'],
        'capability_type'       => 'post',
        'has_archive'           => false,
        'hierarchical'          => false,
        'menu_position'         => null,
        'supports'              => ['title', 'author'],
        'menu_icon'             => 'dashicons-forms'
    ];

    register_post_type('af_form', $args);
}

add_action('init', 'af_register_post_type__af_form');


/**
 * Register post type : af_entry
 */
function af_register_post_type__af_entry()
{
    $labels = [
        'name'                  => 'Entrées',
        'singular_name'         => 'Entrée',
        'menu_name'             => 'AdvancedForm',
        'name_admin_bar'        => 'Entrée',
        'add_new'               => 'Ajouter une entrée',
        'add_new_item'          => 'Ajouter une nouvelle entrée',
        'new_item'              => 'Nouvelle entrée',
        'edit_item'             => 'Éditer l\'entrée',
        'view_item'             => 'Voir l\'entrée',
        'all_items'             => 'Entrées',
        'search_items'          => 'Rechercher des entrées',
        'parent_item_colon'     => 'Parents de l\'entrée',
        'not_found'             => 'Aucune entrée trouvé',
        'not_found_in_trash'    => 'Pas d\'entrée trouvée dans la poubelle',
        'insert_into_item'      => 'Insérer dans l\'entrée',
        'uploaded_to_this_item' => 'Téléchargé sur cette entrée',
        'filter_items_list'     => 'Filtrer les entrée',
        'items_list_navigation' => 'Navigation dans la liste d\'entrées',
        'items_list'            => 'Liste d\'entrées',
    ];
    
    $args = [
        'labels'                => $labels,
        'public'                => false,
        'publicly_queryable'    => false,
        'has_archive'           => true,
        'show_ui'               => true,
        'show_in_menu'          => 'edit.php?post_type=af_form',
        'capability_type'       => 'post',
        'supports'              => ['title', 'author'],
        'capabilities'          => [
            'create_posts'      => false, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
        ],
        'map_meta_cap'          => true, // Set to `false`, if users are not allowed to edit/delete existing posts
    ];
    
    register_post_type('af_entry', $args);
}

add_action('init', 'af_register_post_type__af_entry');
