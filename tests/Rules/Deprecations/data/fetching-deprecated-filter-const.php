<?php

namespace DeprecatedFilterConst;

function doFoo(mixed $filter): void {
	$x = filter_input(INPUT_GET,
		'search',
		FILTER_SANITIZE_STRING
	);
}
