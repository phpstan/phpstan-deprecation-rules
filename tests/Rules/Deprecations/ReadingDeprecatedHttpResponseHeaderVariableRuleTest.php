<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Php\PhpVersion;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ReadingDeprecatedHttpResponseHeaderVariableRule>
 */
class ReadingDeprecatedHttpResponseHeaderVariableRuleTest extends RuleTestCase
{

	private int $phpVersionId = 80500;

	protected function getRule(): Rule
	{
		return new ReadingDeprecatedHttpResponseHeaderVariableRule(
			new DeprecatedScopeHelper([new DefaultDeprecatedScopeResolver()]),
			new PhpVersion($this->phpVersionId, PhpVersion::SOURCE_CONFIG),
		);
	}

	public function testPhp85(): void
	{
		$this->phpVersionId = 80500;
		$this->analyse([__DIR__ . '/data/bug-173.php'], [
			[
				'The predefined locally scoped $http_response_header variable is deprecated, call http_get_last_response_headers() instead.',
				7,
			],
			[
				'The predefined locally scoped $http_response_header variable is deprecated, call http_get_last_response_headers() instead.',
				12,
			],
			[
				'The predefined locally scoped $http_response_header variable is deprecated, call http_get_last_response_headers() instead.',
				51,
			],
			[
				'The predefined locally scoped $http_response_header variable is deprecated, call http_get_last_response_headers() instead.',
				57,
			],
			[
				'The predefined locally scoped $http_response_header variable is deprecated, call http_get_last_response_headers() instead.',
				70,
			],
			[
				'The predefined locally scoped $http_response_header variable is deprecated, call http_get_last_response_headers() instead.',
				76,
			],
			[
				'The predefined locally scoped $http_response_header variable is deprecated, call http_get_last_response_headers() instead.',
				77,
			],
			[
				'The predefined locally scoped $http_response_header variable is deprecated, call http_get_last_response_headers() instead.',
				87,
			],
			[
				'The predefined locally scoped $http_response_header variable is deprecated, call http_get_last_response_headers() instead.',
				93,
			],
			[
				'The predefined locally scoped $http_response_header variable is deprecated, call http_get_last_response_headers() instead.',
				105,
			],
		]);
	}

	public function testPhp84(): void
	{
		$this->phpVersionId = 80400;
		$this->analyse([__DIR__ . '/data/bug-173.php'], []);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../../rules.neon',
			...parent::getAdditionalConfigFiles(),
		];
	}

}
