<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\RestrictedUsage\RestrictedMethodUsageRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RestrictedMethodUsageRule>
 */
class CallToDeprecatedMethodRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(RestrictedMethodUsageRule::class);
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
					'Call to deprecated method deprecatedFoo() of class CheckDeprecatedMethodCall\Bar.',
					10,
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
					"Call to deprecated method prophesize() of class CheckDeprecatedMethodCall\\UsingDeprecatedMethodFromTrait:\nUse TraitReplacingDeprecatedMethod::prophesize()",
					64,
				],
			],
		);
	}

	public function testDeprecatedMethodOfAnonymousClass(): void
	{
		$file = __DIR__ . '/data/deprecated-member-of-anonymous-class.php';
		$this->analyse(
			[$file],
			[
				[
					'Call to deprecated method deprecatedMethod() of class class@anonymous/' . $file . ':5.',
					20,
				],
				[
					'Call to method method() of deprecated class class@anonymous/' . $file . ':24.',
					32,
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
