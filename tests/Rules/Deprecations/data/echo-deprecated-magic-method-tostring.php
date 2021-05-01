<?php

namespace CheckDeprecatedStaticMethodCall;

$bar = new MagicBar();
echo $bar;

class MagicBar
{
	/**
	 * @deprecated
	 */
	public function __toString()
	{
		return 'a string';
	}
}
