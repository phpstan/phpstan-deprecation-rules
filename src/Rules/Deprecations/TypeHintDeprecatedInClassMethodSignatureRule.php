<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;

class TypeHintDeprecatedInClassMethodSignatureRule implements \PHPStan\Rules\Rule
{

	/** @var Broker */
	private $broker;

	public function __construct(Broker $broker)
	{
		$this->broker = $broker;
	}

	public function getNodeType(): string
	{
		return InClassMethodNode::class;
	}

	/**
	 * @param InClassMethodNode $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[] errors
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		/** @var MethodReflection $method */
		$method = $scope->getFunction();
		if (!$method instanceof MethodReflection) {
			throw new \PHPStan\ShouldNotHappenException();
		}
		$methodSignature = ParametersAcceptorSelector::selectSingle($method->getVariants());

		$errorLists = [];
		foreach ($methodSignature->getParameters() as $i => $parameter) {
			/** @var ParameterReflection $parameter */
			$errorLists[] = $this->checkClasses(
				$parameter->getType()->getReferencedClasses(),
				'argument #' . $i
			);
		}

		$errorLists[] = $this->checkClasses(
			$methodSignature->getReturnType()->getReferencedClasses(),
			'return type'
		);

		return array_merge(...$errorLists);
	}

	/**
	 * @param string[] $referencedClasses
	 * @param string $where
	 * @return string[]
	 */
	private function checkClasses(array $referencedClasses, string $where): array
	{
		$errors = [];
		foreach ($referencedClasses as $referencedClass) {
			try {
				$class = $this->broker->getClass($referencedClass);
			} catch (\PHPStan\Broker\ClassNotFoundException $e) {
				continue;
			}

			if (!$class->isDeprecated()) {
				continue;
			}

			$classDescription = null;
			if (method_exists($class, 'getDeprecatedDescription')) {
				$classDescription = $class->getDeprecatedDescription();
			}

			if ($classDescription === null) {
				$errors[] = sprintf(
					'Usage of deprecated class %s in %s.',
					$referencedClass,
					$where
				);
			} else {
				$errors[] = sprintf(
					"Usage of deprecated class %s in %s:\n%s",
					$referencedClass,
					$where,
					$classDescription
				);
			}
		}

		return $errors;
	}

}
