<?php

trait AF_Form_FrontForm
{
    /**
     * Generate form fields html or form html
     */
    public function generate_form_html($create_form = true)
    {
        // css
        add_action( 'wp_enqueue_scripts', function() {
            wp_enqueue_style('advancedform--form', plugins_url('/advancedform/assets/css/public/form.css'));
        } );

        // sring for message text and status
        $message_status = $this->message ? $this->message['status'] : '';
        $message_text = $this->message ? $this->message['text'] : '';

        // generate fields html
        $this->data['fields_html'] = [];

        foreach ($this->data['fields'] as $field_id => $field) {
            $this->data['fields_html'][$field_id] = $this->generate_field_html($field);
        }

        // reCAPTCHA
        if ( isset ( $this->data['recaptcha_version'] ) && isset ( $this->data['recaptcha_language'] ) ) {
            $this->add_recaptcha_to_form_front($this->data['recaptcha_version'], $this->data['recaptcha_language']);
        }

        // return data ////
        if (!$create_form) {
            add_action('wp_footer', function () { echo '<style>.af-country-data{display:none;}</style>'; });

            return [
                'data' => $this->data,
                'error' => $this->errors,
                'required' => array(
                    'html' => wp_nonce_field('advancedform', 'af_nonce', true, false) . '<input class="af-country-data" type="hidden" name="country" />' . '<input type="hidden" name="af_form_id" value="' . $this->data['id'] . '" />',
                    'details' => array(
                        'nonce' => wp_nonce_field('advancedform', 'af_nonce', true, false),
                        "honeypot" => '<input class="af-country-data" type="hidden" name="country" />',
                        'form_id' => '<input type="hidden" name="af_form_id" value="' . $this->data['id'] . '" />'
                    )
                )
            ];
        }
        
        // create form html

        // container
        $form_html = "<div class='container-advancedform {$message_status}'>";

        // message text
        $form_html .= $message_text !== '' ? "<p class='message'>{$message_text}</p>" : '';

        // form
        $form_html .= '<form class="advancedform" advancedform="' . $this->data['id'] . '" method="post" enctype="multipart/form-data">';

        foreach ($this->data['fields_html'] as $field_id => $field_html) {
            $field = $this->data['fields'][$field_id];

            // reCAPTCHA v2
            if ($this->data['fields'][$field_id]['type'] == 'recaptcha_v2') {
                $form_html .= '<div class="advancedform-field-container ' . esc_attr($field['class']) . '" field-id="' . $field_id . '">';
                $form_html .= $field_html['label'];
                $form_html .= $field_html['row'];
                $form_html .= '</br>';
                $form_html .= '</div>';

                continue;
            }

            // reCPATCHA v3
            if ($field_id == 'recaptcha_v3') {
                $form_html .= $field_html;
                
                continue;
            }

            // hidden field
            if ($this->data['fields'][$field_id]['type'] == 'hidden') {
                $form_html .= '<div class="advancedform-field-container ' . esc_attr($field['class']) . ' ';
                $form_html .= $field['required'] ? 'required' : '';
                $form_html .= '" field-id="' . $field_id . '">';
                $form_html .=       '<span>' . $field_html['row'] . '</span>';
                $form_html .= '</div>';

                continue;
            }

            // other fields
            $form_html .= '<div class="advancedform-field-container ' . esc_attr($field['class']) . ' ' . esc_attr($field['type']) . ' ';
            $form_html .= $field['required'] ? 'required' : '';
            $form_html .= '" field-id="' . $field_id . '">';
            $form_html .=   '<p>';
            $form_html .=       $field_html['label'];
            $form_html .=       '</br>';
            $form_html .=       $field_html['error'] ? '<span>' . $field_html['error'] . '</span></br>' : '';
            $form_html .=       '<span>' . $field_html['row'] . '</span>';
            $form_html .=   '</p>';
            $form_html .= '</div>';
        }
        
        // honeypot 🍯 
        add_action('wp_footer', function () { echo '<style>.af-country-data{display:none;}</style>'; });
        $form_html .= '<input class="af-country-data" type="hidden" name="country" />';
        
        // nonce field (token)
        $form_html .= wp_nonce_field('advancedform', 'af_nonce', true, false);

        // send the af_form id
        $form_html .= '<input type="hidden" name="af_form_id" value="' . $this->data['id'] . '" />';

        $form_html .= $this->data['information_message'] ? '<p class="information-message">' . $this->data['information_message'] . '</p>' : '';

        $form_html .= '<p><button type="submit"><span>Envoyer</span></button></p>';
        $form_html .= '</form>';
        $form_html .= '</div>';

        return $form_html;
    }

    
    /**
     * Generate field HTML
     */
    public function generate_field_html($field_data, $default_value = null)
    {
        $field_id  = $field_data['id'];

        // get errors
        $errors = $this->errors;

        // if there is an error, return the submitted value, otherwise the default value
        if ($errors) {
            $value = isset($_POST[$field_id]) ? $_POST[$field_id] : ''; // the submitted value
            $error_html = '';

            if (isset($errors[$field_id]) && $errors[$field_id]) {
                
                foreach ($errors[$field_id] as $error) {
                    $error_html .= '<span class="error">' . $error['text'] . '</span>';
                }
            }
        } else {
            
            if ( !in_array( $field_data['type'], array( 'files', 'thumbnail', 'recaptcha_v2', 'recaptcha_v3', 'password', 'hidden', 'checkbox' ) ) ) {
                $default_value = $default_value !== null ? $default_value : $field_data['show_default_' . $field_data['type']];
            }
            $value = $default_value ? $default_value : ''; // if there's a default value we take it
            $error_html = '';
        }
        
        // create common HTML
        $label_html = "<label for='field-{$field_id}'>{$field_data['label']}</label>";
        $value_html = 'value="' . esc_attr($value) . '"';
        $class_attr = esc_attr($field_data['class']);
        $required_html = $field_data['required'] ? 'required' : '';
        $placeholder_html = 'placeholder="' . esc_attr($field_data['placeholder']) . '"';

        switch ($field_data['type'])
        {
            case 'title':
            case 'text':
                $length_min_html = $field_data['length_min'] !== '' ? "minlength='{$field_data['length_min']}'" : '';
                $length_max_html = $field_data['length_max'] !== '' ? "maxlength='{$field_data['length_max']}'" : '';
                $row_html = "<input id='field-{$field_id}' class='advancedform-field-data {$class_attr}' type='text' {$length_min_html} {$length_max_html} name='{$field_id}' {$value_html} {$placeholder_html} {$required_html} />";
                break;

            case 'textarea':
                $length_min_html = $field_data['length_min'] !== '' ? "minlength='{$field_data['length_min']}'" : '';
                $length_max_html = $field_data['length_max'] !== '' ? "maxlength='{$field_data['length_max']}'" : '';
                $row_html = "<textarea id='field-{$field_id}' class='advancedform-field-data {$class_attr}' {$length_min_html} {$length_max_html} name='{$field_id}' {$placeholder_html} {$required_html}>{$value}</textarea>";
                break;

            case 'content':
                $length_min_html = $field_data['length_min'] !== '' ? "minlength='{$field_data['length_min']}'" : '';
                $length_max_html = $field_data['length_max'] !== '' ? "maxlength='{$field_data['length_max']}'" : '';
                $row_html = "<textarea id='field-{$field_id}' class='advancedform-field-data {$class_attr}' {$length_min_html} {$length_max_html} name='{$field_id}' {$placeholder_html} {$required_html}>{$field_data['show_default_content']}</textarea>";
                break;

            case 'files':
            case 'thumbnail':
                $extensions_allowed = str_replace(' ', '', $field_data['file_extensions_allowed']);
                $extensions_allowed = $extensions_allowed !== '' ? explode('\\n', $extensions_allowed) : [];
                $extensions_allowed_html = '';

                foreach ($extensions_allowed as $extension_allowed) $extensions_allowed_html .= '.' . $extension_allowed . ',';
                $extensions_allowed_html = 'accept="' . $extensions_allowed_html . '"';

                $multiple_html = $field_data['files_multiple'] && $field_data['type'] !== 'thumbnail' ? 'multiple' : '';
                $name_html = $field_data['files_multiple'] && $field_data['type'] !== 'thumbnail' ? $field_id . '[]' : $field_id; // add "[]" at the end of the name for multiple files
                $row_html = "<input id='field-{$field_id}' class='advancedform-field-data {$class_attr}' type='file' {$extensions_allowed_html} name='{$name_html}' {$required_html} {$multiple_html} />";
                break;

            case 'email':
                $row_html = "<input id='field-{$field_id}' class='advancedform-field-data {$class_attr}' type='email' name='{$field_id}' {$value_html} placeholder='{$field_data['placeholder']}' {$required_html} />";
                break;
            
            case 'number':
                $number_min_html = $field_data['number_min'] !== '' ? "min='{$field_data['number_min']}'" : '';
                $number_max_html = $field_data['number_max'] !== '' ? "max='{$field_data['number_max']}'" : '';
                $row_html = "<input id='field-{$field_id}' class='advancedform-field-data {$class_attr}' type='number' {$number_min_html} {$number_max_html} name='{$field_id}' {$value_html} placeholder='{$field_data['placeholder']}' {$required_html} />";
                break;

            case 'date':
                $date_min_html = $field_data['date_min'] !== '' ? "min='{$field_data['date_min']}'" : '';
                $date_max_html = $field_data['date_max'] !== '' ? "max='{$field_data['date_max']}'" : '';
                $row_html = "<input id='field-{$field_id}' class='advancedform-field-data {$class_attr}' type='date' {$date_min_html} {$date_max_html} name='{$field_id}' {$value_html} {$required_html} />";
                break;
            
            case 'datetime':
                $datetime_min_html = $field_data['datetime_min'] !== '' ? "min='{$field_data['datetime_min']}'" : '';
                $datetime_max_html = $field_data['datetime_max'] !== '' ? "max='{$field_data['datetime_max']}'" : '';
                $row_html = "<input id='field-{$field_id}' class='advancedform-field-data {$class_attr}' type='datetime-local' {$datetime_min_html} {$datetime_max_html} name='{$field_id}' {$value_html} {$required_html} />";
                break;
            
            case 'time':
                $time_min_html = $field_data['time_min'] !== '' ? "min='{$field_data['time_min']}'" : '';
                $time_max_html = $field_data['time_max'] !== '' ? "max='{$field_data['time_max']}'" : '';
                $row_html = "<input id='field-{$field_id}' class='advancedform-field-data {$class_attr}' type='time' {$time_min_html} {$time_max_html} name='{$field_id}' {$value_html} {$required_html} />";
                break;

            case 'color':
                $row_html = "<input id='field-{$field_id}' class='advancedform-field-data {$class_attr}' type='color' name='{$field_id}' {$value_html} {$required_html} />";
                break;

            case 'password':
                $regex_html = $field_data['regex'] !== '' ? "pattern='{$field_data['regex']}'" : '';
                $row_html = "<input id='field-{$field_id}' class='advancedform-field-data {$class_attr}' type='password' {$regex_html} name='{$field_id}' {$value_html} {$placeholder_html} {$required_html} />";
                break;

            case 'hidden':
                $label_html = '';
                $row_html = "<input id='field-{$field_id}' class='advancedform-field-data {$class_attr}' type='hidden' name='{$field_id}' {$value_html} />";
                break;
            
            case 'choice':
                $logic = $field_data['choice_logic'];
                $row_html = '';

                switch ($logic)
                {
                    case 'select':
                        $row_html .= "<select id='field-{$field_id}' class='advancedform-field-data {$class_attr}' name='{$field_id}'>";

                        foreach (explode('\n', $field_data['choices']) as $choice) {
                            $value_html = $value == $choice ? "selected" : "";
                            $explode_choice = explode('|', $choice);
                            $choice_attr = esc_attr($choice);

                            if (count($explode_choice) == 2) {
                                // $choice_attr = esc_attr($explode_choice[0]);
                                $choice = $explode_choice[1];
                            }

                            $row_html .= "<option value='{$choice_attr}' {$value_html}>{$choice}</option>";
                        }

                        $row_html .= "</select>";
                        break;

                    case 'checkbox':

                        if ($errors) {
                            $checkbox_number = $_POST[$field_id];
                            $value = '';
                            
                            for ($i = 0; $i < $checkbox_number; $i++) {
                                $value .= $_POST[$field_id . '-' . $i] ? $_POST[$field_id . '-' . $i] . '\n' : '';
                            }
                        }

                        $number_of_choices = 0;
                        $selected_choices = explode('\n', $value);
                        
                        foreach (explode('\n', $field_data['choices']) as $k => $choice) {
                            $default_value_html = in_array($choice, $selected_choices) ? "checked" : "";
                            $choice_attr = esc_attr($choice);
                            $number_of_choices++;

                            $row_html .= "<div>";
                            $row_html .= "<input id='field-{$field_id}-{$k}' class='advancedform-field-data {$class_attr}' type='checkbox' name='{$field_id}-{$k}' value='{$choice_attr}' {$default_value_html} />";
                            $row_html .= "<label for='field-{$field_id}-{$k}'>{$choice}</label>";
                            $row_html .= "</div>";
                        }

                        $row_html .= "<input type='hidden' name='{$field_id}' value='{$number_of_choices}' />"; // checkbox number
                        break;

                    case 'radio':
                        
                        foreach (explode('\n', $field_data['choices']) as $k => $choice) {
                            $value_html = $value == $choice ? "checked" : "";

                            $row_html .= "<div>";
                            $row_html .= "<input id='field-{$field_id}-{$k}' class='advancedform-field-data {$class_attr}' type='radio' name='{$field_id}' value='{$choice}' {$value_html} />";
                            $row_html .= "<label for='field-{$field_id}-{$k}'>{$choice}</label>";
                            $row_html .= "</div>";
                        }
                        break;
                }
                break;

            case 'checkbox':
                $id = uniqid();
                $row_html = "<div><p><input id='$id' type='checkbox' $required_html /><label for='$id'>" . $field_data['checkbox_message'] . "</label></p></div>";

            case 'recaptcha_v2':

                if ($this->get_recaptcha(2)) {
                    $row_html = '<div id="g-recaptcha" class="g-recaptcha" data-sitekey="' . $this->get_recaptcha(2)['public_key'] . '"></div>';
                }
                break;
        };

        if ( $field_data['display_label'] === false ) {
            $label_html = '';
        }

        return [
            'label' => $label_html,
            'row'   => $row_html,
            'error' => $error_html
        ];
    }
}