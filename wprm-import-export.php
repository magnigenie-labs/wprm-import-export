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
 * Version:           1.0.3
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
 * Helper to get the upload directory path for templates.
 */
function wprm_get_upload_path( $sub = '' ) {
	$upload_dir = wp_upload_dir();
	$dir = path_join( $upload_dir['basedir'], 'wprm-templates' );
	if ( ! empty( $sub ) ) {
		$dir = path_join( $dir, $sub );
	}
	if ( ! file_exists( $dir ) ) {
		wp_mkdir_p( $dir );
	}
	return $dir;
}

/**
 * Helper to get the upload directory URL for templates.
 */
function wprm_get_upload_url( $sub = '' ) {
	$upload_dir = wp_upload_dir();
	$url = $upload_dir['baseurl'] . '/wprm-templates';
	if ( ! empty( $sub ) ) {
		$url .= '/' . $sub;
	}
	return $url;
}

/**
 * The code that runs during plugin activation.
 */
function activate_wprm_import_export() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'wprm_import_export_data';

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`demoname` varchar(255) NOT NULL,
	`filetype` varchar(50) NOT NULL,
	`thumbnail` varchar(255) NOT NULL,
	`filename` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	// Seed default templates
	wprm_seed_default_templates();
}

/**
 * Seeds default templates and copies files to the uploads directory.
 */
function wprm_seed_default_templates() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'wprm_import_export_data';

	$src_demo_dir = path_join( WPRM_IMP_EXP_DIR, 'admin/demo' );
	$src_thumb_dir = path_join( WPRM_IMP_EXP_DIR, 'admin/thumbnail' );

	$dst_demo_dir = wprm_get_upload_path( 'demo' );
	$dst_thumb_dir = wprm_get_upload_path( 'thumbnail' );

	$default_templates = array(
		array(
			'demoname'  => 'Free Demo 1',
			'filetype'  => 'Free',
			'thumbnail' => 'demo1.png',
			'filename'  => 'freedemo1.json',
		),
		array(
			'demoname'  => 'Pro Demo 1',
			'filetype'  => 'Pro',
			'thumbnail' => 'demo2.png',
			'filename'  => 'wprmenu-settings-export-01-13-2025-12-00-15.json',
		),
		array(
			'demoname'  => 'Pro Demo 2',
			'filetype'  => 'Pro',
			'thumbnail' => 'demo3.png',
			'filename'  => 'wprmenu-settings-export-01-15-2025-06-09-49.json',
		),
		array(
			'demoname'  => 'Pro Demo 3',
			'filetype'  => 'Pro',
			'thumbnail' => 'demo4.png',
			'filename'  => 'wprmenu-settings-export-01-15-2025-06-21-50.json',
		),
		array(
			'demoname'  => 'Pro Demo 4',
			'filetype'  => 'Pro',
			'thumbnail' => 'demo5.png',
			'filename'  => 'wprmenu-settings-export-01-15-2025-06-41-05.json',
		),
	);

	foreach ( $default_templates as $tpl ) {
		$src_demo_file = path_join( $src_demo_dir, $tpl['filename'] );
		$src_thumb_file = path_join( $src_thumb_dir, $tpl['thumbnail'] );

		$dst_demo_file = path_join( $dst_demo_dir, $tpl['filename'] );
		$dst_thumb_file = path_join( $dst_thumb_dir, $tpl['thumbnail'] );

		if ( file_exists( $src_demo_file ) && ! file_exists( $dst_demo_file ) ) {
			copy( $src_demo_file, $dst_demo_file );
		}
		if ( file_exists( $src_thumb_file ) && ! file_exists( $dst_thumb_file ) ) {
			copy( $src_thumb_file, $dst_thumb_file );
		}

		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE filename = %s", $tpl['filename'] ) );
		if ( ! $exists ) {
			$wpdb->insert( $table_name, array(
				'demoname'  => $tpl['demoname'],
				'filetype'  => $tpl['filetype'],
				'thumbnail' => $tpl['thumbnail'],
				'filename'  => $tpl['filename'],
			));
		}
	}
}
register_activation_hook( WPRM_IMP_EXP_FILE, 'activate_wprm_import_export' );

