<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<OverrideDeprecatedPropertyRule>
 */
class OverrideDeprecatedPropertyRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new OverrideDeprecatedPropertyRule(new DeprecatedScopeHelper([new DefaultDeprecatedScopeResolver()]));
	}

	public function testDeprecatedPropertyOverride(): void
	{
		$this->analyse(
			[__DIR__ . '/data/override-deprecated-property.php'],
			[
				[
					'Class OverrideDeprecatedProperty\Child overrides deprecated property deprecatedProperty of class OverrideDeprecatedProperty\Ancestor.',
					25,
				],
			]
		);
	}

}
