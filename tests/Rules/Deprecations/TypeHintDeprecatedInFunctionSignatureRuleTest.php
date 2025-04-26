<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Functions\ExistingClassesInTypehintsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ExistingClassesInTypehintsRule>
 */
class TypeHintDeprecatedInFunctionSignatureRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(ExistingClassesInTypehintsRule::class);
	}

	public function test(): void
	{
		require_once __DIR__ . '/data/typehint-function-deprecated-class-definition.php';
		$this->analyse(
			[__DIR__ . '/data/typehint-function-deprecated-class.php'],
			[
				['Parameter $property of function TypeHintDeprecatedInFunctionSignature\setProperties() has typehint with deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty.', 9],
				['Parameter $property2 of function TypeHintDeprecatedInFunctionSignature\setProperties() has typehint with deprecated interface TypeHintDeprecatedInFunctionSignature\DeprecatedInterface.', 10],
				["Parameter \$property4 of function TypeHintDeprecatedInFunctionSignature\setProperties() has typehint with deprecated class TypeHintDeprecatedInFunctionSignature\VerboseDeprecatedProperty:\nI'll be back", 12],
				['Parameter $property6 of function TypeHintDeprecatedInFunctionSignature\setProperties() has typehint with deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty.', 14],
				['Return type of function TypeHintDeprecatedInFunctionSignature\setProperties() has typehint with deprecated class TypeHintDeprecatedInFunctionSignature\DeprecatedProperty.', 15],
			],
		);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../../rules.neon',
			...parent::getAdditionalConfigFiles(),
		];
	}

}
