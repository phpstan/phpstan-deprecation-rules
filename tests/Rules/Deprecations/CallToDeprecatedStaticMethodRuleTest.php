<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<CallToDeprecatedStaticMethodRule>
 */
class CallToDeprecatedStaticMethodRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new CallToDeprecatedStaticMethodRule($this->createReflectionProvider(), self::getContainer()->getByType(RuleLevelHelper::class));
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
					'Call to method foo() of deprecated class CheckDeprecatedStaticMethodCall\Foo.',
					11,
				],
				[
					'Call to method deprecatedFoo() of deprecated class CheckDeprecatedStaticMethodCall\Foo.',
					12,
				],
				[
					'Call to deprecated method deprecatedFoo() of class CheckDeprecatedStaticMethodCall\Foo.',
					12,
				],
				[
					'Call to method deprecatedFoo2() of deprecated class CheckDeprecatedStaticMethodCall\Foo.',
					13,
				],
				[
					'Call to deprecated method deprecatedFoo2() of class CheckDeprecatedStaticMethodCall\Foo.',
					13,
				],
				[
					"Call to method foo() of deprecated class CheckDeprecatedStaticMethodCall\Foo:\nDo not touch this at all.",
					15,
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
			]
		);
	}

}
