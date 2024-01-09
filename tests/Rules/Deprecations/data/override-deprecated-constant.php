<?php

namespace OverrideDeprecatedConstant;

class Ancestor
{
	/**
	 * @deprecated
	 */
	public const DEPRECATED = '';

	/**
	 * @deprecated
	 */
	private const PRIVATE_DEPRECATED = '';
}

class Child extends Ancestor
{
	public const DEPRECATED = '';

	private const PRIVATE_DEPRECATED = '';
}
