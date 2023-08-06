<?php

namespace TypeHintDeprecatedInClassMethodSignature;

class Foo
{

    private $properties;

    /**
     * @param DeprecatedProperty $property6
     */
    public function setProperties(
        DeprecatedProperty $property,
        ?DeprecatedInterface $property2,
        $property3,
        VerboseDeprecatedProperty $property4,
        string $property5,
        $property6
    ): DeprecatedProperty {
        $this->properties = [
            $property,
            $property2,
            $property3,
            $property4,
            $property5,
            $property6
        ];

        return $property;
    }

}

class FooImplOverride implements IThinkYourStuck
{

    /**
     * @param Property $property
     */
    public function oops($property): void
    {
    }

}

class FooImplNoOverride implements IThinkYourStuck
{

    public function oops($property): void
    {
    }

}

/**
 * @deprecated
 */
class DeprecatedClass
{

    public function bar(DeprecatedProperty $property)
    {
    }

}

new class {
    private $property;

    public function __construct(DeprecatedProperty $property = null)
    {
        $this->property = $property;
    }

    public function getProperty(): ?DeprecatedProperty
    {
        return $this->property;
    }
};
