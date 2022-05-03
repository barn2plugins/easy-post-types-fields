<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\DeepCopy;

use function function_exists;
if (\false === function_exists('Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\DeepCopy\\deep_copy')) {
    /**
     * Deep copies the given value.
     *
     * @param mixed $value
     * @param bool  $useCloneMethod
     *
     * @return mixed
     */
    function deep_copy($value, $useCloneMethod = \false)
    {
        return (new DeepCopy($useCloneMethod))->copy($value);
    }
}