/**
 * Automatically check, create, and migrate/merge the database table from the old schema if present.
 */
function wprm_check_database_table() {
	global $wpdb;
	$new_table = $wpdb->prefix . 'wprm_import_export_data';
	$old_table = $wpdb->prefix . 'json_data';

	$new_table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $new_table ) ) === $new_table;

	if ( ! $new_table_exists ) {
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE IF NOT EXISTS $new_table (
		`id` bigint(20) NOT NULL AUTO_INCREMENT,
		`demoname` varchar(255) NOT NULL,
		`filetype` varchar(50) NOT NULL,
		`thumbnail` varchar(255) NOT NULL,
		`filename` varchar(255) NOT NULL,
		PRIMARY KEY (`id`)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	// Migrate/Merge entries from the old table if it exists
	$old_table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $old_table ) ) === $old_table;
	if ( $old_table_exists ) {
		$old_rows = $wpdb->get_results( "SELECT * FROM $old_table" );
		if ( ! empty( $old_rows ) ) {
			$src_demo_dir = path_join( WPRM_IMP_EXP_DIR, 'admin/demo' );
			$src_thumb_dir = path_join( WPRM_IMP_EXP_DIR, 'admin/thumbnail' );

			$dst_demo_dir = wprm_get_upload_path( 'demo' );
			$dst_thumb_dir = wprm_get_upload_path( 'thumbnail' );

			foreach ( $old_rows as $row ) {
				$exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $new_table WHERE filename = %s", $row->filename ) );
				if ( ! $exists ) {
					$src_demo_file = path_join( $src_demo_dir, $row->filename );
					$src_thumb_file = path_join( $src_thumb_dir, $row->thumbnail );

					$dst_demo_file = path_join( $dst_demo_dir, $row->filename );
					$dst_thumb_file = path_join( $dst_thumb_dir, $row->thumbnail );

					if ( file_exists( $src_demo_file ) && ! file_exists( $dst_demo_file ) ) {
						copy( $src_demo_file, $dst_demo_file );
					}
					if ( file_exists( $src_thumb_file ) && ! file_exists( $dst_thumb_file ) ) {
						copy( $src_thumb_file, $dst_thumb_file );
					}

					$id_exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $new_table WHERE id = %d", $row->id ) );
					$insert_data = array(
						'demoname'  => $row->demoname,
						'filetype'  => $row->filetype,
						'thumbnail' => $row->thumbnail,
						'filename'  => $row->filename,
					);
					if ( ! $id_exists ) {
						$insert_data['id'] = $row->id;
					}
					$wpdb->insert( $new_table, $insert_data );
				}
			}
		}
	}

	// If the new table is still empty after migration/merge, seed default templates
	$new_count = $wpdb->get_var( "SELECT COUNT(*) FROM $new_table" );
	if ( intval( $new_count ) === 0 ) {
		wprm_seed_default_templates();
	}

	// Clean up duplicate Pro Demo 4 if it exists in the database
	$wpdb->query( "DELETE t1 FROM $new_table t1 INNER JOIN $new_table t2 WHERE t1.id > t2.id AND t1.filename = t2.filename AND t1.filename = 'wprmenu-settings-export-01-15-2025-06-41-05.json'" );
}
add_action( 'init', 'wprm_check_database_table' );

/**
 * Currently plugin version.
 * Start at version 1.0.0
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WPRM_IMPORT_EXPORT_VERSION', '1.0.3' );

require WPRM_IMP_EXP_DIR . 'includes/class-wprm-import-export.php';
require WPRM_IMP_EXP_DIR . 'admin/class-admin-wprm-import-export.php';
require WPRM_IMP_EXP_DIR . 'admin/functions.php';