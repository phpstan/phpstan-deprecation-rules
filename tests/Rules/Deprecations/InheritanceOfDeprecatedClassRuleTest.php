<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Classes\ExistingClassInClassExtendsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ExistingClassInClassExtendsRule>
 */
class InheritanceOfDeprecatedClassRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(ExistingClassInClassExtendsRule::class);
	}

	public function testInheritanceOfDeprecatedClassInClasses(): void
	{
		require_once __DIR__ . '/data/inheritance-of-deprecated-class-definition.php';
		$this->analyse(
			[__DIR__ . '/data/inheritance-of-deprecated-class-in-classes.php'],
			[
				[
					'Class InheritanceOfDeprecatedClass\Bar2 extends deprecated class InheritanceOfDeprecatedClass\DeprecatedFoo.',
					10,
				],
				[
					"Class InheritanceOfDeprecatedClass\Bar3 extends deprecated class InheritanceOfDeprecatedClass\DeprecatedWithDescription:\nInheritance is deprecated.",
					15,
				],
			],
		);
	}

	public function testInheritanceOfDeprecatedClassInAnonymousClasses(): void
	{
		require_once __DIR__ . '/data/inheritance-of-deprecated-class-definition.php';
		$this->analyse(
			[__DIR__ . '/data/inheritance-of-deprecated-class-in-anonymous-classes.php'],
			[
				[
					'Anonymous class extends deprecated class InheritanceOfDeprecatedClass\DeprecatedFoo.',
					9,
				],
				[
					"Anonymous class extends deprecated class InheritanceOfDeprecatedClass\DeprecatedWithDescription:\nInheritance is deprecated.",
					13,
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
