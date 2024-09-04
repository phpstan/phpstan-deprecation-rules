<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\Cast;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function sprintf;

/**
 * @implements Rule<Cast>
 */
class UsageOfDeprecatedCastRule implements Rule
{

	private DeprecatedScopeHelper $deprecatedScopeHelper;

	public function __construct(DeprecatedScopeHelper $deprecatedScopeHelper)
	{
		$this->deprecatedScopeHelper = $deprecatedScopeHelper;
	}

	public function getNodeType(): string
	{
		return Cast::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($this->deprecatedScopeHelper->isScopeDeprecated($scope)) {
			return [];
		}

		$castedType = $scope->getType($node->expr);
		if (! $castedType->hasMethod('__toString')->yes()) {
			return [];
		}
		$method = $castedType->getMethod('__toString', $scope);

		if (! $method->isDeprecated()->yes()) {
			return [];
		}
		$description = $method->getDeprecatedDescription();
		if ($description === null) {
			return [
				RuleErrorBuilder::message(sprintf(
					'Casting class %s to string is deprecated.',
					$method->getDeclaringClass()->getName(),
				))->identifier('class.toStringDeprecated')->build(),
			];
		}

		return [
			RuleErrorBuilder::message(sprintf(
				"Casting class %s to string is deprecated.:\n%s",
				$method->getDeclaringClass()->getName(),
				$description,
			))->identifier('class.toStringDeprecated')->build(),
		];
	}

}
