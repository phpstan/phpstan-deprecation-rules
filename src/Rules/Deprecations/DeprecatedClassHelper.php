<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use function sprintf;

class DeprecatedClassHelper
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;
	}

	public function getClassDeprecationDescription(ClassReflection $class): string
	{
		$description = $class->getDeprecatedDescription();
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

			if (!$class->isDeprecated()) {
				continue;
			}

			$deprecatedClasses[] = $class;
		}

		return $deprecatedClasses;
	}

}
