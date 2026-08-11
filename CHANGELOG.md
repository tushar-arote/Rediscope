# Changelog

All notable changes to this project are documented in this file, based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

### Added
- `SECURITY.md`, `CONTRIBUTING.md`, issue/PR templates

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
