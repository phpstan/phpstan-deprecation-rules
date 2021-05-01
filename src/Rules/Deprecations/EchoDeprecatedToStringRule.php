<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Type;
use PhpParser\Node\Stmt\Echo_;

/**
 * @implements \PHPStan\Rules\Rule<Echo_>
 */
class EchoDeprecatedToStringRule implements \PHPStan\Rules\Rule
{

	/**
	 * @var RuleLevelHelper
	 */
	private $ruleLevelHelper;

	public function __construct(RuleLevelHelper $ruleLevelHelper)
	{
		$this->ruleLevelHelper = $ruleLevelHelper;
	}

	public function getNodeType(): string
	{
		return Echo_::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
			return [];
		}

		$messages = [];

		foreach ($node->exprs as $key => $expr) {
			if (!$expr instanceof Node\Expr\Variable) {
				continue;
			}

			$type = $this->ruleLevelHelper->findTypeToCheck(
				$scope,
				$expr,
				'',
				static function (Type $type): bool {
					return !$type->toString() instanceof ErrorType;
				}
			)->getType();

			if (!$type instanceof ObjectType) {
				continue;
			}

			$classReflection = $type->getClassReflection();
			$methodReflection = $classReflection->getNativeMethod('__toString', $scope);

			if (!$methodReflection->isDeprecated()->yes()) {
				continue;
			}

			$description = $methodReflection->getDeprecatedDescription();
			if ($description === null) {
				$messages[] = sprintf(
					'Call to deprecated method %s() of class %s.',
					$methodReflection->getName(),
					$methodReflection->getDeclaringClass()->getName()
				);
			} else {
				$messages[] = sprintf(
					"Call to deprecated method %s() of class %s:\n%s",
					$methodReflection->getName(),
					$methodReflection->getDeclaringClass()->getName(),
					$description
				);
			}
		}

		return $messages;
	}

}
