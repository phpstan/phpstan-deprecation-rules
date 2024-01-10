<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PHPStan\Analyser\Scope;

class DeprecatedScopeHelper
{

	/** @var DeprecatedScopeResolver[]  */
	private $resolvers;

	/**
	 * @param DeprecatedScopeResolver[] $checkers
	 */
	public function __construct(array $checkers)
	{
		$this->resolvers = $checkers;
	}

	public function isScopeDeprecated(Scope $scope, Node $node): bool
	{
		foreach ($this->resolvers as $checker) {
			if ($checker instanceof NodeAwareDeprecatedScopeResolver) {
				$checker->withNode($node);
			}
			if ($checker->isScopeDeprecated($scope)) {
				return true;
			}
		}

		return false;
	}

}
