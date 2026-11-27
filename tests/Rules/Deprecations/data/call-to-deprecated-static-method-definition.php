<?php

namespace CheckDeprecatedStaticMethodCall;

class Foo
{

	public static function foo()
	{

	}

	/**
	 * @deprecated
	 */
	public static function deprecatedFoo()
	{

	}

	/**
	 * @deprecated
	 */
	public static function deprecatedFoo2()
	{

	}

	/**
	 * @deprecated This is probably a singleton.
	 */
	public static function deprecatedWithDescription()
	{

	}

}

class Bar extends Foo
{

	public static function deprecatedFoo()
	{

	}

}

/**
 * @deprecated
 */
class DeprecatedBar extends Foo
{

	public static function doDeprecatedBar()
	{
		
	}

}

/**
 * @deprecated Do not touch this at all.
 */
class DeprecatedBaz extends Foo
{

}

/**
 * @deprecated
 */
trait DeprecatedTrait
{

	public static function staticMethodFromDeprecatedTrait(): void
	{
	}

}

/**
 * @deprecated Use something else.
 */
trait DeprecatedTraitWithDescription
{

	public static function staticMethodFromDeprecatedTraitWithDescription(): void
	{
	}

}

class ClassUsingDeprecatedTrait
{

	use DeprecatedTrait;
	use DeprecatedTraitWithDescription;

}
