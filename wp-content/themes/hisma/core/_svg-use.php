<?php

add_action( 'wp_head', function() {
    $html = "";
    $svg_files = glob( get_template_directory() . '/assets/icons/*.svg' );
    foreach ( $svg_files as $svg_file ) {
        $svg = file_get_contents( $svg_file );
        $filename = pathinfo( $svg_file, PATHINFO_FILENAME );
        $svg = str_replace( '<svg', "<symbol id='icon-{$filename}' aria-hidden='true' focusable='false'", $svg );
        $svg = str_replace( '</svg>', '</symbol>', $svg );
        $html .= $svg;
    }
    echo "<svg style='display: none;'>{$html}</svg>";
}, 10 );