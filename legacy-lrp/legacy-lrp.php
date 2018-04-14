<?php

/*
Plugin Name: Legacy LRP Plugin
Plugin URI: http://www.legacylrp.co.uk
Description: Plugin to allow the mangement of data associated with the Legacy LRP system
Version: 1.0.0
Author: Richard Jeremiah
Author URI: http://www.binaryplus.co.uk
*/
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
} // end if

require_once(plugin_dir_path( __FILE__ ) .'warband-manager/WarbandManager.php');
require_once(plugin_dir_path( __FILE__ ) .'warband-manager/WarbandForm.php');


add_action('template_loaded', array('WarbandManager', 'get_instance'));
add_action('init', WarbandForm::save_warband_submission());
require_once( plugin_dir_path( __FILE__ ) . 'legacy-lrp-page-template-loader.php' );
add_action( 'plugins_loaded', array( 'Legacy_LRP_Page_Template_Loader', 'get_instance' ) );