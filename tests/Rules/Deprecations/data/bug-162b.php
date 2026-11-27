<?php

namespace Bug162b;

function foo(int $errno):void {
	if (
		version_compare( PHP_VERSION, '8.4', '<' )
		&& E_STRICT === $errno
	) {
		// ...
	}
}
