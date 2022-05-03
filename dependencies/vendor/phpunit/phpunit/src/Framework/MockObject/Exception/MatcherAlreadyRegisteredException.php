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

use function sprintf;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class MatcherAlreadyRegisteredException extends \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\Exception implements Exception
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Matcher with id <%s> is already registered', $id));
    }
}
