<?php

namespace OverrideDeprecatedMethod;

class Ancestor
{
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
}

class GrandChild extends Child
{
	public function explicitlyNotDeprecatedMethod(): void
	{}
}
