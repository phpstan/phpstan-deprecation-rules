<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\ClassNameUsageLocation;
use PHPStan\Rules\RestrictedUsage\RestrictedClassNameUsageExtension;
use PHPStan\Rules\RestrictedUsage\RestrictedUsage;
use function rtrim;
use function sprintf;
use function strtolower;

class RestrictedDeprecatedClassNameUsageExtension implements RestrictedClassNameUsageExtension
{

	private DeprecatedScopeHelper $deprecatedScopeHelper;

	private ReflectionProvider $reflectionProvider;

	private bool $bleedingEdge;

	public function __construct(
		DeprecatedScopeHelper $deprecatedScopeHelper,
		ReflectionProvider $reflectionProvider,
		bool $bleedingEdge
	)
	{
		$this->deprecatedScopeHelper = $deprecatedScopeHelper;
		$this->reflectionProvider = $reflectionProvider;
		$this->bleedingEdge = $bleedingEdge;
	}

	public function isRestrictedClassNameUsage(
		ClassReflection $classReflection,
		Scope $scope,
		ClassNameUsageLocation $location
	): ?RestrictedUsage
	{
		if (!$classReflection->isDeprecated()) {
			return null;
		}

		if ($this->deprecatedScopeHelper->isScopeDeprecated($scope)) {
			return null;
		}

		$currentClassName = $location->getCurrentClassName();
		if ($currentClassName !== null && $this->reflectionProvider->hasClass($currentClassName)) {
			$currentClassReflection = $this->reflectionProvider->getClass($currentClassName);
			if ($currentClassReflection->isDeprecated()) {
				return null;
			}
		}

		$identifierPart = sprintf('deprecated%s', $classReflection->getClassTypeDescription());
		$defaultUsage = RestrictedUsage::create(
			$location->createMessage(
				sprintf('deprecated %s %s', strtolower($classReflection->getClassTypeDescription()), $classReflection->getDisplayName()),
			),
			$location->createIdentifier($identifierPart),
		);

		$description = $classReflection->getDeprecatedDescription();

		if ($location->value === ClassNameUsageLocation::CLASS_IMPLEMENTS) {
			if ($currentClassName === null) {
				if ($description === null) {
					return RestrictedUsage::create(
						sprintf(
							'Anonymous class implements deprecated %s %s.',
							strtolower($classReflection->getClassTypeDescription()),
							$classReflection->getDisplayName(),
						),
						$location->createIdentifier($identifierPart),
					);
				}

				return RestrictedUsage::create(
					sprintf(
						"Anonymous class implements deprecated %s %s:\n%s",
						strtolower($classReflection->getClassTypeDescription()),
						$classReflection->getDisplayName(),
						$description,
					),
					$location->createIdentifier($identifierPart),
				);
			}

			if ($description !== null) {
				return RestrictedUsage::create(
					sprintf(
						"Class %s implements deprecated %s %s:\n%s",
						$currentClassName,
						strtolower($classReflection->getClassTypeDescription()),
						$classReflection->getDisplayName(),
						$description,
					),
					$location->createIdentifier($identifierPart),
				);
			}

			return $defaultUsage;
		}

		if ($location->value === ClassNameUsageLocation::CLASS_EXTENDS) {
			if ($currentClassName === null) {
				if ($description === null) {
					return RestrictedUsage::create(
						sprintf(
							'Anonymous class extends deprecated %s %s.',
							strtolower($classReflection->getClassTypeDescription()),
							$classReflection->getDisplayName(),
						),
						$location->createIdentifier($identifierPart),
					);
				}

				return RestrictedUsage::create(
					sprintf(
						"Anonymous class extends deprecated %s %s:\n%s",
						strtolower($classReflection->getClassTypeDescription()),
						$classReflection->getDisplayName(),
						$description,
					),
					$location->createIdentifier($identifierPart),
				);
			}

			if ($description !== null) {
				return RestrictedUsage::create(
					sprintf(
						"Class %s extends deprecated %s %s:\n%s",
						$currentClassName,
						strtolower($classReflection->getClassTypeDescription()),
						$classReflection->getDisplayName(),
						$description,
					),
					$location->createIdentifier($identifierPart),
				);
			}

			return $defaultUsage;
		}

		if ($location->value === ClassNameUsageLocation::INTERFACE_EXTENDS) {
			if ($description !== null) {
				return RestrictedUsage::create(
					sprintf(
						"Interface %s extends deprecated %s %s:\n%s",
						$currentClassName,
						strtolower($classReflection->getClassTypeDescription()),
						$classReflection->getDisplayName(),
						$description,
					),
					$location->createIdentifier($identifierPart),
				);
			}

			return $defaultUsage;
		}

		if ($location->value === ClassNameUsageLocation::INSTANTIATION) {
			if ($description !== null) {
				return RestrictedUsage::create(
					sprintf(
						"Instantiation of deprecated %s %s:\n%s",
						strtolower($classReflection->getClassTypeDescription()),
						$classReflection->getDisplayName(),
						$description,
					),
					$location->createIdentifier('deprecated'),
				);
			}

			return $defaultUsage;
		}

		if ($location->value === ClassNameUsageLocation::TRAIT_USE) {
			if ($description !== null) {
				return RestrictedUsage::create(
					sprintf(
						"Usage of deprecated %s %s in class %s:\n%s",
						strtolower($classReflection->getClassTypeDescription()),
						$classReflection->getDisplayName(),
						$currentClassName,
						$description,
					),
					$location->createIdentifier('deprecated'),
				);
			}

			return $defaultUsage;
		}

		if ($location->value === ClassNameUsageLocation::STATIC_METHOD_CALL) {
			$method = $location->getMethod();
			if ($method !== null) {
				if ($method->isDeprecated()->yes() || $method->getDeclaringClass()->isDeprecated()) {
					return null;
				}
			}

			return $defaultUsage;
		}

		if ($location->value === ClassNameUsageLocation::STATIC_PROPERTY_ACCESS) {
			$property = $location->getProperty();
			if ($property !== null) {
				if ($property->isDeprecated()->yes() || $property->getDeclaringClass()->isDeprecated()) {
					return null;
				}
			}

			return $defaultUsage;
		}

		if ($location->value === ClassNameUsageLocation::CLASS_CONSTANT_ACCESS) {
			$constant = $location->getClassConstant();
			if ($constant !== null) {
				if ($constant->isDeprecated()->yes() || $constant->getDeclaringClass()->isDeprecated()) {
					return null;
				}
			}

			return $defaultUsage;
		}

		if ($location->value === ClassNameUsageLocation::PARAMETER_TYPE || $location->value === ClassNameUsageLocation::RETURN_TYPE) {
			$message = $location->createMessage(
				sprintf('deprecated %s %s', strtolower($classReflection->getClassTypeDescription()), $classReflection->getDisplayName()),
			);
			if ($classReflection->getDeprecatedDescription() !== null) {
				$message = rtrim($message, '.') . ":\n" . $classReflection->getDeprecatedDescription();
			}
			return RestrictedUsage::create(
				$message,
				$location->createIdentifier($identifierPart),
			);
		}

		if (!$this->bleedingEdge) {
			return null;
		}

		return $defaultUsage;
	}

}
