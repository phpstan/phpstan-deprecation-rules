<?php

namespace Bug162;

function foo(int $errno):void {
	if (PHP_VERSION_ID >= 80400) {
		if (E_STRICT === $errno) {

		}
	}

	if (PHP_VERSION_ID < 80400) {
		if (E_STRICT === $errno) {

		}
	}

	if (E_STRICT === $errno ) {
	}
}
