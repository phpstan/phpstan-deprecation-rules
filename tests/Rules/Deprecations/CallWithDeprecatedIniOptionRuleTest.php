<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Php\PhpVersion;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use const PHP_VERSION_ID;

/**
 * @extends RuleTestCase<CallWithDeprecatedIniOptionRule>
 */
class CallWithDeprecatedIniOptionRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new CallWithDeprecatedIniOptionRule(
			$this->createReflectionProvider(),
			new DeprecatedScopeHelper([new DefaultDeprecatedScopeResolver()]),
			self::getContainer()->getByType(PhpVersion::class),
		);
	}

	public function testRule(): void
	{
		$expectedErrors = [];
		if (PHP_VERSION_ID >= 80300) {
			$expectedErrors = [
				[
					"Call to function ini_set() with deprecated option 'assert.active'.",
					11,
				],
				[
					"Call to function ini_get() with deprecated option 'assert.active'.",
					12,
				],
			];
		}

		$this->analyse(
			[__DIR__ . '/data/call-with-deprecation-ini-option.php'],
			$expectedErrors,
		);
	}

}
