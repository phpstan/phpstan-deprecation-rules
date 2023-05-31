<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClosureNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use function sprintf;
use function strtolower;

/**
 * @implements Rule<InClosureNode>
 */
class TypeHintDeprecatedInClosureSignatureRule implements Rule
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
		return InClosureNode::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($this->deprecatedScopeHelper->isScopeDeprecated($scope)) {
			return [];
		}

		$functionSignature = $scope->getAnonymousFunctionReflection();
		if ($functionSignature === null) {
			throw new ShouldNotHappenException();
		}

		$errors = [];
		foreach ($functionSignature->getParameters() as $parameter) {
			$deprecatedClasses = $this->deprecatedClassHelper->filterDeprecatedClasses($parameter->getType()->getReferencedClasses());
			foreach ($deprecatedClasses as $deprecatedClass) {
				$errors[] = RuleErrorBuilder::message(sprintf(
					'Parameter $%s of anonymous function has typehint with deprecated %s %s%s',
					$parameter->getName(),
					strtolower($deprecatedClass->getClassTypeDescription()),
					$deprecatedClass->getName(),
					$this->deprecatedClassHelper->getClassDeprecationDescription($deprecatedClass)
				))->identifier(sprintf('parameter.deprecated%s', $deprecatedClass->getClassTypeDescription()))->build();
			}
		}

		$deprecatedClasses = $this->deprecatedClassHelper->filterDeprecatedClasses($functionSignature->getReturnType()->getReferencedClasses());
		foreach ($deprecatedClasses as $deprecatedClass) {
			$errors[] = RuleErrorBuilder::message(sprintf(
				'Return type of anonymous function has typehint with deprecated %s %s%s',
				strtolower($deprecatedClass->getClassTypeDescription()),
				$deprecatedClass->getName(),
				$this->deprecatedClassHelper->getClassDeprecationDescription($deprecatedClass)
			))->identifier(sprintf('return.deprecated%s', $deprecatedClass->getClassTypeDescription()))->build();
		}

		return $errors;
	}

}
