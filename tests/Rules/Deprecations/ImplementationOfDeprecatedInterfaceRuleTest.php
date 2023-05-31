<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ImplementationOfDeprecatedInterfaceRule>
 */
class ImplementationOfDeprecatedInterfaceRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new ImplementationOfDeprecatedInterfaceRule(
			$this->createReflectionProvider(),
			new DeprecatedScopeHelper([new DefaultDeprecatedScopeResolver()])
		);
	}

	public function testImplementationOfDeprecatedInterfacesInClasses(): void
	{
		require_once __DIR__ . '/data/implementation-of-deprecated-interface-definition.php';
		$this->analyse(
			[__DIR__ . '/data/implementation-of-deprecated-interface-in-classes.php'],
			[
				[
					'Class ImplementationOfDeprecatedInterface\Foo2 implements deprecated interface ImplementationOfDeprecatedInterface\DeprecatedFooable.',
					10,
				],
				[
					'Class ImplementationOfDeprecatedInterface\Foo3 implements deprecated interface ImplementationOfDeprecatedInterface\DeprecatedFooable.',
					15,
				],
				[
					'Class ImplementationOfDeprecatedInterface\Foo3 implements deprecated interface ImplementationOfDeprecatedInterface\DeprecatedFooable2.',
					15,
				],
				[
					"Class ImplementationOfDeprecatedInterface\Foo4 implements deprecated interface ImplementationOfDeprecatedInterface\DeprecatedWithDescription:\nBetter interface imminent?",
					20,
				],
			]
		);
	}

	public function testImplementationOfDeprecatedInterfacesInAnonymousClasses(): void
	{
		require_once __DIR__ . '/data/implementation-of-deprecated-interface-definition.php';
		$this->analyse(
			[__DIR__ . '/data/implementation-of-deprecated-interface-in-anonymous-classes.php'],
			[
				[
					'Anonymous class implements deprecated interface ImplementationOfDeprecatedInterface\DeprecatedFooable.',
					9,
				],
				[
					'Anonymous class implements deprecated interface ImplementationOfDeprecatedInterface\DeprecatedFooable.',
					13,
				],
				[
					'Anonymous class implements deprecated interface ImplementationOfDeprecatedInterface\DeprecatedFooable2.',
					13,
				],
				[
					"Anonymous class implements deprecated interface ImplementationOfDeprecatedInterface\DeprecatedWithDescription:\nBetter interface imminent?",
					17,
				],
			]
		);
	}

}
