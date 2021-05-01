<?php

namespace EchoDeprecatedToStringRule;

function ()
{
	$bar = new MagicBar();
	echo $bar;
	echo $bar . "hallo";
	echo "la". ($bar."3") . "lu";
	echo "la". (string)($bar) . "lu";
	echo "la". ($x=$bar) . "lu";
	echo "la". ($x.=$bar) . "lu";
	echo "" ?: $bar;
	echo "" == $bar;
	echo "" != $bar;
	echo "" === $bar;
	echo "" !== $bar;
	echo "" <=> $bar;

	$barDesc = new MagicBarWithDesc();
	echo $barDesc;
	echo $barDesc . "hallo";
	echo "la". ($barDesc."3") . "lu";
	echo "la". (string)($barDesc) . "lu";
	echo "la". ($x=$barDesc) . "lu";
	echo "la". ($x.=$barDesc) . "lu";
	echo "" ?: $barDesc;
	echo "" == $barDesc;
	echo "" != $barDesc;
	echo "" === $barDesc;
	echo "" !== $barDesc;
	echo "" <=> $barDesc;

	$noDeps = new NoDeprecation();
	echo $noDeps;
	echo $noDeps . "hallo";
	echo "la". ($noDeps."3") . "lu";
	echo "la". (string)($noDeps) . "lu";
	echo "la". ($x=$noDeps) . "lu";
	echo "la". ($x.=$noDeps) . "lu";
	echo "" ?: $noDeps;
	echo "" == $noDeps;
	echo "" != $noDeps;
	echo "" === $noDeps;
	echo "" !== $noDeps;
	echo "" <=> $noDeps;

	echo "la". "le" . "lu";
	echo "la". 5 . "lu";
	echo "la". (5+3) . "lu";
	echo "la". (5*3) . "lu";
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
