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
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\TextUI;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\TestListener;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\TestResult;
/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
interface ResultPrinter extends TestListener
{
    public function printResult(TestResult $result) : void;
    public function write(string $buffer) : void;
}
