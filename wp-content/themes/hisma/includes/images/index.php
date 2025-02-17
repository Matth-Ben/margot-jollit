<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'DANKA_SETTINGS_IMAGES_SLUG', 'danka-settings-images' );

require_once __DIR__ . '/DankaImageWebp.php';

DankaImageWebp::init();
