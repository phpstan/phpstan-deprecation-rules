<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\FunctionNotFoundException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function sprintf;

/**
 * @implements Rule<FuncCall>
 */
class CallToDeprecatedFunctionRule implements Rule
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
		return FuncCall::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($this->deprecatedScopeHelper->isScopeDeprecated($scope)) {
			return [];
		}

		if (!($node->name instanceof Name)) {
			return [];
		}

		try {
			$function = $this->reflectionProvider->getFunction($node->name, $scope);
		} catch (FunctionNotFoundException $e) {
			// Other rules will notify if the function is not found
			return [];
		}

		if ($function->isDeprecated()->yes()) {
			$description = $function->getDeprecatedDescription();
			if ($description === null) {
				return [
					RuleErrorBuilder::message(sprintf(
						'Call to deprecated function %s().',
						$function->getName()
					))->identifier('function.deprecated')->build(),
				];
			}

			return [
				RuleErrorBuilder::message(sprintf(
					"Call to deprecated function %s():\n%s",
					$function->getName(),
					$description
				))->identifier('function.deprecated')->build(),
			];
		}

		return [];
	}

}
