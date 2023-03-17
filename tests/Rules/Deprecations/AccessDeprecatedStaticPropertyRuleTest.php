<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<AccessDeprecatedStaticPropertyRule>
 */
class AccessDeprecatedStaticPropertyRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new AccessDeprecatedStaticPropertyRule($this->createReflectionProvider(), self::getContainer()->getByType(RuleLevelHelper::class));
	}

	public function testAccessDeprecatedStaticProperty(): void
	{
		require_once __DIR__ . '/data/access-deprecated-static-property-definition.php';
		$this->analyse(
			[__DIR__ . '/data/access-deprecated-static-property.php'],
			[
				[
					'Access to deprecated static property $deprecatedFoo of class AccessDeprecatedStaticProperty\Foo.',
					8,
				],
				[
					'Access to deprecated static property $deprecatedFoo of class AccessDeprecatedStaticProperty\Foo.',
					9,
				],
				[
					'Access to deprecated static property $deprecatedFoo of class AccessDeprecatedStaticProperty\Foo.',
					16,
				],
				[
					'Access to deprecated static property $deprecatedFoo of class AccessDeprecatedStaticProperty\Foo.',
					17,
				],
				[
					'Access to deprecated static property $deprecatedFooFromTrait of class AccessDeprecatedStaticProperty\Foo.',
					22,
				],
				[
					'Access to deprecated static property $deprecatedFooFromTrait of class AccessDeprecatedStaticProperty\Foo.',
					23,
				],
				[
					'Access to deprecated static property $deprecatedFooFromTrait of class AccessDeprecatedStaticProperty\Foo.',
					30,
				],
				[
					'Access to deprecated static property $deprecatedFooFromTrait of class AccessDeprecatedStaticProperty\Foo.',
					31,
				],
				[
					"Access to deprecated static property \$deprecatedWithDescription of class AccessDeprecatedStaticProperty\Foo:\nThis is probably a singleton.",
					33,
				],
				[
					'Access to deprecated static property $deprecatedFoo of class AccessDeprecatedStaticProperty\Foo.',
					117,
				],
				[
					'Access to deprecated static property $deprecatedOtherFoo of class AccessDeprecatedStaticProperty\Child.',
					118,
				],
				[
					'Access to deprecated static property $deprecatedFoo of class AccessDeprecatedStaticProperty\Foo.',
					119,
				],
				[
					'Access to deprecated static property $deprecatedOtherFoo of class AccessDeprecatedStaticProperty\Child.',
					120,
				],
			]
		);
	}

}
