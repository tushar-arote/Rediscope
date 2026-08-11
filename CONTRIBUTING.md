# Contributing to Rediscope

Thanks for considering a contribution! Bug reports, feature requests, and pull requests are all welcome.

## Reporting Bugs

Search [existing issues](https://github.com/tushar-arote/Rediscope/issues) first. If it's new, open an issue with:

- Your PHP, Laravel, and Rediscope versions
- Steps to reproduce
- What you expected vs. what happened

For security vulnerabilities, see [SECURITY.md](SECURITY.md) instead of opening a public issue.

## Development Setup

```bash
git clone https://github.com/tushar-arote/Rediscope.git
cd Rediscope
composer install
npm install
```

Frontend assets are served directly by the package (no publish step needed). If you're changing `resources/js` or `resources/sass`, rebuild with:

```bash
npm run watch   # or: npm run prod
```

## Running Tests

This package is tested with PHPUnit via Orchestra Testbench — no full Laravel app or Redis server needed for most tests:

```bash
composer install
vendor/bin/phpunit
```

If you're fixing a bug, please follow test-driven development: write a failing test that reproduces the bug first, confirm it fails, then implement the fix and confirm it passes.

## Submitting a Pull Request

1. Fork the repo and create a branch from `master`
2. Make your change, with tests covering it
3. Make sure `vendor/bin/phpunit` passes locally
4. Open a PR describing what changed and why

CI runs the full PHP/Laravel version matrix on every PR — make sure it's green before requesting review.

## Coding Style

Follow the conventions already used in the surrounding code (PSR-12 for PHP). Keep pull requests focused on a single change — separate unrelated fixes into separate PRs.
