<?php

trait AF_Form_Download
{
    /**
     * Create and download an XLSX file for advancedform entries
     */
    public function download_entries($format, $advancedform_id) {
        $post_type = $this->get_advancedform($advancedform_id)['post_type'];
        $post_type = $post_type == '' ? 'advancedform_entry' : $post_type;

        // get data
        $query = new WP_Query([
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'meta_query' => [ 'key' => 'advancedform', 'value' => $advancedform_id]
        ]);

        // create filename
        $advancedform_title = get_the_title($advancedform_id);
        $date_now = (new DateTime('now'))->format('YmdHi');
        $filename = "{$advancedform_title}-{$date_now}.{$format}";

        // execute the correct function
        if ($post_type == 'advancedform_entry') {
            
            switch ($format) {
                
                case 'xlsx':
                    $this->download_entries_data_as_xlsx($query, $filename);
    
                    break;
                
                case 'json':
                    $this->download_entries_data_as_json($query, $filename);
    
                    break;
            }
        } else {

            switch ($format) {
                
                case 'xlsx':
                    $this->download_acf_data_as_xlsx($query, $filename);
    
                    break;
                
                case 'json':
                    $this->download_acf_data_as_json($query, $filename);
    
                    break;
            }
        }
    }
    
    
    /**
     * Download with xlsx format
     */
    public function download_entries_data_as_xlsx($query, $filename) {
      
        $uniq_labels = []; // several fields may have the same label but not the same field_id
        $columns_titles = [];
        $columns = [];
    
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                // $fields_data = $post_type == 'advancedform_entry' ? $this->get_af_entry(get_the_ID()) : $this->get_acf_fields_of_post(get_the_ID());
                $fields_data = $this->get_af_entry(get_the_ID());
                $values = null;

                // create all columns titles
                foreach ($fields_data as $field_id => $field_data) {
                    $label = $field_data['label'];
    
                    if (!in_array($field_id, $columns_titles)) {
                        $columns_titles[] = $field_id;
                        $uniq_labels[$field_id] = $label;
                    }
                }
    
                // add values in order
                foreach ($columns_titles as $label) {
                    $value = '';
    
                    foreach ($fields_data as $field_id => $field_data) {
    
                        if ($label == $field_id) {
                            $value = $field_data['value'];
                            
                            // format data
                            switch ($field_data['type']) {
    
                                case 'files':
                                    $files = json_decode(json_encode(maybe_unserialize($value)), true); // here, wordpress auto serialize this array to class in db, it's a way to recover an array 
                                    $index = 1;
                                    $value = '';
    
                                    foreach ($files as $attachment_id => $data) {
                                        $value .= "<a href='{$data['url']}'>{$data['filename']}</a>";
                                        $value .= $index < count($files) ? ', ' : '';
                                        $index++;
                                    }
    
                                    break;
    
                                case 'choice':
                                    $choices = explode('\\n', $value);
                                    $value = '';
                                    $i = 1;
                                    
                                    // remove empty value
                                    foreach ($choices as $index => $item) {
                                        if ($item == '') unset($choices[$index]);
                                    }
    
                                    foreach ($choices as $choice) {
                                        $value .= $choice;
                                        $value .= $i < count($choices) ? ', ' : '';
                                        $i++;
                                    }
    
                                    break;
    
                                case 'date';
                                    $value = (new DateTime($value))->format('Y-m-d');
    
                                    break;
    
                                case 'datetime';
                                    $value = (new DateTime($value))->format('Y-m-d H:i:s');
    
                                    break;
                                
                                case 'time';
                                    $value = (new DateTime($value))->format('H:i:s');
    
                                    break;
                            }
                        }
                    }
    
                    $values[] = $value;
                }
    
                // don't add empty column
                if ($values) {
    
                    // add id and creation date
                    array_unshift($values, get_the_ID(), get_the_date('Y-m-d H:i:s', get_the_ID()));
    
                    $columns[] = $values;
                }
            }
        }
    
        foreach ($columns_titles as $i => $field_id) {
            $columns_titles[$i] = "<b>{$uniq_labels[$field_id]}</b>";
        }
    
        // add title for ids and title for creation dates
        array_unshift($columns_titles, '<b>id</b>', '<b>Date de création</b>');
    
        // add columns titles
        array_unshift($columns, $columns_titles);
        
        $xlsx = SimpleXLSXGen::fromArray($columns);
        $xlsx->downloadAs($filename);

        die;
    }


    /**
     * 
     */
    public function download_acf_data_as_xlsx($query, $filename) {
         
        $uniq_labels = []; // several fields may have the same label but not the same field_id
        $columns_titles = [];
        $columns = [];
    
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $acf_fields = $this->get_acf_fields_of_post(get_the_ID());
                $fields_data = [];
                $values = null;

                // get acf data supported
                foreach ($acf_fields as $acf_field) {

                    if (in_array($acf_field['type'], $this->$acf_fields_supported)) {

                        $fields_data[$acf_field['key']] = [
                            'value' => get_field($acf_field['name'], get_the_ID()),
                            'type' => $acf_field['type'],
                        ];
    
                        if (in_array($acf_field['type'], ['file', 'image', 'gallery'])) {
                            $fields_data[$acf_field['key']]['format'] = $acf_field['return_format']; // file format returned by ACF
                        }
                    }
                }

                // create all columns titles
                foreach ($acf_fields as $acf_field) {

                    if (in_array($acf_field['type'], $this->$acf_fields_supported)) {
                        $label = $acf_field['label'];
        
                        if (!in_array($acf_field['key'], $columns_titles)) {
                            $columns_titles[] = $acf_field['key'];
                            $uniq_labels[$acf_field['key']] = $acf_field['name'];
                        }
                    }
                }
    
                // add values in order
                foreach ($columns_titles as $label) {
                    $value = '';
    
                    foreach ($fields_data as $field_id => $field_data) {
    
                        if ($label == $field_id) {
                            $value = $field_data['value'] == null ? '' : $field_data['value'];
                            
                            // format data
                            switch ($field_data['type']) {
    
                                case 'gallery':

                                    $images = $value;
                                    $value = '';
                                    $i = 1;

                                    foreach ($images as $image) {
                                        $value .= $field_data['format'] == 'array' ? $image['url'] : '';
                                        $value .= $field_data['format'] == 'url' ? $image : '';
                                        $value .= $field_data['format'] == 'id' ? wp_get_attachment_url($image) : '';
                                        $value .= $i < count($images) ? ', ' : '';
                                        $i++;
                                    }

                                    break;

                                case 'image':
                                case 'file':

                                    $value = $field_data['format'] == 'array' ? $value['url'] : $value;
                                    $value = $field_data['format'] == 'url' ? $value : $value;
                                    $value = $field_data['format'] == 'id' ? wp_get_attachment_url($value) : $value;

                                    break;
    
                                case 'checkbox':

                                    $choices = $value;
                                    $value = '';
                                    $i = 1;
    
                                    foreach ($choices as $choice) {
                                        $value .= $choice;
                                        $value .= $i < count($choices) ? ', ' : '';
                                        $i++;
                                    }
    
                                    break;
                            }
                        }
                    }
    
                    $values[] = $value;
                }
    
                // don't add empty column
                if ($values) {
    
                    // add id and creation date
                    array_unshift($values, get_the_ID(), get_the_date('Y-m-d H:i:s', get_the_ID()));
    
                    $columns[] = $values;
                }
            }
        }
    
        foreach ($columns_titles as $i => $field_id) {
            $columns_titles[$i] = "<b>{$uniq_labels[$field_id]}</b>";
        }
    
        // add title for ids and title for creation dates
        array_unshift($columns_titles, '<b>id</b>', '<b>Date de création</b>');
    
        // add columns titles
        array_unshift($columns, $columns_titles);
    
        $xlsx = SimpleXLSXGen::fromArray($columns);
        $xlsx->downloadAs($filename);
    }


    /**
     * Download with json format
     */
    public function download_entries_data_as_json($query, $filename) {

        $data = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $fields_data = $this->get_af_entry(get_the_ID());
                $id = get_the_ID();

                $data[] = [
                    'id' => $id,
                    'title' => get_the_title($id),
                    'creation_date' => get_the_date('Y-m-d H:i:s', $id),
                    'fields' => $fields_data,
                ];
            }
        }

        // create json
        $data_json = json_encode($data);

        ob_clean();

        header("Content-disposition: attachment; filename={$filename}");
        header('Content-type: application/json');

        echo $data_json;
        
        exit();
    }
    
    /**
     * Download with json format
     */
    public function download_acf_data_as_json($query, $filename) {
        $data = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $acf_fields = $this->get_acf_fields_of_post(get_the_ID());
                $id = get_the_ID();

                $data[$id] = [
                    'id' => $id,
                    'title' => get_the_title($id),
                    'creation_date' => get_the_date('Y-m-d H:i:s', $id),
                    'fields' => []
                ];

                // get acf data supported
                foreach ($acf_fields as $acf_field) {

                    if (in_array($acf_field['type'], $this->$acf_fields_supported)) {

                        $data[$id]['fields'][$acf_field['key']] = [
                            'value' => get_field($acf_field['name'], get_the_ID()),
                            'type' => $acf_field['type'],
                        ];
    
                        if (in_array($acf_field['type'], ['file', 'image', 'gallery'])) {
                            $data[$id]['fields'][$acf_field['key']]['format'] = $acf_field['return_format']; // file format returned by ACF
                        }
                    }
                }
            }
        }

        // create json
        $data_json = json_encode($data);

        ob_clean();

        header("Content-disposition: attachment; filename={$filename}");
        header('Content-type: application/json');

        echo $data_json;
        
        exit();
    }
}
