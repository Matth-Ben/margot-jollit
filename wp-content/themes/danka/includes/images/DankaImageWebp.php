<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class DankaImageWebp {
    
    public static $slug = DANKA_SETTINGS_IMAGES_SLUG;

    public static function init() {
        add_action( 'admin_menu', array( get_called_class(), 'add_subpage' ) );
        add_action( 'admin_init',  array( get_called_class(), 'register_settings' ) );
        add_action( 'admin_notices', array( get_called_class(), 'register_notice' ) );

        add_filter( 'wp_handle_upload', array( get_called_class(), 'convert_to_webp' ) );

        add_action( 'wp_ajax_count_all_images_can_be_converted', array( get_called_class(), 'async_count_all_images_can_be_converted' ) );
        add_action( 'wp_ajax_nopriv_count_all_images_can_be_converted', array( get_called_class(), 'async_count_all_images_can_be_converted' ) );
        add_action( 'wp_ajax_convert_images', array( get_called_class(), 'async_convert_images' ) );
        add_action( 'wp_ajax_nopriv_convert_images', array( get_called_class(), 'async_convert_images' ) );
    }

    public static function add_subpage() {
        add_submenu_page(
            DANKA_SETTINGS_SLUG,                  // parent
            'Images',                                       // <title></title>
            'Images',                                       // menu link text
            'manage_options',                                   // capability to access the page
            self::$slug,                            // page URL slug
            array( get_called_class(), 'render_page' ),  // callback function with content
            //5                                                   // priority
        );
    }

    public static function render_page() {
        ?>
        <div class="wrap">
            <h1><?php echo get_admin_page_title() ?></h1>
            <form method="post" action="options.php">
                <?php
                    settings_fields( self::$slug ); // settings group name
                    do_settings_sections( self::$slug ); // just a page slug
                    submit_button(); // "Save Changes" button
                ?>
            </form>
        </div>
	    <?php
    }

    public static function register_settings() {
        $id = self::$slug . '-id';
    
        // Create section
        add_settings_section(
            $id, // section ID
            'Conversion Webp', // title (optional)
            '', // callback function to display the section (optional)
            self::$slug
        );
    
        // Register fields
        register_setting( self::$slug, "danka_image_autoconvert", array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
    
        // Add fields
        add_settings_field(
            "danka_image_autoconvert",
            'Auto conversion',
            array( get_called_class(), 'render_checkbox' ),
            self::$slug,
            $id,
            array( 'name' => "danka_image_autoconvert" )
        );
    
        add_settings_field(
            "danka_image_convert_all_images",                                              // id
            'Convertir toutes les images jpeg et png',                                          // title
            array( get_called_class(), 'render_convert_all_images' ),    // callback
            self::$slug,                                                                            // page
            $id,                                                                       // section
            array( 'name' => "danka_convert_all_images" )
        );
    }

    public static function render_checkbox( $args ) {
        printf(
            '<label><input type="checkbox" id="%s" name="%s" %s /></label>',
            $args[ 'name' ],
            $args[ 'name' ],
            get_option( $args['name'] ) ? 'checked' : ''
        );
    }

    public static function render_convert_all_images() {
        $url = admin_url() . 'options-media.php?danka__convert_all_images=true';
    
        echo "<a href='$url' id='danka-convert-all-images' class='button'>Convertir au format webp</a>";
        echo "<p id='danka-convert-all-images-informations'></p>";
        echo "<p id='danka-convert-all-images-status'></p>";
        ?>
        <script>
            document.addEventListener( 'DOMContentLoaded', () => {
                const button = document.getElementById( 'danka-convert-all-images' )
                const informations = document.getElementById( 'danka-convert-all-images-informations' )
                const status = document.getElementById( 'danka-convert-all-images-status' )
    
    
                button.addEventListener( 'click', async e => {
                    e.preventDefault()
                    
                    const request = await fetch( ajaxurl, {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'Cache-Control': 'no-cache',
                        },
                        body: new URLSearchParams( { action: 'count_all_images_can_be_converted' } )
                    } )
                    const ids = await request.json()
    
                    console.log( ids )
    
                    informations.innerHTML = "Chargement..."
                    
                    if ( ids.length > 0 ) {
                        let new_urls = []
    
                        informations.innerHTML = `${ids.length} ${ids.length > 1 ? ' images peuvent être converties' : ' image peut être convertie'}`
                        status.innerHTML = `Convertion des images en cours (${new_urls.length}/${ids.length})`
    
                        ids.forEach( async id => {
                            const images_request = await fetch( ajaxurl, {
                                method: 'post',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                    'Cache-Control': 'no-cache',
                                },
                                body: new URLSearchParams( {
                                    action: 'convert_images',
                                    images: JSON.stringify( [id] )
                                } )
                            } )
                            const response = await images_request.json()
    
                            new_urls = [...new_urls, ...response]
    
                            
                            if ( new_urls.length != ids.length ) {
                                status.innerHTML = `Convertion des images en cours (${new_urls.length}/${ids.length})`
                            } else {
                                status.innerHTML = `Convertion terminé ! (${ids.length}/${ids.length})`
                            }
                        } )
                    } else {
                        informations.innerHTML = "Aucune image ne peut être convertie"
                    }
                } )
            } )
        </script>
        <?php
    }

    public static function register_notice() {
        if (
            isset( $_GET[ 'page' ] ) 
            && self::$slug == $_GET[ 'page' ]
            && isset( $_GET[ 'settings-updated' ] ) 
            && true == $_GET[ 'settings-updated' ]
        ) {
            ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <strong>Vos préférences en été enregistrées.</strong>
                    </p>
                </div>
            <?php
        }
    }

    public static function convert_to_webp( $data ) {

        if ( isset( $_POST['danka_image_convert_all_images'] ) ) {
            $is_allowed = true;
        } else {
            $is_allowed = get_option( 'danka_image_autoconvert' ) === 'on';
        }

        if ( $is_allowed === false ) {
            return $data;
        }

        // Nouveau chemin de fichier
        $file_parts = explode( '.', $data['file'] );
        unset( $file_parts[count( $file_parts ) - 1] );
        $new_file = implode( $file_parts ) . '.webp';

        // Nouvelle URL
        $url_parts = explode( '.', $data['url'] );
        unset( $url_parts[count( $url_parts ) - 1] );
        $new_url = implode( $url_parts ) . '.webp';

        $has_new_image = false;

        switch ( $data['type'] ) {
            
            case 'image/jpeg':
                $image = imagecreatefromjpeg( $data['file'] );
                $has_new_image = imagewebp( $image, $new_file, 80 );
                imagedestroy( $image );
                break;

            case 'image/png':
                $image = imagecreatefrompng( $data['file'] );
                imagepalettetotruecolor( $image );
                imagealphablending( $image, true );
                imagesavealpha( $image, true );
                $has_new_image = imagewebp( $image, $new_file, 80 );
                imagedestroy( $image );
                break;
        }

        if ( $has_new_image ) {
            unlink( $data['file'] );
            $data['file'] = $new_file;
            $data['url'] = $new_url;
            $data['type'] = 'image/webp';
        }
        
        return $data;
    }

    public static function convert_images( $ids ) {

        global $wpdb;

        $temp_folder_dir = wp_upload_dir()['basedir'] . '/temp';
        $temp_folder_url = wp_upload_dir()['baseurl'] . '/temp';

        // créer le fichier temporaire
        if ( ! is_dir( $temp_folder_dir ) ) {
            mkdir( $temp_folder_dir );
        }

        $new_urls = array();

        foreach ( $ids as $id ) {
            $old_id = $id;
            $path = get_attached_file( $old_id );
            $temp_path = $temp_folder_dir . '/' . basename( $path );
            $temp_uri = $temp_folder_url . '/' . basename( $path );

            $sql = $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE '_thumbnail_id' AND meta_value LIKE %s", $old_id );
            $posts_with_this_thumbnail = $wpdb->get_results( $sql );

            // déplacer l'ancien fichier
            rename( $path, $temp_path );

            // supprimer le média
            wp_delete_attachment( $old_id );

            // créer un nouveau média
            $new_id = self::wp_insert_attachment_from_url( $temp_uri );
            
            // supprimer l'ancien fichier
            unlink( $temp_path );

            // changer le nouvel id avec le nouveau
            if ( $new_id ) {
                $wpdb->update(
                    $wpdb->prefix . 'postmeta',
                    array( 'post_id' => $old_id ),
                    array( 'post_id' => $new_id )
                );
            
                $wpdb->update(
                    $wpdb->prefix . 'posts',
                    array( 'ID' => $old_id ),
                    array( 'ID' => $new_id )
                );

                $new_urls[] = wp_get_attachment_url( $new_id );
            }

            // remettre les thumbnails
            foreach ( $posts_with_this_thumbnail as $p ) {
                add_post_meta( $p->post_id, '_thumbnail_id', $old_id );
            }
        }

        // supprimer le dossier temporaire
        if ( file_exists( $temp_folder_dir ) && is_readable( $temp_folder_dir ) && count( scandir( $temp_folder_dir ) ) == 2 ) {
            rmdir( $temp_folder_dir );
        }

        return $new_urls;
    }

    public static function wp_insert_attachment_from_url( $url, $parent_post_id = null ) {

        if ( ! class_exists( 'WP_Http' ) ) {
            require_once ABSPATH . WPINC . '/class-http.php';
        }
    
        $http = new WP_Http();
        $response = $http->request( $url );
        if ( 200 !== $response['response']['code'] ) {
            return false;
        }
    
        $upload = wp_upload_bits( basename( $url ), null, $response['body'] );
        if ( ! empty( $upload['error'] ) ) {
            return false;
        }
    
        $file_path        = $upload['file'];
        $file_name        = basename( $file_path );
        $file_type        = wp_check_filetype( $file_name, null );
        $attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
        $wp_upload_dir    = wp_upload_dir();
    
        $post_info = array(
            'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
            'post_mime_type' => $file_type['type'],
            'post_title'     => $attachment_title,
            'post_content'   => '',
            'post_status'    => 'inherit',
        );
    
        // Create the attachment.
        $attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );
    
        // Include image.php.
        require_once ABSPATH . 'wp-admin/includes/image.php';
    
        // Generate the attachment metadata.
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
    
        // Assign metadata to attachment.
        wp_update_attachment_metadata( $attach_id, $attach_data );
    
        return $attach_id;
    }

    public static function async_count_all_images_can_be_converted() {

        $data = array();
        $query = new WP_Query( array(
            'post_type'         => 'attachment',
            'post_status'	    => 'inherit',
            'post_mime_type'    => array( 'image/jpeg', 'image/png' ),
            'posts_per_page'    => -1,
        ) );

        if ( $query->posts ) {
            foreach ( $query->posts as $p ) {
                $data[] = $p->ID;
            }
        }

        echo json_encode( $data );
        wp_die();
    }
    
    
    public static function async_convert_images() {

        $images_id = isset( $_POST['images'] ) ? json_decode( $_POST['images'], true ) : null;

        if ( $images_id ) {
            $_POST['convert_all_images'] = true;
            $new_urls = self::convert_images( $images_id );

            echo json_encode( $new_urls );
            wp_die();
        }

        echo json_encode( false );
        wp_die();
    }
}