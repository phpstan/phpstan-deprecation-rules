<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ConstantReflection;
use PHPStan\Reflection\EnumCaseReflection;
use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Reflection\ExtendedPropertyReflection;
use PHPStan\Reflection\FunctionReflection;

class DefaultDeprecationProvider implements DeprecationProvider
{

	/**
	 * @param ExtendedPropertyReflection|ExtendedMethodReflection|FunctionReflection|ClassReflection|ConstantReflection|EnumCaseReflection $reflection
	 */
	public function getDeprecation($reflection): ?Deprecation
	{
		if ($reflection instanceof ClassReflection) {
			return $reflection->isDeprecated()
				? Deprecation::create()->withDescription($reflection->getDeprecatedDescription())
				: null;
		}

		return $reflection->isDeprecated()->yes()
			? Deprecation::create()->withDescription($reflection->getDeprecatedDescription())
			: null;
	}

}
