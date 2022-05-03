<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/code-unit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\SebastianBergmann\CodeUnit;

/**
 * @psalm-immutable
 */
final class TraitMethodUnit extends CodeUnit
{
    /**
     * @psalm-assert-if-true TraitMethodUnit $this
     */
    public function isTraitMethod() : bool
    {
        return \true;
    }
}