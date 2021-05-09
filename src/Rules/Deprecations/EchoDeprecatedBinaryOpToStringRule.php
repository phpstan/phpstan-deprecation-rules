<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\TypeUtils;

/**
 * @implements \PHPStan\Rules\Rule<Echo_>
 */
class EchoDeprecatedBinaryOpToStringRule implements \PHPStan\Rules\Rule
{
	/** @var Broker */
	private $broker;

	/** @var RuleLevelHelper */
	private $ruleLevelHelper;

	public function __construct(Broker $broker, RuleLevelHelper $ruleLevelHelper)
	{
		$this->broker = $broker;
		$this->ruleLevelHelper = $ruleLevelHelper;
	}

	public function getNodeType(): string
	{
		return Node\Expr\BinaryOp::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
			return [];
		}

		$messages = [];
		$message = $this->checkExpr($node->left, $scope);
		if ($message) {
			$messages[] = $message;
		}
		$message = $this->checkExpr($node->right, $scope);
		if ($message) {
			$messages[] = $message;
		}

		return $messages;
	}

	private function checkExpr(Node\Expr $node, Scope $scope): ?string
	{
		$methodCalledOnType = $scope->getType($node);
		$referencedClasses = TypeUtils::getDirectClassNames($methodCalledOnType);

		foreach ($referencedClasses as $referencedClass) {
			try {
				$classReflection = $this->broker->getClass($referencedClass);
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
			} catch (\PHPStan\Broker\ClassNotFoundException $e) {
				// Other rules will notify if the class is not found
			} catch (\PHPStan\Reflection\MissingMethodFromReflectionException $e) {
				// Other rules will notify if the the method is not found
			}
		}

		return null;
	}
}
