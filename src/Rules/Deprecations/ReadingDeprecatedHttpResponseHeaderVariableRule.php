<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Foreach_;
use PHPStan\Analyser\Scope;
use PHPStan\Php\PhpVersion;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function is_string;

/**
 * @implements Rule<Node>
 */
class ReadingDeprecatedHttpResponseHeaderVariableRule implements Rule
{

	private const PHP_8_5 = 80500;

	private DeprecatedScopeHelper $deprecatedScopeHelper;

	private PhpVersion $phpVersion;

	public function __construct(
		DeprecatedScopeHelper $deprecatedScopeHelper,
		PhpVersion $phpVersion
	)
	{
		$this->deprecatedScopeHelper = $deprecatedScopeHelper;
		$this->phpVersion = $phpVersion;
	}

	public function getNodeType(): string
	{
		// A foreach value is compiled as a read, but PHPStan does not visit
		// that variable on its own. The foreach statement is visited.
		return Node::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($node instanceof Foreach_) {
			$node = $node->valueVar;
		}

		if (!$node instanceof Variable) {
			return [];
		}

		if ($this->deprecatedScopeHelper->isScopeDeprecated($scope)) {
			return [];
		}

		if ($this->phpVersion->getVersionId() < self::PHP_8_5) {
			return [];
		}

		if (!is_string($node->name) || $node->name !== 'http_response_header') {
			return [];
		}

		if ($node->getAttribute(HttpResponseHeaderFetchVisitor::ATTRIBUTE_NAME) !== HttpResponseHeaderFetchVisitor::FETCH_READ) {
			return [];
		}

		return [
			RuleErrorBuilder::message(
				'The predefined locally scoped $http_response_header variable is deprecated, call http_get_last_response_headers() instead.',
			)->identifier('variable.deprecated')->build(),
		];
	}

}
