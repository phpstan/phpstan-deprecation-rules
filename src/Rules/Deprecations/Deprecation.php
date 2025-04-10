<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use PHPStan\BetterReflection\Reflection\ReflectionFunction;
use PHPStan\BetterReflection\Reflection\ReflectionMethod;
use PHPStan\BetterReflection\Reflection\ReflectionProperty;
use PHPStan\Reflection\PropertyReflection;

/**
 * @api
 */
class Deprecation
{

	private ?string $description;

	private function __construct()
	{
	}

	public static function create(): self {
		return new self();
	}

	public function withDescription(?string $description): self {
		$clone = clone $this;
		$clone->description = $description;
		return $clone;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

}
