=== WP Responsive Menu - Import/Export ===
Contributors: magnigenie
Tags: wp responsive menu, import, export, menu templates, settings migration
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.1.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

WP Responsive Menu - Import/Export plugin adds robust import and export capabilities to WP Responsive Menu. Back up, migrate, download, and manage your responsive menu configurations and templates effortlessly.

== Installation ==

1. Upload the `wprm-import-export` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Access the demo manager from the 'WPR Menu Demo' menu in your WordPress Dashboard.

== Changelog ==

= 1.1.7 =
* Added: Generated 15 fully matching, flat, custom color-blocked mockup thumbnail images for each of the Pro templates, aligning with their respective JSON styles.

= 1.1.6 =
* Improved: Updated all 15 Pro templates with rich Pro-only features (Google Web Fonts, Social link integrations, WooCommerce shopping blocks, and full-width containers) to make them look distinct and superior to the Free templates.
* Improved: Remapped all Pro thumbnails to the flat native premium Pro mockups, fully separating them from the Free template layout designs.

= 1.1.5 =
* Added: Assigned unique, high-quality, pre-styled mockup thumbnail images for each of the 15 Pro templates.

= 1.1.4 =
* Improved: Set showcase preview image scale to cover, stretching flat designs edge-to-edge to eliminate white backgrounds inside phone frames.

= 1.1.3 =
* Fixed: Added secure HTTPS remote API pre-populator to bypass HTTP redirects and caching issues, ensuring both Free and Pro templates sync instantly on the client site.

= 1.1.2 =
* Added: 15 brand new, modern JSON templates for the Pro version (Classic Corporate, Neon Cyberpunk, Elegance Premium, Retro Arcade, Eco Greenery, Coffee Shop, Tech Start, Art & Creative, Luxury Pearl, Modern Agency, WooCommerce Store, Travel Explorer, Fitness Peak, Foodie Delight, and Vintage Gold).
* Improved: Removed device borders and background details from the thumbnail images, leaving only flat, clean UI previews.
* Improved: Updated the showcase device frames dynamically to modern thin-bezel smartphone models with a Dynamic Island notch via admin CSS overrides.

= 1.1.1 =
* Fixed: Updated seeder logic to force-overwrite default template assets, resolving issues where old thumbnail images wouldn't update on the server.
* Fixed: Added option interceptor filters. During template import, the user's current menu icon configuration (animations, colors, sizes, positions) is preserved and the site's logo is fetched dynamically from the theme customizer.

= 1.1.0 =
* Improved: Created a single mobile screen mockup for all the 5 new templates (Sunset Minimalist, Ocean Breeze, Forest Pine, Royal Amethyst, and Charcoal Sleek) to show the menu interface realistically without background clutter.
* Fixed: Improved contrast and readability of the Sunset Minimalist template by setting the default menu link text to dark terracotta brown so it stands out cleanly on the light peach background.

= 1.0.9 =
* Fixed: Added CSS overrides to force all template showcase thumbnails to fit completely within their boxes on the core Import page instead of being zoomed in and cut off.

= 1.0.8 =
* Added: Custom, unique thumbnail images matching the actual style and color schemes for each of the 5 new Free templates.
* Fixed: Added automatic seeder synchronization. Existing template entries in the database are now dynamically updated when default thumbnail mappings are updated.

= 1.0.7 =
* Fixed: Added transient cache-busting logic. When admins visit the menu demo manager or import page, the local WordPress template cache transient (`wprm_api_demo_items_list`) is automatically cleared. This ensures new server-side templates show up immediately without a 24-hour cache delay.

= 1.0.6 =
* Added: 5 new gorgeously styled menu templates for the Free version: Sunset Minimalist, Ocean Breeze, Forest Pine, Royal Amethyst, and Charcoal Sleek.
* Improved: Made sure that the new templates only use properties fully supported by the Free version of the plugin.

= 1.0.5 =
* Improved: Created a premium card layout for the templates dashboard with hover animations, rounded corners, clean flex layouts, and color-coded buttons.
* Fixed: Enqueued scripts and styles now use the dynamic plugin version constant to prevent browser caching of old JS/CSS files, resolving issues where button click handlers weren't firing.

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
