// create compatible fields for acf fields
advancedform.acf_field_to_advancedform_field = () => {
    let confirm_message = jQuery('#import-acf-fields-post').attr('data-confirm').replace('\\n', '\n'),
        post_type = jQuery('#advancedform-post-type').val()

    if (!confirm(confirm_message)) return

    jQuery(`#container-fields .field`).remove()
    jQuery(`#container-fields`).add(`
        <div class="advancedform-admin-field">
            <div class="advancedform-admin-label"></div>
            <div class="advancedform-admin-input">
                <div>
                    <a href="#0" class="advancedform-field-name">Chargement...</a>
                </div>
            </div>
        </div>
    `)
    
    jQuery.ajax({
        url: parameters.ajax_url,
        type: 'POST',
        data: {
            'action': 'load_acf_fields',
            'post_type': post_type,
            'af_form_id': parameters.af_form_id,
        }
    })
    .done(function(data) {

        if (data == '') return

        data = JSON.parse(data)
        
        if (Object.keys(data.fields).length > 0) {
            advancedform.add_fields(data)
            advancedform.create_form_json()
        }

        if (data.not_supported.length > 0) {
            let text = ''

            jQuery(data.not_supported).each(function(index) {
                text += index < data.not_supported.length - 1 ? this + ', ' : this
            })

            alert(`Les champs de type : "${text}" ne sont pas supportés par le plugin`)
        }
    })
}
