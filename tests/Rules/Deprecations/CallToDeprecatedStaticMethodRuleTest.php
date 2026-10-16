<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\RestrictedUsage\RestrictedStaticMethodUsageRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RestrictedStaticMethodUsageRule>
 */
class CallToDeprecatedStaticMethodRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(RestrictedStaticMethodUsageRule::class);
	}

	public function testDeprecatedStaticMethodCall(): void
	{
		require_once __DIR__ . '/data/call-to-deprecated-static-method-definition.php';
		$this->analyse(
			[__DIR__ . '/data/call-to-deprecated-static-method.php'],
			[
				[
					'Call to deprecated method deprecatedFoo() of class CheckDeprecatedStaticMethodCall\Foo.',
					6,
				],
				[
					'Call to deprecated method deprecatedFoo() of class CheckDeprecatedStaticMethodCall\Bar.',
					8,
				],
				[
					'Call to deprecated method deprecatedFoo2() of class CheckDeprecatedStaticMethodCall\Foo.',
					9,
				],
				[
					'Call to deprecated method deprecatedFoo() of class CheckDeprecatedStaticMethodCall\Foo.',
					12,
				],
				[
					'Call to deprecated method deprecatedFoo2() of class CheckDeprecatedStaticMethodCall\Foo.',
					13,
				],
				[
					"Call to deprecated method deprecatedWithDescription() of class CheckDeprecatedStaticMethodCall\Foo:\nThis is probably a singleton.",
					16,
				],
				[
					'Call to deprecated method deprecatedFoo() of class CheckDeprecatedStaticMethodCall\Foo.',
					33,
				],
				[
					'Call to deprecated method deprecatedFoo() of class CheckDeprecatedStaticMethodCall\Foo.',
					74,
				],
				[
					'Call to deprecated method deprecatedOtherFoo() of class CheckDeprecatedStaticMethodCall\Child.',
					75,
				],
				[
					'Call to deprecated method deprecatedFoo() of class CheckDeprecatedStaticMethodCall\Foo.',
					76,
				],
				[
					'Call to deprecated method deprecatedOtherFoo() of class CheckDeprecatedStaticMethodCall\Child.',
					77,
				],
				[
					'Call to method doDeprecatedBar() of deprecated class CheckDeprecatedStaticMethodCall\DeprecatedBar.',
					81,
				],
				[
					'Call to method staticMethodFromDeprecatedTrait() of deprecated trait CheckDeprecatedStaticMethodCall\DeprecatedTrait.',
					83,
				],
				[
					"Call to method staticMethodFromDeprecatedTraitWithDescription() of deprecated trait CheckDeprecatedStaticMethodCall\\DeprecatedTraitWithDescription:\nUse something else.",
					84,
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
