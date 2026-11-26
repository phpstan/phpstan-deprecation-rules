<?php

namespace HttpResponseHeader173;

function readHeader(): void
{
	echo $http_response_header[0];
}

function assignHeader(): void
{
	$http_response_header = [];
}

function takesHeader(array $http_response_header): void
{
	echo $http_response_header[0];
}

function otherVariable(): void
{
	echo $ok;
}

function variableVariable(string $name): void
{
	echo $$name;
}

/** @deprecated */
function inDeprecatedScope(): void
{
	echo $http_response_header[0];
}
