<?php

class AF_Form
{
    use AF_Form_Data, AF_Form_Ajax, AF_Form_FrontForm, AF_Form_Restrictions, AF_Form_Download, AF_Form_Alerts, AF_Form_reCAPTCHA, AF_Form_ACF, AF_Form_Files, AF_Form_Save;


    /**
     * Contains the form data
     */
    public $data = null;


    /**
     * If the form is submitted
     */
    public $is_submitted = false;
    
    
    /**
     * If the form is submitted
     */
    public $errors = null;
    
    
    /**
     * If the form is submitted
     */
    public $message = null;


    public function __construct($id)
    {   
        // get the form data
        $this->data = $this->get_af_form($id);

        // if the form is submitted
        if ($this->data !== null && $this->is_submitted) {

            if ($this->intercept_data()) {
                $this->verify_recaptcha();
                $this->verify_restrictions();
                
                if ($this->errors === null) {
                    $this->save_submitted_form();
                    $this->send_alerts($this->data['alerts']);
                }
            }
        }
    }
}
