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
				['Parameter $property of method TypeHintDeprecatedInFunctionSignature\Foo::setProperties() has typehint with deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty.', 13],
				['Parameter $property2 of method TypeHintDeprecatedInFunctionSignature\Foo::setProperties() has typehint with deprecated interface TypeHintDeprecatedInFunctionSignature\DeprecatedInterface.', 13],
				["Parameter \$property4 of method TypeHintDeprecatedInFunctionSignature\Foo::setProperties() has typehint with deprecated class TypeHintDeprecatedInFunctionSignature\VerboseDeprecatedProperty:\nI'll be back", 13],
				['Parameter $property6 of method TypeHintDeprecatedInFunctionSignature\Foo::setProperties() has typehint with deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty.', 13],
				['Return type of method TypeHintDeprecatedInFunctionSignature\Foo::setProperties() has typehint with deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty.', 13],
			]
		);
	}

}
