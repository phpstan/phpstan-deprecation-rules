<?php

namespace TypeHintDeprecatedInFunctionSignature;

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

class FooImplOverride implements IThinkYourStucked
{

    /**
     * @param Property $property
     */
    public function oops($property): void
    {
    }

}

class FooImplNoOverride implements IThinkYourStucked
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
