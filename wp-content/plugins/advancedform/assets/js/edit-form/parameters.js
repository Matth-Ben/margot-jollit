advancedform.parameters_name = [

    // necessary
    'label',
    'display_label',
    'type',
    'class',
    'postmetakey',
    
    // for fields with text and number
    'placeholder',
    
    // restrictions
    'file_extensions_allowed',
    'file_extensions_disallowed',
    'files_multiple',
    'length_min',
    'length_max',
    'number_min',
    'number_max',
    'size_min',
    'size_max',
    'date_min',
    'date_max',
    'datetime_min',
    'datetime_max',
    'time_min',
    'time_max',
    'regex',
    'required',
    
    // choice
    'choices',
    'choice_logic',

    // checkbox
    'checkbox_message',

    // show default
    'show_default_title',
    'show_default_content',
    'show_default_text',
    'show_default_textarea',
    'show_default_number',
    'show_default_email',
    'show_default_choice',
    'show_default_date',
    'show_default_datetime',
    'show_default_time',
    'show_default_color',
    
    // submit default
    'submit_default_title',
    'submit_default_content',
    'submit_default_thumbnail',
    'submit_default_text',
    'submit_default_textarea',
    'submit_default_number',
    'submit_default_email',
    'submit_default_files',
    'submit_default_date',
    'submit_default_datetime',
    'submit_default_time',
    'submit_default_hidden',
]


advancedform.parameters_by_field_type = {

    // default post field
    'title':        ['label', 'display_label', 'type', 'class', 'required', 'placeholder', 'show_default_title', 'submit_default_title', 'length_min', 'length_max'],
    'content':      ['label', 'display_label', 'type', 'class', 'required', 'placeholder', 'show_default_content', 'submit_default_content', 'length_min', 'length_max'],
    'thumbnail':    ['label', 'display_label', 'type', 'class', 'required', 'file_extensions_allowed', 'file_extensions_disallowed', 'submit_default_thumbnail', 'size_min', 'size_max'],

    // custom field
    'text':         ['label', 'display_label', 'type', 'class', 'required', 'postmetakey', 'placeholder', 'show_default_text', 'submit_default_text', 'length_min', 'length_max'],
    'textarea':     ['label', 'display_label', 'type', 'class', 'required', 'postmetakey', 'placeholder', 'show_default_textarea', 'submit_default_textarea', 'length_min', 'length_max'],
    'files':        ['label', 'display_label', 'type', 'class', 'required', 'postmetakey', 'files_multiple', 'submit_default_files', 'file_extensions_allowed', 'file_extensions_disallowed', 'size_min', 'size_max'],
    'number':       ['label', 'display_label', 'type', 'class', 'required', 'postmetakey', 'placeholder', 'show_default_number', 'submit_default_number', 'number_min', 'number_max'],
    'email':        ['label', 'display_label', 'type', 'class', 'required', 'postmetakey', 'placeholder', 'show_default_email', 'submit_default_email'],
    'choice':       ['label', 'display_label', 'type', 'class', 'required', 'postmetakey', 'choices', 'choice_logic', 'show_default_choice', 'submit_default_choice'],
    'checkbox':     ['label', 'display_label', 'type', 'class', 'required', 'postmetakey', 'checkbox_message'],
    'date':         ['label', 'display_label', 'type', 'class', 'required', 'postmetakey', 'show_default_date', 'submit_default_date', 'date_min', 'date_max'],
    'datetime':     ['label', 'display_label', 'type', 'class', 'required', 'postmetakey', 'show_default_datetime', 'submit_default_datetime', 'datetime_min', 'datetime_max'],
    'time':         ['label', 'display_label', 'type', 'class', 'required', 'postmetakey', 'show_default_time', 'submit_default_time', 'time_min', 'time_max'],
    'color':        ['label', 'display_label', 'type', 'class', 'required', 'postmetakey', 'show_default_color', 'submit_default_color'],
    'password':     ['label', 'display_label', 'type', 'class', 'required', 'postmetakey', 'placeholder', 'regex'],
    'hidden':       ['label', 'display_label', 'type', 'class', 'required', 'postmetakey'],
    'recaptcha_v2':    ['label', 'display_label', 'type', 'class'],
}
