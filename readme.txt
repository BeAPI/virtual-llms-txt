=== Virtual llms.txt ===
Contributors: contributors
Tags: llms.txt, ai, plain-text, virtual-file, settings
Requires at least: 6.3
Tested up to: 6.8
Requires PHP: 8.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Serve a virtual llms.txt document from a per-site settings screen (plain text).

== Description ==

This plugin stores your llms.txt body in the WordPress database and serves it at your site's public URL ending in `llms.txt`, with the `text/plain` content type.

Each site in a multisite network has its own settings and document.

For background on the llms.txt convention, see the [llms.txt overview](https://llmstxt.org/).

== Installation ==

1. Upload the `virtual-llms-txt` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Settings → Virtual llms.txt** and edit the document body.

== Frequently Asked Questions ==

= Will a physical llms.txt file on the server conflict? =

If the web server serves a real file at that path before WordPress runs, that file will take precedence. Remove or rename the physical file if you want this plugin to handle the URL.

= Does this work with multisite? =

Yes. Options are stored per site; configure each site separately.

== Changelog ==

= 1.0.0 =
* Initial release.
