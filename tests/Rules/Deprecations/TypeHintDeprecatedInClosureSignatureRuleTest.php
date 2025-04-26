<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Functions\ExistingClassesInClosureTypehintsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ExistingClassesInClosureTypehintsRule>
 */
class TypeHintDeprecatedInClosureSignatureRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(ExistingClassesInClosureTypehintsRule::class);
	}

	public function test(): void
	{
		require_once __DIR__ . '/data/typehint-anonymous-function-deprecated-class-definition.php';
		$this->analyse(
			[__DIR__ . '/data/typehint-anonymous-function-deprecated-class.php'],
			[
				['Parameter $property of anonymous function has typehint with deprecated class TypeHintDeprecatedInClosureSignature\DeprecatedProperty.', 6],
				['Parameter $property2 of anonymous function has typehint with deprecated interface TypeHintDeprecatedInClosureSignature\DeprecatedInterface.', 7],
				["Parameter \$property4 of anonymous function has typehint with deprecated class TypeHintDeprecatedInClosureSignature\VerboseDeprecatedProperty:\nI'll be back", 9],
				['Return type of anonymous function has typehint with deprecated class TypeHintDeprecatedInClosureSignature\DeprecatedProperty.', 11],
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
