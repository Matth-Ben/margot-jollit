<?php

trait AF_Form_Ajax
{
    /**
     * Get ACF fields for a post type
     */
    public function ajax_get_acf_fields($post_type) {

        if (!function_exists('acf_get_field_groups')) return;

        $fields = [];
        $not_supported = [];

        // add title
        $uniqid = uniqid();
        $fields[$uniqid] = [
            'id' => $uniqid,
            'label' => 'Titre',
            'type' => 'title'
        ];

        // add thumbnail
        if (post_type_supports($post_type, 'thumbnail')) {
            $uniqid = uniqid();
            $fields[$uniqid] = [
                'id' => $uniqid,
                'label' => 'Image à la une',
                'type' => 'thumbnail',
                'file_extensions_disallowed' => 'svg'
            ];
        }
        
        // add thumbnail
        if (post_type_supports($post_type, 'editor')) {
            $uniqid = uniqid();
            $fields[$uniqid] = [
                'id' => $uniqid,
                'label' => 'Contenu',
                'type' => 'content'
            ];
        }

        // get existing post
        $query = new WP_Query(['post_type' => $post_type, 'post_per_page' => 1, 'post_status' => ['publish', 'trash']]);

        // if there don't have any post type, create one
        if (count($query->posts) > 0) {
            $post = $query->posts[0];
            $post_id = $post->ID;
        } else {
            $post_id = wp_insert_post([
                'post_type' => $post_type,
                'title' => 'af-get-acf-data',
                'post_status' => 'trash'
            ]);
        }

        $acf_fields = $this->get_acf_fields_of_post($post_id);

        foreach ($acf_fields as $meta_key => $field) {
            $data = [];

            if (!in_array($field['type'], $this->acf_fields_supported)) {
                $not_supported[] = $field['type'];
            } else {
                $data = null;
                $uniqid = uniqid();
                
                switch ($field['type'])
                {
                    case 'text':
                    case 'oembed':
                        $data = [
                            'type'                  => 'text',
                            'placeholder'           => $field['placeholder'],
                            'show_default_text'     => $field['default_value'],
                            'length_max'            => $field['max_length'],
                            'postmetakey'           => $meta_key,
                        ];
                        break;
    
                    case 'textarea':
                    case 'wysiwyg':
                    case 'acfe_code_editor':
                        $data = [
                            'type'                  => 'textarea',
                            'placeholder'           => $field['placeholder'],
                            'show_default_textarea' => $field['default_value'],
                            'postmetakey'           => $meta_key,
                        ];
                        break;
                    
                    case 'number':
                        $data = [
                            'type'                  => 'number',
                            'placeholder'           => $field['placeholder'],
                            'show_default_number'   => $field['default_value'],
                            'postmetakey'           => $meta_key,
                        ];
                        break;
    
                    case 'email':
                        $data = [
                            'type'                  => 'email',
                            'placeholder'           => $field['placeholder'],
                            'show_default_email'    => $field['default_value'],
                            'postmetakey'           => $meta_key,
                        ];
                        break;
                    
                    case 'image':
                        $file_extensions_allowed = str_replace(' ', '', $field['mime_types']);
                        $file_extensions_allowed = str_replace(',', '\\n', $file_extensions_allowed);
    
                        $data = [
                            'type'                      => $field['acfe_thumbnail'] == 0 ? 'files' : 'thumbnail',
                            'postmetakey'               => $meta_key,
                            'file_extensions_allowed'   => $file_extensions_allowed ? $file_extensions_allowed : 'jpg\\npng'
                        ];
                        break;
    
                    case 'file':
                        $file_extensions_allowed = str_replace(' ', '', $field['mime_types']);
                        $file_extensions_allowed = str_replace(',', '\\n', $file_extensions_allowed);
    
                        $data = [
                            'type'                      => 'files',
                            'postmetakey'               => $meta_key,
                            'file_extensions_allowed'   => $file_extensions_allowed
                        ];
                        break;
    
                    case 'gallery':
                        $file_extensions_allowed = str_replace(' ', '', $field['mime_types']);
                        $file_extensions_allowed = str_replace(',', '\\n', $file_extensions_allowed);
    
                        $data = [
                            'type'                      => 'files',
                            'postmetakey'               => $meta_key,
                            'file_extensions_allowed'   => $file_extensions_allowed ? $file_extensions_allowed : 'jpg\\npng',
                            'files_multiple'            => true
                        ];
                        break;
    
                    case 'select':
                        $choices = '';
                        $index = 0;
                        
                        foreach ($field['choices'] as $c) {
                            $choices .= $index + 1 !== count($field['choices']) ? $c . '\\n' : $c;
                            $index++;
                        }
    
                        $data = [
                            'type'                  => 'choice',
                            'show_default_choice'   => $field['default_value'] ? $field['default_value'] : '',
                            'postmetakey'           => $meta_key,
                            'choice_logic'          => 'select',
                            'choices'               => $choices
                        ];
                        break;
                    
                    case 'radio':
                        $choices = '';
                        $index = 0;
                        
                        foreach ($field['choices'] as $c) {
                            $choices .= $index + 1 !== count($field['choices']) ? $c . '\\n' : $c;
                            $index++;
                        }
    
                        $data = [
                            'type'                  => 'choice',
                            'show_default_choice'   => $field['default_value'],
                            'postmetakey'           => $meta_key,
                            'choice_logic'          => 'radio',
                            'choices'               => $choices
                        ];
                        break;
    
                    case 'checkbox':
                        $choices = '';
                        $default_choices = '';
                        
                        $index = 0;
                        foreach ($field['choices'] as $c) {
                            $choices .= $index + 1 !== count($field['choices']) ? $c . '\n' : $c;
                            $index++;
                        }
                        
                        $index = 0;
                        foreach ($field['default_value'] as $c) {
                            $default_choices .= $index + 1 !== count($field['choices']) ? $c . '\n' : $c;
                            $index++;
                        }
    
                        $data = [
                            'type'              => 'choice',
                            'show_default_choice'    => $default_choices,
                            'postmetakey'      => $meta_key,
                            'choice_logic'      => 'checkbox',
                            'choices'           => $choices
                        ];
                        break;
                    
                    case 'true_false':
                        $data = [
                            'type'              => 'choice',
                            'show_default_choice'    => $field['default_value'] == 1 ? 'Vrai' : '',
                            'postmetakey'      => $meta_key,
                            'choice_logic'      => 'checkbox',
                            'choices'           => 'Vrai'
                        ];
                        break;
    
                    case 'date_picker':
                        $data = [
                            'type'              => 'date',
                            'postmetakey'      => $meta_key,
                        ];
                        break;
                    
                    case 'date_time_picker':
                        $data = [
                            'type'              => 'datetime',
                            'postmetakey'      => $meta_key,
                        ];
                        break;
    
                    case 'time_picker':
                        $data = [
                            'type'              => 'time',
                            'postmetakey'      => $meta_key,
                        ];
                        break;
    
                    case 'color_picker':
                        $data = [
                            'type'              => 'color',
                            'postmetakey'      => $meta_key,
                            'show_default_color'     => $field['default_value']
                        ];
                        break;
                    
                    case 'password':
                        $data = [
                            'type'              => 'password',
                            'postmetakey'      => $meta_key
                        ];
                        break;
                }

                if ($data) {
                    $data['id'] = $uniqid;
                    $data['label'] = $field['label'];
                    $data['required'] = $field['required'];
                    $fields[$uniqid] = $data;
                }
            }
        }
        
        return ['fields' => $fields, 'not_supported' => $not_supported];
    }
}
