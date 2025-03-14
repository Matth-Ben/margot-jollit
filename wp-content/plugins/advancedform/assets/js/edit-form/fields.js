// add field
advancedform.add_field = (field = null, create_form_json = true) => {
    let field_id = field ? field.id : ''

    jQuery('#container-fields').append(advancedform.field_html(field))
        
    if (field_id === '') {
        jQuery('#container-fields .field.new').attr('field-id', advancedform.uniqid())
        jQuery('#container-fields .field.new').addClass('show').find('.field-body').slideToggle()
        jQuery('#container-fields .field.new').removeClass('new')
    }

    advancedform.refresh_fields_index()
    advancedform.init_drag_and_drop()
    advancedform.refresh_uniq_fields()

    if (create_form_json) advancedform.create_form_json()
}


// add fields
advancedform.add_fields = (data) => {
    
    if (data && Object.keys(data.fields).length !== 0) {

        for (let [id, parameters] of Object.entries(data.fields)) {
            advancedform.add_field(parameters, false)

            jQuery(advancedform.parameters_name).each(function() {
                let parameter = jQuery(`#container-fields [field-id="${id}"] [data-parameter="${this}"]`)

                if (jQuery(parameter).attr('type') !== 'checkbox') {
                    if (typeof parameters[this] == 'string') parameters[this] = parameters[this].replaceAll('\\n', '\n') // textarea
                    jQuery(parameter).val(parameters[this])
                } else {
                    jQuery(parameter).prop('checked', parameters[this])
                }

                // // create preview for default files
                // if (parameters['type'] == 'files' && this == 'default_files') {
                //     if (parameters[this]) advancedform.create_preview_default_files(id)
                // }
                
                // // create preview for default thumbnail
                // if (parameters['type'] == 'thumbnail' && this == 'default_thumbnail') {
                //     if (parameters[this]) advancedform.create_preview_default_files(id)
                // }
            })
        }
    } else {
        advancedform.add_field()
    }

    advancedform.refresh_fields_index()
    advancedform.refresh_fields_name()
    advancedform.refresh_textarea_height()
    advancedform.refresh_fields_parameters()
    advancedform.init_drag_and_drop()
    advancedform.create_all_preview_default_files()
    advancedform.refresh_entry_title_choices()
}


// duplicate field
advancedform.duplicate_field = (id) => {
    let field = jQuery(`#container-fields [field-id="${id}"]`),
        new_id = advancedform.uniqid(),
        new_field = jQuery(field).clone()

    jQuery(new_field).attr('field-id', new_id)
    jQuery(field).after(new_field)
    
    // new label
    let new_label = jQuery(`#container-fields [field-id="${id}"] [data-parameter="label"]`).val()
        new_label = new_label ? `${new_label} (copie)` : '(copie)'
    jQuery(`#container-fields [field-id="${new_id}"] [data-parameter="label"]`).val(new_label)

    // new type
    let uniq_types = ['title', 'content', 'thumbnail'],
        new_type = jQuery(`#container-fields [field-id="${id}"] [data-parameter="type"]`).val()
        new_type = uniq_types.includes(new_type) ? 'text' : new_type
    jQuery(`#container-fields [field-id="${new_id}"] [data-parameter="type"]`).val(new_type)
    
    // new postmetakey
    jQuery(`#container-fields [field-id="${new_id}"] [data-parameter="postmetakey"]`).val('')

    advancedform.create_form_json()
    
    advancedform.init()
}