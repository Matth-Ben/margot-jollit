<?php

add_action( 'init', function() {
    $post_types = get_post_types( array(
        'public' => true,
        '_builtin' => false
    ) );

    foreach ( $post_types as $post_type ) {
        if ( function_exists( "acf_add_options_page" ) ) {
            acf_add_options_page( array(
                "page_title" => "Archive ($post_type)",
                "menu_title" => "Archive",
                "parent_slug" => "edit.php?post_type=$post_type",
                "menu_slug" => "archive-$post_type",
                "post_id" => "archive_$post_type"
            ) );
        }
    }
}, 100 );
