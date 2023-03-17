<?php

namespace OverrideDeprecatedProperty;

class Ancestor
{
	/**
	 * @deprecated
	 */
	public $deprecatedProperty;

	/**
	 * @deprecated
	 */
	private $privateDeprecatedProperty;

	/**
	 * @deprecated
	 */
	public $explicitlyNotDeprecated;
}

class Child extends Ancestor
{
	public $deprecatedProperty;

	private $privateDeprecatedProperty;

	/**
	 * @not-deprecated
	 */
	public $explicitlyNotDeprecated;
}

class GrandChild extends Child
{
	public $explicitlyNotDeprecated;
}
