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
					'Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBar.',
					14,
				],
				[
					'Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBar.',
					15,
				],
				[
					'Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBar.',
					16,
				],
				[
					'Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBar.',
					17,
				],
				[
					'Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBar.',
					18,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					21,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					22,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					23,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					24,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					25,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					26,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					27,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					28,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					29,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					30,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedToStringRule\MagicBarWithDesc:\nuse XY instead.",
					31,
				],
			]
		);
	}

}
