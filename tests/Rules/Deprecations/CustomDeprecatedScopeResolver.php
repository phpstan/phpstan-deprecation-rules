<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Analyser\Scope;
use function strpos;

class CustomDeprecatedScopeResolver implements DeprecatedScopeResolver
{

	public function isScopeDeprecated(Scope $scope): bool
	{
		$function = $scope->getFunction();
		return $function !== null
			&& $function->getDocComment() !== null
			&& strpos($function->getDocComment(), '@group legacy') !== false;
	}

}
