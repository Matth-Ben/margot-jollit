// add alert
advancedform.add_alert = (data_alert = null) => {
    let index = jQuery('#container-advancedform-alerts > .alert').length + 1,
        alert_html = advancedform.alert_html(index, data_alert),
        editorSettings = {mediaButtons: false, tinymce: true, quicktags: true}

    // add alert html
    jQuery('#container-advancedform-alerts').append(alert_html)

    // set content
    if (data_alert) {
        jQuery(`#editor-alert-${index}`).val(data_alert.content)
    }

    // init the wp editor
    wp.editor.initialize(`editor-alert-${index}`, editorSettings)
}


// remove alert
advancedform.remove_alert = (index) => {
    
    // remove alert html
    jQuery(`#container-advancedform-alerts .alert-${index}`).remove()

    // update text index "Alert 1", "Alert 2", etc
    jQuery('#container-advancedform-alerts .alert').each(function(i) {

        jQuery(this).find('label span').text(i + 1)
    })
}


// add alert
advancedform.add_alerts = (alerts) => {
    
    jQuery(alerts).each(function() {
        advancedform.add_alert(this)
    })
}
