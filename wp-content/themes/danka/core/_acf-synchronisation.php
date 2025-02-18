<?php

add_filter( 'acf/settings/save_json', 'acf_export_json' );
function acf_export_json( $path ) {
	$path = get_stylesheet_directory() . '/acf-json';
	return $path;
}

/**
 * Export automatique des pages d’options dynamiques (ACF Extended) 
 * au format JSON dans le dossier /acf-json du thème actif.
 */
add_action('save_post_acfe-dop', 'my_export_acfe_dop_to_json', 20);
function my_export_acfe_dop_to_json( $post_id ) {

    // Vérifier que l'article est bien du type 'acfe-dop' et qu'il est publié
    // (On peut aussi vouloir gérer le statut 'draft' selon les besoins.)
    if ( get_post_type($post_id) !== 'acfe-dop' ) {
        return;
    }

    // Récupérer les métadonnées standard
    $post_status = get_post_status($post_id);
    $post_title  = get_the_title($post_id);

    // Les principales métas utilisées par ACF Extended pour définir la page d’options
    // (liste non exhaustive, adapter selon vos besoins)
    $page_slug        = get_post_meta($post_id, 'acfe_dop_name', true);           // Slug unique
    $page_menu_title  = get_post_meta($post_id, 'acfe_dop_menu_title', true);
    $page_capability  = get_post_meta($post_id, 'acfe_dop_capability', true);
    $page_position    = get_post_meta($post_id, 'acfe_dop_position', true);
    $page_parent_slug = get_post_meta($post_id, 'acfe_dop_parent', true);
    $page_icon        = get_post_meta($post_id, 'acfe_dop_icon_url', true);
    // etc.

    // Construire un tableau de configuration
    $export = array(
        'ID'          => $post_id,
        'post_title'  => $post_title,
        'post_status' => $post_status,

        // Champs clés pour recréer cette page d’options
        'slug'         => $page_slug,
        'menu_title'   => $page_menu_title,
        'capability'   => $page_capability,
        'menu_parent'  => $page_parent_slug,
        'menu_position'=> $page_position,
        'menu_icon'    => $page_icon,

        // Vous pouvez ajouter toutes les métadonnées ACFE nécessaires
    );

    // Chemin de sauvegarde (similaire à ACF : /acf-json dans le thème)
    $json_path = get_stylesheet_directory() . '/acf-json';
    // S’assurer que le dossier existe
    if ( ! file_exists( $json_path ) ) {
        mkdir( $json_path, 0755, true );
    }

    // Nom du fichier basé sur le slug pour éviter les collisions
    // (On peut aussi se baser sur l’ID, ou concaténer ID + slug.)
    $filename = 'acfe-dop-' . sanitize_title( $post_title ) . '.json';
    $full_path = trailingslashit( $json_path ) . $filename;

    // Encode en JSON (joli format)
    $json_data = wp_json_encode( $export, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

    // Écrire dans le fichier
    file_put_contents( $full_path, $json_data );
}

/**
 * Synchronise les pages d'options ACF Extended (acfe-dop) 
 * à partir des fichiers JSON dans /acf-json
 */
add_action( 'admin_init', 'my_acfe_dop_sync_from_json' );
function my_acfe_dop_sync_from_json() {
    // Chemin de votre dossier "acf-json" (selon votre config)
    $json_path = get_stylesheet_directory() . '/acf-json/';

    // Vérifie si le dossier existe
    if ( ! is_dir( $json_path ) ) {
        return;
    }

    // Récupère tous les fichiers JSON qui commencent par "acfe-dop-"
    $files = glob( $json_path . 'acfe-dop-*.json' );
    if ( ! $files ) {
        return;
    }

    foreach ( $files as $file ) {
        $content = file_get_contents( $file );
        $data    = json_decode( $content, true );

        // On s'assure que le JSON est un tableau, sinon on ignore
        if ( ! is_array( $data ) ) {
            continue;
        }

        // On attend un 'slug' unique pour identifier la page d’options
        $slug = isset( $data['slug'] ) ? $data['slug'] : false;
        if ( ! $slug ) {
            // Pas de slug, on ne peut pas lier ce JSON à un post existant
            continue;
        }

        // Cherche s’il existe déjà un post acfe-dop avec ce slug
        $existing = get_posts( array(
            'post_type'  => 'acfe-dop',
            'meta_query' => array(
                array(
                    'key'   => 'acfe_dop_name', // la meta qui contient le "slug" de la page d’options
                    'value' => $slug,
                ),
            ),
            'post_status' => 'any',
            'numberposts' => 1,
        ) );

        if ( $existing ) {
            // ----- METTRE À JOUR LE POST EXISTANT -----
            $post_id = $existing[0]->ID;

            // Mise à jour du post lui-même (titre, statut…)
            wp_update_post( array(
                'ID'          => $post_id,
                'post_title'  => ! empty( $data['post_title'] ) 
                                    ? $data['post_title'] 
                                    : $slug,
                'post_status' => ! empty( $data['post_status'] ) 
                                    ? $data['post_status'] 
                                    : 'publish',
            ) );

            // Mise à jour des métas indispensables
            update_post_meta( $post_id, 'acfe_dop_name', $slug );

            if ( isset( $data['menu_title'] ) ) {
                update_post_meta( $post_id, 'acfe_dop_menu_title', $data['menu_title'] );
            }
            if ( isset( $data['capability'] ) ) {
                update_post_meta( $post_id, 'acfe_dop_capability', $data['capability'] );
            }
            if ( isset( $data['menu_parent'] ) ) {
                update_post_meta( $post_id, 'acfe_dop_parent', $data['menu_parent'] );
            }
            if ( isset( $data['menu_position'] ) ) {
                update_post_meta( $post_id, 'acfe_dop_position', $data['menu_position'] );
            }
            if ( isset( $data['menu_icon'] ) ) {
                update_post_meta( $post_id, 'acfe_dop_icon_url', $data['menu_icon'] );
            }
            // … etc. selon les métas que vous exportez

        } else {
            // ----- CRÉER UN NOUVEAU POST -----
            $new_post_id = wp_insert_post( array(
                'post_type'   => 'acfe-dop',
                'post_title'  => ! empty( $data['post_title'] ) ? $data['post_title'] : $slug,
                'post_status' => ! empty( $data['post_status'] ) ? $data['post_status'] : 'publish',
            ) );

            if ( $new_post_id && ! is_wp_error( $new_post_id ) ) {
                update_post_meta( $new_post_id, 'acfe_dop_name', $slug );

                if ( isset( $data['menu_title'] ) ) {
                    update_post_meta( $new_post_id, 'acfe_dop_menu_title', $data['menu_title'] );
                }
                if ( isset( $data['capability'] ) ) {
                    update_post_meta( $new_post_id, 'acfe_dop_capability', $data['capability'] );
                }
                if ( isset( $data['menu_parent'] ) ) {
                    update_post_meta( $new_post_id, 'acfe_dop_parent', $data['menu_parent'] );
                }
                if ( isset( $data['menu_position'] ) ) {
                    update_post_meta( $new_post_id, 'acfe_dop_position', $data['menu_position'] );
                }
                if ( isset( $data['menu_icon'] ) ) {
                    update_post_meta( $new_post_id, 'acfe_dop_icon_url', $data['menu_icon'] );
                }
            }
        }
    }
}