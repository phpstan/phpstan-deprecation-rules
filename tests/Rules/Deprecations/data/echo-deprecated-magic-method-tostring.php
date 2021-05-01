<?php

namespace CheckDeprecatedStaticMethodCall;

function ()
{
	$bar = new MagicBar();
	echo $bar;
};

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
