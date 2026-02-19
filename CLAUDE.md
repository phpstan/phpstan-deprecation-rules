# CLAUDE.md

## Project Overview

**phpstan/phpstan-deprecation-rules** is a PHPStan extension that detects usage of deprecated code. It reports errors when your code uses classes, methods, functions, properties, constants, traits, or INI options that are marked with the `@deprecated` PHPDoc annotation (or custom deprecation markers).

This is an official PHPStan extension, distributed as a Composer package (`phpstan/phpstan-deprecation-rules`). It is installed via Composer and integrated into PHPStan through the `rules.neon` configuration file (automatically when using `phpstan/extension-installer`).

## Repository Structure

```
src/
  DependencyInjection/
    LazyDeprecatedScopeResolverProvider.php  # Lazy DI provider for scope resolvers
  Rules/Deprecations/
    CallWithDeprecatedIniOptionRule.php       # Detects deprecated PHP INI options in ini_* calls
    DefaultDeprecatedScopeResolver.php        # Default scope resolver (skips deprecated scopes)
    DeprecatedScopeHelper.php                 # Aggregates scope resolvers
    DeprecatedScopeResolver.php               # Interface for custom scope resolvers (@api)
    FetchingDeprecatedConstRule.php            # Detects usage of deprecated constants
    RestrictedDeprecated*Extension.php         # Extensions for class names, methods, functions,
                                              #   properties, and class constants
tests/
  Rules/Deprecations/
    *Test.php                                 # PHPUnit test cases extending RuleTestCase
    data/                                     # Test fixture PHP files (definitions + usages)
  bootstrap.php                               # Autoloader bootstrap for tests
rules.neon                                    # Extension configuration (services + rules registered here)
```

## PHP Version Support

This repository supports **PHP 7.4+**. The `composer.json` `config.platform.php` is set to `7.4.6`.

Do not use language features unavailable in PHP 7.4 (e.g. enums, fibers, readonly properties, intersection types, first-class callable syntax, named arguments in internal function calls).

## Development Commands

All commands are defined in the `Makefile`:

- **`make check`** — runs all checks (lint, coding standard, tests, PHPStan)
- **`make tests`** — runs PHPUnit tests (`vendor/bin/phpunit`)
- **`make lint`** — runs PHP parallel lint on `src/` and `tests/`
- **`make cs`** — runs PHP_CodeSniffer with the `phpstan/build-cs` coding standard
- **`make cs-fix`** — auto-fixes coding standard violations
- **`make cs-install`** — clones and installs the `phpstan/build-cs` repository
- **`make phpstan`** — runs PHPStan self-analysis at level 8

Before running `make cs` or `make cs-fix`, you need to run `make cs-install` first to clone the coding standard repository.

## Testing

Tests use **PHPUnit 9.6** with `RuleTestCase` from PHPStan's testing framework. The test pattern is:

1. Each rule/extension has a corresponding `*Test.php` file in `tests/Rules/Deprecations/`.
2. Test fixture files live in `tests/Rules/Deprecations/data/` — typically a `*-definition.php` file (defining deprecated symbols) and a corresponding usage file.
3. Tests call `$this->analyse()` with the fixture file path and an array of expected errors (each being `[message, line]`).
4. Tests load `rules.neon` via `getAdditionalConfigFiles()`.

Run tests with:
```bash
make tests
```

## Static Analysis

The project analyses itself with PHPStan at **level 8** with bleeding edge enabled. Configuration is in `phpstan.neon`, which includes `rules.neon` and `phpstan-baseline.neon`.

Paths analysed: `src/` and `tests/` (excluding `tests/*/data/*`).

Run static analysis with:
```bash
make phpstan
```

## Coding Standard

Uses `phpstan/build-cs` (branch `2.x`) for PHP_CodeSniffer rules. Install it first, then run:
```bash
make cs-install
make cs
```

## Architecture

### How Rules Work

The extension registers rules and "restricted usage extensions" in `rules.neon`:

- **Restricted usage extensions** (implementing PHPStan's `Restricted*UsageExtension` interfaces) handle most deprecation checks: class names, methods, functions, properties, and class constants. These extensions check if the symbol being used is deprecated and if the calling scope is itself deprecated (in which case the usage is not reported).
- **`FetchingDeprecatedConstRule`** is a standalone rule for deprecated global constants.
- **`CallWithDeprecatedIniOptionRule`** detects calls to `ini_get`/`ini_set`/etc. with deprecated INI option names (bleeding edge only).

### Deprecated Scope Resolution

Usage of deprecated code inside code that is itself deprecated is not reported. This is handled by the `DeprecatedScopeHelper` which aggregates `DeprecatedScopeResolver` implementations. The `DefaultDeprecatedScopeResolver` checks the standard `@deprecated` annotation. Users can register custom resolvers via the `phpstan.deprecations.deprecatedScopeResolver` service tag.

### Extension Points

- **`DeprecatedScopeResolver`** interface — implement to define custom scopes where deprecated usage should be suppressed.
- **Custom deprecation markers** — PHPStan supports custom `#[Deprecated]` attributes and other markers via its deprecation extension point.

## CI

The GitHub Actions workflow (`.github/workflows/build.yml`) runs on pull requests and pushes to `2.0.x`:

- **Lint** — PHP parallel lint across PHP 7.4–8.4
- **Coding Standard** — PHP_CodeSniffer with `phpstan/build-cs`
- **Tests** — PHPUnit across PHP 7.4–8.4 with both lowest and highest dependency versions
- **Static Analysis** — PHPStan across PHP 7.4–8.4 with both lowest and highest dependency versions
- **Mutation Testing** — Infection (via `phpstan/build-infection`) on PHP 8.2–8.4, runs after tests and static analysis pass

## Branch Strategy

The main development branch is **`2.0.x`**. CI is configured to run on pushes to this branch.

## Code Style Notes

- Files use `declare(strict_types = 1)` (note the spaces around `=`).
- Indentation is tabs (size 4) for PHP, XML, and NEON files.
- YAML files use 2-space indentation.
- LF line endings, UTF-8 charset, trailing whitespace trimmed, final newline inserted.
