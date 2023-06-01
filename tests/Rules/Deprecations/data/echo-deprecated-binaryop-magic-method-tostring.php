<?php

namespace EchoDeprecatedBinaryOpToStringRule;

function ()
{
	$bar = new MagicBar();
	echo "" == $bar;
	echo "" != $bar;
	echo "" === $bar;
	echo "" !== $bar;
	echo "" <=> $bar;

	$barDesc = new MagicBarWithDesc();
	echo "" == $barDesc;
	echo "" != $barDesc;
	echo "" === $barDesc;
	echo "" !== $barDesc;
	echo "" <=> $barDesc;

	$noDeps = new NoDeprecation();
	echo "" == $noDeps;
	echo "" != $noDeps;
	echo "" === $noDeps;
	echo "" !== $noDeps;
	echo "" <=> $noDeps;
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
