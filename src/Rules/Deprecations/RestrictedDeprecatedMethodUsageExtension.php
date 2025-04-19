<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Rules\RestrictedUsage\RestrictedMethodUsageExtension;
use PHPStan\Rules\RestrictedUsage\RestrictedUsage;
use function sprintf;
use function strtolower;

class RestrictedDeprecatedMethodUsageExtension implements RestrictedMethodUsageExtension
{

	private DeprecatedScopeHelper $deprecatedScopeHelper;

	public function __construct(DeprecatedScopeHelper $deprecatedScopeHelper)
	{
		$this->deprecatedScopeHelper = $deprecatedScopeHelper;
	}

	public function isRestrictedMethodUsage(
		ExtendedMethodReflection $methodReflection,
		Scope $scope
	): ?RestrictedUsage
	{
		if ($this->deprecatedScopeHelper->isScopeDeprecated($scope)) {
			return null;
		}

		if (!$methodReflection->isDeprecated()->yes()) {
			return null;
		}

		$description = $methodReflection->getDeprecatedDescription();
		if ($description === null) {
			return RestrictedUsage::create(
				sprintf(
					'Call to deprecated method %s() of %s %s.',
					$methodReflection->getName(),
					strtolower($methodReflection->getDeclaringClass()->getClassTypeDescription()),
					$methodReflection->getDeclaringClass()->getName(),
				),
				'method.deprecated',
			);
		}

		return RestrictedUsage::create(
			sprintf(
				"Call to deprecated method %s() of %s %s:\n%s",
				$methodReflection->getName(),
				strtolower($methodReflection->getDeclaringClass()->getClassTypeDescription()),
				$methodReflection->getDeclaringClass()->getName(),
				$description,
			),
			'method.deprecated',
		);
	}

}
