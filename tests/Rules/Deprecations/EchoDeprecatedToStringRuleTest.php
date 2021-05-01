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
				[
					'Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBar.',
					10,
				],
				[
					'Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBar.',
					11,
				],
				[
					'Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBar.',
					12,
				],
				[
					'Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBar.',
					13,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					16,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					17,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					18,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					19,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					20,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					21,
				],
			]
		);
	}

}
