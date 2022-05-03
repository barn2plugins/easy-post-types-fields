<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Builder\InvocationStubber;
/**
 * @method InvocationStubber method($constraint)
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
interface Stub
{
    public function __phpunit_getInvocationHandler() : InvocationHandler;
    public function __phpunit_hasMatchers() : bool;
    public function __phpunit_setReturnValueGeneration(bool $returnValueGeneration) : void;
}
