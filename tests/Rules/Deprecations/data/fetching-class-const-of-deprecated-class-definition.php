<?php

namespace FetchingClassConstOfDeprecatedClass;

class Foo
{

	public const FOO = 'FOO';

	/**
	 * @deprecated
	 */
	public const DEPRECATED_FOO = 'FOO';

	/**
	 * @deprecated Use different constant.
	 */
	public const DEPRECATED_WITH_DESCRIPTION = 'BAR';

}

/**
 * @deprecated
 */
class DeprecatedFoo
{

	public const FOO = 'FOO';

	/**
	 * @deprecated
	 */
	public const DEPRECATED_FOO = 'FOO';

}

/**
 * @deprecated Deprecated for some reason.
 */
class DeprecatedBar
{

	public const FOO = 'FOO';

}
