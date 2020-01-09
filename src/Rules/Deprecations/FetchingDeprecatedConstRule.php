<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\GlobalConstantReflection;
use PHPStan\Reflection\ReflectionProvider;

/**
 * @implements \PHPStan\Rules\Rule<ConstFetch>
 */
class FetchingDeprecatedConstRule implements \PHPStan\Rules\Rule
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	/** @var array<string,string> */
	private $deprecatedConstants = [
		'FILTER_FLAG_SCHEME_REQUIRED' => 'Use of constant %s is deprecated since PHP 7.3.',
		'FILTER_FLAG_HOST_REQUIRED' => 'Use of constant %s is deprecated since PHP 7.3.',
	];

	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;
	}

	public function getNodeType(): string
	{
		return ConstFetch::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (DeprecatedScopeHelper::isScopeDeprecated($scope)) {
			return [];
		}

		if (!$this->reflectionProvider->hasConstant($node->name, $scope)) {
			return [];
		}

		$constantReflection = $this->reflectionProvider->getConstant($node->name, $scope);

		if ($this->isDeprecated($constantReflection)) {
			return [sprintf(
				$this->deprecatedConstants[$constantReflection->getName()] ?? 'Use of constant %s is deprecated.',
				$constantReflection->getName()
			)];
		}

		return [];
	}

	private function isDeprecated(GlobalConstantReflection $constantReflection): bool
	{
		return $constantReflection->isDeprecated()->yes()
			|| isset($this->deprecatedConstants[$constantReflection->getName()]);
	}

}
