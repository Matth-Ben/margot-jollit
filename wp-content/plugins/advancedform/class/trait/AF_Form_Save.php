<?php

trait AF_Form_Save
{
    /**
     * Save submitted form data
     */
    public function save_submitted_form()
    {
        $post_type = $this->data['post_type'];

        if ($post_type) {
            $this->create_new_post();
        } else {
            $this->create_new_entry();
        }

        // add success message
        $this->message = [
            'text' => $this->data['success_message'],
            'status' => 'success'
        ];
    }

    /**
     * Create new entry
     */
    public function create_new_entry( $id = null )
    {
        // title
        $field_id_for_entry_title = $this->data['entry_title'];
        $field_for_entry_title = $field_id_for_entry_title && isset($this->data['fields'][$field_id_for_entry_title]) && $this->data['fields'][$field_id_for_entry_title] ? $this->data['fields'][$field_id_for_entry_title] : '';
        $title = $field_id_for_entry_title && isset($_POST[$field_id_for_entry_title]) && $_POST[$field_id_for_entry_title] ? $_POST[$field_id_for_entry_title] : ''; // use data submitted if it exists
        $title = $title === '' && $field_for_entry_title !== '' && isset($field_for_entry_title['submit_default_' . $field_for_entry_title['type']]) ? $field_for_entry_title['submit_default_' . $field_for_entry_title['type']] : $title; // else, use the "submit default value" of the field if it exists

        $postmetas = ['af_form' => $this->data['id']];

        foreach ($this->data['fields'] as $field_id => $field) {

            // save field type, label field and the value
            $postmetas['type_' . $field_id] = $field['type'];
            $postmetas['label_' . $field_id] = $field['label'];
            $postmetas['original_field_' . $field_id] = str_replace('\\n', '\\\\n', serialize($field));
            
            // if value is in $_POST
            if ($field['type'] !== 'files') {
                $postmetas['value_' . $field_id] = $_POST[$field_id];
    
                // use "submit_default_value" if value is empty
                if ($_POST[$field_id] == '' && isset($field['submit_default_' . $field['type']])) {
                    $postmetas['value_' . $field_id] = $field['submit_default_' . $field['type']];
                }
            }
            
            // format data for file(s)
            if ($field['type'] == 'files') {
                $pictures = null;
                $value = [];

                // save file(s) on disk
                // if there are no files, we use default files
                if ($_FILES[$field_id]['name'] == '' || $_FILES[$field_id]['name'][0] == '') {
                    $default_files = json_decode($this->data['fields'][$field_id]['submit_default_files'], true);

                    if ($default_files) {
                        foreach ($default_files as $attachment_id => $item) $pictures[] = $attachment_id;
                    }
                } else {
                    $acf_type = $field['files_multiple'] ? 'gallery' : 'file';
                    $pictures = $this->get_postmetavalue_compatible_with_acf($_POST[$field_id], $acf_type, $field_id);
                    $pictures = is_array($pictures) ? $pictures : [$pictures]; // must be an array
                }

                // save data in db
                // if there are file(s) we save it
                if ($pictures) {

                    foreach ($pictures as $attachment_id) {
                        $attachment_src = wp_get_attachment_image_src($attachment_id, 'thumbnail', true);
                        $url = wp_get_attachment_url($attachment_id);
                        $filename = explode('/', $url);
                        $filename = $filename[count($filename) - 1];

                        $value[$attachment_id] = [
                            'preview_url'   => $attachment_src[0],
                            'url'           => $url,
                            'is_image'      => $attachment_src[3],
                            'filename'      => $filename
                        ];
                    }
                }

                $postmetas['value_' . $field_id] = $value;
            }
            
            // format data for choice field (checkbox can be have many values)
            if ($field['type'] == 'choice' && $field['choice_logic'] == 'checkbox') {
                $choices = $this->get_postmetavalue_compatible_with_acf($_POST[$field_id], 'checkbox', $field_id);
                $value = '';

                foreach ($choices as $choice) $value .= $choice . '\\\\n';

                $postmetas['value_' . $field_id] = $value;
            }
        }

        $post_data = [
            'post_title'    => $title !== '' ? $title : 'advancedform',
            'post_type'     => 'af_entry',
            'post_status'   => 'publish',
            'meta_input'    => $postmetas
        ];

        if ($id !== null) {
            $post_data['ID'] = $id;
            return wp_update_post($post_data);
        }

        $id = wp_insert_post($post_data);

        wp_set_post_terms( $id, 'af-' . $postmetas['af_form'], 'the_form' );

        return $id;
    }


