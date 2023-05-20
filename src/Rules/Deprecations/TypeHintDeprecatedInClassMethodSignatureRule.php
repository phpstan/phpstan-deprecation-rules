<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function sprintf;
use function strtolower;

/**
 * @implements Rule<InClassMethodNode>
 */
class TypeHintDeprecatedInClassMethodSignatureRule implements Rule
{

	/** @var DeprecatedClassHelper */
	private $deprecatedClassHelper;

	/** @var DeprecatedScopeHelper */
	private $deprecatedScopeHelper;

	public function __construct(DeprecatedClassHelper $deprecatedClassHelper, DeprecatedScopeHelper $deprecatedScopeHelper)
	{
		$this->deprecatedClassHelper = $deprecatedClassHelper;
		$this->deprecatedScopeHelper = $deprecatedScopeHelper;
	}

	public function getNodeType(): string
	{
		return InClassMethodNode::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($this->deprecatedScopeHelper->isScopeDeprecated($scope)) {
			return [];
		}

		$method = $node->getMethodReflection();
		$methodSignature = ParametersAcceptorSelector::selectSingle($method->getVariants());

		$errors = [];
		foreach ($methodSignature->getParameters() as $parameter) {
			$deprecatedClasses = $this->deprecatedClassHelper->filterDeprecatedClasses($parameter->getType()->getReferencedClasses());
			foreach ($deprecatedClasses as $deprecatedClass) {
				if ($method->getDeclaringClass()->isAnonymous()) {
					$errors[] = RuleErrorBuilder::message(sprintf(
						'Parameter $%s of method %s() in anonymous class has typehint with deprecated %s %s%s',
						$parameter->getName(),
						$method->getName(),
						strtolower($deprecatedClass->getClassTypeDescription()),
						$deprecatedClass->getName(),
						$this->deprecatedClassHelper->getClassDeprecationDescription($deprecatedClass)
					))->identifier(sprintf('parameter.deprecated%s', $deprecatedClass->getClassTypeDescription()))->build();
				} else {
					$errors[] = RuleErrorBuilder::message(sprintf(
						'Parameter $%s of method %s::%s() has typehint with deprecated %s %s%s',
						$parameter->getName(),
						$method->getDeclaringClass()->getName(),
						$method->getName(),
						strtolower($deprecatedClass->getClassTypeDescription()),
						$deprecatedClass->getName(),
						$this->deprecatedClassHelper->getClassDeprecationDescription($deprecatedClass)
					))->identifier(sprintf('parameter.deprecated%s', $deprecatedClass->getClassTypeDescription()))->build();
				}
			}
		}

		$deprecatedClasses = $this->deprecatedClassHelper->filterDeprecatedClasses($methodSignature->getReturnType()->getReferencedClasses());
		foreach ($deprecatedClasses as $deprecatedClass) {
			if ($method->getDeclaringClass()->isAnonymous()) {
				$errors[] = RuleErrorBuilder::message(sprintf(
					'Return type of method %s() in anonymous class has typehint with deprecated %s %s%s',
					$method->getName(),
					strtolower($deprecatedClass->getClassTypeDescription()),
					$deprecatedClass->getName(),
					$this->deprecatedClassHelper->getClassDeprecationDescription($deprecatedClass)
				))->identifier(sprintf('return.deprecated%s', $deprecatedClass->getClassTypeDescription()))->build();
			} else {
				$errors[] = RuleErrorBuilder::message(sprintf(
					'Return type of method %s::%s() has typehint with deprecated %s %s%s',
					$method->getDeclaringClass()->getName(),
					$method->getName(),
					strtolower($deprecatedClass->getClassTypeDescription()),
					$deprecatedClass->getName(),
					$this->deprecatedClassHelper->getClassDeprecationDescription($deprecatedClass)
				))->identifier(sprintf('return.deprecated%s', $deprecatedClass->getClassTypeDescription()))->build();
			}
		}

		return $errors;
	}

}
