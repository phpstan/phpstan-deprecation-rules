<?php

namespace TypeHintDeprecatedInClassMethodSignature;

/**
 * @deprecated
 */
class DeprecatedProperty
{

}

/**
 * @deprecated I'll be back
 */
class VerboseDeprecatedProperty
{

}

/**
 * @deprecated
 */
interface DeprecatedInterface
{

}

class Property
{

}

interface IThinkYoureStuck
{

    /**
     * @param Property|DeprecatedProperty $property
     */
    public function oops($property): void;

}
