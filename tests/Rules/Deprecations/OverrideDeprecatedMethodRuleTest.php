<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<OverrideDeprecatedMethodRule>
 */
class OverrideDeprecatedMethodRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new OverrideDeprecatedMethodRule(new DeprecatedScopeHelper([new DefaultDeprecatedScopeResolver()]));
	}

	public function testDeprecatedMethodOverride(): void
	{
		$this->analyse(
			[__DIR__ . '/data/override-deprecated-method.php'],
			[
				[
					'Class OverrideDeprecatedMethod\Child overrides deprecated method deprecatedMethod of class OverrideDeprecatedMethod\Ancestor.',
					49,
				],
				[
					'Class OverrideDeprecatedMethod\Child overrides deprecated method deprecatedInInterface of interface OverrideDeprecatedMethod\Deprecated.',
					61,
				],
				[
					'Class OverrideDeprecatedMethod\Child overrides deprecated method deprecatedInTrait of class OverrideDeprecatedMethod\Ancestor.',
					64,
				],
				[
					'Class OverrideDeprecatedMethod\GrandChild overrides deprecated method deprecatedInChild of class OverrideDeprecatedMethod\Child.',
					73,
				],
			]
		);
	}

}
