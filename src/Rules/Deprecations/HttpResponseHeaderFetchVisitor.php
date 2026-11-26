<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\AssignOp\Coalesce as AssignCoalesce;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\Empty_;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\List_;
use PhpParser\Node\Expr\PostDec;
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Expr\PreDec;
use PhpParser\Node\Expr\PreInc;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Unset_;
use PhpParser\NodeVisitorAbstract;
use function array_pop;
use function count;
use function is_array;
use function is_string;

/**
 * Marks plain reads of $http_response_header.
 *
 * PHP 8.5 warns only for BP_VAR_R, and only while the current op array has
 * not already accepted the name as a write (BP_VAR_W) or as a parameter.
 * isset/empty/unset and the left side of ?? do not warn and do not count
 * as a write. A foreach value is compiled as BP_VAR_R, so it does warn.
 */
class HttpResponseHeaderFetchVisitor extends NodeVisitorAbstract
{

	public const ATTRIBUTE_NAME = 'httpResponseHeaderFetch';

	public const FETCH_READ = 'read';

	/** @var bool[] */
	private array $assignedStack = [];

	/** @var Node[] */
	private array $nodeStack = [];

	/**
	 * @param Node[] $nodes
	 */
	public function beforeTraverse(array $nodes)
	{
		$this->assignedStack = [false];
		$this->nodeStack = [];

		return null;
	}

	/**
	 * @param Node[] $nodes
	 */
	public function afterTraverse(array $nodes)
	{
		$this->assignedStack = [];
		$this->nodeStack = [];

		return null;
	}

	public function enterNode(Node $node)
	{
		if ($node instanceof FunctionLike) {
			$this->assignedStack[] = $this->parametersShadowTheVariable($node);
		}

		if ($node instanceof Variable && is_string($node->name) && $node->name === 'http_response_header') {
			$this->classifyRead($node);
		}

		$this->nodeStack[] = $node;

		return null;
	}

	public function leaveNode(Node $node)
	{
		if (
			($node instanceof Assign || $node instanceof AssignRef)
			&& ($node->var instanceof Array_ || $node->var instanceof List_)
			&& $this->subtreeContainsName($node->var)
		) {
			// list() compiles the right-hand side before the writes.
			$this->markAssigned();
		}

		if ($node instanceof AssignCoalesce && $this->subtreeContainsName($node->var)) {
			// ??= reads with BP_VAR_IS, evaluates the default, then writes.
			$this->markAssigned();
		}

		if ($node instanceof FunctionLike) {
			array_pop($this->assignedStack);
		}

		array_pop($this->nodeStack);

		return null;
	}

	private function classifyRead(Variable $node): void
	{
		$write = $this->enclosingWrite($node);
		if ($write === 'assign' && !$this->listAssignment()) {
			// A simple or dim write is compiled before the right-hand side.
			$this->markAssigned();
			return;
		}
		if ($write === 'assignRefRhs' || $write === 'foreachKey') {
			$this->markAssigned();
			return;
		}
		if ($write === 'assign' || $this->isSilentFetch($node) || $this->isInPlaceTarget($node) || $this->isAssigned()) {
			return;
		}

		$node->setAttribute(self::ATTRIBUTE_NAME, self::FETCH_READ);
	}

	private function parametersShadowTheVariable(FunctionLike $function): bool
	{
		foreach ($function->getParams() as $param) {
			if ($param->var instanceof Variable && $param->var->name === 'http_response_header') {
				return true;
			}
		}

		return false;
	}

	private function enclosingWrite(Variable $node): ?string
	{
		$child = $node;
		for ($i = count($this->nodeStack) - 1; $i >= 0; $i--) {
			$parent = $this->nodeStack[$i];
			if ($parent instanceof FunctionLike) {
				return null;
			}
			if ($parent instanceof Assign || $parent instanceof AssignRef) {
				if ($parent->var === $child) {
					return 'assign';
				}
				if ($parent instanceof AssignRef && $parent->expr === $child) {
					return 'assignRefRhs';
				}

				return null;
			}
			if ($parent instanceof Foreach_) {
				if ($parent->keyVar === $child) {
					return 'foreachKey';
				}

				return null;
			}

			$child = $parent;
		}

		return null;
	}

	private function listAssignment(): bool
	{
		for ($i = count($this->nodeStack) - 1; $i >= 0; $i--) {
			$parent = $this->nodeStack[$i];
			if ($parent instanceof Assign || $parent instanceof AssignRef) {
				return $parent->var instanceof Array_ || $parent->var instanceof List_;
			}
		}

		return false;
	}

	private function isSilentFetch(Variable $node): bool
	{
		$child = $node;
		for ($i = count($this->nodeStack) - 1; $i >= 0; $i--) {
			$parent = $this->nodeStack[$i];
			if ($parent instanceof FunctionLike) {
				return false;
			}
			if ($parent instanceof Isset_ || $parent instanceof Empty_ || $parent instanceof Unset_) {
				return true;
			}
			if ($parent instanceof Coalesce && ($parent->left === $child || $this->isDimBase($parent->left, $child))) {
				return true;
			}

			$child = $parent;
		}

		return false;
	}

	private function isInPlaceTarget(Variable $node): bool
	{
		$child = $node;
		for ($i = count($this->nodeStack) - 1; $i >= 0; $i--) {
			$parent = $this->nodeStack[$i];
			if ($parent instanceof FunctionLike) {
				return false;
			}
			if (
				($parent instanceof PreInc || $parent instanceof PostInc || $parent instanceof PreDec || $parent instanceof PostDec || $parent instanceof AssignOp)
				&& ($parent->var === $child || $this->isDimBase($parent->var, $child))
			) {
				return true;
			}

			$child = $parent;
		}

		return false;
	}

	private function isDimBase(Node $root, Node $child): bool
	{
		$current = $root;
		while ($current instanceof ArrayDimFetch) {
			if ($current === $child) {
				return true;
			}
			$current = $current->var;
		}

		return $current === $child;
	}

	private function subtreeContainsName(Node $node): bool
	{
		if ($node instanceof Variable && $node->name === 'http_response_header') {
			return true;
		}

		foreach ($node->getSubNodeNames() as $subName) {
			$subNode = $node->{$subName};
			if ($subNode instanceof Node && $this->subtreeContainsName($subNode)) {
				return true;
			}
			if (!is_array($subNode)) {
				continue;
			}
			foreach ($subNode as $item) {
				if ($item instanceof Node && $this->subtreeContainsName($item)) {
					return true;
				}
			}
		}

		return false;
	}

	private function markAssigned(): void
	{
		$last = count($this->assignedStack) - 1;
		if ($last < 0) {
			return;
		}

		$this->assignedStack[$last] = true;
	}

	private function isAssigned(): bool
	{
		$last = count($this->assignedStack) - 1;

		return $last >= 0 && $this->assignedStack[$last];
	}

}
