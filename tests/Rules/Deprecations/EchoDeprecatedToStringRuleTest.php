<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\RuleLevelHelper;

/**
 * @extends \PHPStan\Testing\RuleTestCase<EchoDeprecatedToStringRule>
 */
class EchoDeprecatedToStringRuleTest extends \PHPStan\Testing\RuleTestCase
{

	protected function getRule(): \PHPStan\Rules\Rule
	{
		$ruleLevelHelper = new RuleLevelHelper($this->createBroker(), true, false, true);

		return new EchoDeprecatedToStringRule($ruleLevelHelper);
	}

	public function testDeprecatedMagicMethodToStringCall(): void
	{
		require_once __DIR__ . '/data/echo-deprecated-magic-method-tostring.php';
		$this->analyse(
			[__DIR__ . '/data/echo-deprecated-magic-method-tostring.php'],
			[
				[
					'Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBar.',
					8,
				],
				[
					'Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBar.',
					9,
				],
			]
		);
	}

}
