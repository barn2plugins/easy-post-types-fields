<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\DeepCopy\Filter\Doctrine;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\DeepCopy\Filter\Filter;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\DeepCopy\Reflection\ReflectionHelper;
/**
 * @final
 */
class DoctrineCollectionFilter implements Filter
{
    /**
     * Copies the object property doctrine collection.
     *
     * {@inheritdoc}
     */
    public function apply($object, $property, $objectCopier)
    {
        $reflectionProperty = ReflectionHelper::getProperty($object, $property);
        $reflectionProperty->setAccessible(\true);
        $oldCollection = $reflectionProperty->getValue($object);
        $newCollection = $oldCollection->map(function ($item) use($objectCopier) {
            return $objectCopier($item);
        });
        $reflectionProperty->setValue($object, $newCollection);
    }
}
