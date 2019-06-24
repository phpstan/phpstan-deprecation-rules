<?php

namespace TypeHintDeprecatedInFunctionSignature;

class Foo
{

    private $properties;

    public function setProperties(
        DeprecatedProperty $property,
        ?DeprecatedProperty $property2,
        $property3,
        Property $property4,
        string $property5
    ): DeprecatedProperty {
        $this->properties = [
            $property,
            $property2,
            $property3,
            $property4,
            $property5,
        ];

        return $property;
    }

}
