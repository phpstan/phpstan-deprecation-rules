<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

/**
 * @extends \PHPStan\Testing\RuleTestCase<UsageOfDeprecatedCastRule>
 */
class UsageOfDeprecatedCastRuleTest extends \PHPStan\Testing\RuleTestCase
{

	protected function getRule(): \PHPStan\Rules\Rule
	{
		return new UsageOfDeprecatedCastRule();
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
