<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\DeepCopy\Filter\Doctrine;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\DeepCopy\Filter\Filter;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\DeepCopy\Reflection\ReflectionHelper;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Doctrine\Common\Collections\ArrayCollection;
/**
 * @final
 */
class DoctrineEmptyCollectionFilter implements Filter
{
    /**
     * Sets the object property to an empty doctrine collection.
     *
     * @param object   $object
     * @param string   $property
     * @param callable $objectCopier
     */
    public function apply($object, $property, $objectCopier)
    {
        $reflectionProperty = ReflectionHelper::getProperty($object, $property);
        $reflectionProperty->setAccessible(\true);
        $reflectionProperty->setValue($object, new ArrayCollection());
    }
}
