<?php

namespace CheckDeprecatedMethodCall;

$foo = new Foo();
$foo->foo();
$foo->deprecatedFoo();

$bar = new Bar();
$bar->deprecatedFoo();
$bar->deprecatedFoo2();

$foo->fooFromTrait();
$foo->deprecatedFooFromTrait();
$foo->deprecatedWithDescription();

/**
 * @deprecated
 */
function deprecated_scope()
{
	$foo = new Foo();
	$foo->foo();
	$foo->deprecatedFoo();

	$bar = new Bar();
	$bar->deprecatedFoo();
	$bar->deprecatedFoo2();

	$foo->fooFromTrait();
	$foo->deprecatedFooFromTrait();

}

/**
 * @deprecated
 */
class DeprecatedScope
{

	public function foo()
	{
		$foo = new Foo();
		$foo->foo();
		$foo->deprecatedFoo();

		$bar = new Bar();
		$bar->deprecatedFoo();
		$bar->deprecatedFoo2();

		$foo->fooFromTrait();
		$foo->deprecatedFooFromTrait();
	}

}


final class UsingDeprecatedMethodFromTrait extends MethodMovedToTraitClass
{
	use TraitCallingDeprecatedMethod;

	public function callProphesize(): void
	{
		$this->prophesize();
	}
}

final class UsingTraitReplacementForDeprecatedMethod extends MethodMovedToTraitClass
{
	use TraitReplacingDeprecatedMethod;

	public function callProphesize(): void
	{
		$this->prophesize();
	}
}
