<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Doctrine\Instantiator;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Doctrine\Instantiator\Exception\ExceptionInterface;
/**
 * Instantiator provides utility methods to build objects without invoking their constructors
 */
interface InstantiatorInterface
{
    /**
     * @param string $className
     *
     * @return object
     *
     * @throws ExceptionInterface
     *
     * @template T of object
     * @phpstan-param class-string<T> $className
     */
    public function instantiate($className);
}
