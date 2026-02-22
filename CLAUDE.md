# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel Dibi is a GUI database management tool for Laravel applications, packaged as a Composer library. It provides a web-based interface at `/dibi` with a Vue 3 SPA frontend and Laravel backend API. Supports MySQL (primary) and SQL Server.

## Common Commands

### Build
```bash
npm run dev          # Vite dev server
npm run build        # Production build (minified)
```

### Test
```bash
vendor/bin/phpunit                        # Run all tests
vendor/bin/phpunit --filter=TestClassName  # Run a single test class
```

### Lint / Code Style
```bash
npm run lint              # Check JS/Vue files with ESLint
npm run lint:fix          # Auto-fix ESLint issues
vendor/bin/php-cs-fixer fix   # Format PHP files
```

### Package Installation (in a Laravel app)
```bash
php artisan dibi:install     # First-time setup (publishes assets + service provider)
php artisan dibi:publish     # Update published assets
```

## Architecture

### Backend (PHP)

The package is auto-discovered via `DibiServiceProvider`. The `Dibi` class (`src/Dibi.php`) is the central facade handling configuration, auth gates, database connection management, and frontend script variable injection.

**Database Repository Pattern:** `DatabaseRepositoryFactory` creates driver-specific repositories (`MysqlDatabaseRepository`, `SqlsrvDatabaseRepository`) that extend `AbstractDatabaseRepository` implementing the `DatabaseRepository` interface. All database operations (listing tables, fetching rows, running SQL) go through this abstraction.

**Routes** (`routes/web.php`): All routes are prefixed with the configured path (default `/dibi`). Three API endpoints (POST) handle connection switching, SQL execution, and table row filtering. A catch-all GET route serves the SPA.

**Middleware:** `EnsureUserIsAuthorized` checks the authorization gate; `EnsureUpToDateAssets` verifies published assets match the package version.

### Frontend (Vue 3 SPA)

Built with Vite. Entry point: `resources/js/app.js`. Root component: `resources/js/App.vue`. Uses Vue Router 4 with three main views:

- **Dashboard** — table listing, connection selector
- **TableDetails** — data browser with pagination/filtering/sorting + structure tab
- **SqlQuery** — Monaco Editor for SQL with split-pane results

Components are globally registered in `resources/js/components.js`. Styling uses TailwindCSS 3 with the `@tailwindcss/forms` plugin. Monaco Editor provides SQL syntax highlighting.

Built assets output to `public/` and are served from `/vendor/dibi` path.

### Key Data Models

`Table`, `TableColumn`, `TableIndex`, and `InformationSchema` are plain PHP objects in `src/` representing database metadata. `DBObject` is their abstract base class.

## Code Style

- **PHP:** php-cs-fixer (config in `.php-cs-fixer.dist.php`)
- **JS/Vue:** ESLint with 4-space indent, semicolons, single quotes, trailing commas
- **Pre-commit hooks:** Husky + lint-staged automatically runs php-cs-fixer on PHP and eslint --fix on JS/Vue files
