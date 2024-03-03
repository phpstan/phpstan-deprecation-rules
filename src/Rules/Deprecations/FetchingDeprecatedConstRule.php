<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Php\PhpVersion;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function sprintf;
use function sscanf;

/**
 * @implements Rule<ConstFetch>
 */
class FetchingDeprecatedConstRule implements Rule
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	/** @var DeprecatedScopeHelper */
	private $deprecatedScopeHelper;

	/** @var PhpVersion */
	private $phpVersion;

	public function __construct(
		ReflectionProvider $reflectionProvider,
		DeprecatedScopeHelper $deprecatedScopeHelper,
		PhpVersion $phpVersion
	)
	{
		$this->reflectionProvider = $reflectionProvider;
		$this->deprecatedScopeHelper = $deprecatedScopeHelper;
		$this->phpVersion = $phpVersion;
	}

	public function getNodeType(): string
	{
		return ConstFetch::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($this->deprecatedScopeHelper->isScopeDeprecated($scope)) {
			return [];
		}

		if (!$this->reflectionProvider->hasConstant($node->name, $scope)) {
			return [];
		}

		$constantReflection = $this->reflectionProvider->getConstant($node->name, $scope);
		$deprecatedMessage = $constantReflection->getDeprecatedDescription();

		// handle `@deprecated 7.3` as a min php version constraint.
		// this notation is used in jetbrains/phpstorm-stubs
		sscanf($deprecatedMessage ?? '', '%d.%d', $phpMajor, $phpMinor);
		if ($phpMajor !== null && $phpMinor !== null) {
			$phpVersionId = sprintf('%d%02d%02d', $phpMajor, $phpMinor, 0);

			if ($this->phpVersion->getVersionId() >= $phpVersionId) {
				return [
					RuleErrorBuilder::message(sprintf(
						'Use of constant %s is deprecated since PHP %s.',
						$constantReflection->getName(),
						$deprecatedMessage
					))->identifier('constant.deprecated')->build(),
				];
			}
		}

		if ($constantReflection->isDeprecated()->yes()) {
			return [
				RuleErrorBuilder::message(sprintf(
					$constantReflection->getDeprecatedDescription() ?? 'Use of constant %s is deprecated.',
					$constantReflection->getName()
				))->identifier('constant.deprecated')->build(),
			];
		}

		return [];
	}

}
