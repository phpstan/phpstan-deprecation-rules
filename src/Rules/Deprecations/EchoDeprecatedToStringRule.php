<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Stmt\Echo_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\ErrorType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

/**
 * @implements \PHPStan\Rules\Rule<Echo_>
 */
class EchoDeprecatedToStringRule implements \PHPStan\Rules\Rule
{

	/** @var RuleLevelHelper */
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
			if ($expr instanceof Node\Expr\Variable) {
				$message = $this->checkExpr($expr, $scope);

				if ($message) {
					$messages[] = $message;
				}
			} elseif ($expr instanceof Node\Expr\BinaryOp\Concat) {
				$message = $this->checkExpr($expr->left, $scope);
				if ($message) {
					$messages[] = $message;
				}

				$message = $this->checkExpr($expr->right, $scope);
				if ($message) {
					$messages[] = $message;
				}
			}
		}

		return $messages;
	}

	private function checkExpr(Node\Expr $expr, Scope $scope): ?string
	{
		$type = $this->ruleLevelHelper->findTypeToCheck(
			$scope,
			$expr,
			'',
			static function (Type $type): bool {
				return !$type->toString() instanceof ErrorType;
			}
		)->getType();

		if (!$type instanceof ObjectType) {
			return null;
		}

		$classReflection = $type->getClassReflection();

		if ($classReflection === null) {
			return null;
		}

		$methodReflection = $classReflection->getNativeMethod('__toString');

		if (!$methodReflection->isDeprecated()->yes()) {
			return null;
		}

		$description = $methodReflection->getDeprecatedDescription();
		if ($description === null) {
			return sprintf(
				'Call to deprecated method %s() of class %s.',
				$methodReflection->getName(),
				$methodReflection->getDeclaringClass()->getName()
			);
		}

		return sprintf(
			"Call to deprecated method %s() of class %s:\n%s",
			$methodReflection->getName(),
			$methodReflection->getDeclaringClass()->getName(),
			$description
		);
	}

}
