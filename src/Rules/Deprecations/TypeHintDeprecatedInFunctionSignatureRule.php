<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;

class TypeHintDeprecatedInFunctionSignatureRule implements \PHPStan\Rules\Rule
{

	/** @var Broker */
	private $broker;

	public function __construct(Broker $broker)
	{
		$this->broker = $broker;
	}

	public function getNodeType(): string
	{
		return Node\FunctionLike::class;
	}

	/**
	 * @param Node\FunctionLike $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[] errors
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		$errors = [];
		foreach ($node->getParams() as $i => $param) {
			$className = $this->getClassName($param->type, $scope);
			if ($className === null) {
				continue;
			}

			$errors[] = $this->checkClasses($className, 'argument #' . $i);
		}

		$className = $this->getClassName($node->getReturnType(), $scope);
		if ($className !== null) {
			$errors[] = $this->checkClasses($className, 'return type');
		}

		return array_filter($errors);
	}

	/**
	 * @param null|Node\Name|Node\Identifier|Node\NullableType $type
	 * @param Scope $scope
	 * @return string|null
	 */
	private function getClassName($type, Scope $scope): ?string
	{
		if ($type instanceof Node\NullableType) {
			return $this->getClassName($type->type, $scope);
		}

		if ($type instanceof Node\Name) {
			return $scope->resolveName($type);
		}

		if ($type instanceof Node\Identifier) {
			return null;
		}

		return null;
	}


	private function checkClasses(string $referencedClass, string $where): ?string
	{
		try {
			$class = $this->broker->getClass($referencedClass);
		} catch (\PHPStan\Broker\ClassNotFoundException $e) {
			return null;
		}

		if ($class->isDeprecated()) {
			$classDescription = null;
			if (method_exists($class, 'getDeprecatedDescription')) {
				$classDescription = $class->getDeprecatedDescription();
			}

			if ($classDescription === null) {
				return sprintf(
					'Usage of deprecated class %s in %s.',
					$referencedClass,
					$where
				);
			}

			return sprintf(
				"Usage of deprecated class %s in %s:\n%s",
				$referencedClass,
				$where,
				$classDescription
			);
		}

		return null;
	}

}
