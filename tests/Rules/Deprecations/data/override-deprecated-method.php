<?php

namespace OverrideDeprecatedMethod;

trait DeprecationTrait
{
	/**
	 * @deprecated
	 */
	public function deprecatedInTrait(): void
	{}
}

class Ancestor
{
	use DeprecationTrait;
	/**
	 * @deprecated
	 */
	public function deprecatedMethod(): void
	{}

	/**
	 * @deprecated
	 */
	private function privateDeprecatedMethod(): void
	{}

	/**
	 * @deprecated
	 */
	public function explicitlyNotDeprecatedMethod(): void
	{}
}

interface Deprecated
{
	/**
	 * @deprecated
	 */
	public function deprecatedInInterface(): void;
}

class Child extends Ancestor implements Deprecated
{
	use DeprecationTrait {
		deprecatedInTrait as deprecatedInChild;
	}
	public function deprecatedMethod(): void
	{}

	private function privateDeprecatedMethod(): void
	{}

	/**
	 * @not-deprecated
	 */
	public function explicitlyNotDeprecatedMethod(): void
	{}

	public function deprecatedInInterface(): void
	{}

	public function deprecatedInTrait(): void
	{}
}

class GrandChild extends Child
{
	public function explicitlyNotDeprecatedMethod(): void
	{}

	public function deprecatedInChild(): void
	{}
}
