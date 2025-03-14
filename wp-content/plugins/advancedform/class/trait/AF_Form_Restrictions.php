<?php

trait AF_Form_Restrictions
{
    /**
     * Verify if the constraints are met
     */
    protected function verify_restrictions()
    {
        $errors = null;

        foreach ($this->data['fields'] as $field_id => $field_data) {
            $value = isset( $_POST[$field_id] ) ? $_POST[$field_id] : null;

            // if the field is required but empty
            if ($this->field_is_missing($field_data)) {
                $errors[$field_id]['required']['restriction'] = 'required';
                $errors[$field_id]['required']['value'] = isset($_POST[$field_id]) ? $_POST[$field_id] : '';

                continue;
            }

            // don't verify empty field
            if ($this->field_is_empty($field_data)) continue;

            // verify the constraints
            switch ($field_data['type'])
            {
                case 'title':
                case 'text':
                case 'textarea':
                    $length_min = $field_data['length_min'];
                    $length_max = $field_data['length_max'];

                    if ($length_min !== '' && strlen($value) < $length_min) {
                        $errors[$field_id]['length_min']['restriction'] = $length_min;
                        $errors[$field_id]['length_min']['value'] = strlen($value);
                    }
                    
                    if ($length_max !== '' && strlen($value) > $length_max) {
                        $errors[$field_id]['length_max']['restriction'] = $length_max;
                        $errors[$field_id]['length_max']['value'] = strlen($value);
                    }
                    break;

                case 'number':
                    $number_min = $field_data['number_min'];
                    $number_max = $field_data['number_max'];

                    if ($number_min !== '' && $value < $number_min) {
                        $errors[$field_id]['number_min']['restriction'] = $number_min;
                        $errors[$field_id]['number_min']['value'] = $value;
                    }
                    
                    if ($number_max !== '' && $value > $number_max) {
                        $errors[$field_id]['number_max']['restriction'] = $number_max;
                        $errors[$field_id]['number_max']['value'] = $value;
                    }
                    break;

                case 'date':
                    $value = new DateTime($value);
                    $date_min = $field_data['date_min'] ? new DateTime($field_data['date_min']) : '';
                    $date_max = $field_data['date_max'] ? new DateTime($field_data['date_max']) : '';

                    if ($date_min !== '' && $value < $date_min) {
                        $errors[$field_id]['date_min']['restriction'] = $date_min->format('Y-m-d');
                        $errors[$field_id]['date_min']['value'] = $value->format('Y-m-d');
                    }
                    
                    if ($date_max !== '' && $value > $date_max) {
                        $errors[$field_id]['date_max']['restriction'] = $date_max->format('Y-m-d');
                        $errors[$field_id]['date_max']['value'] = $value->format('Y-m-d');
                    }
                    break;

                case 'datetime':
                    $value = new DateTime($value);
                    $datetime_min = $field_data['datetime_min'] ? new DateTime($field_data['datetime_min']) : '';
                    $datetime_max = $field_data['datetime_max'] ? new DateTime($field_data['datetime_max']) : '';

                    if ($datetime_min !== '' && $value < $datetime_min) {
                        $errors[$field_id]['datetime_min']['restriction'] = $datetime_min->format('Y-m-d H:i');
                        $errors[$field_id]['datetime_min']['value'] = $value->format('Y-m-d H:i');
                    }
                    
                    if ($datetime_max !== '' && $value > $datetime_max) {
                        $errors[$field_id]['datetime_max']['restriction'] = $datetime_max->format('Y-m-d H:i');
                        $errors[$field_id]['datetime_max']['value'] = $value->format('Y-m-d H:i');
                    }
                    break;

                case 'time':
                    $value = new DateTime($value);
                    $time_min = $field_data['time_min'] ? new DateTime($field_data['time_min']) : '';
                    $time_max = $field_data['time_max'] ? new DateTime($field_data['time_max']) : '';

                    if ($time_min !== '' && $value < $time_min) {
                        $errors[$field_id]['time_min']['restriction'] = $time_min->format('H:i');
                        $errors[$field_id]['time_min']['value'] = $value->format('H:i');
                    }
                    
                    if ($time_max !== '' && $value > $time_max) {
                        $errors[$field_id]['time_max']['restriction'] = $time_max->format('H:i');
                        $errors[$field_id]['time_max']['value'] = $value->format('H:i');
                    }
                    break;

                case 'password':
                    $regex = $field_data['regex'];
                    
                    if ($regex) {
                        if (substr($regex, 0, 1) !== '/') $regex = '/' . $regex;
                        if (substr($regex, -1) !== '/') $regex = $regex . '/';
    
                        if ($regex !== '' && !preg_match('/'.$regex.'/', $_POST[$field_id])) {
                            $errors[$field_id]['regex']['restriction'] = $regex;
                            $errors[$field_id]['regex']['value'] = $_POST[$field_id];
                        }
                    }

                    break;
                
                case 'thumbnail':
                case 'files':
                    $size_min = $field_data['size_min'] == '' ? '' : floatval($field_data['size_min']);
                    $size_max = $field_data['size_max'] == '' ? '' : floatval($field_data['size_max']);
                    $files_multiple_is_allowed = $field_data['files_multiple'];

                    $extensions_allowed = $field_data['file_extensions_allowed'];
                    $extensions_allowed = str_replace(' ', '', $extensions_allowed);
                    $extensions_allowed = $extensions_allowed !== '' ? explode('\\n', $extensions_allowed) : '';

                    $extensions_disallowed = $field_data['file_extensions_disallowed'];
                    $extensions_disallowed = str_replace(' ', '', $extensions_disallowed);
                    $extensions_disallowed = $extensions_disallowed !== '' ? explode('\\n', $extensions_disallowed) : '';

                    $args = [
                        'size_min' => $size_min,
                        'size_max' => $size_max,
                        'file_extensions_allowed' => $extensions_allowed,
                        'file_extensions_disallowed' => $extensions_disallowed,
                        'field_id' => $field_id
                    ];

                    // many files ?
                    if (is_array($_FILES[$field_id]['name'])) {

                        // if files multiple is not allowed, return an error
                        if (!$files_multiple_is_allowed) {
                            $errors[$field_id]['files_multiple']['value'] = count($_FILES[$field_id]['name']);
                            break;
                        }

                        for ($i = 0; $i < count($_FILES[$field_id]['name']); $i++) {
                            $size = $_FILES[$field_id]['size'][$i] / 1000000;
                            $name = $_FILES[$field_id]['name'][$i];

                            $errors = $this->verify_file($size, $name, $args, $errors);
                        }
                    } else {
                        $size = $_FILES[$field_id]['size'] / 1000000;
                        $name = $_FILES[$field_id]['name'];
                        
                        $errors = $this->verify_file($size, $name, $args, $errors);
                    }
                    break;
            }
        }

        $this->errors = $errors;
        
        if ($errors !== null) $this->generate_texts_errors();
    }


