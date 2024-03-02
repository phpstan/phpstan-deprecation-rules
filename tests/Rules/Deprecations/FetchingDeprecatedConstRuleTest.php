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
				'Use of constant FILTER_FLAG_SCHEME_REQUIRED is deprecated since PHP 7.3.',
				5,
			];
			$expectedErrors[] = [
				'Use of constant FILTER_FLAG_HOST_REQUIRED is deprecated since PHP 7.3.',
				6,
			];
			$expectedErrors[] = [
				'Use of constant FILTER_FLAG_SCHEME_REQUIRED is deprecated since PHP 7.3.',
				7,
			];
			$expectedErrors[] = [
				'Use of constant FILTER_FLAG_HOST_REQUIRED is deprecated since PHP 7.3.',
				8,
			];
			$expectedErrors[] = [
				'Use of constant FILTER_FLAG_SCHEME_REQUIRED is deprecated since PHP 7.3.',
				37,
			];
			$expectedErrors[] = [
				'Use of constant FILTER_FLAG_HOST_REQUIRED is deprecated since PHP 7.3.',
				38,
			];
		}

		require_once __DIR__ . '/data/fetching-deprecated-const-definition.php';
		$this->analyse(
			[__DIR__ . '/data/fetching-deprecated-const.php'],
			$expectedErrors
		);
	}

	public function testDeprecatedFilterConsts()
	{
		if (!defined('FILTER_SANITIZE_STRING')) {
			$this->markTestSkipped('Required constants are not available');
		}

		$expectedErrors = [];

		if (PHP_VERSION_ID >= 80100) {
			$expectedErrors[] = [
				'Use of constant FILTER_SANITIZE_STRING is deprecated.',
				8,
			];
		}

		$expectedErrors[] = [
			'Use of constant MY_CONST is deprecated.',
			17,
		];
		$expectedErrors[] = [
			'Use of constant MY_CONST2 is deprecated.',
			25,
		];

		require_once __DIR__ . '/data/fetching-deprecated-filter-const.php';
		$this->analyse(
			[__DIR__ . '/data/fetching-deprecated-filter-const.php'],
			$expectedErrors
		);
	}

}
