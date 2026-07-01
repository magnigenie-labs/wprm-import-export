<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://restropress.com/
 * @since             1.0.0
 * @package           WPRM_Import_Export
 *
 * @wordpress-plugin
 * Plugin Name:       WP Responsive Menu - Import/Export
 * Plugin URI:        wprm_import_export
 * Description:       This Plugin Will Add Import/Export Functionality to WP Responsive Menu.
 * Version:           1.0.0
 * Author:            Magnigenie
 * Author URI:        https://restropress.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wprm_import_export
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WPRM_IMP_EXP_FILE' ) ) {
	define( 'WPRM_IMP_EXP_FILE', __FILE__ );
}

if ( ! defined( 'WPRM_IMP_EXP_DIR' ) ) {
	define( 'WPRM_IMP_EXP_DIR', plugin_dir_path( WPRM_IMP_EXP_FILE ) );
}

if ( ! defined( 'WPRM_IMP_EXP_URL' ) ) {
	define( 'WPRM_IMP_EXP_URL', plugin_dir_url( WPRM_IMP_EXP_FILE ) );
}
	
/**
 * The code that runs during plugin activation.
 */
function activate_wprm_import_export() {

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'json_data';

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	`id` int(50) NOT NULL AUTO_INCREMENT,
	`demoname` varchar(50) NOT NULL,
	`filetype` varchar(255) NOT NULL,
	`thumbnail` varchar(255) NOT NULL,
	`filename` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
register_activation_hook( WPRM_IMP_EXP_FILE, 'activate_wprm_import_export' );

/**
 * Currently plugin version.
 * Start at version 1.0.0
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WPRM_IMPORT_EXPORT_VERSION', '1.0.0' );

require WPRM_IMP_EXP_DIR . 'includes/class-wprm-import-export.php';
require WPRM_IMP_EXP_DIR . 'admin/class-admin-wprm-import-export.php';
require WPRM_IMP_EXP_DIR . 'admin/functions.php';