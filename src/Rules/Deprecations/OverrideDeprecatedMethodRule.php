<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function sprintf;

/**
 * @implements Rule<ClassMethod>
 */
class OverrideDeprecatedMethodRule implements Rule
{

	/** @var DeprecatedScopeHelper */
	private $deprecatedScopeHelper;

	public function __construct(DeprecatedScopeHelper $deprecatedScopeHelper)
	{
		$this->deprecatedScopeHelper = $deprecatedScopeHelper;
	}

	public function getNodeType(): string
	{
		return ClassMethod::class;
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

		$ancestors = $class->getAncestors();

		$methodName = (string) $node->name;

		$method = $class->getMethod($methodName, $scope);

		if ($method->isDeprecated()->no()) {
			return [];
		}

		foreach ($ancestors as $ancestor) {
			if ($ancestor === $class) {
				continue;
			}

			if (!$ancestor->hasMethod($methodName)) {
				continue;
			}

			$ancestorMethod = $ancestor->getMethod($methodName, $scope);

			if (!$ancestorMethod->isDeprecated()->yes()) {
				return [];
			}

			return [RuleErrorBuilder::message(sprintf(
				'Class %s overrides deprecated method %s of %s %s.',
				$class->getName(),
				$methodName,
				$ancestor->isInterface() ? 'interface' : 'class',
				$ancestor->getName()
			))->identifier('method.deprecated')->build()];
		}

		return [];
	}

}
