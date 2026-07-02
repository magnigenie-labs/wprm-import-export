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
 * Version:           1.2.1
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
	update_option( 'wprm_import_export_db_version', WPRM_IMPORT_EXPORT_VERSION );
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
			'demoname'  => 'Sunset Minimalist',
			'filetype'  => 'Free',
			'thumbnail' => 'sunset-minimalist.png',
			'filename'  => 'sunset-minimalist.json',
		),
		array(
			'demoname'  => 'Ocean Breeze',
			'filetype'  => 'Free',
			'thumbnail' => 'ocean-breeze.png',
			'filename'  => 'ocean-breeze.json',
		),
		array(
			'demoname'  => 'Forest Pine',
			'filetype'  => 'Free',
			'thumbnail' => 'forest-pine.png',
			'filename'  => 'forest-pine.json',
		),
		array(
			'demoname'  => 'Royal Amethyst',
			'filetype'  => 'Free',
			'thumbnail' => 'royal-amethyst.png',
			'filename'  => 'royal-amethyst.json',
		),
		array(
			'demoname'  => 'Charcoal Sleek',
			'filetype'  => 'Free',
			'thumbnail' => 'charcoal-sleek.png',
			'filename'  => 'charcoal-sleek.json',
		),
		array(
			'demoname'  => 'Classic Corporate',
			'filetype'  => 'Pro',
			'thumbnail' => 'pro-classic-corporate.png',
			'filename'  => 'pro-classic-corporate.json',
		),
		array(
			'demoname'  => 'Neon Cyberpunk',
			'filetype'  => 'Pro',
			'thumbnail' => 'pro-neon-cyberpunk.png',
			'filename'  => 'pro-neon-cyberpunk.json',
		),
		array(
			'demoname'  => 'Elegance Premium',
			'filetype'  => 'Pro',
			'thumbnail' => 'pro-elegance-premium.png',
			'filename'  => 'pro-elegance-premium.json',
		),
		array(
			'demoname'  => 'Retro Arcade',
			'filetype'  => 'Pro',
			'thumbnail' => 'pro-retro-arcade.png',
			'filename'  => 'pro-retro-arcade.json',
		),
		array(
			'demoname'  => 'Eco Greenery',
			'filetype'  => 'Pro',
			'thumbnail' => 'pro-eco-greenery.png',
			'filename'  => 'pro-eco-greenery.json',
		),
		array(
			'demoname'  => 'Coffee Shop',
			'filetype'  => 'Pro',
			'thumbnail' => 'pro-coffee-shop.png',
			'filename'  => 'pro-coffee-shop.json',
		),
		array(
			'demoname'  => 'Tech Start',
			'filetype'  => 'Pro',
			'thumbnail' => 'pro-tech-start.png',
			'filename'  => 'pro-tech-start.json',
		),
		array(
			'demoname'  => 'Art & Creative',
			'filetype'  => 'Pro',
			'thumbnail' => 'pro-art-creative.png',
			'filename'  => 'pro-art-creative.json',
		),
		array(
			'demoname'  => 'Luxury Pearl',
			'filetype'  => 'Pro',
			'thumbnail' => 'pro-luxury-pearl.png',
			'filename'  => 'pro-luxury-pearl.json',
		),
		array(
			'demoname'  => 'Modern Agency',
			'filetype'  => 'Pro',
			'thumbnail' => 'pro-modern-agency.png',
			'filename'  => 'pro-modern-agency.json',
		),
		array(
			'demoname'  => 'WooCommerce Store',
			'filetype'  => 'Pro',
			'thumbnail' => 'pro-woocommerce-store.png',
			'filename'  => 'pro-woocommerce-store.json',
		),
		array(
			'demoname'  => 'Travel Explorer',
			'filetype'  => 'Pro',
			'thumbnail' => 'pro-travel-explorer.png',
			'filename'  => 'pro-travel-explorer.json',
		),
		array(
			'demoname'  => 'Fitness Peak',
			'filetype'  => 'Pro',
			'thumbnail' => 'pro-fitness-peak.png',
			'filename'  => 'pro-fitness-peak.json',
		),
		array(
			'demoname'  => 'Foodie Delight',
			'filetype'  => 'Pro',
			'thumbnail' => 'pro-foodie-delight.png',
			'filename'  => 'pro-foodie-delight.json',
		),
		array(
			'demoname'  => 'Vintage Gold',
			'filetype'  => 'Pro',
			'thumbnail' => 'pro-vintage-gold.png',
			'filename'  => 'pro-vintage-gold.json',
		),
	);

	foreach ( $default_templates as $tpl ) {
		$src_demo_file = path_join( $src_demo_dir, $tpl['filename'] );
		$src_thumb_file = path_join( $src_thumb_dir, $tpl['thumbnail'] );

		$dst_demo_file = path_join( $dst_demo_dir, $tpl['filename'] );
		$dst_thumb_file = path_join( $dst_thumb_dir, $tpl['thumbnail'] );

		// Always copy files to ensure they are up to date!
		if ( file_exists( $src_demo_file ) ) {
			copy( $src_demo_file, $dst_demo_file );
		}
		if ( file_exists( $src_thumb_file ) ) {
			copy( $src_thumb_file, $dst_thumb_file );
		}

		$exists = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE filename = %s", $tpl['filename'] ) );
		if ( ! $exists ) {
			$wpdb->insert( $table_name, array(
				'demoname'  => $tpl['demoname'],
				'filetype'  => $tpl['filetype'],
				'thumbnail' => $tpl['thumbnail'],
				'filename'  => $tpl['filename'],
			));
		} else {
			// Update the database with the new default thumbnail if it matches one of our default templates
			if ( $exists->thumbnail !== $tpl['thumbnail'] ) {
				$wpdb->update( $table_name, array( 'thumbnail' => $tpl['thumbnail'] ), array( 'id' => $exists->id ) );
			}
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

	// Seed default templates only on version upgrade or clean install
	$installed_ver = get_option( 'wprm_import_export_db_version' );
	if ( $installed_ver !== WPRM_IMPORT_EXPORT_VERSION ) {
		wprm_seed_default_templates();
		update_option( 'wprm_import_export_db_version', WPRM_IMPORT_EXPORT_VERSION );
	}

	// Clean up duplicate Pro Demo 4 if it exists in the database
	$wpdb->query( "DELETE t1 FROM $new_table t1 INNER JOIN $new_table t2 WHERE t1.id > t2.id AND t1.filename = t2.filename AND t1.filename = 'wprmenu-settings-export-01-15-2025-06-41-05.json'" );
}
add_action( 'init', 'wprm_check_database_table' );

/**
 * Force fetch and cache the secure HTTPS API response for the template list
 * when the admin is on the menu demo manager or import page.
 */
function wprm_force_update_demo_list() {
	if ( is_admin() && isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'wpr-menu-demo', 'wprmenu-demo-import' ), true ) ) {
		$response = wp_remote_get( 'https://demo.magnigenie.com/wp-json/wprmenu-server/v1' );
		if ( ! is_wp_error( $response ) && isset( $response['body'] ) ) {
			$items = json_decode( $response['body'] );
			if ( $items ) {
				set_transient( 'wprm_api_demo_items_list', $items, 60 * 60 * 24 );
			}
		}
	}
}
add_action( 'admin_init', 'wprm_force_update_demo_list' );

