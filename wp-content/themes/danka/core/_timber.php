<?php

// context
add_filter( 'timber/context', function( $context ) {
    $context['menus'] = get_menus();
    $context['assets'] = get_template_directory_uri() . '/assets';
    $context['_session'] = $_SESSION;
    $context['_get'] = $_GET;
    $context['_post'] = $_POST;

    // styleguide
    $path = get_template_directory() . '/data.json';

    if ( file_exists( $path ) ) {
        $content = file_get_contents( $path );
        $app_data = $content ? json_decode( $content, true ) : null;

        if ( $app_data ) {
            $context['app_data'] = $app_data;
        }
    }

    return $context;
} );


// loader
add_filter( 'timber/loader/loader', function ( $loader ) {
    $loader->addPath( get_template_directory() . "/assets", "assets" );
    $loader->addPath( get_template_directory() . "/assets/icons", "icons" );
    $loader->addPath( get_template_directory() . "/assets/svg", "svg" );
    $loader->addPath( get_template_directory() . "/views/components", "components" );
    $loader->addPath( get_template_directory() . "/views/acf-components", "acf-components" );

    return $loader;
} );


// twig
add_filter( 'timber/twig', function( $twig ) {
    $twig->addExtension( new \Twig\Extension\StringLoaderExtension() );
    $twig->getExtension( \Twig\Extension\CoreExtension::class )->setTimezone( 'Europe/Paris' );

    // WordPress
    $twig->addFunction( new \Timber\Twig_Function( 'get_term', 'get_term' ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_meta', 'get_meta' ) );
    $twig->addFunction( new \Timber\Twig_Function( 'wp_list_categories', 'wp_list_categories' ) );

    // utils
    $twig->addFunction( new \Timber\Twig_Function( 'uniqid', function() { return uniqid(); } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'timber_post', function( $id = null ) {
        return $id ? \Timber\Timber::get_post( $id, get_custom_timber_post( $id ) ) : null;
    } ) );
    // $twig->addFunction( new \Timber\Twig_Function( 'timber_pagination', function( $query = null ) {
    //     return $id ? \Timber\Pagination( $query ) : null;
    // } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'timber_term', function( $id ) { return new \Timber\Term( $id ); } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_menus', function() { return get_menus(); } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_pinned_posts', function( $post_type, $limit, $pinned_post_ids = null, $other_arguments = null ) {
        $posts = array();
        $arguments = array(
            'post_type' => $post_type,
            'posts_per_page' => $pinned_post_ids ? $limit - count( $pinned_post_ids ) : $limit,
            'post__not_in' => $pinned_post_ids ?? array(),
        );

        if ( $other_arguments ) {
            $arguments = array_merge( $arguments, $other_arguments );
        }

        $posts = new \Timber\PostQuery( $arguments );

        if ( $pinned_post_ids ) {
            if ( $posts ) {
                foreach ( $posts as $p ) {
                    $pinned_post_ids[] = $p->id;
                }
            }

            $posts = new \Timber\PostQuery( array (
                'post_type' => $post_type,
                'posts_per_page' => -1,
                'post__in' => $pinned_post_ids,
                'orderby' => 'post__in',
            ) );
        }

        return $posts;
    } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_posts', function ( $arguments = null ) {
        $post_class = get_custom_timber_post( null, isset( $arguments['post_type'] ) ? $arguments['post_type'] : null );
        return $arguments ? new \Timber\PostQuery( $arguments, $post_class ) : null;
    } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_languages', function () {
        if ( function_exists( 'pll_the_languages' ) ) {
            return pll_the_languages( array( 'raw' => true ) );
        }

        return null;
    } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_home_url', function () {
        return function_exists( 'pll_home_url' ) ? pll_home_url() : get_bloginfo( 'url' );
    } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_current_language', function () {
        return function_exists( 'pll_current_language' ) ? pll_current_language() : explode( '-', get_bloginfo( 'language' ) )[0];
    } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_current_language_name', function () {
        return function_exists( 'pll_current_language' ) ? pll_current_language( 'name' ) : null;
    } ) );

    // data
    $twig->addFunction( new \Timber\Twig_Function( 'get_data', function() {
        $path = get_template_directory() . '/data.json';
        
        if ( file_exists( $path ) ) {
            return  json_decode( file_get_contents( $path ), true );
        }
    } ) );

    $plugin_functions = array(
        'acf' => array(
            'get_field',
            'get_fields',
        ),
        'polylang' => array(
            'pll__',
        ),
        'woocommerce' => array(
            'wc_get_product',
            'get_woocommerce_currency_symbol'
        )
    );

    foreach ( $plugin_functions as $function_names ) {
        foreach ( $function_names as $function_name ) {
            if ( function_exists( $function_name ) ) {
                $twig->addFunction( new \Timber\Twig_Function( $function_name, $function_name ) );
            } else {
                $twig->addFunction( new \Timber\Twig_Function( $function_name, function() {
                    return null;
                } ) );
            }
        }
    }

    // Danka
    $twig->addFunction( new \Timber\Twig_Function( 'get_breadcrumbs', function( $post_id = null ) {
        if ( class_exists( 'Breadcrumbs' ) ) {
            return Breadcrumbs::get_breadcrumbs( $post_id );
        }
    } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_share', function() { return SocialNetwork::get_share(); } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_social_network', function() { return SocialNetwork::get_social_network(); } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_image_html', function( $id ) { return AppImages::get_image_html( $id ); } ) );
    
    $twig->addFunction( new \Timber\Twig_Function( 'slugify', function( $string ) {
        // Convertir la chaîne en minuscules
        $string = strtolower($string);

        // Remplacer les caractères accentués par leurs équivalents non accentués
        $unwanted_array = array(
            'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a', 'ã' => 'a', 'å' => 'a', 'æ' => 'ae',
            'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i',
            'î' => 'i', 'ï' => 'i', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
            'õ' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ÿ' => 'y',
            'ß' => 'ss', 'ø' => 'o', 'þ' => 'th', 'ð' => 'd'
        );
        $string = strtr($string, $unwanted_array);

        // Remplacer les espaces et les caractères non alphanumériques par des tirets
        $string = preg_replace('/[^a-z0-9]+/', '-', $string);

        // Supprimer les tirets superflus
        $string = trim($string, '-');

        return $string;
    } ) );
    
    $twig->addFunction( new \Timber\Twig_Function( 'sort_terms_hierarchicaly', function( Array $cats, $parentId = 0 ) {
        return sort_terms_hierarchicaly( $cats, $parentId );
    } ) );

    return $twig;
} );


// Ajouter les fichiers twig à l'éditeur de thème
add_filter( 'wp_theme_editor_filetypes', function( $types ) {
    $types[] = 'twig';

    return $types;
} );
