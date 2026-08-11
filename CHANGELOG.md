# Changelog

All notable changes to this project are documented in this file, based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

## [2.1.1] - 2026-08-11

### Added
- Dark/light theme toggle button in the header, persisted via `localStorage`

### Fixed
- **Critical**: dashboard rendered completely blank with no AJAX calls. `routes.js` and `app.js` used raw `require()` for `.vue` components, but the current Babel/webpack setup wraps their exports in an ES-module shape that only `import` unwraps — Vue Router and `Vue.component()` silently received the wrapper object instead of the component
- Connection dropdown listed a bogus `"options"` entry — `Rediscope::getConnections()` and a duplicated implementation in `RedisManagerController::connections()` both matched Redis client config keys (`options`, `clusters`) as if they were connections
- Deleting keys (individually or in bulk) silently did nothing — `bootstrap` was pinned to `^5.3.0` while the confirmation dialog used Bootstrap 4's jQuery `.modal()` API, which v5 removed, so the confirmation modal never opened
- Header read "Redis Manager" instead of "Rediscope"; connection dropdown and info button touched the header edge (both side effects of the same Bootstrap version mismatch)

### Changed
- Trimmed duplicate content from the README

## [2.1.0] - 2026-08-11

### Added
- `favicon.ico`, served via the same direct-asset route as the rest of the frontend
- `publishes()` registration for the config file, so `vendor:publish --tag=rediscope-config` actually works
- Logo and dashboard screenshot in the README
- `SECURITY.md`, `CONTRIBUTING.md`, `CHANGELOG.md`, issue/PR templates, `FUNDING.yml`

### Fixed
- `npm install` was unresolvable from a clean checkout (`laravel-mix`, `popper.js`, and `vue-json-pretty` were pinned to versions that either never existed or don't support Vue 2)
- `Rediscope::scan()` reported every key's type as `none` due to a key-prefix being applied twice before the `TYPE`/`TTL` lookup
- `Rediscope::instance()` only honored the requested Redis connection on its first call in a process, ignoring it on every later call
- `RedisManagerController::manager()` 500ing when the frontend sends the literal string `"null"` for the `conn` parameter

## [2.0.2] - 2026-08-11

### Fixed
- CSS assets served with a `Content-Type` that browsers refuse to apply as a stylesheet (content-sniffing detected CSS as `text/plain`)

## [2.0.1] - 2026-08-11

### Added
- Serve compiled frontend assets directly from the package (`/vendor/rediscope/*`), removing the need for a `npm install && npm run prod` step after `composer require`

### Fixed
- Path traversal prefix-check bypass in the asset controller

## [2.0.0] - 2026-08-11

### Added
- Support for PHP 8.1–8.4 and Laravel 8–12
- Full CI matrix across supported PHP/Laravel/Testbench combinations

## [1.0.x]

Legacy releases supporting PHP 7.2+ and Laravel 6–7.
