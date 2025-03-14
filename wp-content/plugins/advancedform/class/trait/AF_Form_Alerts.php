<?php

trait AF_Form_Alerts
{
    public $current_alert = null;

    /**
     * Send all emails
     */
    public function send_alerts($alerts) {
        
        foreach ($alerts as $index => $alert) {

            if ($alert['email_to'] !== '' && $alert['subject'] !== '' && $alert['content'] !== '') {
                $alert = $this->insert_data( $alert );
                $this->alerts[$index] = $alert;
                $this->current_alert = $alert;

                // Hooking up our functions to WordPress filters 
                add_filter('wp_mail_from', [$this, 'wpb_sender_email']);
                add_filter('wp_mail_from_name', [$this, 'wpb_sender_name']);

                $to = $alert['email_to'];
                $subject = $alert['subject'];
                $body = $alert['content'];
                $headers = ['Content-Type: text/html; charset=UTF-8'];
                
                if ($alert['cc']) {

                    foreach ($alert['cc'] as $cc) {
                        $headers[] = "Cc: {$cc}";
                    }
                }

                wp_mail($to, $subject, $body, $headers);
            }
        }
    }

    /**
     * Function to change email address
     */
    public function wpb_sender_email( $original_email_address ) {
		$email_from = $this->current_alert['email_from'];
        
        if ($email_from) {
            return $email_from;
        }
    }

    /**
     * Function to change sender name
     */
    public function wpb_sender_name( $original_email_from ) {
		$pseudo = $this->current_alert['pseudo'];
		
        if ($pseudo) {
            return $pseudo;
        }
    }

    public function replace_by_value( $alert, $target, $value ) {
        $alert['pseudo'] = str_replace( $target, $value, $alert['pseudo'] );
        $alert['email_from'] = str_replace( $target, $value, $alert['email_from'] );
        $alert['email_to'] = str_replace( $target, $value, $alert['email_to'] );
        $alert['subject'] = str_replace( $target, $value, $alert['subject'] );
        $alert['content'] = str_replace( $target, $value, $alert['content'] );

        if ( $alert['cc'] ) {
            foreach ( $alert['cc'] as $i => $item ) {
                $alert['cc'][$i] = str_replace( $target, $value, $alert['cc'][$i] );
            }
        }

        return $alert; 
    }

    public function insert_data( $alert ) {
        
        if ( $this->data['fields'] ) {
            foreach ( $this->data['fields'] as $field ) {
                if ( isset( $_POST[$field['id']] ) && $_POST[$field['id']] ) {
                    $field_value = stripslashes( $_POST[$field['id']] );
                    
                    if ( $field['type'] === "choice" ) {
                        foreach ( explode( '\n', $field['choices'] ) as $item ) {
                            if ( $field_value == $item ) {
                                $explode_item = explode( '|', $item );

                                if ( isset( $explode_item[1] ) ) {
                                    $alert = $this->replace_by_value( $alert, '{{' . $field['label'] . '}}', $explode_item[1] );
                                    $alert = $this->replace_by_value( $alert, '{' . $field['label'] . '}', $explode_item[0] );
                                }
                            }
                        }
                    } else {
                        $alert = $this->replace_by_value( $alert, '{' . $field['label'] . '}', htmlentities( $field_value ) );
                    }
                }
            }
        }

        // dump(htmlentities("<p>Lorem d'ipsum</p>"));

        return $alert;
    }
}