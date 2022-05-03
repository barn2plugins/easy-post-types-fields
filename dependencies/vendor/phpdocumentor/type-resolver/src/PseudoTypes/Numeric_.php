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
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpDocumentor\Reflection\PseudoTypes;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpDocumentor\Reflection\PseudoType;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpDocumentor\Reflection\Type;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpDocumentor\Reflection\Types\AggregatedType;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpDocumentor\Reflection\Types\Compound;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpDocumentor\Reflection\Types\Float_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpDocumentor\Reflection\Types\Integer;
/**
 * Value Object representing the 'numeric' pseudo-type, which is either a numeric-string, integer or float.
 *
 * @psalm-immutable
 */
final class Numeric_ extends AggregatedType implements PseudoType
{
    public function __construct()
    {
        AggregatedType::__construct([new NumericString(), new Integer(), new Float_()], '|');
    }
    public function underlyingType() : Type
    {
        return new Compound([new NumericString(), new Integer(), new Float_()]);
    }
    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString() : string
    {
        return 'numeric';
    }
}
