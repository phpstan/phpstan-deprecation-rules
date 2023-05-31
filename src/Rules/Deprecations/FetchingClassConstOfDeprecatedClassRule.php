<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Type;
use function sprintf;
use function strtolower;

/**
 * @implements Rule<ClassConstFetch>
 */
class FetchingClassConstOfDeprecatedClassRule implements Rule
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	/** @var RuleLevelHelper */
	private $ruleLevelHelper;

	/** @var DeprecatedScopeHelper */
	private $deprecatedScopeHelper;

	public function __construct(ReflectionProvider $reflectionProvider, RuleLevelHelper $ruleLevelHelper, DeprecatedScopeHelper $deprecatedScopeHelper)
	{
		$this->reflectionProvider = $reflectionProvider;
		$this->ruleLevelHelper = $ruleLevelHelper;
		$this->deprecatedScopeHelper = $deprecatedScopeHelper;
	}

	public function getNodeType(): string
	{
		return ClassConstFetch::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($this->deprecatedScopeHelper->isScopeDeprecated($scope)) {
			return [];
		}

		if (!$node->name instanceof Identifier) {
			return [];
		}

		$constantName = $node->name->name;
		$referencedClasses = [];

		if ($node->class instanceof Name) {
			$referencedClasses[] = $scope->resolveName($node->class);
		} else {
			$classTypeResult = $this->ruleLevelHelper->findTypeToCheck(
				$scope,
				$node->class,
				'', // We don't care about the error message
				static function (Type $type) use ($constantName): bool {
					return $type->canAccessConstants()->yes() && $type->hasConstant($constantName)->yes();
				}
			);

			if ($classTypeResult->getType() instanceof ErrorType) {
				return [];
			}

			$referencedClasses = $classTypeResult->getReferencedClasses();
		}

		$errors = [];

		foreach ($referencedClasses as $referencedClass) {
			try {
				$class = $this->reflectionProvider->getClass($referencedClass);
			} catch (ClassNotFoundException $e) {
				continue;
			}

			if ($class->isDeprecated()) {
				$classDescription = $class->getDeprecatedDescription();
				if ($classDescription === null) {
					$errors[] = RuleErrorBuilder::message(sprintf(
						'Fetching class constant %s of deprecated %s %s.',
						$constantName,
						strtolower($class->getClassTypeDescription()),
						$referencedClass
					))->identifier(sprintf('classConstant.deprecated%s', $class->getClassTypeDescription()))->build();
				} else {
					$errors[] = RuleErrorBuilder::message(sprintf(
						"Fetching class constant %s of deprecated %s %s:\n%s",
						$constantName,
						strtolower($class->getClassTypeDescription()),
						$referencedClass,
						$classDescription
					))->identifier(sprintf('classConstant.deprecated%s', $class->getClassTypeDescription()))->build();
				}
			}

			if (strtolower($constantName) === 'class') {
				continue;
			}

			if (!$class->hasConstant($constantName)) {
				continue;
			}

			$constantReflection = $class->getConstant($constantName);

			if (!$constantReflection->isDeprecated()->yes()) {
				continue;
			}

			$description = $constantReflection->getDeprecatedDescription();
			if ($description === null) {
				$errors[] = RuleErrorBuilder::message(sprintf(
					'Fetching deprecated class constant %s of %s %s.',
					$constantName,
					strtolower($class->getClassTypeDescription()),
					$referencedClass
				))->identifier('classConstant.deprecated')->build();
			} else {
				$errors[] = RuleErrorBuilder::message(sprintf(
					"Fetching deprecated class constant %s of %s %s:\n%s",
					$constantName,
					strtolower($class->getClassTypeDescription()),
					$referencedClass,
					$description
				))->identifier('classConstant.deprecated')->build();
			}
		}

		return $errors;
	}

}
