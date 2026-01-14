# CLAUDE.md

Guidance for Claude Code working with this repository.

## Overview

**Maker Maker** - Thin-client WordPress plugin on TypeRocket Pro v6 for business logic management. Core scaffolding from `mxcro/makermaker-core`, this plugin provides domain-specific MVC.

## Quick Reference

```bash
# Dependencies
composer install && npm install

# Testing
composer test              # All tests
composer test:unit         # Unit only
composer test:ci           # With 85% coverage

# Assets
npm run dev                # Build once
npm run watch              # Watch mode
npm run prod               # Production

# Galaxy CLI
php galaxy make:crud Product --template=standard
```

## Constants

```php
MAKERMAKER_PLUGIN_DIR       // Plugin directory path
MAKERMAKER_PLUGIN_URL       // Plugin URL
GLOBAL_WPDB_PREFIX          // WordPress table prefix
```

## Key Entry Points

- `makermaker.php` → hooks `typerocket_loaded` (priority 9)
- `app/MakermakerTypeRocketPlugin.php` → main plugin class
- `database/migrations/` → **DATA TRUTH** for schema

## REST API

Zero-config via ReflectiveRestWrapper: `/tr-api/rest/{resource}/{id?}/actions/{action?}`

Supports: `?search=`, `?field=value`, sorting, pagination

---

## Deep Dive Documentation

For detailed patterns and implementation guides, see `.planning/codebase/`:

| Document                                              | Contents                                             |
| ----------------------------------------------------- | ---------------------------------------------------- |
| [ARCHITECTURE.md](.planning/codebase/ARCHITECTURE.md) | Layers, data flow, request lifecycle, entry points   |
| [CONVENTIONS.md](.planning/codebase/CONVENTIONS.md)   | Naming, code style, controller/model/policy patterns |
| [STRUCTURE.md](.planning/codebase/STRUCTURE.md)       | Directory layout, file locations, where to add code  |
| [TESTING.md](.planning/codebase/TESTING.md)           | Pest setup, mocking, coverage, test patterns         |
| [STACK.md](.planning/codebase/STACK.md)               | Languages, frameworks, dependencies                  |
| [INTEGRATIONS.md](.planning/codebase/INTEGRATIONS.md) | Database, REST API, email, logging                   |
| [CONCERNS.md](.planning/codebase/CONCERNS.md)         | Tech debt, known bugs, security, performance         |

### Feature-Specific Docs

| Document                                                                           | Contents               |
| ---------------------------------------------------------------------------------- | ---------------------- |
| [REFLECTIVE_REST_API.md](.planning/codebase/docs/REFLECTIVE_REST_API.md)           | REST wrapper internals |
| [REFLECTIVE_ACTIONS_GUIDE.md](.planning/codebase/docs/REFLECTIVE_ACTIONS_GUIDE.md) | Custom REST actions    |
| [Model.md](.planning/codebase/docs/Model.md)                                       | Model reference        |

---

## Communication Rules

- Extreme concision. Sacrifice grammar for the sake of concision.
- No verbose READMEs, summaries, trees, diagrams unless explicitly requested
- End plans with concise unresolved questions

## Version Control - Git

1. Commits must be surgical but not overly granular
2. Commit messages must be one liners
3. No self insertion
4. DO not commit docs (.md) files
