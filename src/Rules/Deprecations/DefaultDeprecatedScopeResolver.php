<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Analyser\Scope;

final class DefaultDeprecatedScopeResolver implements DeprecatedScopeResolver
{

	private DeprecationHelper $deprecationHelper;

	public function __construct(DeprecationHelper $deprecationHelper)
	{
		$this->deprecationHelper = $deprecationHelper;
	}

	public function isScopeDeprecated(Scope $scope): bool
	{
		$class = $scope->getClassReflection();
		if ($class !== null && $this->deprecationHelper->getDeprecation($class) !== null) {
			return true;
		}

		$trait = $scope->getTraitReflection();
		if ($trait !== null && $this->deprecationHelper->getDeprecation($trait) !== null) {
			return true;
		}

		$function = $scope->getFunction();
		if ($function !== null && $this->deprecationHelper->getDeprecation($function) !== null) {
			return true;
		}

		return false;
	}

}
