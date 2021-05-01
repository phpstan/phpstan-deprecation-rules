<?php

namespace EchoDeprecatedToStringRule;

function ()
{
	$bar = new MagicBar();
	echo $bar;
	echo $bar . "hallo";
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
