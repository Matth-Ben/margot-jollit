<?php

if ( !function_exists( 'sort_terms_hierarchicaly' ) ) {
    function sort_terms_hierarchicaly(Array $cats, $parentId = 0) {
        $into = [];

        foreach ($cats as $i => $cat) {
            if ($cat->parent == $parentId) {
                $cat->children = sort_terms_hierarchicaly($cats, $cat->term_id);
                $into[$cat->term_id] = $cat;
            }
        }

        return $into;
    }
}