<?php

namespace InstantiationOfDeprecatedClass;

$foo = new Foo();
$deprecatedFoo = new DeprecatedFoo();
$deprecated2 = new DeprecatedWithDescription();

/**
 * @deprecated
 */
function deprecated_scope()
{
	$foo = new Foo();
	$deprecatedFoo = new DeprecatedFoo();
}

/**
 * @deprecated
 */
class DeprecatedScope
{
	public function foo()
	{
		$foo = new Foo();
		$deprecatedFoo = new DeprecatedFoo();
	}
}

// #1: `namespacedName` property doesn't exist in anonymous classes
new class() extends Foo {

};
