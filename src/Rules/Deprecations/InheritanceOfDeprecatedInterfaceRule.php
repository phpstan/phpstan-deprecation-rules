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

	/** @var ReflectionProvider */
	private $reflectionProvider;

	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;
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

		if ($interface->isDeprecated()) {
			return [];
		}

		$errors = [];

		foreach ($node->extends as $parentInterfaceName) {
			$parentInterfaceName = (string) $parentInterfaceName;

			try {
				$parentInterface = $this->reflectionProvider->getClass($parentInterfaceName);

				if (!$parentInterface->isDeprecated()) {
					continue;
				}

				$description = $parentInterface->getDeprecatedDescription();
				if ($description === null) {
					$errors[] = RuleErrorBuilder::message(sprintf(
						'Interface %s extends deprecated interface %s.',
						$interfaceName,
						$parentInterfaceName
					))->identifier('interface.extendsDeprecatedInterface')->build();
				} else {
					$errors[] = RuleErrorBuilder::message(sprintf(
						"Interface %s extends deprecated interface %s:\n%s",
						$interfaceName,
						$parentInterfaceName,
						$description
					))->identifier('interface.extendsDeprecatedInterface')->build();
				}
			} catch (ClassNotFoundException $e) {
				// Other rules will notify if the interface is not found
			}
		}

		return $errors;
	}

}
