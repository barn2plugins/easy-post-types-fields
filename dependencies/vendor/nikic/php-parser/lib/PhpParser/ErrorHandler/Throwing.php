<?php

declare (strict_types=1);
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\ErrorHandler;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Error;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\ErrorHandler;
/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
class Throwing implements ErrorHandler
{
    public function handleError(Error $error)
    {
        throw $error;
    }
}
