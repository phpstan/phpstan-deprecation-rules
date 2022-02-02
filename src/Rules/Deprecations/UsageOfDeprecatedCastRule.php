<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\Cast;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use function sprintf;

/**
 * @implements Rule<Cast>
 */
class UsageOfDeprecatedCastRule implements Rule
{

	public function getNodeType(): string
	{
		return Cast::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
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
			return [sprintf(
				'Casting class %s to string is deprecated.',
				$method->getDeclaringClass()->getName()
			)];
		}

		return [sprintf(
			"Casting class %s to string is deprecated.:\n%s",
			$method->getDeclaringClass()->getName(),
			$description
		)];
	}

}
