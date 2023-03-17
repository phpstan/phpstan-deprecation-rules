<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function sprintf;

/**
 * @implements Rule<Property>
 */
class OverrideDeprecatedPropertyRule implements Rule
{

	/** @var DeprecatedScopeHelper */
	private $deprecatedScopeHelper;

	public function __construct(DeprecatedScopeHelper $deprecatedScopeHelper)
	{
		$this->deprecatedScopeHelper = $deprecatedScopeHelper;
	}

	public function getNodeType(): string
	{
		return Property::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($this->deprecatedScopeHelper->isScopeDeprecated($scope)) {
			return [];
		}

		if (!$scope->isInClass()) {
			return [];
		}

		if ($node->isPrivate()) {
			return [];
		}

		$class = $scope->getClassReflection();

		$parents = $class->getParents();

		$propertyName = (string) $node->props[0]->name;

		$property = $class->getProperty($propertyName, $scope);

		if ($property->isDeprecated()->no()) {
			return [];
		}

		foreach ($parents as $parent) {
			if (!$parent->hasProperty($propertyName)) {
				continue;
			}

			$parentProperty = $parent->getProperty($propertyName, $scope);

			if (!$parentProperty->isDeprecated()->yes()) {
				return [];
			}

			return [RuleErrorBuilder::message(sprintf(
				'Class %s overrides deprecated property %s of class %s.',
				$class->getName(),
				$propertyName,
				$parent->getName()
			))->identifier('property.deprecated')->build()];
		}

		return [];
	}

}
