<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework;

/**
 * Compatibility class to work with PHPUnit 7
 *
 * @internal
 */
abstract class BaseTestListener implements TestListener
{
    use TestListenerDefaultImplementation;
}
