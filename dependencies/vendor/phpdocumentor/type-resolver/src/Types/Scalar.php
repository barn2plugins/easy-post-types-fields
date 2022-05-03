<?php

declare (strict_types=1);
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpDocumentor\Reflection\Types;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpDocumentor\Reflection\Type;
/**
 * Value Object representing the 'scalar' pseudo-type, which is either a string, integer, float or boolean.
 *
 * @psalm-immutable
 */
final class Scalar implements Type
{
    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString() : string
    {
        return 'scalar';
    }
}