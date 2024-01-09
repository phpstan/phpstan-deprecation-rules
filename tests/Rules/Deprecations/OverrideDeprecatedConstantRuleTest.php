<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<OverrideDeprecatedConstantRule>
 */
class OverrideDeprecatedConstantRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new OverrideDeprecatedConstantRule(new DeprecatedScopeHelper([new DefaultDeprecatedScopeResolver()]));
	}

	public function testDeprecatedConstantOverride(): void
	{
		$this->analyse(
			[__DIR__ . '/data/override-deprecated-constant.php'],
			[
				[
					'Class OverrideDeprecatedConstant\Child overrides deprecated const DEPRECATED of class OverrideDeprecatedConstant\Ancestor.',
					20,
				],
			]
		);
	}

}
