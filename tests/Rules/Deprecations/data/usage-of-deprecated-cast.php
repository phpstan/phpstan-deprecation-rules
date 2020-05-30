<?php

namespace UsageOfDeprecatedCast;

class Foo
{
	/**
	 * @deprecated
	 */
	public function __toString()
	{
		return 'foo';
	}
}

function foo(Foo $foo): string {
	return (string) $foo;
}

/**
 * @deprecated
 */
function deprecatedScope(Foo $foo): string
{
	return (string) $foo;
}
