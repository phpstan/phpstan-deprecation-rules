<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\RuleLevelHelper;

/**
 * @extends \PHPStan\Testing\RuleTestCase<EchoDeprecatedBinaryOpToStringRule>
 */
class EchoDeprecatedBinaryOpToStringRuleTest extends \PHPStan\Testing\RuleTestCase
{

	protected function getRule(): \PHPStan\Rules\Rule
	{
		$ruleLevelHelper = new RuleLevelHelper($this->createBroker(), true, false, true);

		return new EchoDeprecatedBinaryOpToStringRule($this->createBroker(), $ruleLevelHelper);
	}

	public function testDeprecatedMagicMethodToStringCall(): void
	{
		require_once __DIR__ . '/data/echo-deprecated-binaryop-magic-method-tostring.php';
		$this->analyse(
			[__DIR__ . '/data/echo-deprecated-binaryop-magic-method-tostring.php'],
			[
				[
					'Call to deprecated method __toString() of class EchoDeprecatedBinaryOpToStringRule\MagicBar.',
					8,
				],
				[
					'Call to deprecated method __toString() of class EchoDeprecatedBinaryOpToStringRule\MagicBar.',
					9,
				],
				[
					'Call to deprecated method __toString() of class EchoDeprecatedBinaryOpToStringRule\MagicBar.',
					10,
				],
				[
					'Call to deprecated method __toString() of class EchoDeprecatedBinaryOpToStringRule\MagicBar.',
					11,
				],
				[
					'Call to deprecated method __toString() of class EchoDeprecatedBinaryOpToStringRule\MagicBar.',
					12,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedBinaryOpToStringRule\MagicBarWithDesc:\nuse XY instead.",
					15,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedBinaryOpToStringRule\MagicBarWithDesc:\nuse XY instead.",
					16,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedBinaryOpToStringRule\MagicBarWithDesc:\nuse XY instead.",
					17,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedBinaryOpToStringRule\MagicBarWithDesc:\nuse XY instead.",
					18,
				],
				[
					"Call to deprecated method __toString() of class EchoDeprecatedBinaryOpToStringRule\MagicBarWithDesc:\nuse XY instead.",
					19,
				],
			]
		);
	}

}
