<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use function defined;
use const PHP_VERSION_ID;

/**
 * @extends RuleTestCase<FetchingDeprecatedConstRule>
 */
class FetchingDeprecatedConstRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new FetchingDeprecatedConstRule(
			$this->createReflectionProvider(),
			new DeprecatedScopeHelper([new DefaultDeprecatedScopeResolver()])
		);
	}

	public function testFetchingDeprecatedConst(): void
	{
		if (!defined('FILTER_FLAG_SCHEME_REQUIRED') || !defined('FILTER_FLAG_HOST_REQUIRED')) {
			$this->markTestSkipped('Required constants are not available, PHP≥8?');
		}

		$expectedErrors = [];

		if (PHP_VERSION_ID >= 70300) {
			$expectedErrors[] = [
				'Use of constant FILTER_FLAG_SCHEME_REQUIRED is deprecated.',
				5,
			];
			$expectedErrors[] = [
				'Use of constant FILTER_FLAG_HOST_REQUIRED is deprecated.',
				6,
			];
			$expectedErrors[] = [
				'Use of constant FILTER_FLAG_SCHEME_REQUIRED is deprecated.',
				7,
			];
			$expectedErrors[] = [
				'Use of constant FILTER_FLAG_HOST_REQUIRED is deprecated.',
				8,
			];
			$expectedErrors[] = [
				'Use of constant FILTER_FLAG_SCHEME_REQUIRED is deprecated.',
				37,
			];
			$expectedErrors[] = [
				'Use of constant FILTER_FLAG_HOST_REQUIRED is deprecated.',
				38,
			];
		}

		require_once __DIR__ . '/data/fetching-deprecated-const-definition.php';
		$this->analyse(
			[__DIR__ . '/data/fetching-deprecated-const.php'],
			$expectedErrors
		);
	}

}
