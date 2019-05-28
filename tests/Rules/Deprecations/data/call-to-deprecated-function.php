<?php

namespace CheckDeprecatedFunctionCall;

foo();
\CheckDeprecatedFunctionCall\foo();

deprecated_foo();
\CheckDeprecatedFunctionCall\deprecated_foo();
deprecated_with_description();

/**
 * @deprecated
 */
function deprecated_scope()
{
	deprecated_foo();
	\CheckDeprecatedFunctionCall\deprecated_foo();
}

/**
 * @deprecated
 */
class DeprecatedScope
{

	function foo()
	{
		deprecated_foo();
		\CheckDeprecatedFunctionCall\deprecated_foo();
	}

}
