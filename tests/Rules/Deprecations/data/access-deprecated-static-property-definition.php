<?php

namespace AccessDeprecatedStaticProperty;

trait FooTrait {

	public static $fooFromTrait;

	/**
	 * @deprecated
	 */
	public static $deprecatedFooFromTrait;

}

class Foo {

	use FooTrait;

	public static $foo;

	/**
	 * @deprecated
	 */
	public static $deprecatedFoo;

	/**
	 * @deprecated This is probably a singleton.
	 */
	public static $deprecatedWithDescription;

}
