<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Reflection\ClassReflection;
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
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
			return [];
		}

		/** @var MethodReflection $method */
		$method = $scope->getFunction();
		if (!$method instanceof MethodReflection) {
			throw new \PHPStan\ShouldNotHappenException();
		}
		$methodSignature = ParametersAcceptorSelector::selectSingle($method->getVariants());

		$errors = [];
		foreach ($methodSignature->getParameters() as $i => $parameter) {
			/** @var ParameterReflection $parameter */
			$deprecatedClasses = $this->filterDeprecatedClasses($parameter->getType()->getReferencedClasses());
			foreach ($deprecatedClasses as $deprecatedClass) {
				$errors[] = sprintf(
					'Parameter $%s of method %s::%s() has typehint with deprecated %s %s%s',
					$parameter->getName(),
					$method->getDeclaringClass()->getName(),
					$method->getName(),
					$this->getClassType($deprecatedClass),
					$deprecatedClass->getName(),
					$this->getClassDeprecationDescription($deprecatedClass)
				);
			}
		}

		$deprecatedClasses = $this->filterDeprecatedClasses($methodSignature->getReturnType()->getReferencedClasses());
		foreach ($deprecatedClasses as $deprecatedClass) {
			$errors[] = sprintf(
				'Return type of method %s::%s() has typehint with deprecated %s %s%s',
				$method->getDeclaringClass()->getName(),
				$method->getName(),
				$this->getClassType($deprecatedClass),
				$deprecatedClass->getName(),
				$this->getClassDeprecationDescription($deprecatedClass)
			);
		}

		return $errors;
	}

	private function getClassType(ClassReflection $class): string
	{
		if ($class->isInterface()) {
			return 'interface';
		}

		return 'class';
	}

	private function getClassDeprecationDescription(ClassReflection $class): string
	{
		$description = null;
		if (method_exists($class, 'getDeprecatedDescription')) {
			$description = $class->getDeprecatedDescription();
		}

		if ($description === null) {
			return '.';
		}

		return sprintf(":\n%s", $description);
	}

	/**
	 * @param string[] $referencedClasses
	 * @return ClassReflection[]
	 */
	private function filterDeprecatedClasses(array $referencedClasses): array
	{
		$deprecatedClasses = [];
		foreach ($referencedClasses as $referencedClass) {
			try {
				$class = $this->broker->getClass($referencedClass);
			} catch (\PHPStan\Broker\ClassNotFoundException $e) {
				continue;
			}

			if (!$class->isDeprecated()) {
				continue;
			}

			$deprecatedClasses[] = $class;
		}

		return $deprecatedClasses;
	}

}
