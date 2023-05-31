<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\MissingPropertyFromReflectionException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Type;
use function sprintf;
use function strtolower;

/**
 * @implements Rule<StaticPropertyFetch>
 */
class AccessDeprecatedStaticPropertyRule implements Rule
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	/** @var RuleLevelHelper */
	private $ruleLevelHelper;

	/** @var DeprecatedScopeHelper */
	private $deprecatedScopeHelper;

	public function __construct(ReflectionProvider $reflectionProvider, RuleLevelHelper $ruleLevelHelper, DeprecatedScopeHelper $deprecatedScopeHelper)
	{
		$this->reflectionProvider = $reflectionProvider;
		$this->ruleLevelHelper = $ruleLevelHelper;
		$this->deprecatedScopeHelper = $deprecatedScopeHelper;
	}

	public function getNodeType(): string
	{
		return StaticPropertyFetch::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($this->deprecatedScopeHelper->isScopeDeprecated($scope)) {
			return [];
		}

		if (!$node->name instanceof Identifier) {
			return [];
		}

		$propertyName = $node->name->name;
		$referencedClasses = [];

		if ($node->class instanceof Name) {
			$referencedClasses[] = $scope->resolveName($node->class);
		} else {
			$classTypeResult = $this->ruleLevelHelper->findTypeToCheck(
				$scope,
				$node->class,
				'', // We don't care about the error message
				static function (Type $type) use ($propertyName): bool {
					return $type->canAccessProperties()->yes() && $type->hasProperty($propertyName)->yes();
				}
			);

			if ($classTypeResult->getType() instanceof ErrorType) {
				return [];
			}

			$referencedClasses = $classTypeResult->getReferencedClasses();
		}

		foreach ($referencedClasses as $referencedClass) {
			try {
				$class = $this->reflectionProvider->getClass($referencedClass);
				$property = $class->getProperty($propertyName, $scope);
			} catch (ClassNotFoundException $e) {
				continue;
			} catch (MissingPropertyFromReflectionException $e) {
				continue;
			}

			if ($property->isDeprecated()->yes()) {
				$description = $property->getDeprecatedDescription();
				if ($description === null) {
					return [
						RuleErrorBuilder::message(sprintf(
							'Access to deprecated static property $%s of %s %s.',
							$propertyName,
							strtolower($property->getDeclaringClass()->getClassTypeDescription()),
							$property->getDeclaringClass()->getName()
						))->identifier('staticProperty.deprecated')->build(),
					];
				}

				return [
					RuleErrorBuilder::message(sprintf(
						"Access to deprecated static property $%s of %s %s:\n%s",
						$propertyName,
						strtolower($property->getDeclaringClass()->getClassTypeDescription()),
						$property->getDeclaringClass()->getName(),
						$description
					))->identifier('staticProperty.deprecated')->build(),
				];
			}
		}

		return [];
	}

}
