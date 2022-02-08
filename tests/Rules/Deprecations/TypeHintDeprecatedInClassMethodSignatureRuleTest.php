<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<TypeHintDeprecatedInClassMethodSignatureRule>
 */
class TypeHintDeprecatedInClassMethodSignatureRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new TypeHintDeprecatedInClassMethodSignatureRule(new DeprecatedClassHelper($this->createReflectionProvider()));
	}

	public function test(): void
	{
		require_once __DIR__ . '/data/typehint-class-method-deprecated-class-definition.php';
		$this->analyse(
			[__DIR__ . '/data/typehint-class-method-deprecated-class.php'],
			[
				['Parameter $property of method TypeHintDeprecatedInClassMethodSignature\Foo::setProperties() has typehint with deprecated class TypeHintDeprecatedInClassMethodSignature\DeprecatedProperty.', 13],
				['Parameter $property2 of method TypeHintDeprecatedInClassMethodSignature\Foo::setProperties() has typehint with deprecated interface TypeHintDeprecatedInClassMethodSignature\DeprecatedInterface.', 13],
				["Parameter \$property4 of method TypeHintDeprecatedInClassMethodSignature\Foo::setProperties() has typehint with deprecated class TypeHintDeprecatedInClassMethodSignature\VerboseDeprecatedProperty:\nI'll be back", 13],
				['Parameter $property6 of method TypeHintDeprecatedInClassMethodSignature\Foo::setProperties() has typehint with deprecated class TypeHintDeprecatedInClassMethodSignature\DeprecatedProperty.', 13],
				['Return type of method TypeHintDeprecatedInClassMethodSignature\Foo::setProperties() has typehint with deprecated class TypeHintDeprecatedInClassMethodSignature\DeprecatedProperty.', 13],
				['Parameter $property of method TypeHintDeprecatedInClassMethodSignature\FooImplNoOverride::oops() has typehint with deprecated class TypeHintDeprecatedInClassMethodSignature\DeprecatedProperty.', 50],
				['Parameter $property of method __construct() in anonymous class has typehint with deprecated class TypeHintDeprecatedInClassMethodSignature\DeprecatedProperty.', 71],
				['Return type of method getProperty() in anonymous class has typehint with deprecated class TypeHintDeprecatedInClassMethodSignature\DeprecatedProperty.', 76],
			]
		);
	}

}
