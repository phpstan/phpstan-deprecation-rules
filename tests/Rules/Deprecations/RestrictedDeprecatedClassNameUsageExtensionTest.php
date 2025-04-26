<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Methods\CallStaticMethodsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<CallStaticMethodsRule>
 */
class RestrictedDeprecatedClassNameUsageExtensionTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(CallStaticMethodsRule::class);
	}

	public function testStaticMethodCallOnDeprecatedSubclass(): void
	{
		require_once __DIR__ . '/data/call-to-deprecated-static-method-definition.php';

		$this->analyse([__DIR__ . '/data/call-to-deprecated-static-method.php'], [
			[
				'Call to static method foo() on deprecated class CheckDeprecatedStaticMethodCall\DeprecatedBar.',
				11,
			],
			[
				"Call to static method foo() on deprecated class CheckDeprecatedStaticMethodCall\DeprecatedBaz:\nDo not touch this at all.",
				15,
			],
		]);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../../rules.neon',
			...parent::getAdditionalConfigFiles(),
		];
	}

}
