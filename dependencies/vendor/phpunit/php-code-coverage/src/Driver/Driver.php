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

use function sprintf;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\SebastianBergmann\CodeCoverage\BranchAndPathCoverageNotSupportedException;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\SebastianBergmann\CodeCoverage\DeadCodeDetectionNotSupportedException;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\SebastianBergmann\CodeCoverage\Filter;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\SebastianBergmann\CodeCoverage\NoCodeCoverageDriverAvailableException;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\SebastianBergmann\CodeCoverage\NoCodeCoverageDriverWithPathCoverageSupportAvailableException;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\SebastianBergmann\CodeCoverage\RawCodeCoverageData;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
abstract class Driver
{
    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const LINE_NOT_EXECUTABLE = -2;
    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const LINE_NOT_EXECUTED = -1;
    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const LINE_EXECUTED = 1;
    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const BRANCH_NOT_HIT = 0;
    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const BRANCH_HIT = 1;
    /**
     * @var bool
     */
    private $collectBranchAndPathCoverage = \false;
    /**
     * @var bool
     */
    private $detectDeadCode = \false;
    /**
     * @throws NoCodeCoverageDriverAvailableException
     * @throws PcovNotAvailableException
     * @throws PhpdbgNotAvailableException
     * @throws Xdebug2NotEnabledException
     * @throws Xdebug3NotEnabledException
     * @throws XdebugNotAvailableException
     *
     * @deprecated Use DriverSelector::forLineCoverage() instead
     */
    public static function forLineCoverage(Filter $filter) : self
    {
        return (new Selector())->forLineCoverage($filter);
    }
    /**
     * @throws NoCodeCoverageDriverWithPathCoverageSupportAvailableException
     * @throws Xdebug2NotEnabledException
     * @throws Xdebug3NotEnabledException
     * @throws XdebugNotAvailableException
     *
     * @deprecated Use DriverSelector::forLineAndPathCoverage() instead
     */
    public static function forLineAndPathCoverage(Filter $filter) : self
    {
        return (new Selector())->forLineAndPathCoverage($filter);
    }
    public function canCollectBranchAndPathCoverage() : bool
    {
        return \false;
    }
    public function collectsBranchAndPathCoverage() : bool
    {
        return $this->collectBranchAndPathCoverage;
    }
    /**
     * @throws BranchAndPathCoverageNotSupportedException
     */
    public function enableBranchAndPathCoverage() : void
    {
        if (!$this->canCollectBranchAndPathCoverage()) {
            throw new BranchAndPathCoverageNotSupportedException(sprintf('%s does not support branch and path coverage', $this->nameAndVersion()));
        }
        $this->collectBranchAndPathCoverage = \true;
    }
    public function disableBranchAndPathCoverage() : void
    {
        $this->collectBranchAndPathCoverage = \false;
    }
    public function canDetectDeadCode() : bool
    {
        return \false;
    }
    public function detectsDeadCode() : bool
    {
        return $this->detectDeadCode;
    }
    /**
     * @throws DeadCodeDetectionNotSupportedException
     */
    public function enableDeadCodeDetection() : void
    {
        if (!$this->canDetectDeadCode()) {
            throw new DeadCodeDetectionNotSupportedException(sprintf('%s does not support dead code detection', $this->nameAndVersion()));
        }
        $this->detectDeadCode = \true;
    }
    public function disableDeadCodeDetection() : void
    {
        $this->detectDeadCode = \false;
    }
    public abstract function nameAndVersion() : string;
    public abstract function start() : void;
    public abstract function stop() : RawCodeCoverageData;
}