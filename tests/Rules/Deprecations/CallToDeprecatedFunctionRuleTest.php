<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<CallToDeprecatedFunctionRule>
 */
class CallToDeprecatedFunctionRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new CallToDeprecatedFunctionRule($this->createReflectionProvider());
	}

	public function testDeprecatedFunctionCall(): void
	{
		require_once __DIR__ . '/data/call-to-deprecated-function-definition.php';
		$this->analyse(
			[__DIR__ . '/data/call-to-deprecated-function.php'],
			[
				[
					'Call to deprecated function CheckDeprecatedFunctionCall\deprecated_foo().',
					8,
				],
				[
					'Call to deprecated function CheckDeprecatedFunctionCall\deprecated_foo().',
					9,
				],
				[
					"Call to deprecated function CheckDeprecatedFunctionCall\\deprecated_with_description():\nGlobal function? Seriously?",
					10,
				],
			]
		);
	}

}
