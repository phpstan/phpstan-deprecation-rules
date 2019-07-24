<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Broker\Broker;
use PHPStan\Reflection\ClassReflection;

class DeprecatedClassHelper
{

	/** @var Broker */
	private $broker;

	public function __construct(Broker $broker)
	{
		$this->broker = $broker;
	}

	public function getClassType(ClassReflection $class): string
	{
		if ($class->isInterface()) {
			return 'interface';
		}

		return 'class';
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
				$class = $this->broker->getClass($referencedClass);
			} catch (\PHPStan\Broker\ClassNotFoundException $e) {
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
