<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\MissingMethodFromReflectionException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Type\TypeUtils;
use function sprintf;

/**
 * @implements Rule<MethodCall>
 */
class CallToDeprecatedMethodRule implements Rule
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;
	}

	public function getNodeType(): string
	{
		return MethodCall::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
			return [];
		}

		if (!$node->name instanceof Identifier) {
			return [];
		}

		$methodName = $node->name->name;
		$methodCalledOnType = $scope->getType($node->var);
		$referencedClasses = TypeUtils::getDirectClassNames($methodCalledOnType);

		foreach ($referencedClasses as $referencedClass) {
			try {
				$classReflection = $this->reflectionProvider->getClass($referencedClass);
				$methodReflection = $classReflection->getMethod($methodName, $scope);

				if (!$methodReflection->isDeprecated()->yes()) {
					continue;
				}

				$description = $methodReflection->getDeprecatedDescription();
				if ($description === null) {
					return [sprintf(
						'Call to deprecated method %s() of class %s.',
						$methodReflection->getName(),
						$methodReflection->getDeclaringClass()->getName()
					)];
				}

				return [sprintf(
					"Call to deprecated method %s() of class %s:\n%s",
					$methodReflection->getName(),
					$methodReflection->getDeclaringClass()->getName(),
					$description
				)];
			} catch (ClassNotFoundException $e) {
				// Other rules will notify if the class is not found
			} catch (MissingMethodFromReflectionException $e) {
				// Other rules will notify if the the method is not found
			}
		}

		return [];
	}

}
