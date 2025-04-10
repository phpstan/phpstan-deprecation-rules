<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Reflection\ClassConstantReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ConstantReflection;
use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Reflection\ExtendedPropertyReflection;
use PHPStan\Reflection\FunctionReflection;

class DeprecationHelper
{

	/** @var list<DeprecationProvider> */
	private array $providers;

	/**
	 * @param list<DeprecationProvider> $providers
	 */
	public function __construct(array $providers)
	{
		$this->providers = $providers;
	}

	/**
	 * @param ExtendedPropertyReflection|ExtendedMethodReflection|FunctionReflection|ClassReflection|ConstantReflection|ClassConstantReflection $reflection
	 */
	public function getDeprecation($reflection): ?Deprecation
	{
		foreach ($this->providers as $provider) {
			$deprecation = $provider->getDeprecation($reflection);
			if ($deprecation !== null) {
				return $deprecation;
			}
		}

		return null;
	}

}
