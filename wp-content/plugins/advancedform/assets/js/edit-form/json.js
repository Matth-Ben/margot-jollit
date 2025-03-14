// Create json
advancedform.create_form_json = () => {
    let data = {}
        data.fields = {},
        data.alerts = []

    // fields
    jQuery('#container-fields .field').each(function() {
        let field_id = jQuery(this).attr('field-id'),
            current_field = this

        data.fields[field_id] = {}

        data.fields[field_id].id = field_id
        
        jQuery(advancedform.parameters_name).each(function() {
            let parameter = jQuery(current_field).find('[data-parameter="' + this + '"]')


            if (jQuery(parameter).attr('type') == 'checkbox') {
                data.fields[field_id][this] = jQuery(parameter).is(':checked')
            } else {
                data.fields[field_id][this] = jQuery(parameter).val()
            }
        })
    })

    console.log(data)

    // post type
    data.post_type = jQuery('#advancedform-post-type').val()
    
    // post status
    data.status = jQuery('#advancedform-post-status').val()
    
    // entry title
    data.entry_title = jQuery('#advancedform-entry-title').val()
    
    // success message
    data.success_message = jQuery('#advancedform-success-message').val()
    
    // error message
    data.error_message = jQuery('#advancedform-error-message').val()
    
    // information message
    data.information_message = jQuery('#advancedform-information-message').val()
    
    // recaptcha version
    data.recaptcha_version = jQuery('#advancedform-recaptcha-version').val()
    
    // recaptcha language
    data.recaptcha_language = jQuery('#advancedform-recaptcha-language').val()

    // alerts
    jQuery('#container-advancedform-alerts .alert').each(function() {
        let index = jQuery(this).attr('data-alert'),
            multiple_cc = []

        jQuery(this).find('.container-cc input').each(function() {
            multiple_cc.push(this.value)
        })

        data.alerts.push({
            'pseudo': jQuery(this).find('input.pseudo').val(),
            'email_from': jQuery(this).find('input.email-from').val(),
            'email_to': jQuery(this).find('input.email-to').val(),
            'subject': jQuery(this).find('input.subject').val(),
            'cc': multiple_cc,
            'content': wp.editor.getContent(`editor-alert-${index}`).replaceAll('\n', '')
        })
    })

    // remove disabled fields
    for (let [id, field] of Object.entries(data.fields)) {
        
        if (jQuery(`.field[field-id="${field.id}"]`).hasClass('disabled')) {
            delete data.fields[id]
        }
    }

    jQuery('#af-form-data').val(JSON.stringify(data))
    console.log(data)
}
