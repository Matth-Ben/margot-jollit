<?php

trait AF_Form_ACF
{
    /**
     * ACF fields supported
     */
    public $acf_fields_supported = [

        /*** Basic fields ***/
        'text',
        'textarea',
        'number',
        // 'number',
        'email',
        // 'url',
        'password',
        'acfe_hidden', // ?

        /*** Content ***/
        'image',
        'file',
        'wysiwyg',
        'oembed',
        'gallery',
        'acfe_code_editor', // ?

        /*** Choice ***/
        'select',
        'checkbox',
        'radio',
        // 'button_group',
        'true_false',
        
        /*** Relation ***/
        // 'link',
        // 'post_object',
        // 'page_link',
        // 'relationship',
        // 'taxonomy',
        // 'user',
        
        /*** jQuery ***/
        // 'google_map',
        'date_picker',
        'date_time_picker',
        'time_picker',
        'color_picker',

        /*** Disposition (don't contains any data) ***/
        // 'message',
        // 'accordion',
        // 'tab',
        // 'group',
        // 'repeater',
        // 'flexible_content',
        // 'clone',
    ];

    /**
     * Get all ACF fields of a post
     */
    public function get_acf_fields_of_post($post_id) {
        $groups = acf_get_field_groups(['post_id' => $post_id]);
        $fields = [];

        foreach ($groups as $group) {
            
            if ($group['key'] !== "group_acfe_author") {
                $fields_group = acf_get_fields($group['key']);
                $all_fields_group = $this->get_all_acf_fields_of_group($fields_group);
                $fields = array_merge($fields, $all_fields_group);
            }
        }
        
        return $fields;
    }


    /**
     * Get fields and sub fields (and sub sub fields, etc) of an ACF group
     */
    public function get_all_acf_fields_of_group($fields_group, $parent_name = '') {
        $fields = [];

        foreach ($fields_group as $f) {
            $key = $parent_name == '' ? $f['name'] : "{$parent_name}_{$f['name']}";
            
            if (isset($f['sub_fields']) && $f['sub_fields']) {
                $sub_fields = $this->get_all_acf_fields_of_group($f['sub_fields'], $key);
                $fields = array_merge($fields, $sub_fields);
            } else {
                $fields[$key] = $f;
            }
        }

        return $fields;
    }


    /**
     * Return data compatible with ACF
     */
    public function get_postmetavalue_compatible_with_acf($value, $acf_type, $field_id) {
        
        $postmetavalue_compatible_with_acf = null;

        // if is not supported, return
        if (!in_array($acf_type, $this->acf_fields_supported)) return;

        switch ($acf_type)
        {
            case 'acfe_slug':
                $postmetavalue_compatible_with_acf = sanitize_title($value);
                break;

            case 'image':
            case 'file':

                if (!isset($_FILES[$field_id]['name']) || !isset($_FILES[$field_id]['tmp_name'])) break;
                if ($_FILES[$field_id]['name'] == '' || $_FILES[$field_id]['tmp_name'] == '') break;

                $postmetavalue_compatible_with_acf = $this->save_attachment($_FILES[$field_id]['name'], $_FILES[$field_id]['tmp_name']);
                break;

            case 'gallery':
                $attachment_ids = [];

                if (!isset($_FILES[$field_id]) || !isset($_FILES[$field_id])) break;

                if ($_FILES[$field_id]['name'] !== []) {
                    $number_of_medias = count($_FILES[$field_id]['name']);

                    for ($i = 0; $i < $number_of_medias; $i++) {
                        
                        if ($_FILES[$field_id]['name'][$i] !== '' && $_FILES[$field_id]['tmp_name'][$i] !== '') {
                            $id = $this->save_attachment($_FILES[$field_id]['name'][$i], $_FILES[$field_id]['tmp_name'][$i]);
                            if (is_int($id)) $attachment_ids[] = strval($id);
                        }
                    }
                }

                $postmetavalue_compatible_with_acf = $attachment_ids;
                break;

            case 'checkbox':
                $number_of_choices = $value;
                $choices = [];
                
                for ($i = 0; $i < $number_of_choices; $i++) {
                    
                    if (isset($_POST[$field_id . '-' . $i])) {
                        $choices[] = $_POST[$field_id . '-' . $i];
                    }
                }

                $postmetavalue_compatible_with_acf = $choices;
                break;

            case 'true_false':
                $number_of_choices = $value;
                $checkbox_value = '';

                if ($number_of_choices == '1') $checkbox_value = $_POST[$field_id . '-' . 0];

                $postmetavalue_compatible_with_acf = $checkbox_value == '' ? 0 : 1;
                break;
            
            default:
                $postmetavalue_compatible_with_acf = $value;
                break;
        }

        return $postmetavalue_compatible_with_acf;
    }
}