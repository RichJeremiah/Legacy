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

require_once(plugin_dir_path( __FILE__ ) .'warband-manager/WarbandForm.php');
add_action('init', 'WarbandForm::save_warband_submission');
add_action('wp_ajax_check_warband_name', array('WarbandForm', 'check_warband_name'));
add_action('wp_ajax_nopriv_check_warband_name', array('WarbandForm', 'check_warband_name'));

require_once(plugin_dir_path( __FILE__ ) .'warband-manager/WarbandManager.php');
add_action('wp_ajax_set_user_approved', 'WarbandManager::set_user_approved');
add_action('wp_ajax_set_user_rejected', 'WarbandManager::set_user_rejected');
add_action('wp_ajax_set_warband_membership_public', 'WarbandManager::set_warband_member_public');
add_action('wp_ajax_set_warband_membership_private', 'WarbandManager::set_warband_member_private');

require_once( plugin_dir_path( __FILE__ ) . 'legacy-lrp-page-template-loader.php' );
add_action( 'plugins_loaded', array( 'Legacy_LRP_Page_Template_Loader', 'get_instance' ) );


