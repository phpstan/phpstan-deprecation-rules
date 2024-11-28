<?php

namespace CallWithDeprecatedIniOption;

function doFooBar(): void {
	var_dump(ini_set('memory_limit', '2048M'));
	var_dump(ini_get('memory_limit'));
}

function doFoo(): void {
	var_dump(ini_set('assert.active', false));
	var_dump(ini_get('assert.active'));
}

/** @deprecated */
function inDeprecatedFunction(): void {
	var_dump(ini_set('assert.active', false));
	var_dump(ini_get('assert.active'));
}
