=== WP Responsive Menu - Import/Export ===
Contributors: magnigenie
Tags: wp responsive menu, import, export, menu templates, settings migration
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

WP Responsive Menu - Import/Export plugin adds robust import and export capabilities to WP Responsive Menu. Back up, migrate, download, and manage your responsive menu configurations and templates effortlessly.

== Installation ==

1. Upload the `wprm-import-export` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Access the demo manager from the 'WPR Menu Demo' menu in your WordPress Dashboard.

== Changelog ==

= 1.0.4 =
* Added: Delete Template button in the admin dashboard. This allows administrators to easily delete custom, duplicate, or unwanted templates securely from both the database and the uploads folder.

= 1.0.3 =
* Fixed: Cleaned up the duplicate "Pro Demo 4" template database row from the database dynamically if it was created.

= 1.0.2 =
* Fixed: Added robust database migration and merge logic to automatically move any pre-existing custom templates (such as "Big Shopping" and "Windom Academy") from the old `json_data` table to the new `wprm_import_export_data` table while preserving their original IDs.
* Fixed: Automatically copies files of migrated templates from the plugin folder to the new uploads directory.
* Fixed: Added a self-healing DB check on init to automatically set up the database table.

= 1.0.1 =
* Added: Feature to download template JSON settings files directly from the dashboard.
* Added: Automatic database seeding of default templates on plugin activation.
* Added: Nonce verification and strict permission checks to protect against CSRF and privilege escalation.
* Fixed: Replaced unsafe direct file uploads with secure `wp_handle_upload()` to prevent RCE (Remote Code Execution) vulnerabilities.
* Fixed: Relocated uploads to the standard WordPress uploads folder (`wp-content/uploads/wprm-templates/`) to prevent custom templates from being deleted during plugin updates.
* Fixed: Replaced local loopback HTTP calls with high-performance direct file operations.
* Fixed: Renamed the custom database table to `wprm_import_export_data` to prevent naming collisions.
* Improved: Escaped all admin display variables to prevent Stored XSS.

= 1.0.0 =
* Initial version released
