<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

class TypeHintDeprecatedInFunctionSignatureRuleTest extends \PHPStan\Testing\RuleTestCase
{

	protected function getRule(): \PHPStan\Rules\Rule
	{
		$broker = $this->createBroker();

		return new TypeHintDeprecatedInFunctionSignatureRule($broker);
	}

	public function test(): void
	{
		require_once __DIR__ . '/data/typehint-deprecated-class-definition.php';
		$this->analyse(
			[__DIR__ . '/data/typehint-deprecated-class.php'],
			[
				['Usage of deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty in argument #0.', 10],
				['Usage of deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty in argument #1.', 10],
				['Usage of deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty in return type.', 10],
			]
		);
	}

}
