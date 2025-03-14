// Drag & Drop
advancedform.init_drag_and_drop = () => {
    jQuery('#container-fields').sortable({
        handle: '.advancedform-field-index',
        cancel: '',
        update: () => {
            advancedform.refresh_fields_index()
            advancedform.refresh_entry_title_choices()
            advancedform.create_form_json()
        }
    })
}