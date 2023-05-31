<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\MissingMethodFromReflectionException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Type;
use function sprintf;
use function strtolower;

/**
 * @implements Rule<StaticCall>
 */
class CallToDeprecatedStaticMethodRule implements Rule
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	/** @var RuleLevelHelper */
	private $ruleLevelHelper;

	/** @var DeprecatedScopeHelper */
	private $deprecatedScopeHelper;

	public function __construct(ReflectionProvider $reflectionProvider, RuleLevelHelper $ruleLevelHelper, DeprecatedScopeHelper $deprecatedScopeHelper)
	{
		$this->reflectionProvider = $reflectionProvider;
		$this->ruleLevelHelper = $ruleLevelHelper;
		$this->deprecatedScopeHelper = $deprecatedScopeHelper;
	}

	public function getNodeType(): string
	{
		return StaticCall::class;
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
		$referencedClasses = [];

		if ($node->class instanceof Name) {
			$referencedClasses[] = $scope->resolveName($node->class);
		} else {
			$classTypeResult = $this->ruleLevelHelper->findTypeToCheck(
				$scope,
				$node->class,
				'', // We don't care about the error message
				static function (Type $type) use ($methodName): bool {
					return $type->canCallMethods()->yes() && $type->hasMethod($methodName)->yes();
				}
			);

			if ($classTypeResult->getType() instanceof ErrorType) {
				return [];
			}

			$referencedClasses = $classTypeResult->getReferencedClasses();
		}

		$errors = [];

		foreach ($referencedClasses as $referencedClass) {
			try {
				$class = $this->reflectionProvider->getClass($referencedClass);
				$methodReflection = $class->getMethod($methodName, $scope);
			} catch (ClassNotFoundException $e) {
				continue;
			} catch (MissingMethodFromReflectionException $e) {
				continue;
			}

			if ($class->isDeprecated()) {
				$classDescription = $class->getDeprecatedDescription();
				if ($classDescription === null) {
					$errors[] = RuleErrorBuilder::message(sprintf(
						'Call to method %s() of deprecated %s %s.',
						$methodReflection->getName(),
						strtolower($methodReflection->getDeclaringClass()->getClassTypeDescription()),
						$methodReflection->getDeclaringClass()->getName()
					))->identifier(sprintf('staticMethod.deprecated%s', $methodReflection->getDeclaringClass()->getClassTypeDescription()))->build();
				} else {
					$errors[] = RuleErrorBuilder::message(sprintf(
						"Call to method %s() of deprecated %s %s:\n%s",
						$methodReflection->getName(),
						strtolower($methodReflection->getDeclaringClass()->getClassTypeDescription()),
						$methodReflection->getDeclaringClass()->getName(),
						$classDescription
					))->identifier(sprintf('staticMethod.deprecated%s', $methodReflection->getDeclaringClass()->getClassTypeDescription()))->build();
				}
			}

			if (!$methodReflection->isDeprecated()->yes()) {
				continue;
			}

			$description = $methodReflection->getDeprecatedDescription();
			if ($description === null) {
				$errors[] = RuleErrorBuilder::message(sprintf(
					'Call to deprecated method %s() of %s %s.',
					$methodReflection->getName(),
					strtolower($methodReflection->getDeclaringClass()->getClassTypeDescription()),
					$methodReflection->getDeclaringClass()->getName()
				))->identifier('staticMethod.deprecated')->build();
			} else {
				$errors[] = RuleErrorBuilder::message(sprintf(
					"Call to deprecated method %s() of %s %s:\n%s",
					$methodReflection->getName(),
					strtolower($methodReflection->getDeclaringClass()->getClassTypeDescription()),
					$methodReflection->getDeclaringClass()->getName(),
					$description
				))->identifier('staticMethod.deprecated')->build();
			}
		}

		return $errors;
	}

}
