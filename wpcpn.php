<?php
/**
 * WordPress Central Posts Network
 *
 *
 * @package   WPPCN
 * @author    Nícholas André <nicholas@iotecnologia.com.br>
 * @license   GPL-2.0+
 * @link      https://github.com/nicholasio/wp-central-posts-network
 *
 * @wordpress-plugin
 * Plugin Name: 	WordPress Central Posts Network
 * Plugin URI: 		https://github.com/nicholasio/wp-central-posts-network
 * Description: 		A Plugin that let you choose any posts on any site in the network to display on the main site.
 * Version: 		0.5
 * Author: 		Nícholas André
 * Author URI:		http://nicholasandre.com.br
 * Text Domain:		wpcpn
 * License: 		GPL-2.0+
 * License URI:		http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: 	/languages
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('WPCPN_CAN_ADD_POST', 1);
define('WPCPN_CANT_ADD_POST', 0);
define('WPCPN_IS_MAIN_SITE', get_current_blog_id() == 1 );

require_once( plugin_dir_path( __FILE__ ) . 'includes/trait-singleton.php');
require_once( plugin_dir_path( __FILE__ ) . 'includes/WPCPN_Fragment_Cache.php');
require_once( plugin_dir_path( __FILE__ ) . 'admin/models/class-wpcpn-admin-model.php' );
require_once( plugin_dir_path( __FILE__ ) . 'admin/models/class-wpcpn-requests.php');
require_once( plugin_dir_path( __FILE__ ) . 'admin/class-post-selector.php');
require_once( plugin_dir_path( __FILE__ ) . 'includes/network_queries.php' );

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
require_once( plugin_dir_path( __FILE__ ) . 'public/includes/functions.php');
require_once( plugin_dir_path( __FILE__ ) . 'public/class-wpcpn.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'WPCPN', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WPCPN', 'deactivate' ) );


add_action( 'plugins_loaded', array( 'WPCPN', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/**
 * The administrative panel must only be show for super admin users running on the main site
 */
if ( is_admin() ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-wpcpn-admin.php' );
	add_action( 'after_setup_theme', array( 'WPCPN_Admin', 'get_instance' ) );
}
