<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\RestrictedUsage\RestrictedUsageOfDeprecatedStringCastRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RestrictedUsageOfDeprecatedStringCastRule>
 */
class UsageOfDeprecatedCastRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(RestrictedUsageOfDeprecatedStringCastRule::class);
	}

	public function testUsageOfDeprecatedCast(): void
	{
		require_once __DIR__ . '/data/usage-of-deprecated-cast.php';
		$this->analyse(
			[__DIR__ . '/data/usage-of-deprecated-cast.php'],
			[
				[
					'Casting class UsageOfDeprecatedCast\Foo to string is deprecated.',
					17,
				],
			],
		);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../../rules.neon',
			...parent::getAdditionalConfigFiles(),
		];
	}

}
