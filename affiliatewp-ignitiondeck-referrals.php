<?php
/*
Plugin Name: AffiliateWP - Ignitiondeck - Referrals
Description: Creates an AffiliateWP referral when a user completes an IgnitionDeck Commerce purchase
Version:     1.0.0
Author:      Wooninjas and yaronguez
Author URI:  http://www.wooninjas.com and https://trestian.com
License:     GPL2
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Core plugin class
 */
require plugin_dir_path( __FILE__ ) . 'classes/class-awp-idc.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_awp_idc() {

	$plugin = new AffiliateWP_IDC_Integration();
	$plugin->run();

}
run_awp_idc();