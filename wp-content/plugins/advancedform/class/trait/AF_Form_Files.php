<?php

trait AF_Form_Files
{
    /**
     * Save an attachment
     */
    public static function save_attachment($filename, $tmp_name) {
        $name           = explode('.', $filename);
        $name           = count($name) === 2 ? $name[0] : $filename;
        $upload_dir     = wp_upload_dir();
        $image_data     = file_get_contents( $tmp_name );
        $file           = wp_mkdir_p($upload_dir['path']) ? $upload_dir['path'] . '/' . $filename : $upload_dir['basedir'] . '/' . $filename;
        $wp_filetype    = wp_check_filetype( $filename, null );

        file_put_contents( $file, $image_data );

        $attachment = array(
            'post_mime_type'    => $wp_filetype['type'],
            'post_title'        => sanitize_file_name( $name ),
            'post_content'      => '',
            'post_status'       => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $file);

        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        return $attach_id;
    }
}