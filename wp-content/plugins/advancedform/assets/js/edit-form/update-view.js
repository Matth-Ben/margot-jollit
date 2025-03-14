// refresh index fields
advancedform.refresh_fields_index = () => {
    jQuery('#container-fields .field').each(function(index) {
        jQuery(this).find('.advancedform-field-index').html(index + 1)
    })
}


// refresh exists once title, content, thumbnail fields
advancedform.refresh_uniq_fields = () => {
    let uniq_types = {
        'title': { exists: false },
        'content': { exists: false },
        'thumbnail': { exists: false },
        'recaptcha_v2': { exists: false },
    }

    for ([type, data] of Object.entries(uniq_types)) {

        jQuery(`#container-fields .field [data-parameter="type"]`).each(function() {
            if (jQuery(this).val() == type) data.exists = true
        })

        if (data.exists) {
            jQuery(`#container-fields .field [data-parameter="type"]`).each(function() {
                if (jQuery(this).val() == type) {
                    jQuery(this).find(`option[value="${type}"]`).attr('disabled', false)
                } else {
                    jQuery(this).find(`option[value="${type}"]`).attr('disabled', true)
                }
            })
        } else {
            jQuery(`#container-fields .field [data-parameter="type"] option[value="${type}"]`).attr('disabled', false)
        }
    }
}


// refresh parameters displayed for choice type
advancedform.refresh_fields_parameters = () => {

    jQuery('#container-fields .field').each(function() {
        var current_field = this,
            type = jQuery(this).find('[data-parameter="type"]').val()

        jQuery(this).find('.field-body .advancedform-admin-field').hide() // hide all

        jQuery(advancedform.parameters_by_field_type[type]).each(function() { // show the necessary parameters
            jQuery(current_field).find(`[data-parameter="${this}"]`).parents('.advancedform-admin-field').css('display', 'flex')
        })

        if (['choice', 'hidden', 'recaptcha_v2'].includes(type)) {
            jQuery(current_field).find('[data-parameter="required"]').hide()
        } else {
            jQuery(current_field).find('[data-parameter="required"]').show()
        }
    })
}


// refresh all field's name
advancedform.refresh_fields_name = () => {
    jQuery(`#container-fields .field`).each(function() {
        let name = jQuery(this).find('[data-parameter="label"]').val()
        
        if (name) jQuery(this).find('.advancedform-field-name').html(name)
    })
}


// refresh if it is a form for post type or not
advancedform.refresh_is_post_type = () => {
    
    if (jQuery('#advancedform-post-type').val() !== '') {
        jQuery(document.body).addClass('advancedform-new-post')
        jQuery(document.body).removeClass('advancedform-new-entry')
    } else {
        jQuery(document.body).addClass('advancedform-new-entry')
        jQuery(document.body).removeClass('advancedform-new-post')

        jQuery('[data-parameter="type"]').each(function() {
            let type = jQuery(this).val()

            if (['title', 'content', 'thumbnail'].includes(type)) {
                jQuery(this).val('text')
                jQuery(this).trigger('change')
            }
        })
    }

    if (jQuery('#advancedform-post-status').val() == null) {
        jQuery('#advancedform-post-status').val('publish')
    }
}


// refresh choices for parameter "entry title"
advancedform.refresh_entry_title_choices = () => {
    let select = jQuery('#advancedform-entry-title'),
        current_value = jQuery(select).val()

    jQuery(select).html('')

    jQuery('#container-fields > .field').each(function() {
        let value = jQuery(this).attr('field-id'),
            label = jQuery(this).find('[data-parameter="label"]').val()
        
        jQuery(select).append(`<option value=${value}>${label}</option>`)
    })

    jQuery(select).val(current_value)
}


// remove default value for date, datetime and time
advancedform.refresh_empty_parameter = (field_id, parameter_name) => {
    jQuery(`.field[field-id="${field_id}"] [data-parameter=${parameter_name}]`).val('')
}


// add or remove reCAPTCHA v2 in form edition
advancedform.refresh_recaptcha_v2 = () => {

    let select_recaptcha = jQuery('#advancedform-recaptcha-version')

    console.log(jQuery(select_recaptcha).val())

    if (jQuery(select_recaptcha).val() == 2) {
        jQuery(document.body).addClass('recaptcha-v2')

        jQuery('#container-fields .field[data-type="recaptcha_v2"]').each(function() {
            jQuery(this).removeClass('disabled')
        })
    } else {
        jQuery(document.body).removeClass('recaptcha-v2')

        jQuery('#container-fields .field[data-type="recaptcha_v2"]').each(function() {
            jQuery(this).addClass('disabled')
        })
    }

    if (jQuery(select_recaptcha).val() == null) {
        jQuery(select_recaptcha).val('')
    }
}
