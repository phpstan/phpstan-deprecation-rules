<?php declare(strict_types = 1);

namespace PHPStan\DependencyInjection;

use PHPStan\Rules\Deprecations\DeprecatedScopeHelper;

final class LazyDeprecatedScopeResolverProvider
{

	public const EXTENSION_TAG = 'phpstan.deprecations.deprecatedScopeResolver';

	/** @var Container */
	private $container;

	/** @var DeprecatedScopeHelper */
	private $scopeHelper;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function get(): DeprecatedScopeHelper
	{
		if ($this->scopeHelper === null) {
			$this->scopeHelper = new DeprecatedScopeHelper(
				$this->container->getServicesByTag(self::EXTENSION_TAG)
			);
		}
		return $this->scopeHelper;
	}

}
