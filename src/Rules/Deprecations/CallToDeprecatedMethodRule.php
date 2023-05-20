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
use PHPStan\Rules\RuleErrorBuilder;
use function sprintf;
use function strtolower;

/**
 * @implements Rule<MethodCall>
 */
class CallToDeprecatedMethodRule implements Rule
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	/** @var DeprecatedScopeHelper */
	private $deprecatedScopeHelper;

	public function __construct(ReflectionProvider $reflectionProvider, DeprecatedScopeHelper $deprecatedScopeHelper)
	{
		$this->reflectionProvider = $reflectionProvider;
		$this->deprecatedScopeHelper = $deprecatedScopeHelper;
	}

	public function getNodeType(): string
	{
		return MethodCall::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($this->deprecatedScopeHelper->isScopeDeprecated($scope)) {
			return [];
		}

		if (!$node->name instanceof Identifier) {
			return [];
		}

		$methodName = $node->name->name;
		$methodCalledOnType = $scope->getType($node->var);
		$referencedClasses = $methodCalledOnType->getObjectClassNames();

		foreach ($referencedClasses as $referencedClass) {
			try {
				$classReflection = $this->reflectionProvider->getClass($referencedClass);
				$methodReflection = $classReflection->getMethod($methodName, $scope);

				if (!$methodReflection->isDeprecated()->yes()) {
					continue;
				}

				$description = $methodReflection->getDeprecatedDescription();
				if ($description === null) {
					return [
						RuleErrorBuilder::message(sprintf(
							'Call to deprecated method %s() of %s %s.',
							$methodReflection->getName(),
							strtolower($methodReflection->getDeclaringClass()->getClassTypeDescription()),
							$methodReflection->getDeclaringClass()->getName()
						))->identifier('method.deprecated')->build(),
					];
				}

				return [
					RuleErrorBuilder::message(sprintf(
						"Call to deprecated method %s() of %s %s:\n%s",
						$methodReflection->getName(),
						strtolower($methodReflection->getDeclaringClass()->getClassTypeDescription()),
						$methodReflection->getDeclaringClass()->getName(),
						$description
					))->identifier('method.deprecated')->build(),
				];
			} catch (ClassNotFoundException $e) {
				// Other rules will notify if the class is not found
			} catch (MissingMethodFromReflectionException $e) {
				// Other rules will notify if the the method is not found
			}
		}

		return [];
	}

}
