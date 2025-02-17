<?php

// context
add_filter( 'timber/context', function( $context ) {
    $context['menus'] = get_menus();
    $context['assets'] = get_template_directory_uri() . '/assets';
    $context['_session'] = $_SESSION;
    $context['_get'] = $_GET;
    $context['_post'] = $_POST;

    return $context;
} );


// loader
add_filter( 'timber/loader/loader', function ( $loader ) {
    $loader->addPath( get_template_directory() . "/assets", "assets" );
    $loader->addPath( get_template_directory() . "/assets/icons", "icons" );
    $loader->addPath( get_template_directory() . "/assets/svg", "svg" );
    $loader->addPath( get_template_directory() . "/views/components", "components" );
    $loader->addPath( get_template_directory() . "/views/blocks", "acf-components" );
    $loader->addPath( get_template_directory() . "/views/utils", "utils" );

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
    $twig->addFunction( new \Timber\Twig_Function( 'getContrastColor', function($hexColor) { return getContrastColor($hexColor); } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'timber_term', function( $id ) { return new \Timber\Term( $id ); } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_site', function() { return new Timber\Site(); } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_menus', function() { return get_menus(); } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_posts', function ( $arguments = null ) {
        $post_class = get_custom_timber_post( null, isset( $arguments['post_type'] ) ? $arguments['post_type'] : null );
        return $arguments ? \Timber\Timber::get_posts( $arguments, $post_class ) : null;
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
    $twig->addFunction(new \Timber\Twig_Function('get_last_events', function ($max = 3, $currentPost = null, $postType = 'event') {
        // Préparer la liste des IDs à exclure
        $excludedPosts = $currentPost ? [$currentPost] : [];

        // Récupérer les événements
        return \Timber\Timber::get_posts(array(
            'numberposts' => $max,
            'order' => 'ASC',
            'post_type' => $postType,
            'post__not_in' => $excludedPosts, // Exclure $currentPost si défini
        ));
    }));
    $twig->addFunction(new \Timber\Twig_Function('check_event_date', function ($posts, $currentPost = null, $postType = 'event') {
        $upcomingEventIds = array();
    
        // Récupérer les événements valides à partir des posts donnés
        foreach ($posts as $post) {
            // Exclure le post correspondant à $currentPost
            if ($currentPost && $post->ID == $currentPost) {
                continue; // Passe au post suivant
            }
    
            $eventDate = get_field('date', $post->ID);
            $date = DateTime::createFromFormat('d/m/Y', $eventDate);
    
            // Vérifier que la date est valide et future
            if ($date && $date->format('Y-m-d') > date('Y-m-d')) {
                $upcomingEventIds[] = $post->ID;
            }
        }
    
        // Si moins de 3 événements trouvés, compléter avec d'autres événements
        if (count($upcomingEventIds) < 3) {
            // Ajouter les événements manquants en utilisant `post__not_in`
            $newPosts = \Timber\Timber::get_posts(array(
                'numberposts' => 3 - count($upcomingEventIds), // Compléter jusqu'à 3 éléments
                'order' => 'ASC',
                'meta_key' => 'date',
                'orderby' => 'meta_value',
                'post_type' => $postType,
                'post__not_in' => array_merge($upcomingEventIds, [$currentPost]), // Exclure les événements déjà collectés et $currentPost
                'meta_query' => array(
                    array(
                        'key' => 'date',
                        'value' => date('Y-m-d'),
                        'compare' => '>',
                        'type' => 'DATE'
                    )
                )
            ));
    
            // Ajouter les nouveaux posts trouvés à la liste des événements
            foreach ($newPosts as $post) {
                $upcomingEventIds[] = $post->ID;
            }
        }
    
        // Limiter les IDs à un maximum de 3
        $upcomingEventIds = array_slice($upcomingEventIds, 0, 3);
    
        // Récupérer tous les événements finaux par leurs IDs
        $finalEvents = \Timber\Timber::get_posts(array(
            'post__in' => $upcomingEventIds,
            'orderby' => 'meta_value',
            'meta_key' => 'date',
            'order' => 'ASC',
            'post_type' => $postType
        ));
    
        return $finalEvents;
    }));
    $twig->addFunction(new \Timber\Twig_Function('get_image_by_id', function($image_id) {
        if ( ! $image_id ) {
            return null;
        }
        return new \Timber\Image( $image_id );
    }));

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
    $twig->addFunction( new \Timber\Twig_Function( 'get_breadcrumbs', function() {
        if ( class_exists( 'Breadcrumbs' ) ) {
            return Breadcrumbs::get_breadcrumbs();
        }
    } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_share', function() { return SocialNetwork::get_share(); } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_social_network', function() { return SocialNetwork::get_social_network(); } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'get_image_html', function( $id ) { return AppImages::get_image_html( $id ); } ) );
    $twig->addFunction( new \Timber\Twig_Function( 'has_anchor', function($data) {
        $has_anchor = false;

        foreach ($data as $value) {
            if ($value['anchor']['anchor_id'] != '') {
                $has_anchor = true;
            }
        }

        return $has_anchor;
    } ) );
    
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

function getContrastColor($hexColor) {
    // Supprimer le symbole # si présent
    $hexColor = str_replace('#', '', $hexColor);

    // Convertir la couleur hexadécimale en composantes RGB
    $r = hexdec(substr($hexColor, 0, 2));
    $g = hexdec(substr($hexColor, 2, 2));
    $b = hexdec(substr($hexColor, 4, 2));

    // Calculer la luminosité (selon l'algorithme de luminosité relative)
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

    // Si la luminosité est inférieure à 0.5, le fond est sombre => texte clair
    return $luminance > 0.5 ? 'dark' : 'light';
}