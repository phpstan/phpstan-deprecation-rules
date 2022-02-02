<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<InheritanceOfDeprecatedInterfaceRule>
 */
class InheritanceOfDeprecatedInterfaceRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new InheritanceOfDeprecatedInterfaceRule($this->createReflectionProvider());
	}

	public function testInheritanceOfDeprecatedInterfaces(): void
	{
		require_once __DIR__ . '/data/inheritance-of-deprecated-interface-definition.php';
		$this->analyse(
			[__DIR__ . '/data/inheritance-of-deprecated-interface.php'],
			[
				[
					'Interface InheritanceOfDeprecatedInterface\Foo2 extends deprecated interface InheritanceOfDeprecatedInterface\DeprecatedFooable.',
					10,
				],
				[
					'Interface InheritanceOfDeprecatedInterface\Foo3 extends deprecated interface InheritanceOfDeprecatedInterface\DeprecatedFooable.',
					15,
				],
				[
					'Interface InheritanceOfDeprecatedInterface\Foo3 extends deprecated interface InheritanceOfDeprecatedInterface\DeprecatedFooable2.',
					15,
				],
				[
					"Interface InheritanceOfDeprecatedInterface\Foo4 extends deprecated interface InheritanceOfDeprecatedInterface\DeprecatedWithDescription:\nImplement something else.",
					20,
				],
			]
		);
	}

}
