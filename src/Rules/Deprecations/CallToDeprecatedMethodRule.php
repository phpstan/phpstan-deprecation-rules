<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MissingMethodFromReflectionException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\TypeUtils;

/**
 * @implements \PHPStan\Rules\Rule<MethodCall>
 */
class CallToDeprecatedMethodRule implements \PHPStan\Rules\Rule
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

				$interfaces = $classReflection->getInterfaces();

				$interfaceIsDeprecated = false;
				$deprecatedInterface = null;

				foreach ($interfaces as $interface) {
					try {
						$interfaceMethodReflection = $interface->getMethod($methodName, $scope);
						if ($interfaceMethodReflection->isDeprecated()->yes()) {
							$interfaceIsDeprecated = true;
							$deprecatedInterface = $interfaceMethodReflection;
							break;
						}
					} catch (MissingMethodFromReflectionException $e) {
						// The method did not exist on the interface, which is totally fine.
					}
				}

				if (!$methodReflection->isDeprecated()->yes() && !$interfaceIsDeprecated) {
					continue;
				}

				$description = $methodReflection->getDeprecatedDescription();
				if ($description === null && $deprecatedInterface) {
					// See if the deprecated interface has the description instead.
					$description = $deprecatedInterface->getDeprecatedDescription();
				}
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
			} catch (\PHPStan\Broker\ClassNotFoundException $e) {
				// Other rules will notify if the class is not found
			} catch (\PHPStan\Reflection\MissingMethodFromReflectionException $e) {
				// Other rules will notify if the the method is not found
			}
		}

		return [];
	}

}
