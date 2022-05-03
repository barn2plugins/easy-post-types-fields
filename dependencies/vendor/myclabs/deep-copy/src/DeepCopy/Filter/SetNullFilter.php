<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\DeepCopy\Filter;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\DeepCopy\Reflection\ReflectionHelper;
/**
 * @final
 */
class SetNullFilter implements Filter
{
    /**
     * Sets the object property to null.
     *
     * {@inheritdoc}
     */
    public function apply($object, $property, $objectCopier)
    {
        $reflectionProperty = ReflectionHelper::getProperty($object, $property);
        $reflectionProperty->setAccessible(\true);
        $reflectionProperty->setValue($object, null);
    }
}
