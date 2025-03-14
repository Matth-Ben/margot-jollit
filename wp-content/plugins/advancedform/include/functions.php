<?php

// get front form
function af_form($id, $create_form = true) {
    $af_form = new AF_Form($id);

    return $af_form->generate_form_html($create_form);
}