<?php

namespace CustomDeprecatedScope;

use CheckDeprecatedMethodCall\Foo;

class FooTest
{
	public function testFoo()
	{
		$foo = new Foo();
		$foo->foo();
		$foo->deprecatedFoo();
	}

	/**
	 * @group legacy
	 */
	public function testFooGroupedLegacy()
	{
		$foo = new Foo();
		$foo->foo();
		$foo->deprecatedFoo();
	}
}
