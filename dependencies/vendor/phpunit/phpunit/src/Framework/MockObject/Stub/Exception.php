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
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Stub;

use function sprintf;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Invocation;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\SebastianBergmann\Exporter\Exporter;
use Throwable;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Exception implements Stub
{
    private $exception;
    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }
    /**
     * @throws Throwable
     */
    public function invoke(Invocation $invocation) : void
    {
        throw $this->exception;
    }
    public function toString() : string
    {
        $exporter = new Exporter();
        return sprintf('raise user-specified exception %s', $exporter->export($this->exception));
    }
}