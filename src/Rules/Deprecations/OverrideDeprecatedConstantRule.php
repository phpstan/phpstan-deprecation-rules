<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function sprintf;

/**
 * @implements Rule<ClassConst>
 */
class OverrideDeprecatedConstantRule implements Rule
{

	/** @var DeprecatedScopeHelper */
	private $deprecatedScopeHelper;

	public function __construct(DeprecatedScopeHelper $deprecatedScopeHelper)
	{
		$this->deprecatedScopeHelper = $deprecatedScopeHelper;
	}

	public function getNodeType(): string
	{
		return ClassConst::class;
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

		$name = (string) $node->consts[0]->name;

		foreach ($parents as $parent) {
			if (!$parent->hasConstant($name)) {
				continue;
			}

			$parentConst = $parent->getConstant($name);

			if (!$parentConst->isDeprecated()->yes()) {
				return [];
			}

			return [RuleErrorBuilder::message(sprintf(
				'Class %s overrides deprecated const %s of class %s.',
				$class->getName(),
				$name,
				$parent->getName()
			))->identifier('constant.deprecated')->build()];
		}

		return [];
	}

}
