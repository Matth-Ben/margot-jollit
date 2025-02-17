<?php

define( 'DANKA_SETTINGS_BREADCRUMBS_SLUG', 'danka-settings-breadcrumbs' );

class Breadcrumbs
{
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'register_options_page__breadcrumbs__settings' ) );
        add_action( 'admin_init', array( $this, 'register_settings__breadcrumbs' ) );
    }


    /**
     * Enregistrer une page de sous-menu pour les paramètres
     */
    public function register_options_page__breadcrumbs__settings()
    {
        add_submenu_page(
            DANKA_SETTINGS_SLUG,                  // parent
            'Fil d\'Ariane',                                       // <title></title>
            'Fil d\'Ariane',                                       // menu link text
            'manage_options',                                   // capability to access the page
            DANKA_SETTINGS_BREADCRUMBS_SLUG,                            // page URL slug
            array( $this, 'render_options_page__breadcrumbs__settings' ),  // callback function with content
            5                                                   // priority
        );
    }


    /**
     * 
     */
    public function register_settings__breadcrumbs()
    {
        $post_types = get_post_types( array('public'   => true ), 'objects');
        
        if ( isset( $post_types['attachment'] ) ) {
            unset( $post_types['attachment'] );
        }
        
        if ( isset( $post_types['page'] ) ) {
            unset( $post_types['page'] );
        }
        
        $section_name = 'custom__breadcrumbs';
    
        add_settings_section(
            $section_name,                                                  // id
            'Menu de référence',                          // title
            '',                                                             // callback
            DANKA_SETTINGS_BREADCRUMBS_SLUG                                    // page
        );


        // le menu
        add_settings_field(
            "{$section_name}__menu",                                  // id
            'Menu séléctionné',                                                 // title
            array( $this, "render_settings__breadcrumbs__menu" ),  // callback
            DANKA_SETTINGS_BREADCRUMBS_SLUG,                                        // page
            $section_name,                                                   // section
            array( 'option_name' => "{$section_name}__menu" )
        );
    
        register_setting(
            DANKA_SETTINGS_BREADCRUMBS_SLUG,                                        // option group
            "{$section_name}__menu",                                   // option name
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            )
        );


        $section_name = 'custom__breadcrumbs__archive';
    
        add_settings_section(
            $section_name,                                                  // id
            'Remonté automatique des archives',                          // title
            '',                                                             // callback
            DANKA_SETTINGS_BREADCRUMBS_SLUG                                    // page
        );


        // les posts parent pour chaque type de post
        foreach ( $post_types as $slug => $post_type ) {

            $slug = str_replace( '-', '_', $slug );
            $option_name = "{$section_name}__{$slug}";

            add_settings_field(
                $option_name,                                  // id
                $post_type->label,                                                 // title
                array( $this, "render_settings__breadcrumbs__post_types" ),  // callback
                DANKA_SETTINGS_BREADCRUMBS_SLUG,                                        // page
                $section_name,                                                   // section
                array( 'option_name' => $option_name )
            );
        
            register_setting(
                DANKA_SETTINGS_BREADCRUMBS_SLUG,                                        // option group
                $option_name,                                   // option name
                array(
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                )
            );
        }
    }


    /**
     * 
     */
    public function render_settings__breadcrumbs__menu()
    {
        $option_name = 'custom__breadcrumbs__menu';
        $value =  get_option( $option_name );
        $menu_locations = get_nav_menu_locations();
        $menus = array();

        foreach ( $menu_locations as $slug => $menu_id ) {
            $menu = new Timber\Menu( $menu_id );
            $menus[$menu->id] = $menu;
        }

        echo "<select id='$option_name' name='$option_name'>";
        arsort( $menus );

        foreach ( $menus as $menu) {
            if ( $menu->theme_location === $value ) {
                echo "<option value='" . $menu->theme_location . "' selected>" . $menu->title . "</option>";
                continue;
            }
            echo "<option value='" . $menu->theme_location . "'>" . $menu->title . "</option>";
        }

        echo "</select>";
    }


    /**
     * 
     */
    public function render_settings__breadcrumbs__post_types( $args )
    {
        $option_name = $args['option_name'];
        $value = get_option( $option_name );
        $checked = $value ? 'checked' : '';

        // echo "<input id={$option_name} name='{$option_name}' type='text' value='{$value}' />";
        echo "<input id={$option_name} name='{$option_name}' type='checkbox' $checked />";
    }
    
    
    /**
     * 
     */
    public function render_options_page__breadcrumbs__settings()
    {
        // // css
        // wp_enqueue_style('fields-array', plugins_url('/advancedform/assets/css/options-page-settings.css'));
        
        ?>
            <form method="POST" action="options.php">
                <?php
                    do_settings_sections( DANKA_SETTINGS_BREADCRUMBS_SLUG );
                    settings_fields( DANKA_SETTINGS_BREADCRUMBS_SLUG );
                    submit_button();
                ?>
            </form>
        <?php
    }


    /**
    * Récupère le menu Timber
    */
    public static function get_timber_menu( $menu_slug ) {

        if ( $menu_slug ) {
            foreach ( get_nav_menu_locations() as $slug => $menu_id) {
                if ($slug === $menu_slug ) {
                    return new Timber\Menu( $menu_id );
                }
            }
        }

        return null;
    }


    /**
     * Récupèrer les ancêtres d'un item dans un menu avec l'ID comme identifiant
     */
    public static function get_item_ancestors_by_id( $id, $items, $ancestors = array() ) {

        foreach ( $items as $key => $item ) {
            $sub_items = $item->children;
            
            if ( is_array( $sub_items ) && count( $sub_items ) > 0 ) {
                
                $search = self::get_item_ancestors_by_id( $id, $sub_items, array_merge( $ancestors, [[
                    'url' => $item->url,
                    'text' => $item->title
                ]] ));

                if ( $search ) {
                    return $search;
                }
            }

            else if ( $item->object_id == $id ) {
                $ancestors[] = [
                    'url' => $item->url,
                    'text' => $item->title
                ];

                return $ancestors;
            }
        }

        return false;
    }


    /**
     * Récupèrer les ancêtres d'un item dans un menu avec l'URL comme identifiant
     */
    public static function get_item_ancestors_by_url( $url, $items, $ancestors = array() ) {
        
        if ( substr( $url, -1 ) === "/" ) {
            $url = substr_replace( $url, "", -1 );
        }
        
        foreach ( $items as $key => $item ) {
            $sub_items = $item->children;
            $item_url = $item->url;

            if ( substr( $item_url, -1 ) === "/" ) {
                $item_url = substr_replace( $item_url, "", -1 );
            }
            
            if ( is_array($sub_items) && count($sub_items) > 0 ) {
                $search = self::get_item_ancestors_by_url($url, $sub_items, array_merge( $ancestors, [[
                    'url' => $item_url,
                    'text' => $item->title
                ]] ));

                if ($search) {
                    return $search;
                }
            }

            else if ($item_url == $url) {
                $ancestors[] = [
                    'url' => $item_url,
                    'text' => $item->title
                ];

                return $ancestors;
            }
        }

        return false;
    }


    /**
     * Récupérer les données fil d'Ariane
     */
    public static function get_breadcrumbs( $post_id = null )
    {   
        if ( is_front_page() ) {
            return array();
        }

        if ( $post_id === null ) {
            wp_reset_query();
            global $post;
            global $wp_query;
            $current_query = true;
        } else {
            if ( is_numeric( $post_id ) ) {
                $post = \Timber\Timber::get_post( $post_id );
            }
            $current_query = false;
        }


        $front_page_id = get_option( 'page_on_front' );
        $menu_slug = get_option( 'custom__breadcrumbs__menu' );
        $menu = self::get_timber_menu( $menu_slug );
        $post_id = $post->ID;
        $post_type = get_post_type( $post_id );

        // si aucun menu n'est sélectionné ou si la page d'accueil est le post actuel
        if ( $front_page_id == $post_id ) {
            return;
        }

        // le premier lien est toujours l'accueil
        $items = array( array(
            'url' => get_home_url(),
            'text' => 'Accueil',
            'id' => $front_page_id
        ) );
        // $items = array();

        if ( is_search() && $post_id === null ) {
            $items[] = array(
                'url' => '',
                'text' => function_exists( 'pll__' ) ? pll__( 'Recherche' ) : 'Recherche'
            );
            return $items;
        }

        // Page parent ou page archive
        $parent = null;
        $archive = null;

        $is_singular = $current_query ? is_singular() : true;
        $is_single = $current_query ? is_single() : ( $post->post_type !== 'page' );
        $is_post_type_archive = $current_query ? is_post_type_archive() : false;

        if ( $is_singular ) {
            $ancestors = get_post_ancestors( $post->ID );

            // Si n'est pas une page, ajouter la page d'archive
            if ( $is_single ) {
                $post_type_object = get_post_type_object( $post_type );
                $text = $post_type_object->labels->name;
                $url = get_post_type_archive_link( $post_type );
                $archive_is_active = get_option( "custom__breadcrumbs__archive__$post_type" );

                if ( function_exists( 'get_field' ) ) {
                    $acf_title = get_field( 'title', $post_type . '_archive' );
                    $text = $acf_title ? $acf_title : $text;
                }

                if ( $archive_is_active && $url && $text ) {
                    $archive =  array(
                        'url' => $url,
                        'text' => $text
                    );
                }

                if ( $archive ) {
                    $archive_ancestors = self::get_item_ancestors_by_url( $archive['url'], $menu->items );
                    
                    if ( $archive_ancestors ) {
                        unset( $archive_ancestors[count($archive_ancestors) - 1] );
                        $items = array_merge( $items, $archive_ancestors );
                    }

                    $items[] = $archive;
                }
            }

            // S'il existe des posts parents
            if ( $ancestors ) {
                $menu_ancestors = self::get_item_ancestors_by_id( $ancestors[count($ancestors) - 1], $menu->items );

                if ( $menu_ancestors ) {
                    unset( $menu_ancestors[count($menu_ancestors) - 1] );
                    $items = array_merge( $items, $menu_ancestors );
                }
                
                for ( $i = count($ancestors) - 1; $i >= 0; $i-- ) {
                    $item = array(
                        'url' => get_permalink( $ancestors[$i] ),
                        'text' => get_the_title( $ancestors[$i] ),
                        'id' => $ancestors[$i]
                    );

                    $items[] = $item;
                }
            }
            
            elseif ( $menu ) {
                $menu_ancestors = self::get_item_ancestors_by_id( $post->ID, $menu->items );
    
                if ( $menu_ancestors ) {
                    unset( $menu_ancestors[count($menu_ancestors) - 1] );
                    $items = array_merge( $items, $menu_ancestors );
                }
            }
        }

        // Si la page web affiche un post, l'ajouter
        if ( $is_singular ) {
            $items[] = [
                'url' => '',
                'text' => get_the_title( $post->ID ),
            ];
        }

        // Si la page web affiche une archive, l'ajouter
        else if ( $is_post_type_archive ) {
            $post_type = $wp_query->query['post_type'];
            $text = $wp_query->queried_object->labels->name;
            $url = get_post_type_archive_link( $post_type );
            $archive_ancestors = $menu && $menu->items ? self::get_item_ancestors_by_url( $url, $menu->items ) : null;

            if ( $archive_ancestors ) {
                unset( $archive_ancestors[count($archive_ancestors) - 1] );
                $items = array_merge( $items, $archive_ancestors );
            }
            
            if ( function_exists( 'get_field' ) ) {
                $acf_title = get_field( 'title', $post_type . '_archive' );
                $text = $acf_title ? $acf_title : $text;
            }

            $items[] = array(
                'url' => '',
                'text' => $text
            );
        }

        $filtered_items = apply_filters( 'danka_breadcrumbs', $items, $post_id );

        if ( $filtered_items ) {
            return $filtered_items;
        }

        return $items;
    }
}

new Breadcrumbs();