    /**
     * 
     */
    public function verify_file($size, $name, $args, $errors) {

        if ($args['size_min'] !== '' && $size < $args['size_min']) {
            $errors[$args['field_id']]['size_min']['restriction'] = $args['size_min'];
            $errors[$args['field_id']]['size_min']['value'] = $size;
        }
        
        if ($args['size_max'] !== '' && $size > $args['size_max']) {
            $errors[$args['field_id']]['size_max']['restriction'] = $args['size_max'];
            $errors[$args['field_id']]['size_max']['value'] = $size;
        }

        if ($args['file_extensions_allowed'] !== '') {
            $file_extension = explode('.', $name);
            $file_extension = $file_extension[count($file_extension) - 1];

            if (!in_array($file_extension, $args['file_extensions_allowed'])) {
                $errors[$args['field_id']]['file_extensions_allowed']['restriction'] = $args['file_extensions_allowed'];
                $errors[$args['field_id']]['file_extensions_allowed']['value'][] = $file_extension;
            }
        }
        
        if ($args['file_extensions_disallowed'] !== '') {
            $file_extension = explode('.', $name);
            $file_extension = $file_extension[count($file_extension) - 1];

            if (in_array($file_extension, $args['file_extensions_disallowed'])) {
                $errors[$args['field_id']]['file_extensions_disallowed']['restriction'] = $args['file_extensions_disallowed'];
                $errors[$args['field_id']]['file_extensions_disallowed']['value'][] = $file_extension;
            }
        }

        return $errors;
    }


    /**
     * 
     */
    public function field_is_missing($field_data)
    {
        if (!$field_data['required']) return false;

        return $this->field_is_empty($field_data);
    }

    /**
     * 
     */
    public function field_is_empty($field_data)
    {
        $field_id = $field_data['id'];
        $is_empty = false;

        switch ($field_data['type'])
            {
                case 'thumbnail':
                case 'files':
                    $is_empty = ($_FILES[$field_id]['name'] == '' || $_FILES[$field_id]['name'][0] == '') ? true : false;
                    break;

                case 'choice': // user can choose an empty choice
                case 'hidden': // user can't access it
                    break;

                default:
                    $is_empty = (!isset($_POST[$field_id]) || $_POST[$field_id] == '') ? true : false;
            }
        
        return $is_empty;
    }


    /**
     * 
     */
    public function generate_texts_errors($language = 'fr')
    {
        $file_path = plugin_dir_path(__FILE__) . "/../../translations/errors_{$language}.json";
        
        if (!file_exists($file_path)) return; // if file doesn't exists
        
        $errors_translations = json_decode(file_get_contents($file_path), true);
        
        foreach ($this->errors as $field_id => $field_errors) { // fields
            foreach ($field_errors as $error_name => $error_data) { // errors

                // if text for the error exists
                if ($errors_translations[$error_name]) {
                    $type = $errors_translations[$error_name]['type']; // dynamic or static text
                    $text = $errors_translations[$error_name]['text']; // string

                    switch ($type)
                    {
                        case 'dynamic':
                            $restriction = $error_data['restriction'];

                            // if there can be several restrictions like file extensions
                            if (is_array($restriction)) {
                                $restrictions = '';

                                foreach ($restriction as $index => $r) $restrictions .= $index < count($restriction) - 1 ? $r . ', ' : $r;

                                $text = str_replace('%a%', $restrictions, $text);
                            } else {
                                $text = str_replace('%a%', $restriction, $text);
                            }

                            $this->errors[$field_id][$error_name]['text'] = $text;
                            break;

                        case 'static':
                            $this->errors[$field_id][$error_name]['text'] = $text;
                            break;
                    }
                }
            }
        }
    }
}
