# Rules for detecting usage of deprecated classes, methods, properties, constants and traits.

[![Build](https://github.com/phpstan/phpstan-deprecation-rules/workflows/Build/badge.svg)](https://github.com/phpstan/phpstan-deprecation-rules/actions)
[![Latest Stable Version](https://poser.pugx.org/phpstan/phpstan-deprecation-rules/v/stable)](https://packagist.org/packages/phpstan/phpstan-deprecation-rules)
[![License](https://poser.pugx.org/phpstan/phpstan-deprecation-rules/license)](https://packagist.org/packages/phpstan/phpstan-deprecation-rules)

* [PHPStan](https://phpstan.org/)

## Installation

To use this extension, require it in [Composer](https://getcomposer.org/):

```
composer require --dev phpstan/phpstan-deprecation-rules
```

If you also install [phpstan/extension-installer](https://github.com/phpstan/extension-installer) then you're all set!

<details>
  <summary>Manual installation</summary>

If you don't want to use `phpstan/extension-installer`, include rules.neon in your project's PHPStan config:

```
includes:
    - vendor/phpstan/phpstan-deprecation-rules/rules.neon
```
</details>

## Compatibility with deprecation mechanisms

| Mechanism                                                                             | Emits runtime deprecation | Supported by PHPStan?    |
|---------------------------------------------------------------------------------------|---------------------------|--------------------------|
| `@deprecated` PHPDoc                                                                  | No                        | Yes — via this extension |
| `#[\Deprecated]`, [since PHP 8.4](https://www.php.net/manual/en/class.deprecated.php) | Yes                       | Yes — via this extension |
| `trigger_deprecation()`                                                               | Yes                       | No                       |

## Deprecating code you don't own

This extension emits deprecation warnings on code, which uses properties/functions/methods/classes which are annotated as `@deprecated`.

In case you don't own the code which you want to be considered deprecated, use [PHPStan Stub Files](https://phpstan.org/user-guide/stub-files) to declare deprecations for vendor files like:
```
/** @deprecated */
class ThirdPartyClass {}
```

## `#[\Deprecated]` attribute (Since PHP 8.4)

PHP 8.4 introduced a native [`#[\Deprecated]`](https://wiki.php.net/rfc/deprecated_attribute) attribute that triggers a runtime deprecation notice when a symbol is called.

**This extension detects it** — no `@deprecated` PHPDoc annotation is required alongside it:

```php
#[\Deprecated('Will be removed in 2.0, use constructor injection instead.')]
public function __construct(...) {}
```

PHPStan will report call sites with the `method.deprecated` identifier, including the attribute's description string.

> [!NOTE]
> Detection of `#[\Deprecated]` is provided by this extension (`phpstan-deprecation-rules`), **not** by PHPStan core. Removing this extension silences those errors entirely.

## Custom deprecation markers

You can implement extensions to support even e.g. custom `#[MyDeprecated]` attribute.  [Learn more](https://phpstan.org/developing-extensions/custom-deprecations).


## Custom deprecated scopes

Usage of deprecated code is not reported in code that is also deprecated:

```php
/** @deprecated */
function doFoo(): void
{
    // not reported:
    anotherDeprecatedFunction();
}
```

If you have [a different way](https://github.com/phpstan/phpstan-deprecation-rules/issues/64) of marking code that calls deprecated symbols on purpose and you don't want these calls to be reported either, you can write an extension by implementing the [`DeprecatedScopeResolver`](https://github.com/phpstan/phpstan-deprecation-rules/blob/1.1.x/src/Rules/Deprecations/DeprecatedScopeResolver.php) interface.

For example if you mark your PHPUnit tests that test deprecated code with `@group legacy`, you can implement the extension this way:

```php
class GroupLegacyScopeResolver implements DeprecatedScopeResolver
{

	public function isScopeDeprecated(Scope $scope): bool
	{
		$function = $scope->getFunction();
		return $function !== null
			&& $function->getDocComment() !== null
			&& strpos($function->getDocComment(), '@group legacy') !== false;
	}

}
```

And register it in your [configuration file](https://phpstan.org/config-reference):

```neon
services:
	-
		class: GroupLegacyScopeResolver
		tags:
			- phpstan.deprecations.deprecatedScopeResolver
```

[Learn more about Scope](https://phpstan.org/developing-extensions/scope), a core concept for implementing custom PHPStan extensions.
