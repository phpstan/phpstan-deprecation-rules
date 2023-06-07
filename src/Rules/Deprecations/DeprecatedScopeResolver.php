<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Analyser\Scope;

/** @api */
interface DeprecatedScopeResolver
{

	public function isScopeDeprecated(Scope $scope): bool;

}
