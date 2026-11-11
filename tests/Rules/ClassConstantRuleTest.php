<?php declare(strict_types = 1);

namespace PHPStan\Rules;

use PHPStan\Rules\Classes\ClassConstantRule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ClassConstantRule>
 */
class ClassConstantRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(ClassConstantRule::class);
	}

	public function testRule(): void
	{
		require_once __DIR__ . '/Deprecations/data/fetching-class-const-of-deprecated-class-definition.php';
		$this->analyse(
			[__DIR__ . '/Deprecations/data/fetching-class-const-of-deprecated-class.php'],
			[
				[
					'Access to constant on deprecated class FetchingClassConstOfDeprecatedClass\DeprecatedFoo.',
					6,
				],
				[
					'Access to constant on deprecated class FetchingClassConstOfDeprecatedClass\DeprecatedFoo.',
					11,
				],
				[
					'Access to constant on deprecated class FetchingClassConstOfDeprecatedClass\DeprecatedFoo.',
					12,
				],
			],
		);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../rules.neon',
			...parent::getAdditionalConfigFiles(),
		];
	}

}
