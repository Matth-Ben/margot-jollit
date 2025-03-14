// show/hide settings
jQuery(document.body).on('click', '.advancedform-field-name', function() {
    jQuery(this).parents('.field').toggleClass('show').find('.field-body').slideToggle()
})

// click on button and add field
jQuery(document.body).on('click', '#button-add-field', () => {
    advancedform.add_field()
    advancedform.refresh_fields_parameters()
})


// auto refresh json and name
jQuery(document.body).on('change', '#metabox--af-form--fields [data-parameter], #advancedform-post-type, #advancedform-post-status, #advancedform-entry-title', function() {
    advancedform.refresh_fields_name()
    advancedform.refresh_entry_title_choices()
    advancedform.create_form_json()
})


// check if there are not many uniq field's type
jQuery(document.body).on('change', '#metabox-advancedform-fields [data-parameter="type"]', function() {
    advancedform.refresh_uniq_fields()
})


// duplicate field
jQuery(document.body).on('click', '#metabox--af-form--fields .container-field-actions .duplicate', function() {
    let field_id = jQuery(this).parents('.field').attr('field-id')
    
    advancedform.duplicate_field(field_id)
})


// remove field
jQuery(document.body).on('click', '#metabox--af-form--fields .container-field-actions .delete', function() {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce champ ?')) {
        jQuery(this).parents('.field').remove()
        advancedform.refresh_fields_index()
        advancedform.refresh_entry_title_choices()
        advancedform.create_form_json()
    }
})


// check paramaters displayed for choice type
jQuery(document.body).on('change', '[data-parameter="type"]', function() {
    advancedform.refresh_fields_parameters()
})


// check paramaters displayed for choice type
jQuery(document.body).on('change', '[data-parameter="type"]', function() {
    let field = jQuery(this).parents('.field'),
        field_id = jQuery(field).attr('field-id'),
        type = jQuery(field).find('[data-parameter="type"]').val()

    if (type == 'files' || type == 'thumbnail') {
        advancedform.create_preview_default_files(field_id)
    }
})


// show button "import acf fields of the post" & post status
jQuery(document.body).on('change', '#advancedform-post-type', function() {
    advancedform.refresh_is_post_type()
})


// refresh empty parameter
jQuery(document.body).on('click', 'button.refresh-empty-parameter', function() {
    let field_id = jQuery(this).parents('.field').attr('field-id'),
        parameter_name = jQuery(this).parents('.advancedform-admin-input').find('[data-parameter]').attr('data-parameter')

    advancedform.refresh_empty_parameter(field_id, parameter_name)
})


// add an alert
jQuery(document.body).on('click', '#button-add-alert', function() {
    advancedform.add_alert()
})


// add an alert
jQuery(document.body).on('click', '#container-advancedform-alerts .alert .delete', function() {

    if (confirm('Êtes-vous sûr de vouloir supprimer cette alerte ?')) {
        let index = jQuery(this).attr('data-alert')
        
        advancedform.remove_alert(index)
    }
})


// save json when alerts are changed
jQuery(document.body).on('submit', 'form[action="post.php"]', function(e) {
    
    if (!jQuery(this).hasClass('ready')) {
        e.preventDefault()
        advancedform.create_form_json()
        jQuery(this).addClass('ready')
        jQuery(this).submit()
    }
})


// create compatible fields for acf fields
jQuery(document.body).on('click', '.import-acf-fields-post', function() {
    advancedform.acf_field_to_advancedform_field()
})


// add cc to an alert
jQuery(document.body).on('click', 'button.button-add-cc', function() {
    
    jQuery(this).parent().find('> div').append(`
        <div>
            <input type="email" placeholder="Cc" />
            <a href="#0" class="delete remove-cc">Supprimer</a>
        </div>
    `)

    advancedform.create_form_json()
})


// remove cc to an alert
jQuery(document.body).on('click', 'a.remove-cc', function() {
    
    jQuery(this).parent().remove()
    advancedform.create_form_json()
})


// add or remove reCAPTCHA v2 in form edition
jQuery(document.body).on('change', '#advancedform-recaptcha-version', function() {
    advancedform.refresh_recaptcha_v2()
})


// format regex
jQuery(document.body).on('change', 'input[data-parameter="regex"]', function() {
    let value = this.value
    
    if (value.charAt(0) !== '/') value = '/' + value
    if (value.charAt(value.length - 1) !== '/') value = value + '/'

    this.value = value
})