# MakerMaker core boundary

**FRAMEWORK CORE — DO NOT EDIT; update from playground releases.**

MakerMaker is a replaceable generator package. Every tracked file beneath `wp-content/plugins/makermaker/` is core-owned, including its generator, CLI commands, Galaxy integration, templates, runtime integration, package metadata, and tests.

## Ownership

| Location | Owner | Policy |
| --- | --- | --- |
| `plugins/makermaker/` | MakerMaker | Replace the whole directory during install, update, or rollback. |
| `plugins/<site>-app/` | Consumer project | Store project models, controllers, fields, policies, resources, views, routes, migrations, assets, and tests here. Additional bounded-domain sibling plugins are allowed. |

Generated plugins are project workspaces. DevArch and MakerMaker updates must not synchronize or rewrite them. MakerMaker may change future scaffolds; changing an existing generated plugin requires a separate, explicit, diff-producing migration command.

## Compatibility contract

The public contract is:

- the `wp makermaker` commands documented in [README.md](README.md);
- generation into direct sibling directories beneath `WP_PLUGIN_DIR`;
- strict collision refusal and no overwrite of an existing generated plugin;
- generated plugins owning and loading their own application code;
- the documented TypeRocket Pro v6 and Galaxy integration points.

Internal classes and template layout are not public extension APIs. Breaking a documented command, option, generated-plugin runtime contract, or non-overwrite guarantee requires a major release.

WordPress enforces the plugin's `Requires at least` and `Requires PHP` headers. MakerMaker checks for the loaded TypeRocket Pro v6 API and reports an actionable admin or WP-CLI error when it is unavailable. Existing generated plugins must continue to boot when MakerMaker is disabled; they retain their separate TypeRocket dependency.

## Prohibited edits

Do not put project models, controllers, resources, migrations, views, routes, assets, credentials, or generated domain code in this checkout. Core contributors may change these files only as MakerMaker framework work covered by its tests and release review.
