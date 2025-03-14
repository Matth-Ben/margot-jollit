<?php

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

$af_acf_is_active = is_plugin_active('advanced-custom-fields/acf.php') || is_plugin_active('advanced-custom-fields-pro/acf.php');

define('AF_ACF_IS_ACTIVE', $af_acf_is_active);