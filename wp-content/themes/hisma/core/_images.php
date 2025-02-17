<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class AppImages {

    public static $sizes = array();

    public static function init() {
        $app_data = danka_get_app_data();

        if ( isset( $app_data['breakpoints'] ) && is_array( $app_data['breakpoints'] ) ) {
            foreach ( $app_data['breakpoints'] as $size )  {
                if ( intval( $size ) !== 0 ) {
                    array_push( self::$sizes, $size );
                }
            }

            array_push( self::$sizes, 200 );
            array_push( self::$sizes, 2560 );
        }

        add_action( 'admin_menu', function() {
            remove_menu_page('options-media.php'); //remove media
        }, 99999 );

        add_action( 'intermediate_image_sizes_advanced', array( get_called_class(), 'remove_default_image_sizes' ) );
		// add_action( 'admin_print_styles', array( get_called_class(), 'hide_media_settings' ) );
        add_action( 'after_setup_theme', array( get_called_class(), 'add_image_sizes' ) );
        add_filter( 'big_image_size_threshold', '__return_false' );
    }


    /**
     * Supprimer les tailles d'images par défaut qui sont crées pendant l'upload
     * (Voir https://developer.wordpress.org/reference/hooks/intermediate_image_sizes_advanced/)
     */
    public static function remove_default_image_sizes( $sizes )
    {
        unset( $sizes['thumbnail'] );       // 150px
        unset( $sizes['medium'] );          // 300px
        unset( $sizes['medium_large'] );    // 768px
        unset( $sizes['large'] );           // 1024px
        unset( $sizes['1536x1536'] );       // 1536px
        unset( $sizes['2048x2048'] );       // 2048px

        return $sizes;
    }


    /**
     * Dans Options > Médias : cache les options qui gèrent les dimensions d'images à utiliser pendant l'upload
     */
    public static function hide_media_settings()
    {
        if ( 'options-media' !== get_current_screen()->id ) {
            return;
        }
        ?>

        <style>
            #wpbody-content form > h2,
            #wpbody-content form > p { display: none; }
            #wpbody-content form > table:first-of-type tr:nth-of-type( 1 ),
            #wpbody-content form > table:first-of-type tr:nth-of-type( 2 ),
            #wpbody-content form > table:first-of-type tr:nth-of-type( 3 ) { display: none; }
        </style>

        <?php
    }


    /**
     * Ajouter les formats d'image
     */
    public static function add_image_sizes()
    {
        foreach ( self::$sizes as $size ) {
            add_image_size( "size-$size", $size );
        }
    }


    /**
     * Récupèrer l'HTML d'une image
     * 
     * @param int $id
     */
    public static function get_image_html( $id ) // https://developer.mozilla.org/fr/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images
    {
        $src = wp_get_attachment_url( $id );
        $srcset = '';
        $sizes = null;
        $data_sizes = [];

        foreach ( self::$sizes as $index => $size ) {
            $data = wp_get_attachment_image_src( $id, "size-$size" );
            $sizes = $sizes === null ? $size : $sizes;
            
            if ( $data ) {
                $string = $data[1] . '-' . $data[2];
                $src = $data[0];
                $srcset .= "{$src} {$size}w, ";

                if ( !in_array( $string, $data_sizes ) ) {
                    $data_sizes[] = $string;
                }
            }
        }

        $data_sizes = join( ',', $data_sizes );

        return "<img src='$src' srcset='$srcset' sizes='{$sizes}px' data-sizes='{$data_sizes}' />";
    }
}


AppImages::init();
