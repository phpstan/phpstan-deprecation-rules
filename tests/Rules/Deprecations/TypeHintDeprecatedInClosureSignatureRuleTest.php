<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

class TypeHintDeprecatedInClosureSignatureRuleTest extends \PHPStan\Testing\RuleTestCase
{

	protected function getRule(): \PHPStan\Rules\Rule
	{
		$broker = $this->createBroker();

		return new TypeHintDeprecatedInClosureSignatureRule(new DeprecatedClassHelper($broker));
	}

	public function test(): void
	{
		require_once __DIR__ . '/data/typehint-anonymous-function-deprecated-class-definition.php';
		$this->analyse(
			[__DIR__ . '/data/typehint-anonymous-function-deprecated-class.php'],
			[
				['Parameter $property of anonymous function has typehint with deprecated class TypeHintDeprecatedInClosureSignature\DeprecatedProperty.', 5],
				['Parameter $property2 of anonymous function has typehint with deprecated interface TypeHintDeprecatedInClosureSignature\DeprecatedInterface.', 5],
				["Parameter \$property4 of anonymous function has typehint with deprecated class TypeHintDeprecatedInClosureSignature\VerboseDeprecatedProperty:\nI'll be back", 5],
				['Return type of anonymous function has typehint with deprecated class TypeHintDeprecatedInClosureSignature\DeprecatedProperty.', 5],
			]
		);
	}

}
