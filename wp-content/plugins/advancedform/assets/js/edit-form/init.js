document.addEventListener('DOMContentLoaded', function() {

    // init
    advancedform.init = () => {
        let data = jQuery('#af-form-data').val()
            data = data ? JSON.parse(jQuery('#af-form-data').val()) : ''

        jQuery('#container-fields').html('')
        advancedform.add_fields(data)
        advancedform.add_alerts(data.alerts)
        jQuery('#advancedform-post-type').val(data.post_type)
        jQuery('#advancedform-post-status').val(data.status)
        jQuery('#advancedform-entry-title').val(data.entry_title)
        jQuery('#advancedform-success-message').val(data.success_message)
        jQuery('#advancedform-error-message').val(data.error_message)
        jQuery('#advancedform-information-message').val(data.information_message)
        jQuery('#advancedform-recaptcha-version').val(data.recaptcha_version)
        jQuery('#advancedform-recaptcha-language').val(data.recaptcha_language)
        advancedform.refresh_is_post_type()
        advancedform.refresh_entry_title_choices()
        advancedform.refresh_recaptcha_v2()
    }


    // init the page
    advancedform.init()
})
