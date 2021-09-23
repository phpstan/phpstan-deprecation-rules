<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

/**
 * @extends \PHPStan\Testing\RuleTestCase<TypeHintDeprecatedInFunctionSignatureRule>
 */
class TypeHintDeprecatedInFunctionSignatureRuleTest extends \PHPStan\Testing\RuleTestCase
{

	protected function getRule(): \PHPStan\Rules\Rule
	{
		return new TypeHintDeprecatedInFunctionSignatureRule(new DeprecatedClassHelper($this->createReflectionProvider()));
	}

	public function test(): void
	{
		require_once __DIR__ . '/data/typehint-function-deprecated-class-definition.php';
		$this->analyse(
			[__DIR__ . '/data/typehint-function-deprecated-class.php'],
			[
				['Parameter $property of function TypeHintDeprecatedInFunctionSignature\setProperties() has typehint with deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty.', 8],
				['Parameter $property2 of function TypeHintDeprecatedInFunctionSignature\setProperties() has typehint with deprecated interface TypeHintDeprecatedInFunctionSignature\DeprecatedInterface.', 8],
				["Parameter \$property4 of function TypeHintDeprecatedInFunctionSignature\setProperties() has typehint with deprecated class TypeHintDeprecatedInFunctionSignature\VerboseDeprecatedProperty:\nI'll be back", 8],
				['Parameter $property6 of function TypeHintDeprecatedInFunctionSignature\setProperties() has typehint with deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty.', 8],
				['Return type of function TypeHintDeprecatedInFunctionSignature\setProperties() has typehint with deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty.', 8],
			]
		);
	}

}
