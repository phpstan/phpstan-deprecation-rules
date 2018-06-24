# Rules for detecting usage of deprecated classes, methods, properties, constants and traits.

[![Build Status](https://travis-ci.org/phpstan/phpstan-deprecation-rules.svg)](https://travis-ci.org/phpstan/phpstan-deprecation-rules)
[![Latest Stable Version](https://poser.pugx.org/phpstan/phpstan-deprecation-rules/v/stable)](https://packagist.org/packages/phpstan/phpstan-deprecation-rules)
[![License](https://poser.pugx.org/phpstan/phpstan-deprecation-rules/license)](https://packagist.org/packages/phpstan/phpstan-deprecation-rules)

## Usage

To use these rules, require it in [Composer](https://getcomposer.org/):

```
composer require --dev phpstan/phpstan-deprecation-rules
```

And include rules.neon in your project's PHPStan config:

```
includes:
	- vendor/phpstan/phpstan-deprecation-rules/rules.neon
```
