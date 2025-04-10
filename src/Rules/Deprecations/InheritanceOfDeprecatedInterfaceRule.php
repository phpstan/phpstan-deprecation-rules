<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PhpParser\Node;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function sprintf;

/**
 * @implements Rule<Interface_>
 */
class InheritanceOfDeprecatedInterfaceRule implements Rule
{

	private ReflectionProvider $reflectionProvider;

	private DeprecationHelper $deprecationHelper;

	public function __construct(ReflectionProvider $reflectionProvider, DeprecationHelper $deprecationHelper)
	{
		$this->reflectionProvider = $reflectionProvider;
		$this->deprecationHelper = $deprecationHelper;
	}

	public function getNodeType(): string
	{
		return Interface_::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		$interfaceName = isset($node->namespacedName)
			? (string) $node->namespacedName
			: (string) $node->name;

		try {
			$interface = $this->reflectionProvider->getClass($interfaceName);
		} catch (ClassNotFoundException $e) {
			return [];
		}

		$interfaceDeprecation = $this->deprecationHelper->getDeprecation($interface);
		if ($interfaceDeprecation !== null) {
			return [];
		}

		$errors = [];

		foreach ($node->extends as $parentInterfaceName) {
			$parentInterfaceName = (string) $parentInterfaceName;

			try {
				$parentInterface = $this->reflectionProvider->getClass($parentInterfaceName);

				$parentDeprecation = $this->deprecationHelper->getDeprecation($parentInterface);
				if ($parentDeprecation === null) {
					continue;
				}

				$description = $parentDeprecation->getDescription();
				if ($description === null) {
					$errors[] = RuleErrorBuilder::message(sprintf(
						'Interface %s extends deprecated interface %s.',
						$interfaceName,
						$parentInterfaceName,
					))->identifier('interface.extendsDeprecatedInterface')->build();
				} else {
					$errors[] = RuleErrorBuilder::message(sprintf(
						"Interface %s extends deprecated interface %s:\n%s",
						$interfaceName,
						$parentInterfaceName,
						$description,
					))->identifier('interface.extendsDeprecatedInterface')->build();
				}
			} catch (ClassNotFoundException $e) {
				// Other rules will notify if the interface is not found
			}
		}

		return $errors;
	}

}
