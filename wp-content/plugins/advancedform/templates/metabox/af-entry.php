<?php

    $af_form_id = get_post_meta($post->ID, 'af_form', true);
    $af_form = new AF_Form($af_form_id);
    $fields_data = $af_form->get_af_entry($post->ID);

?>

<div class="metabox-advancedform-entry">
    
    <?php foreach ($fields_data as $field_id => $field) { ?>

        <div class="advancedform-admin-field">
            <div class="advancedform-admin-label">
                <label for="advancedform-post-type"><?php echo $field['label'] ?></label>
            </div>
            <div class="advancedform-admin-input">

                <?php
                    switch ($field['type'])
                    {
                        case 'files':
                            $attachments_id = maybe_unserialize($field['value']);
                            $attachments_id = is_array($attachments_id) ? $attachments_id : [$attachments_id]; // if is a single file
                            $value = json_encode(maybe_unserialize($field['value']));

                            if (!$attachments_id) break;

                            echo "<div id='preview-files-{$field_id}' class='container-preview-files'></div>";
                            echo "<button type='button' class='wp-media-add multiple button' target-input='#advancedform-default-files-{$field_id}' target-preview='#preview-files-{$field_id}'>Choisir</button>";
                            echo "<input id='advancedform-default-files-{$field_id}' type='hidden' name='{$field_id}' value='{$value}' />";
                            break;

                        case 'hidden':
                            echo "<input type='text' name='{$field_id}' value='{$field['value']}' />";
                            break;
                        
                        default:
                            $original_field = get_post_meta($post->ID, 'original_field_' . $field_id, true);
                            $field_html = $af_form->generate_field_html(unserialize($original_field), $field['value']);

                            echo $field_html['row'];
                            break;
                    }
                ?>

            </div>
        </div>

    <?php } ?>

    <input type="hidden" name="af_entry_data" value="<?php echo $post->ID ?>">
</div>
