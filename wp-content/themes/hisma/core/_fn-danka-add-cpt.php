<?php

function danka_add_cpt( $singular, $plural, $is_female, $args = null ) {

    function slugify( $text, string $divider = "-" ) {
        $text = preg_replace( "~[^\pL\d]+~u", $divider, $text );
        $text = iconv( "utf-8", "us-ascii//TRANSLIT", $text );
        $text = preg_replace( "~[^-\w]+~", "", $text );
        $text = trim( $text, $divider );
        $text = preg_replace( "~-+~", $divider, $text );
        $text = strtolower( $text );

        if ( empty( $text ) ) {
            return "n-a";
        }

        return $text;
    }

    function lower_case_first( $text ) {
        return str_replace( 'É', 'é', lcfirst( $text ) );
    }

    $slug = slugify( $singular );
    $definite = $is_female ? "la " : "le ";
    $definite = preg_match( '/^[aeiouyéÉ]/i', $singular ) ? "l'" : $definite; 
    $indefinite = $is_female ? "une" : "un";

	$labels = array(
		"name"                => $plural,
		"singular_name"       => $singular,
		"menu_name"           => $plural,
		"all_items"           => ($is_female ? "Toutes" : "Tous") . " les " . lower_case_first( $plural ),
		"view_item"           => "Voir les " . lower_case_first( $plural ),
		"add_new_item"        => "Ajouter " . ($is_female ? "une nouvelle" : "un nouveau") . " " . lower_case_first( $singular ),
		"add_new"             => "Ajouter",
		"edit_item"           => "Editer $definite" . lower_case_first( $singular ),
		"update_item"         => "Modifier $definite" . lower_case_first( $singular ),
		"search_items"        => "Rechercher $indefinite " . lower_case_first( $singular ),
		"not_found"           => "Non " . ($is_female ? "trouvée" : "trouvé"),
		"not_found_in_trash"  => "Non " . ($is_female ? "trouvée" : "trouvé") . " dans la corbeille",
	);

    if ( !$is_female && $definite === "l'" ) {
        $labels["add_new_item"] = str_replace( 'nouveau', 'nouvel', $labels["add_new_item"] );
    }
	
    $args = $args !== null ? $args : array(
        "label" => $plural,
        "labels" => $labels,
        "supports" => array( "title", "editor", "excerpt", "author", "thumbnail", "comments", "revisions", "custom-fields", ),
        "show_in_rest" => true,
        "hierarchical" => false,
        "public" => true,
        "has_archive" => true,
        "rewrite" => array( "slug" =>  $slug ),
    );
	
	register_post_type( str_replace( '-', '', $slug ), $args );
}