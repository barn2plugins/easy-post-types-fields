<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\DeepCopy\Matcher\Doctrine;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\DeepCopy\Matcher\Matcher;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Doctrine\Common\Persistence\Proxy;
/**
 * @final
 */
class DoctrineProxyMatcher implements Matcher
{
    /**
     * Matches a Doctrine Proxy class.
     *
     * {@inheritdoc}
     */
    public function matches($object, $property)
    {
        return $object instanceof Proxy;
    }
}
