<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

/**
 * @extends \PHPStan\Testing\RuleTestCase<FetchingDeprecatedConstRule>
 */
class FetchingDeprecatedConstRuleTest extends \PHPStan\Testing\RuleTestCase
{

	protected function getRule(): \PHPStan\Rules\Rule
	{
		return new FetchingDeprecatedConstRule($this->createReflectionProvider());
	}

	public function testFetchingDeprecatedConst(): void
	{
		if (!defined('FILTER_FLAG_SCHEME_REQUIRED') || !defined('FILTER_FLAG_HOST_REQUIRED')) {
			$this->markTestSkipped('Required constants are not available, PHP≥8?');
		}

		require_once __DIR__ . '/data/fetching-deprecated-const-definition.php';
		$this->analyse(
			[__DIR__ . '/data/fetching-deprecated-const.php'],
			[
				[
					'Use of constant FILTER_FLAG_SCHEME_REQUIRED is deprecated since PHP 7.3.',
					5,
				],
				[
					'Use of constant FILTER_FLAG_HOST_REQUIRED is deprecated since PHP 7.3.',
					6,
				],
				[
					'Use of constant FILTER_FLAG_SCHEME_REQUIRED is deprecated since PHP 7.3.',
					7,
				],
				[
					'Use of constant FILTER_FLAG_HOST_REQUIRED is deprecated since PHP 7.3.',
					8,
				],
				[
					'Use of constant FILTER_FLAG_SCHEME_REQUIRED is deprecated since PHP 7.3.',
					37,
				],
				[
					'Use of constant FILTER_FLAG_HOST_REQUIRED is deprecated since PHP 7.3.',
					38,
				],
			]
		);
	}

}
