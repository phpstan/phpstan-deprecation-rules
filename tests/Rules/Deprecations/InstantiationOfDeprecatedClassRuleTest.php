<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Classes\InstantiationRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<InstantiationRule>
 */
class InstantiationOfDeprecatedClassRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(InstantiationRule::class);
	}

	public function testInstantiationOfDeprecatedClass(): void
	{
		require_once __DIR__ . '/data/instantiation-of-deprecated-class-definition.php';
		$this->analyse(
			[__DIR__ . '/data/instantiation-of-deprecated-class.php'],
			[
				[
					'Instantiation of deprecated class InstantiationOfDeprecatedClass\DeprecatedFoo.',
					6,
				],
				[
					"Instantiation of deprecated class InstantiationOfDeprecatedClass\DeprecatedWithDescription:\nDo not instantiate.",
					7,
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
