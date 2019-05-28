<?php

namespace CheckDeprecatedFunctionCall;

function foo()
{

}

/**
 * @deprecated
 */
function deprecated_foo()
{

}

/**
 * @deprecated Global function? Seriously?
 */
function deprecated_with_description()
{

}
