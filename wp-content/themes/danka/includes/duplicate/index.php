<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class DankaDuplicateManager {

    /**
     * Slug de votre sous-page (ex: ?page=danka-duplicate-settings)
     */
    public static $slug = 'danka-duplicate-settings';

    /**
     * Initialisation de la classe : enregistrement des hooks
     */
    public static function init() {
        // 1) Crée la sous-page
        add_action( 'admin_menu', array( get_called_class(), 'add_subpage' ) );

        // 2) Gère l’enregistrement des réglages (cases à cocher)
        add_action( 'admin_init', array( get_called_class(), 'register_settings' ) );

        // 3) Affiche une notice après sauvegarde des réglages
        add_action( 'admin_notices', array( get_called_class(), 'register_notice' ) );

        // 4) Ajout du lien "Dupliquer" dans les row actions (Articles, Pages, etc.)
        add_filter( 'post_row_actions', array( get_called_class(), 'duplicate_post_link' ), 10, 2 );
        add_filter( 'page_row_actions', array( get_called_class(), 'duplicate_post_link' ), 10, 2 );

        // 5) Action de duplication (admin.php?action=...)
        add_action( 'admin_action_danka_duplicate_post_as_draft', array( get_called_class(), 'duplicate_post_as_draft' ) );
    }

    /**
     * Ajoute un sous-menu "Duplicate" dans le menu parent DANKA_SETTINGS_SLUG
     * (ex: admin.php?page=danka-settings)
     */
    public static function add_subpage() {
        add_submenu_page(
            DANKA_SETTINGS_SLUG,                // Parent slug (constant déjà définie dans votre plugin principal)
            'Duplicate',                        // <title></title> de la page
            'Duplicate',                        // Texte du lien de sous-menu
            'manage_options',                   // Capability requise
            self::$slug,                        // Slug de cette sous-page (ex: ?page=danka-duplicate-settings)
            array( get_called_class(), 'render_page' )
        );
    }

    /**
     * Affiche la page de réglages : cases à cocher pour activer la duplication sur certains CPT
     */
    public static function render_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form method="post" action="options.php">
                <?php
                    // Génère les champs cachés pour sécuriser la sauvegarde
                    settings_fields( self::$slug );
                    // Affiche les sections et champs enregistrés ci-dessous
                    do_settings_sections( self::$slug );
                    // Bouton "Enregistrer" par défaut
                    submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Déclare et enregistre nos réglages (une liste de CPT cochés)
     */
    public static function register_settings() {
        // Identifiant unique pour la section
        $section_id = self::$slug . '_section';

        // 1) Ajout de la section
        add_settings_section(
            $section_id,
            'Réglages de duplication',
            '', // callback facultatif (pour un texte de présentation)
            self::$slug
        );

        // 2) On enregistre l’option qui stockera la liste des CPT autorisés
        //    On appelle cette option "danka_duplicate_cpts"
        register_setting(
            self::$slug,
            'danka_duplicate_cpts',
            array(
                'type'              => 'array',
                'sanitize_callback' => array( get_called_class(), 'sanitize_cpt_list' ),
                'default'           => array(),
            )
        );

        // 3) Ajout du champ qui va afficher les checkboxes
        add_settings_field(
            'danka_duplicate_cpts_field',             // ID du champ
            'Choix des CPT à dupliquer',              // Label affiché
            array( get_called_class(), 'render_cpt_checkboxes' ), // Callback d'affichage
            self::$slug,                              // Page sur laquelle on affiche ce champ
            $section_id,                              // Section ID où placer le champ
            array( 'label_for' => 'danka_duplicate_cpts' )
        );
    }

    /**
     * Callback pour assainir la liste des CPT envoyée depuis le formulaire
     */
    public static function sanitize_cpt_list( $input ) {
        if ( ! is_array( $input ) ) {
            return array();
        }
        return array_map( 'sanitize_text_field', $input );
    }

    /**
     * Affiche les cases à cocher pour chaque CPT public
     */
    public static function render_cpt_checkboxes( $args ) {
        $option_name = 'danka_duplicate_cpts';
        $enabled_cpts = get_option( $option_name, array() );

        // On récupère tous les CPT publics
        $all_cpts = get_post_types( array( 'public' => true ), 'objects' );

        echo '<fieldset>';
        foreach ( $all_cpts as $slug => $cpt_obj ) {
            $checked = in_array( $slug, $enabled_cpts ) ? 'checked' : '';
            printf(
                '<label style="display:block;margin-bottom:4px;">
                    <input type="checkbox" name="%1$s[]" value="%2$s" %3$s />
                    %4$s
                </label>',
                esc_attr( $option_name ),
                esc_attr( $slug ),
                $checked,
                esc_html( $cpt_obj->labels->singular_name )
            );
        }
        echo '</fieldset>';
    }

    /**
     * Affiche une notice de confirmation quand les réglages sont sauvés
     */
    public static function register_notice() {
        if (
            isset( $_GET['page'] )
            && $_GET['page'] === self::$slug
            && isset( $_GET['settings-updated'] )
            && true == $_GET['settings-updated']
        ) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>Vos préférences de duplication ont été enregistrées.</strong></p>
            </div>
            <?php
        }
    }

    /**
     * Ajoute un lien "Dupliquer" dans les actions de chaque post/page
     * UNIQUEMENT si le CPT est autorisé
     */
    public static function duplicate_post_link( $actions, $post ) {
        // Récupère la liste des CPT cochés dans nos réglages
        $enabled_cpts = get_option( 'danka_duplicate_cpts', array() );

        // Si ce CPT est dans la liste ET que l'utilisateur a le droit d'éditer
        if ( in_array( $post->post_type, $enabled_cpts, true ) && current_user_can( 'edit_posts' ) ) {
            // On sécurise l’action avec un nonce
            $nonce = wp_create_nonce( 'danka_duplicate_' . $post->ID );
            $url   = admin_url( 'admin.php?action=danka_duplicate_post_as_draft&post=' . $post->ID . '&_wpnonce=' . $nonce );

            $actions['duplicate'] = sprintf(
                '<a href="%1$s" title="%2$s">%3$s</a>',
                esc_url( $url ),
                esc_attr__( 'Dupliquer cet élément', 'danka' ),
                esc_html__( 'Dupliquer', 'danka' )
            );
        }

        return $actions;
    }

    /**
     * Gère l'action de duplication : "admin.php?action=danka_duplicate_post_as_draft"
     */
    public static function duplicate_post_as_draft() {
        global $wpdb;

        // Vérifie le paramètre post ID
        $post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
        if ( ! $post_id ) {
            wp_die( 'Identifiant de post manquant.' );
        }

        // Vérifie le nonce
        if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'danka_duplicate_' . $post_id ) ) {
            wp_die( 'Échec de vérification de sécurité (nonce invalide).' );
        }

        // Récupère le post original
        $post = get_post( $post_id );
        if ( ! $post ) {
            wp_die( 'Le contenu original est introuvable.' );
        }

        // Vérifie la capacité de l'utilisateur
        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_die( 'Droits insuffisants pour dupliquer ce contenu.' );
        }

        // Prépare la création d’un nouveau brouillon
        $new_post_args = array(
            'comment_status' => $post->comment_status,
            'ping_status'    => $post->ping_status,
            'post_author'    => get_current_user_id(),
            'post_content'   => $post->post_content,
            'post_excerpt'   => $post->post_excerpt,
            'post_name'      => $post->post_name . '-dup',
            'post_parent'    => $post->post_parent,
            'post_password'  => $post->post_password,
            'post_status'    => 'draft', // On crée un brouillon
            'post_title'     => $post->post_title . ' (Copie)',
            'post_type'      => $post->post_type,
            'menu_order'     => $post->menu_order
        );

        // Crée le nouveau post
        $new_post_id = wp_insert_post( $new_post_args );

        // Copie les taxonomies (catégories, étiquettes, etc.)
        $taxonomies = get_object_taxonomies( $post->post_type );
        foreach ( $taxonomies as $taxonomy ) {
            $terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
            wp_set_object_terms( $new_post_id, $terms, $taxonomy );
        }

        // Copie les champs personnalisés
        $post_meta = $wpdb->get_results( 
            $wpdb->prepare( 
                "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", 
                $post_id 
            ) 
        );
        if ( $post_meta ) {
            foreach ( $post_meta as $meta_info ) {
                $meta_key   = $meta_info->meta_key;
                $meta_value = $meta_info->meta_value;
                // On évite de dupliquer l'historique de slug
                if ( '_wp_old_slug' === $meta_key ) {
                    continue;
                }
                update_post_meta( $new_post_id, $meta_key, $meta_value );
            }
        }

        // Redirige l’utilisateur vers l’éditeur du nouveau brouillon
        wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
        exit;
    }
}

// Initialisation
DankaDuplicateManager::init();
