<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function sprintf;

/**
 * @implements Rule<Class_>
 */
class ImplementationOfDeprecatedInterfaceRule implements Rule
{

	private ReflectionProvider $reflectionProvider;

	private DeprecatedScopeHelper $deprecatedScopeHelper;

	private DeprecationHelper $deprecationHelper;

	public function __construct(ReflectionProvider $reflectionProvider, DeprecatedScopeHelper $deprecatedScopeHelper, DeprecationHelper $deprecationHelper)
	{
		$this->reflectionProvider = $reflectionProvider;
		$this->deprecatedScopeHelper = $deprecatedScopeHelper;
		$this->deprecationHelper = $deprecationHelper;
	}

	public function getNodeType(): string
	{
		return Class_::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($this->deprecatedScopeHelper->isScopeDeprecated($scope)) {
			return [];
		}

		$errors = [];

		$className = isset($node->namespacedName)
			? (string) $node->namespacedName
			: (string) $node->name;

		try {
			$class = $this->reflectionProvider->getClass($className);
		} catch (ClassNotFoundException $e) {
			return [];
		}

		$classDeprecation = $this->deprecationHelper->getDeprecation($class);
		if ($classDeprecation !== null) {
			return [];
		}

		foreach ($node->implements as $implement) {
			$interfaceName = (string) $implement;

			try {
				$interface = $this->reflectionProvider->getClass($interfaceName);

				$interfaceDeprecation = $this->deprecationHelper->getDeprecation($interface);
				if ($interfaceDeprecation !== null) {
					$description = $interfaceDeprecation->getDescription();
					if (!$class->isAnonymous()) {
						if ($description === null) {
							$errors[] = RuleErrorBuilder::message(sprintf(
								'Class %s implements deprecated interface %s.',
								$className,
								$interfaceName,
							))->identifier('class.implementsDeprecatedInterface')->build();
						} else {
							$errors[] = RuleErrorBuilder::message(sprintf(
								"Class %s implements deprecated interface %s:\n%s",
								$className,
								$interfaceName,
								$description,
							))->identifier('class.implementsDeprecatedInterface')->build();
						}
					} else {
						if ($description === null) {
							$errors[] = RuleErrorBuilder::message(sprintf(
								'Anonymous class implements deprecated interface %s.',
								$interfaceName,
							))->identifier('class.implementsDeprecatedInterface')->build();
						} else {
							$errors[] = RuleErrorBuilder::message(sprintf(
								"Anonymous class implements deprecated interface %s:\n%s",
								$interfaceName,
								$description,
							))->identifier('class.implementsDeprecatedInterface')->build();
						}
					}
				}
			} catch (ClassNotFoundException $e) {
				// Other rules will notify if the interface is not found
			}
		}

		return $errors;
	}

}
