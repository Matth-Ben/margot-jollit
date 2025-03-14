<?php

/**
 * Save post : af_form
 */
function af_save_post__af_form($post_id)
{
    // don't create infinite loop
    remove_action('save_post', 'af_save_post__af_form');
    
    if (!isset($_POST['af_form_data'])) return $post_id;

    // remove ininite loop
    update_post_meta($post_id, 'af_form_data', $_POST['af_form_data']);

    // set post status "publish"
    wp_update_post([
        'ID'            => $post_id,
        'post_status'   => 'publish'
    ]);
}

add_action('save_post', 'af_save_post__af_form');




/**
 * Save post : af_entry
 */
function save_af_save_post__af_entry($post_id)
{
    // don't create infinite loop
    remove_action('save_post', 'save_af_save_post__af_entry');

    if (!isset($_POST['af_entry_data'])) return $post_id;
    
    $postmetas = get_post_meta($post_id);

    foreach ($postmetas as $postmetakey => $postmetadata) {

        $explode_postmetakey = explode('_', $postmetakey);

        if ($explode_postmetakey[0] == 'type') {
            $field_id = $explode_postmetakey[1];
            $field_type = $postmetas['type_' . $field_id][0];
            $value = $_POST[$field_id];

            // d($_POST);
            // d($field_id);
            // d($field_type);
            // d($value);
            // die;

            // wordpress auto serialize data
            if ($field_type == 'files') {
                $value = str_replace('\\', '', $value);
                $value = json_decode($value);
            }

            // format data for checkbox
            if ($field_type == 'choice') {
                $original_field = unserialize(unserialize($postmetas["original_field_{$field_id}"][0]));
                $choice_logic = $original_field['choice_logic'];
                
                if ($choice_logic == 'checkbox') {
                    $af_form_id = get_post_meta($post_id, 'af_form', true);
                    $af_form = new AF_Form($af_form_id);
                    $choices = $af_form->get_postmetavalue_compatible_with_acf($_POST[$field_id], 'checkbox', $field_id);
                    $value = '';
    
                    foreach ($choices as $choice) $value .= $choice . '\\\\n';
                }
            }

            update_post_meta($post_id, "value_{$field_id}", $value);
        }
    }

    // $postmetas = get_post_meta($post_id);

    // foreach ($postmetas as $postmetakey => $postmetadata) {

    //     if (explode('_', $postmetakey)[0] == 'value') {

    //         if ($_POST[$postmetakey]) {
    //             $value = $_POST[$postmetakey];
    //             $field_id = explode('_', $postmetakey)[1];
    //             $field_type = get_post_meta($post_id, 'type_' . $field_id, true);

    //             // wordpress auto serialize data
    //             if ($field_type == 'files') {
    //                 $value = str_replace('\\', '', $value);
    //                 $value = json_decode($value);
    //             }

    //             update_post_meta($post_id, $postmetakey, $value);
    //         }
    //     }
    // }
}

add_action('save_post', 'save_af_save_post__af_entry');
