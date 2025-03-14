<?php

/**
 * Plugin Name:       AdvancedForm
 * Description:       Gérer les formulaires simplement
 * Version:           1.0
 * Author:            Martin Paul
 * Text Domain:       advancedform_test_abc
 * Domain Path:       /languages
 */


defined( 'ABSPATH' ) || die();


require __DIR__ . '/vendor/autoload.php';


add_action( 'init', function()
{
    load_plugin_textdomain( 'advancedform_test_abc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );


// create xlsx files (excel)
include 'class/SimpleXLSXGen.php';

// advancedform
include 'class/trait/AF_Form_FrontForm.php';
include 'class/trait/AF_Form_Ajax.php';
include 'class/trait/AF_Form_Data.php';
include 'class/trait/AF_Form_Restrictions.php';
include 'class/trait/AF_Form_Download.php';
include 'class/trait/AF_Form_Alerts.php';
include 'class/trait/AF_Form_reCAPTCHA.php';
include 'class/trait/AF_Form_ACF.php';
include 'class/trait/AF_Form_Files.php';
include 'class/trait/AF_Form_Save.php';
include 'class/AF_Form.php';


// Initialize
include 'include/init/index.php';

// Parts
include 'include/functions.php';
include 'include/shortcode.php';
include 'include/ajax.php';
include 'include/admin-columns--af-form.php';
include 'include/admin-columns--af-entry.php';
include 'include/save-post.php';

////
// var_dump( get_taxonomy( 'the_form' ) );
// die;
// http://wp-form.test/wp-admin/edit-tags.php?taxonomy=the_form&post_type=af_entry
// http://wp-form.test/wp-admin/edit.php?post_type=af_entry&the_form=en-famille
