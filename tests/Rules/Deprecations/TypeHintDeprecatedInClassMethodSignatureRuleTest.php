<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

class TypeHintDeprecatedInClassMethodSignatureRuleTest extends \PHPStan\Testing\RuleTestCase
{

	protected function getRule(): \PHPStan\Rules\Rule
	{
		$broker = $this->createBroker();

		return new TypeHintDeprecatedInClassMethodSignatureRule($broker);
	}

	public function test(): void
	{
		require_once __DIR__ . '/data/typehint-deprecated-class-definition.php';
		$this->analyse(
			[__DIR__ . '/data/typehint-deprecated-class.php'],
			[
				['Usage of deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty in argument #0.', 13],
				['Usage of deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty in argument #1.', 13],
				['Usage of deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty in argument #5.', 13],
				['Usage of deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty in return type.', 13],
			]
		);
	}

}
