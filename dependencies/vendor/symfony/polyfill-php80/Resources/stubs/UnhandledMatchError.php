<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies;

if (\PHP_VERSION_ID < 80000) {
    class UnhandledMatchError extends \Error
    {
    }
}
