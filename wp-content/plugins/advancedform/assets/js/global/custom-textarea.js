// refresh attr "rows" of textarea
advancedform.refresh_textarea_height = () => {

    jQuery('.advancedform-admin-input textarea').each(function() {
        let rows = this.value.split(`\n`).length

        jQuery(this).attr('rows', rows > 2 ? rows : 2)
    })
}

// check textarea for set the good number of "rows" (for the height)
jQuery(document.body).on('keyup', '.advancedform-admin-input textarea', function() {
    advancedform.refresh_textarea_height(this)
})

document.addEventListener('DOMContentLoaded', function() {
    advancedform.refresh_textarea_height()
})