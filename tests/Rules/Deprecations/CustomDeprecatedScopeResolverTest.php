<?php declare(strict_types = 1);

namespace PHPStan\Rules\Deprecations;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use function strpos;

/**
 * @extends RuleTestCase<CallToDeprecatedMethodRule>
 */
final class CustomDeprecatedScopeResolverTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		$customScopeResolver = new class implements DeprecatedScopeResolver
		{

			public function isScopeDeprecated(Scope $scope): bool
			{
				$function = $scope->getFunction();
				return $function !== null
					&& $function->getDocComment() !== null
					&& strpos($function->getDocComment(), '@group legacy') !== false;
			}

		};
		return new CallToDeprecatedMethodRule(
			$this->createReflectionProvider(),
			new DeprecatedScopeHelper([
				new DefaultDeprecatedScopeResolver(),
				$customScopeResolver,
			])
		);
	}

	public function testCustomScope(): void
	{
		require_once __DIR__ . '/data/call-to-deprecated-method-definition.php';
		$this->analyse(
			[__DIR__ . '/data/custom-deprecation-scope.php'],
			[
				[
					'Call to deprecated method deprecatedFoo() of class CheckDeprecatedMethodCall\Foo.',
					13,
				],
			]
		);
	}

}
