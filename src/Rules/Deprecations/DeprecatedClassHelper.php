<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use function sprintf;

class DeprecatedClassHelper
{

	private ReflectionProvider $reflectionProvider;

	private DeprecationHelper $deprecationHelper;

	public function __construct(ReflectionProvider $reflectionProvider, DeprecationHelper $deprecationHelper)
	{
		$this->reflectionProvider = $reflectionProvider;
		$this->deprecationHelper = $deprecationHelper;
	}

	public function getClassDeprecationDescription(ClassReflection $class): string
	{
		$deprecation = $this->deprecationHelper->getDeprecation($class);
		if ($deprecation === null) {
			return '.';
		}

		$description = $deprecation->getDescription();
		if ($description === null) {
			return '.';
		}

		return sprintf(":\n%s", $description);
	}

	/**
	 * @param string[] $referencedClasses
	 * @return ClassReflection[]
	 */
	public function filterDeprecatedClasses(array $referencedClasses): array
	{
		$deprecatedClasses = [];
		foreach ($referencedClasses as $referencedClass) {
			try {
				$class = $this->reflectionProvider->getClass($referencedClass);
			} catch (ClassNotFoundException $e) {
				continue;
			}

			if ($this->deprecationHelper->getDeprecation($class) === null) {
				continue;
			}

			$deprecatedClasses[] = $class;
		}

		return $deprecatedClasses;
	}

}
