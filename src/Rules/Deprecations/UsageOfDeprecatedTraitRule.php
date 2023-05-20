<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Stmt\TraitUse;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use function sprintf;

/**
 * @implements Rule<TraitUse>
 */
class UsageOfDeprecatedTraitRule implements Rule
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	/** @var DeprecatedScopeHelper */
	private $deprecatedScopeHelper;

	public function __construct(ReflectionProvider $reflectionProvider, DeprecatedScopeHelper $deprecatedScopeHelper)
	{
		$this->reflectionProvider = $reflectionProvider;
		$this->deprecatedScopeHelper = $deprecatedScopeHelper;
	}

	public function getNodeType(): string
	{
		return TraitUse::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($this->deprecatedScopeHelper->isScopeDeprecated($scope)) {
			return [];
		}

		$classReflection = $scope->getClassReflection();
		if ($classReflection === null) {
			throw new ShouldNotHappenException();
		}

		$errors = [];
		$className = $classReflection->getName();

		foreach ($node->traits as $traitNameNode) {
			$traitName = (string) $traitNameNode;

			try {
				$trait = $this->reflectionProvider->getClass($traitName);
				if (!$trait->isDeprecated()) {
					continue;
				}

				$description = $trait->getDeprecatedDescription();
				if ($description === null) {
					$errors[] = RuleErrorBuilder::message(sprintf(
						'Usage of deprecated trait %s in class %s.',
						$traitName,
						$className
					))->identifier('traitUse.deprecated')->build();
				} else {
					$errors[] = RuleErrorBuilder::message(sprintf(
						"Usage of deprecated trait %s in class %s:\n%s",
						$traitName,
						$className,
						$description
					))->identifier('traitUse.deprecated')->build();
				}
			} catch (ClassNotFoundException $e) {
				continue;
			}
		}

		return $errors;
	}

}
