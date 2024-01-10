<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;

/**
 * This is the interface for custom deprecated scope resolvers that use the current node.
 *
 * @see \PHPStan\Rules\Deprecations\DeprecatedScopeResolver
 *
 * @api
 */
interface NodeAwareDeprecatedScopeResolver
{

	public function withNode(Node $node): void;

}
