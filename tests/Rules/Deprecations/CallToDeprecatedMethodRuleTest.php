<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

/**
 * @extends \PHPStan\Testing\RuleTestCase<CallToDeprecatedMethodRule>
 */
class CallToDeprecatedMethodRuleTest extends \PHPStan\Testing\RuleTestCase
{

	protected function getRule(): \PHPStan\Rules\Rule
	{
		return new CallToDeprecatedMethodRule($this->createReflectionProvider());
	}

	public function testDeprecatedMethodCall(): void
	{
		require_once __DIR__ . '/data/call-to-deprecated-method-definition.php';
		$this->analyse(
			[__DIR__ . '/data/call-to-deprecated-method.php'],
			[
				[
					'Call to deprecated method deprecatedFoo() of class CheckDeprecatedMethodCall\Foo.',
					7,
				],
				[
					'Call to deprecated method deprecatedFoo2() of class CheckDeprecatedMethodCall\Foo.',
					11,
				],
				[
					'Call to deprecated method deprecatedFooFromTrait() of class CheckDeprecatedMethodCall\Foo.',
					14,
				],
				[
					"Call to deprecated method deprecatedWithDescription() of class CheckDeprecatedMethodCall\\Foo:\nCall a different method instead.",
					15,
				],
				[
					"Call to deprecated method superDeprecated() of class CheckDeprecatedMethodCall\\FooClassFromInterface:\nThis is totally deprecated.",
					18,
				],
			]
		);
	}

}
