<?php

namespace CheckDeprecatedMethodCall;

trait FooTrait
{

	public function fooFromTrait()
	{

	}

	/**
	 * @deprecated
	 */
	public function deprecatedFooFromTrait()
	{

	}

}

class Foo
{

	use FooTrait;

	public function foo()
	{

	}

	/**
	 * @deprecated
	 */
	public function deprecatedFoo()
	{

	}

	/**
	 * @deprecated
	 */
	public function deprecatedFoo2()
	{

	}

	/**
	 * @deprecated Call a different method instead.
	 */
	public function deprecatedWithDescription()
	{

	}

}

class Bar extends Foo
{

	public function deprecatedFoo()
	{

	}

}

abstract class MethodMovedToTraitClass
{
	/** @deprecated Use TraitReplacingDeprecatedMethod::prophesize() */
	protected function prophesize(): void
	{
		echo 'Base';
	}
}

trait TraitCallingDeprecatedMethod
{
	protected function prophesize(): void
	{
		echo 'Trait';
	}
}

trait TraitReplacingDeprecatedMethod
{
	/**
	 * @not-deprecated
	 */
	protected function prophesize(): void
	{
		echo 'Trait';
	}
}

/**
 * @deprecated
 */
trait DeprecatedTrait
{

	public function methodFromDeprecatedTrait(): void
	{
	}

	public static function staticMethodFromDeprecatedTrait(): void
	{
	}

}

/**
 * @deprecated Use something else.
 */
trait DeprecatedTraitWithDescription
{

	public function methodFromDeprecatedTraitWithDescription(): void
	{
	}

}

class ClassUsingDeprecatedTrait
{

	use DeprecatedTrait;
	use DeprecatedTraitWithDescription;

}

