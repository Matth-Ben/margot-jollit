// Wordpress uploader
jQuery(document.body).on('click', '.wp-media-add', function(e) {
    e.preventDefault();

    let field_id = jQuery(this).parents('.field').attr('field-id'),
        target_input = jQuery(this).attr('target-input'),
        target_preview = jQuery(this).attr('target-preview'),
        value = jQuery(target_input).val() == '' ? {} : JSON.parse(jQuery(target_input).val()),
        is_multiple = jQuery(this).hasClass('multiple'),
        frame = wp.media({ 
            title: 'Sélectionnez un ou plusieurs fichiers',
            button: { text: 'Terminer' },
            multiple: is_multiple ? 'add' : false
        }),
        interval_preselect_attachments

    // don't select files selected
    frame.on('open', function() {
        let selection = frame.state().get('selection')
        console.log(value)
        for ([id, data] of Object.entries(value)) {
            selection.add([wp.media.attachment(id)])
        }
    })
    
    frame.on('select', function(e) {
        let files = frame.state().get('selection').models,
            new_value = {}

        jQuery(files).each(function() {
            let preview_url, is_image

            if (this.attributes.sizes) {
                let size = this.attributes.sizes.medium !== undefined ? this.attributes.sizes.medium :this.attributes.sizes.full
                preview_url = size.url
                is_image = true
            } else {
                preview_url = this.attributes.icon
                is_image = false
            }

            new_value[this.id] = {
                preview_url: preview_url,
                url: this.attributes.url,
                is_image: is_image,
                filename: this.attributes.filename
            }
        })

        jQuery(target_input).val(JSON.stringify(new_value))
        jQuery(target_input).trigger('change') // create form json on advancedform screen
        advancedform.create_preview_default_files(target_input, target_preview)
    })

    frame.open()

    window.frame = frame
});


// create preview default images
advancedform.create_preview_default_files = (target_input, target_preview) => {
    let data = jQuery(target_input).val()

    jQuery(target_preview).html('')
    
    if (!data) return
    
    for ([id, file_data] of Object.entries(JSON.parse(data))) {
        let uniqid = advancedform.uniqid(),
            class_name = file_data.is_image ? 'image' : 'file'

        jQuery(target_preview).prepend(`
            <div class="${class_name}" data-filename="${file_data.filename}" title="${file_data.filename}">
                <img id="${uniqid}" src="${file_data.preview_url}" data-id="${id}" />
                <a href="${file_data.url}" target="_blank" class="open"></a>
                <a href="#0" class="delete" target-input="${target_input}" target-preview="${target_preview}" target-file="#${uniqid}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </a>
                <div class="filename">${file_data.filename}</div>
            </div>
        `)
    }
}


// create preview default images
advancedform.create_all_preview_default_files = () => {
    let buttons = jQuery('.wp-media-add')

    jQuery(buttons).each(function() {
        let target_input = jQuery(this).attr('target-input'),
            target_preview = jQuery(this).attr('target-preview')

        advancedform.create_preview_default_files(target_input, target_preview)
    })
}


// delete default picture
jQuery(document.body).on('click', '.container-preview-files .delete', function() {
    let target_file = jQuery(this).attr('target-file'),
        target_input = jQuery(this).attr('target-input'),
        target_preview = jQuery(this).attr('target-preview'),
        attachment_id = jQuery(target_file).attr('data-id'),
        data = JSON.parse(jQuery(target_input).val())
    
    delete data[attachment_id]

    let data_json = Object.keys(data).length == 0 ? '' : JSON.stringify(data)

    jQuery(target_input).val(data_json)
    jQuery(target_input).trigger('change') // create form json on advancedform screen
    advancedform.create_preview_default_files(target_input, target_preview)
})