/**
 * Hook into option update to preserve user's current menu icon settings
 * and dynamically fetch/use the website's custom logo during template import.
 */
function wprm_preserve_settings_during_import( $value, $old_value, $option ) {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_POST['action'] ) && $_POST['action'] === 'wprmenu_import_settings' ) {
		if ( is_array( $value ) && is_array( $old_value ) ) {
			// 1. Preserve all menu icon settings (layout, colors, animations, size, classes)
			$icon_keys = array(
				'menu_icon_animation',
				'custom_menu_bg_color',
				'menu_icon_color',
				'menu_icon_hover_color',
				'custom_menu_top',
				'custom_menu_left',
				'menu_symbol_pos',
				'menu_icon_type',
				'custom_menu_font_size',
				'custom_menu_icon_top',
				'menu_icon',
				'menu_close_icon'
			);

			foreach ( $icon_keys as $key ) {
				if ( isset( $old_value[$key] ) ) {
					$value[$key] = $old_value[$key];
				}
			}

			// 2. Get the logo dynamically as per the website logo (via Theme Customizer)
			$custom_logo_id = get_theme_mod( 'custom_logo' );
			if ( $custom_logo_id ) {
				$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
				if ( $logo_url ) {
					$value['bar_logo'] = $logo_url;
				}
			} elseif ( isset( $old_value['bar_logo'] ) && ! empty( $old_value['bar_logo'] ) ) {
				$value['bar_logo'] = $old_value['bar_logo'];
			}
		}
	}
	return $value;
}
add_filter( 'pre_update_option_wprmenu_options', 'wprm_preserve_settings_during_import', 10, 3 );

/**
 * Currently plugin version.
 * Start at version 1.0.0
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WPRM_IMPORT_EXPORT_VERSION', '1.2.1' );

require WPRM_IMP_EXP_DIR . 'includes/class-wprm-import-export.php';
require WPRM_IMP_EXP_DIR . 'admin/class-admin-wprm-import-export.php';
require WPRM_IMP_EXP_DIR . 'admin/functions.php';