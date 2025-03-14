<?php

trait AF_Form_reCAPTCHA
{
    /**
     * Get reCAPTCHA keys if it is set in parameters
     */
    public function get_recaptcha($version)
    {
        $public_key = get_option("af__recaptcha_v{$version}__public_key");
        $secret_key = get_option("af__recaptcha_v{$version}__secret_key");

        return ($public_key == '' || $secret_key == '') ? null : [
            'public_key' => $public_key,
            'secret_key' => $secret_key
        ];
    }

    /**
     * Verifiy reCAPTCHA if form is submitted and need it
     */
    public function verify_recaptcha()
    {
        $version = isset($this->data['recaptcha_version']) ? intval($this->data['recaptcha_version']) : 0;

        if ($version == 0) return;

        $recaptcha = $this->get_recaptcha($version);

        if ($recaptcha == null) return;

        switch ($version)
        {
            case 2:
                foreach ($this->data['fields'] as $field) {
                    if ($field['type'] == 'recaptcha_v2') $form_use_recaptcha = true;
                }

                $form_use_recaptcha = $this->data['recaptcha_version'] == 2 ? true : false;
                $token_name = 'g-recaptcha-response';
                break;

            case 3:
                $form_use_recaptcha = $this->data['recaptcha_version'] == 3 ? true : false;
                $token_name = 'g-token';
                break;
        }

        if ($form_use_recaptcha) {
            $secret_key = $this->get_recaptcha($version)['secret_key'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $token = $_POST[$token_name];

            $url = "https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$token}&remoteip={$ip}";
            $ch = curl_init(); // create curl resource

            curl_setopt($ch, CURLOPT_URL, $url); // set url
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return the transfer as a string

            $output = curl_exec($ch); // $output contains the output string
            $response = json_decode($output, true);
            
            curl_close($ch); // close curl resource to free up system resources
                            
            if (!$response['success']) {
                $this->message = [
                    'text' => $this->data['error_message'],
                    'status' => 'error'
                ];
                if ($this->errors === null) $this->errors = [];
                $this->errors['recaptcha'] = 'error';
            }
        }
    }

    /**
     * Add reCPATCHA v3 to the front form
     */
    public function add_recaptcha_to_form_front($version, $language = '')
    {
        $keys = $this->get_recaptcha($version);

        if ($keys === null) return;

        $public_key = $keys ? $keys['public_key'] : null;

        switch ($version)
        {
            case 2:
                wp_enqueue_script('af--recaptcha-v2--api', "https://www.google.com/recaptcha/api.js?onload=recaptchaOnload&render=explicit");
                wp_enqueue_script('af--recaptcha-v2--script', plugins_url('/advancedform/assets/js/recaptcha-v2/index.js'));
                wp_localize_script('af--recaptcha-v2--script', 'parameters', [
                    'public_key' => $public_key,
                    'language' => $language,
                ]);

                // add async & defer
                add_filter('script_loader_tag', function($tag, $handle)
                {
                    if ('advancedform--recaptcha-v2--api' !== $handle) return $tag;
        
                    return str_replace('src', 'defer async src', $tag);
                }, 10, 2);
                break;

            case 3:
                wp_enqueue_script('af--recaptcha-v3--api', "https://www.google.com/recaptcha/api.js?render={$public_key}&hl={$language}");
                wp_enqueue_script('af--recaptcha-v3--script', plugins_url('/advancedform/assets/js/recaptcha-v3/index.js'));
                wp_localize_script('af--recaptcha-v3--script', 'public_key', $public_key);
                
                // add html field to html fields array
                $this->data['fields_html']['recaptcha_v3'] = '<input type="hidden" name="g-token" />';
                break;
        }
    }
}
