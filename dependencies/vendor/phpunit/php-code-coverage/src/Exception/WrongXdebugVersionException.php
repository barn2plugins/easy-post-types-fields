<?php

declare (strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\SebastianBergmann\CodeCoverage\Driver;

use RuntimeException;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\SebastianBergmann\CodeCoverage\Exception;
final class WrongXdebugVersionException extends RuntimeException implements Exception
{
}
