<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Classes\ExistingClassInTraitUseRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ExistingClassInTraitUseRule>
 */
class UsageOfDeprecatedTraitRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(ExistingClassInTraitUseRule::class);
	}

	public function testUsageOfDeprecatedTrait(): void
	{
		require_once __DIR__ . '/data/usage-of-deprecated-trait-definition.php';
		$this->analyse(
			[__DIR__ . '/data/usage-of-deprecated-trait.php'],
			[
				[
					'Usage of deprecated trait UsageOfDeprecatedTrait\DeprecatedFooTrait in class UsageOfDeprecatedTrait\Foo.',
					9,
				],
				[
					'Usage of deprecated trait UsageOfDeprecatedTrait\DeprecatedFooTrait in class UsageOfDeprecatedTrait\Foo2.',
					17,
				],
				[
					"Usage of deprecated trait UsageOfDeprecatedTrait\DeprecatedTraitWithDescription in class UsageOfDeprecatedTrait\Foo3:\nDo not use traits.",
					24,
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
