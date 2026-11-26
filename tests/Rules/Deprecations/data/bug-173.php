<?php

namespace HttpResponseHeader173;

function readHeader(): void
{
	echo $http_response_header[0];
}

function readThenAssign(): void
{
	echo $http_response_header[0];
	$http_response_header = [];
}

function assignThenRead(): void
{
	$http_response_header = [];
	echo $http_response_header[0];
}

function appendThenRead(): void
{
	$http_response_header[] = 'x';
	echo $http_response_header[0];
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

function issetHeader(): void
{
	var_dump(isset($http_response_header));
}

function issetThenRead(): void
{
	isset($http_response_header);
	echo $http_response_header[0];
}

function coalesceThenRead(): void
{
	$_ = $http_response_header ?? null;
	echo $http_response_header[0];
}

function assignCoalesceThenRead(): void
{
	$http_response_header ??= [];
	echo $http_response_header[0];
}

function outerAssignDoesNotSilenceClosure(): void
{
	$http_response_header = [];
	$inner = function (): void {
		echo $http_response_header[0];
	};
}

function foreachValue(array $list): void
{
	foreach ($list as $http_response_header) {
		echo $http_response_header[0];
	}
}

/** @deprecated */
function inDeprecatedScope(): void
{
	echo $http_response_header[0];
}

echo $http_response_header[0];
$http_response_header = [];
echo $http_response_header[0];

function afterFileAssign(): void
{
	echo $http_response_header[0];
}

function foreachKeySilencesBody(array $list): void
{
	foreach ($list as $http_response_header => $value) {
		echo $http_response_header[0];
	}
}

function listAssignReadsRightHandSide(): void
{
	[$http_response_header] = $http_response_header;
}
