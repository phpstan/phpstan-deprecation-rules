<?php

namespace DeprecatedMemberOfAnonymousClass;

$foo = new class {

	/** @deprecated */
	public $deprecatedProperty;

	/** @deprecated */
	public const DEPRECATED_CONSTANT = 1;

	/** @deprecated */
	public function deprecatedMethod(): void
	{
	}

};

$foo->deprecatedMethod();
echo $foo->deprecatedProperty;
echo $foo::DEPRECATED_CONSTANT;

$bar = new /** @deprecated */ class {

	public function method(): void
	{
	}

};

$bar->method();
