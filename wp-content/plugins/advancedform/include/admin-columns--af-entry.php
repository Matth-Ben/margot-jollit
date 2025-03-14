<?php


// // manage colunms
// function af_manage__af_entry_columns($columns) {

//     $new_columns = [
//         'cb' => $columns['cb'],
//         'title' => $columns['title'],
//         'form' => 'Formulaire',
//         'date' => $columns['date']
//     ];

//     return $new_columns;
// }


// // make columns sortable
// function my_set_sortable_columns( $columns ) {

//     $columns['advancedform'] = 'advancedform';
    
//     //To make a column 'un-sortable' remove it from the array
//     //unset($columns['date']);
    
//     return $columns;
// }


// // populate column cells
// function af_populate__af_entry_columns($column_name, $post_id) {
    
//     if ('form' === $column_name) {
//         $af_form_id = get_post_meta($post_id, 'af_form', true);

//         if ($af_form_id) {
//             $initial_form_has_changed = false;

//             $af_form = new AF_Form($af_form_id);
//             $form_fields = $af_form->data['fields'];
//             $entry_fields = $af_form->get_af_entry($post_id);
            
//             foreach ($entry_fields as $field_id => $field) if (!isset($form_fields[$field_id])) $initial_form_has_changed = true;

//             echo '<a href="?post_type=advancedform_entry&form=' . $af_form_id . '">' . get_the_title($af_form_id) . '</a>';
//             echo $initial_form_has_changed ? '<br><small>Des champs ont été supprimé ou ajouté</small>' : '';
//         } else {
//             echo "<p>Le formulaire n'exite plus</p>";
//         }
//     }
// }


// // set query to sort
// function af_sort_advancedform_entries_query($query) {

//     $orderby = $query->get('orderby');
//     $form_id = isset($_GET['form']) ? $_GET['form'] : '';

//     if ($form_id == '') return;
    
//     $meta_query = [
//         [
//             'key' => 'advancedform',
//             'value' => $form_id,
//             // 'compare' => 'IN', // see note above
//         ]
//     ];

//     $query->set('meta_query', $meta_query );
//     $query->set('orderby', $orderby);
// }


// global $pagenow;

// if (is_admin() && 'edit.php' == $pagenow && isset( $_GET['post_type'] ) && 'af_entry' == $_GET['post_type']) {

//     // manage colunms
//     add_filter('manage_af_entry_posts_columns', 'af_manage__af_entry_columns');

//     // make columns sortable
//     // add_filter('manage_edit-advancedform_entry_sortable_columns', 'my_set_sortable_columns');

//     // populate column cells
//     add_action('manage_af_entry_posts_custom_column', 'af_populate__af_entry_columns', 10, 2);

//     // set query to sort
//     add_action('pre_get_posts', 'af_sort_advancedform_entries_query');
// }
