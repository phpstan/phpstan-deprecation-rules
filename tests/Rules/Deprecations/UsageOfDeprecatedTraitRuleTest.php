<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

/**
 * @extends \PHPStan\Testing\RuleTestCase<UsageOfDeprecatedTraitRule>
 */
class UsageOfDeprecatedTraitRuleTest extends \PHPStan\Testing\RuleTestCase
{

	protected function getRule(): \PHPStan\Rules\Rule
	{
		$broker = $this->createBroker();
		return new UsageOfDeprecatedTraitRule($broker);
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
					16,
				],
				[
					"Usage of deprecated trait UsageOfDeprecatedTrait\DeprecatedTraitWithDescription in class UsageOfDeprecatedTrait\Foo3:\nDo not use traits.",
					24,
				],
			]
		);
	}

}
