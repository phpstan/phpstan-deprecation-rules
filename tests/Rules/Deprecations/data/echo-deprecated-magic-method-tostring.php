<?php

namespace EchoDeprecatedToStringRule;

function ()
{
	$bar = new MagicBar();
	echo $bar;
	echo $bar . "hallo";

	$barDesc = new MagicBarWithDesc();
	echo $barDesc;
	echo $barDesc . "hallo";

	$noDeps = new NoDeprecation();
	echo $noDeps;
	echo $noDeps . "hallo";
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

class MagicBarWithDesc
{
	/**
	 * @deprecated use XY instead.
	 */
	public function __toString()
	{
		return 'a string';
	}
}

class NoDeprecation
{
	public function __toString()
	{
		return 'a string';
	}
}
