<?php


// manage colunms
function af_manage_advancedform_columns($columns) {

    $new_columns = [
        'cb' => $columns['cb'],
        'title' => $columns['title'],
        'entries_number' => 'Nombre d\'entrées',
        'date' => $columns['date']
    ];

    return $new_columns;
}


// populate column cells
function af_populate_advancedform_columns($column_name, $post_id) {
    
    if ('entries_number' === $column_name) {
        $url = 'edit.php?post_type=advancedform_entry&form=' . $post_id;
        $posts = get_posts([
            'post_type' => 'advancedform_entry',
            'meta_query' => [
                [
                    'key'   => 'advancedform',
                    'value' => $post_id,
                ]
            ]
        ]);
        $posts_number = count($posts);

        echo "<a href='{$url}'>{$posts_number}</a>";
    }
}


global $pagenow;

if (is_admin() && 'edit.php' == $pagenow && isset( $_GET['post_type'] ) && 'advancedform' == $_GET['post_type']) {

    // manage colunms
    add_filter('manage_advancedform_posts_columns', 'af_manage_advancedform_columns');

    // populate column cells
    add_action('manage_advancedform_posts_custom_column', 'af_populate_advancedform_columns', 10, 2);
}


// add "View entries" to row actions
function af_modify_list_row_actions_in_form_list_table( $actions, $post ) {
    
    if ( $post->post_type == "advancedform" ) {

        $url = 'edit.php?post_type=advancedform_entry&form=' . $post->ID;
        $actions = ['see-entries' => "<a href='{$url}'>Voir les entrées</a>"] + $actions;
    }
 
    return $actions;
}
add_filter( 'post_row_actions', 'af_modify_list_row_actions_in_form_list_table', 10, 2 );