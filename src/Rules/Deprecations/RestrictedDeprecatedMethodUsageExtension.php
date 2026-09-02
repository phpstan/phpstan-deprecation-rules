<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
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

		if ($methodReflection->getDeclaringClass()->isDeprecated()) {
			$class = $methodReflection->getDeclaringClass();
			$classDescription = $class->getDeprecatedDescription();
			if ($classDescription === null) {
				return RestrictedUsage::create(
					sprintf(
						'Call to method %s() of deprecated %s %s.',
						$methodReflection->getName(),
						strtolower($methodReflection->getDeclaringClass()->getClassTypeDescription()),
						$methodReflection->getDeclaringClass()->getName(),
					),
					sprintf(
						'%s.deprecated%s',
						$methodReflection->isStatic() ? 'staticMethod' : 'method',
						$methodReflection->getDeclaringClass()->getClassTypeDescription(),
					),
				);
			}

			return RestrictedUsage::create(
				sprintf(
					"Call to method %s() of deprecated %s %s:\n%s",
					$methodReflection->getName(),
					strtolower($methodReflection->getDeclaringClass()->getClassTypeDescription()),
					$methodReflection->getDeclaringClass()->getName(),
					$classDescription,
				),
				sprintf(
					'%s.deprecated%s',
					$methodReflection->isStatic() ? 'staticMethod' : 'method',
					$methodReflection->getDeclaringClass()->getClassTypeDescription(),
				),
			);
		}

		$deprecatedDeclaringTrait = $this->findDeprecatedDeclaringTrait(
			$methodReflection->getDeclaringClass(),
			$methodReflection->getName(),
		);
		if ($deprecatedDeclaringTrait !== null) {
			$traitDescription = $deprecatedDeclaringTrait->getDeprecatedDescription();
			if ($traitDescription === null) {
				return RestrictedUsage::create(
					sprintf(
						'Call to method %s() of deprecated trait %s.',
						$methodReflection->getName(),
						$deprecatedDeclaringTrait->getName(),
					),
					sprintf('%s.deprecatedTrait', $methodReflection->isStatic() ? 'staticMethod' : 'method'),
				);
			}

			return RestrictedUsage::create(
				sprintf(
					"Call to method %s() of deprecated trait %s:\n%s",
					$methodReflection->getName(),
					$deprecatedDeclaringTrait->getName(),
					$traitDescription,
				),
				sprintf('%s.deprecatedTrait', $methodReflection->isStatic() ? 'staticMethod' : 'method'),
			);
		}

		if (!$methodReflection->isDeprecated()->yes()) {
			return null;
		}

		$description = $methodReflection->getDeprecatedDescription();
		if (strtolower($methodReflection->getName()) === '__tostring') {
			if ($description === null) {
				return RestrictedUsage::create(
					sprintf(
						'Casting class %s to string is deprecated.',
						$methodReflection->getDeclaringClass()->getName(),
					),
					'class.toStringDeprecated',
				);
			}

			return RestrictedUsage::create(
				sprintf(
					"Casting class %s to string is deprecated.:\n%s",
					$methodReflection->getDeclaringClass()->getName(),
					$description,
				),
				'class.toStringDeprecated',
			);
		}

		if ($description === null) {
			return RestrictedUsage::create(
				sprintf(
					'Call to deprecated method %s() of %s %s.',
					$methodReflection->getName(),
					strtolower($methodReflection->getDeclaringClass()->getClassTypeDescription()),
					$methodReflection->getDeclaringClass()->getName(),
				),
				sprintf('%s.deprecated', $methodReflection->isStatic() ? 'staticMethod' : 'method'),
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
			sprintf('%s.deprecated', $methodReflection->isStatic() ? 'staticMethod' : 'method'),
		);
	}

	private function findDeprecatedDeclaringTrait(ClassReflection $declaringClass, string $methodName): ?ClassReflection
	{
		foreach ($declaringClass->getTraits() as $trait) {
			if (!$trait->hasNativeMethod($methodName) || !$trait->isDeprecated()) {
				continue;
			}

			return $trait;
		}

		return null;
	}

}
