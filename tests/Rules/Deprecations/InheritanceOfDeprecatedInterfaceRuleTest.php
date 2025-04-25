<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Classes\ExistingClassesInInterfaceExtendsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ExistingClassesInInterfaceExtendsRule>
 */
class InheritanceOfDeprecatedInterfaceRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(ExistingClassesInInterfaceExtendsRule::class);
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
