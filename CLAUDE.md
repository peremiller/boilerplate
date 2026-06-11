# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Laravel 5.8 boilerplate** — a full-stack PHP web application framework with a Vue.js + Bootstrap frontend. It serves as a starter template for database-driven web applications.

## Commands

### PHP / Laravel

```bash
# Install dependencies
composer install

# Generate app key (required after fresh clone)
php artisan key:generate

# Run database migrations
php artisan migrate

# Start development server
php artisan serve

# Run all tests
php vendor/bin/phpunit

# Run a single test file
php vendor/bin/phpunit tests/Feature/ExampleTest.php

# Run a specific test method
php vendor/bin/phpunit --filter testBasicTest

# Run only one test suite
php vendor/bin/phpunit --testsuite Feature
php vendor/bin/phpunit --testsuite Unit
```

### Frontend Assets (Laravel Mix / Webpack)

```bash
npm install

npm run dev          # One-time development build
npm run watch        # Watch for changes and rebuild
npm run hot          # Hot module replacement dev server
npm run prod         # Minified production build
```

## Architecture

### Request Lifecycle

All HTTP requests enter via `public/index.php` → Laravel bootstraps from `bootstrap/app.php` → routes are resolved via `routes/web.php` (browser) or `routes/api.php` (API, rate-limited, auth-gated) → controllers in `app/Http/Controllers/` → Blade views in `resources/views/`.

### Service Providers

The application wires up functionality through providers in `app/Providers/`:
- `RouteServiceProvider` — loads and prefixes web/api routes, configures route model binding
- `AuthServiceProvider` — registers authorization policies
- `EventServiceProvider` — maps events to listeners
- `BroadcastServiceProvider` — configures Pusher/broadcast channels

### Middleware Pipeline

Defined in `app/Http/Kernel.php`:
- **Global:** maintenance mode check, POST size validation, string trimming, proxy trust
- **`web` group:** session, cookies, CSRF, auth state sharing
- **`api` group:** stateless rate throttling (`throttle:60,1`), route model binding

### Frontend

`resources/js/app.js` bootstraps Vue 2 and registers components. `resources/js/bootstrap.js` sets up jQuery, Bootstrap, Axios (with CSRF token header), and Lodash on `window`. SCSS lives in `resources/sass/` and is compiled alongside JS via `webpack.mix.js` → `public/css/` and `public/js/`.

### Testing

- `tests/Unit/` — pure logic tests (no HTTP, no DB by default)
- `tests/Feature/` — full HTTP tests via `$this->get('/')` etc.
- Base class `tests/TestCase.php` uses the `CreatesApplication` trait
- PHPUnit config is in `phpunit.xml`; test environment overrides cache (`array`), queue (`sync`), and session (`array`) drivers automatically

## Environment Setup

Copy `.env.example` to `.env` and configure at minimum:
- `APP_KEY` — generate with `php artisan key:generate`
- `DB_*` — MySQL connection (default: `127.0.0.1:3306`, database `homestead`)
- `APP_URL` — used by asset helpers and URL generation