    /**
     * Create new post
     */
    public function create_new_post()
    {
        // title
        $field_id_for_entry_title = $this->data['entry_title'];
        $field_for_entry_title = $field_id_for_entry_title && isset($this->data['fields'][$field_id_for_entry_title]) && $this->data['fields'][$field_id_for_entry_title] ? $this->data['fields'][$field_id_for_entry_title] : '';
        $title = $field_id_for_entry_title && isset($_POST[$field_id_for_entry_title]) && $_POST[$field_id_for_entry_title] ? $_POST[$field_id_for_entry_title] : ''; // use data submitted if it exists
        $title = $title === '' && $field_for_entry_title !== '' && isset($field_for_entry_title['submit_default_' . $field_for_entry_title['type']]) ? $field_for_entry_title['submit_default_' . $field_for_entry_title['type']] : $title; // else, use the "submit default value" of the field if it exists

        $postmetas = [];
        $post_id = wp_insert_post([
            'post_title'    => $title,
            'post_type'     => $this->data['post_type'],
            'post_status'   => $this->data['status']
        ]);
        
        $post_data = [
            'ID'            => $post_id,
            'post_title'    => $title,
            'post_type'     => $this->data['post_type'],
            'post_status'   => $this->data['status']
        ];
        
        $acf_fields_of_post = function_exists('acf_get_field_groups') ? $this->get_acf_fields_of_post($post_id) : [];

        // post meta default
        foreach ($this->data['fields'] as $field_id => $field_data) {

            switch ($field_data['type'])
            {
                case 'title':
                    $default = $field_data['submit_default_title'] ? $field_data['submit_default_title'] : 'advancedform'; // default value
                    $post_data['post_title'] = $_POST[$field_id] !== '' ? $_POST[$field_id] : $default;
                    break;
                
                case 'content':
                    $default = $field_data['submit_default_content'] ? $field_data['submit_default_content'] : ''; // default value
                    $post_data['post_content'] = $_POST[$field_id] !== '' ? $_POST[$field_id] : $default;
                
                    break;

                case 'thumbnail':
                    // default thumbnail
                    if ($_FILES[$field_id]['name'] == '') {
                        $default_thumbnail = json_decode($this->data['fields'][$field_id]['submit_default_thumbnail'], true);
                        $value = '';

                        if ($default_thumbnail) {
                            foreach ($default_thumbnail as $attachment_id => $url_preview) $postmetas['_thumbnail_id'] = $attachment_id;
                        }

                        break;
                    }

                    $attachment_id = $this->save_attachment($_FILES[$field_id]['name'], $_FILES[$field_id]['tmp_name']);
                    $postmetas['_thumbnail_id'] = $attachment_id;
                    break;

                case 'files':

                    // default files
                    if ($_FILES[$field_id]['name'] == '' || $_FILES[$field_id]['name'][0] == '') {
                        $default_files = json_decode($this->data['fields'][$field_id]['default_files'], true);
                        $value = [];

                        if ($default_files) {
                            foreach ($default_files as $attachment_id => $url_preview) $value[] = $attachment_id;
                        }

                        $postmetas[$field_data['postmetakey']] = $value;

                        break;
                    }

                default:
                    // check if the metadata belongs to an acf field
                    $is_acf_field = isset($acf_fields_of_post[$field_data['postmetakey']]);

                    if ($is_acf_field == false) {
                        update_post_meta($post_id, $field_data['postmetakey'], isset($_POST[$field_id]) ? $_POST[$field_id] : '');
                    } else {
                        $value = isset($_POST[$field_id]) ? $_POST[$field_id] : '';
                        $acf_type = $acf_fields_of_post[$field_data['postmetakey']]['type'];
                        $acf_compatible_postmeta_value = $this->get_postmetavalue_compatible_with_acf($value, $acf_type, $field_id);
                        $postmetas[$field_data['postmetakey']] = $acf_compatible_postmeta_value;
                    }
            }
        }

        $post_data['meta_input'] = $postmetas;

        return wp_insert_post($post_data);
    }
}
