<?php

trait AF_Form_Data
{
    /**
     * Intercept submitted form data
     */
    public function intercept_data()
    {   
        // honeypot (spam)
        if ($_POST['country'] !== '') return;

        // CSRF
        if (!isset($_POST['af_nonce']) || !wp_verify_nonce($_POST['af_nonce'], 'advancedform')) return;

        if (!isset($_POST['af_form_nonces'])) {
            $_POST['af_form_nonces'] = array();
        }

        if (!in_array($_POST['af_nonce'], $_POST['af_form_nonces'])) {
            $_POST['af_form_nonces'][] = $_POST['af_nonce'];

            // filter for form submition
            $data_filtered = apply_filters('af_form_submition', $this->data);
            $this->data = $data_filtered ? $data_filtered : $this->data;
    
            return true;
        }

        return false;
    }

    /**
     * Get af_entry by $id
     */
    public function get_af_entry($af_entry_id)
    {
        $fields_data = [];
        $postmetas = get_post_meta($af_entry_id);

        // Get all data
        foreach($postmetas as $meta_key => $meta_value) {

            if (substr($meta_key, 0, 1) !== '_' && $meta_key !== 'af_form' && count(explode('original_field', $meta_key)) == 1) {
                $meta_key_explode = explode('_', $meta_key);
                $key = $meta_key_explode[0];
                $field_id = $meta_key_explode[1];
                
                $fields_data[$field_id][$key] = $meta_value[0];
            }
        }

        return $fields_data;
    }

    /**
     * Get af_form by $id
     */
    public function get_af_form($af_form_id)
    {
        $data_json = get_post_meta($af_form_id, 'af_form_data');

        if (!$data_json) return;

        $data_json = str_replace('\\n', '\\\\n', $data_json[0]); // for line break in textareas
        $data = json_decode($data_json, true);
        $data['id'] = $af_form_id;

        $this->is_submitted = isset($_POST['af_form_id']) && $_POST['af_form_id'] == $af_form_id ? true : false;

        return $data;
    }
}
