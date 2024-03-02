<?php

namespace DeprecatedFilterConst;

function doFoo(mixed $filter): void {
	$x = filter_input(INPUT_GET,
		'search',
		FILTER_SANITIZE_STRING
	);
}

/**
 * @deprecated
 */
const MY_CONST = '1';

echo MY_CONST;


/**
 * @deprecated don't use it!
 */
const MY_CONST2 = '1';

echo MY_CONST2;
