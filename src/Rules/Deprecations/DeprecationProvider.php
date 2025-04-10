<?php

namespace PHPStan\Rules\Deprecations;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ConstantReflection;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\PropertyReflection;

interface DeprecationProvider
{

	/**
	 * @param PropertyReflection|MethodReflection|FunctionReflection|ClassReflection|ConstantReflection $reflection
	 */
	public function getDeprecation($reflection): ?Deprecation;

}
