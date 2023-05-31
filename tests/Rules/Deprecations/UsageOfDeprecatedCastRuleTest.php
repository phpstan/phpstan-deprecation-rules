<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<UsageOfDeprecatedCastRule>
 */
class UsageOfDeprecatedCastRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new UsageOfDeprecatedCastRule(
			new DeprecatedScopeHelper([new DefaultDeprecatedScopeResolver()])
		);
	}

	public function testUsageOfDeprecatedTrait(): void
	{
		require_once __DIR__ . '/data/usage-of-deprecated-cast.php';
		$this->analyse(
			[__DIR__ . '/data/usage-of-deprecated-cast.php'],
			[
				[
					'Casting class UsageOfDeprecatedCast\Foo to string is deprecated.',
					17,
				],
			]
		);
	}

}
