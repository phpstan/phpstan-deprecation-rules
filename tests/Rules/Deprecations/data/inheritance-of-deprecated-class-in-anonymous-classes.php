<?php

namespace InheritanceOfDeprecatedClass;

$foo = new class extends Foo {

};

$deprecatedFoo = new class extends DeprecatedFoo {

};

$deprecatedBar = new class extends DeprecatedWithDescription {

};

/**
 * @deprecated
 */
function deprecated_scope()
{
	$foo = new class extends Foo {

	};

	$deprecatedFoo = new class extends DeprecatedFoo {

	};
}

/**
 * @deprecated
 */
class DeprecatedScope
{

	public function foo()
	{
		$foo = new class extends Foo {

		};

		$deprecatedFoo = new class extends DeprecatedFoo {

		};
	}

}
