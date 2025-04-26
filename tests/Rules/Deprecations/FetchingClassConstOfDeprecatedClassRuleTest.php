<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\RestrictedUsage\RestrictedClassConstantUsageRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RestrictedClassConstantUsageRule>
 */
class FetchingClassConstOfDeprecatedClassRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(RestrictedClassConstantUsageRule::class);
	}

	public function testFetchingClassConstOfDeprecatedClass(): void
	{
		require_once __DIR__ . '/data/fetching-class-const-of-deprecated-class-definition.php';
		$this->analyse(
			[__DIR__ . '/data/fetching-class-const-of-deprecated-class.php'],
			[
				[
					'Fetching deprecated class constant DEPRECATED_FOO of class FetchingClassConstOfDeprecatedClass\Foo.',
					9,
				],
				[
					"Fetching deprecated class constant DEPRECATED_WITH_DESCRIPTION of class FetchingClassConstOfDeprecatedClass\Foo:\nUse different constant.",
					13,
				],
				[
					"Fetching class constant FOO of deprecated class FetchingClassConstOfDeprecatedClass\DeprecatedBar:\nDeprecated for some reason.",
					14,
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
