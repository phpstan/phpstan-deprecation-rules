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
        ?DeprecatedProperty $property2,
        $property3,
        Property $property4,
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
