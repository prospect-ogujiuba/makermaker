# MakerMaker

MakerMaker is a GPL-3.0-or-later must-use WordPress plugin that generates structured custom plugins from the **official scaffold in an installed TypeRocket Pro v6 package**.

It does not bundle TypeRocket Pro, license keys, Galaxy executables, or copied global `app`, `config`, `resources`, and `routes` trees. TypeRocket Pro v6 remains a separately installed private dependency.

## Requirements

- WordPress 6.5+
- PHP 8.2+
- TypeRocket Pro v6 installed and loaded
- Write access to `WP_PLUGIN_DIR`

MakerMaker resolves infrastructure dynamically:

- its own repository from `MAKERMAKER_PATH`/`WPMU_PLUGIN_DIR`;
- TypeRocket from the runtime `TYPEROCKET_PATH` constant;
- the official scaffold below TypeRocket's installed Professional package;
- generated plugins under WordPress's `WP_PLUGIN_DIR`.

No absolute site, container, MU-plugin, or TypeRocket repository path is embedded.

## MU-plugin installation

Clone the repository into the MU-plugin directory and copy its entry loader to the MU root:

```bash
git clone git@github.com:prospect-ogujiuba/makermaker.git \
  wp-content/mu-plugins/makermaker
cp wp-content/mu-plugins/makermaker/makermaker.php \
  wp-content/mu-plugins/makermaker.php
```

DevArch performs these steps through a `github-mu-plugin makermaker` profile directive.

## Admin workflow

Open **Tools → MakerMaker**, enter the plugin name, slug, PHP namespace, and Composer vendor, then generate. Activation is opt-in.

## WP-CLI workflow

```bash
wp makermaker create client-portal \
  --name="Client Portal" \
  --namespace='Maker\\ClientPortal' \
  --vendor=prospect

# Explicitly activate after generation:
wp makermaker create inventory-tools \
  --name="Inventory Tools" \
  --namespace='Maker\\InventoryTools' \
  --activate
```

Generated plugins remain in `wp-content/plugins/<slug>` and use TypeRocket's `BasePlugin` lifecycle, migrations, routes, policies, settings, assets, and views.

## Development

```bash
composer test
composer lint
```

The tests use a synthetic official-shaped fixture; no TypeRocket Professional source is committed.

## Security model

- Admin generation requires `install_plugins` and a valid nonce.
- Slugs and PHP namespaces are strictly validated.
- Destination paths are contained under `WP_PLUGIN_DIR`.
- Existing plugins are never overwritten.
- Official-template symbolic links are rejected.
- Generation publishes atomically from a staging directory and cleans failures.
- No shell command or GitHub API is invoked during generation.
