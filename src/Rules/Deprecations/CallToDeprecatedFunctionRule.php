<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;

class CallToDeprecatedFunctionRule implements \PHPStan\Rules\Rule
{

	/** @var Broker */
	private $broker;

	public function __construct(Broker $broker)
	{
		$this->broker = $broker;
	}

	public function getNodeType(): string
	{
		return FuncCall::class;
	}

	/**
	 * @param FuncCall $node
	 * @param \PHPStan\Analyser\Scope $scope
	 * @return string[] errors
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
			return [];
		}

		if (!($node->name instanceof \PhpParser\Node\Name)) {
			return [];
		}

		try {
			$function = $this->broker->getFunction($node->name, $scope);
		} catch (\PHPStan\Broker\FunctionNotFoundException $e) {
			// Other rules will notify if the function is not found
			return [];
		}

		if ($function->isDeprecated()) {
			$description = null;
			if (method_exists($function, 'getDeprecatedDescription')) {
				$description = $function->getDeprecatedDescription();
			}

			if ($description === null) {
				return [sprintf(
					'Call to deprecated function %s().',
					$function->getName()
				)];
			}

			return [sprintf(
				"Call to deprecated function %s():\n%s",
				$function->getName(),
				$description
			)];
		}

		return [];
	}

}
