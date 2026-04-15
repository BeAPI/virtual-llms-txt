# Virtual llms.txt

A WordPress plugin that serves a **virtual [llms.txt](https://llmstxt.org/)** document: the body is stored in the database and delivered at your site’s canonical public URL (`/llms.txt`) as **`text/plain`**, with no physical file on disk.

---

## Why use this plugin?

**llms.txt** is often used to point AI agents and tools to the right resources on a site (documentation, policies, useful entry points, and so on). Managing that document from WordPress lets you:

- **Edit the text** without FTP or shipping a static file;
- **Scope content per site** (including multisite, where each site has its own option);
- **Keep a stable URL** that matches the usual `https://example.com/llms.txt` convention.

---

## How it works for site owners

1. After activation, open **Settings → Virtual llms.txt**.
2. Enter the **document body** as plain text (often Markdown-style lists of links and short descriptions).
3. Save. The document is available at the URL shown on that screen (typically `llms.txt` at the root of your site URL, depending on **Settings → General**).

### Additional options

- **Delete all plugin settings on deactivation**: when checked, the stored option is removed when the plugin is deactivated (otherwise data is kept if you reactivate later).

### HTTP methods

**GET** responses include the document body. **HEAD** returns the same status and `Content-Type: text/plain` headers without a body, which is handy for lightweight checks.

---

## Technical overview

| Piece | Role |
|--------|------|
| `virtual_llms_txt` option | Serialized array: `content`, `remove_settings_on_deactivate`. One option per site on multisite. |
| `Virtual_Llms_Txt\Front_Controller` | On `template_redirect`, checks whether the request matches the path for `home_url( '/llms.txt' )`; if so, sends headers, then outputs raw text. |
| `Virtual_Llms_Txt\Admin\Settings_Page` | Settings screen, sanitization (`sanitize_textarea_field` on content), **Settings** shortcut on the Plugins list. |
| Activation | Ensures the option exists with defaults when missing. |

Public output is **intentionally not HTML-escaped**: it is served as a text document, not HTML (expected behaviour for llms.txt).

---

## Requirements

- **WordPress** 6.0 or newer  
- **PHP** 8.0 or newer  

---

## Installation (production)

1. Upload the `virtual-llms-txt` folder to `wp-content/plugins/`.
2. Activate the plugin under **Plugins**.
3. Configure the document under **Settings → Virtual llms.txt**.

The root `readme.txt` follows the WordPress.org plugin directory format if you plan to publish there.

---

## Conflict with a real `llms.txt` file

If the web server serves a **static file** at that path **before** WordPress handles the request, that file wins. Remove or rename it on the server if you want this plugin to handle the URL.

---

## Development

### Tooling

| Tool | Purpose |
|------|---------|
| [Composer](https://getcomposer.org/) | PHP dev dependencies and PHPCS scripts |
| [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer) + [WPCS](https://github.com/WordPress/WordPress-Coding-Standards) | WordPress coding standards (see `phpcs.xml`) |
| [@wordpress/env](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) | Local WordPress via Docker |

### Common commands

```bash
# Dependencies
composer install
npm install

# Coding standards
composer cs
composer cbf

# Local WordPress
npm run wp-env:start
npm run wp-env:stop
```

`.wp-env.json` maps this repository to `wp-content/plugins/virtual-llms-txt` (including the **tests** instance), following the same pattern as other BeAPI projects that use `wp-env`.

---

## License

GPL-2.0-or-later — see the main plugin file headers and `readme.txt`.
