<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Doubler\CachedDoubler;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Doubler\Doubler;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Doubler\LazyDouble;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Doubler\ClassPatch;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Prophecy\ObjectProphecy;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Prophecy\RevealerInterface;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Prophecy\Revealer;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Call\CallCenter;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Util\StringUtil;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Exception\Prediction\PredictionException;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Exception\Prediction\AggregateException;
/**
 * Prophet creates prophecies.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Prophet
{
    private $doubler;
    private $revealer;
    private $util;
    /**
     * @var ObjectProphecy[]
     */
    private $prophecies = array();
    /**
     * Initializes Prophet.
     *
     * @param null|Doubler           $doubler
     * @param null|RevealerInterface $revealer
     * @param null|StringUtil        $util
     */
    public function __construct(Doubler $doubler = null, RevealerInterface $revealer = null, StringUtil $util = null)
    {
        if (null === $doubler) {
            $doubler = new CachedDoubler();
            $doubler->registerClassPatch(new ClassPatch\SplFileInfoPatch());
            $doubler->registerClassPatch(new ClassPatch\TraversablePatch());
            $doubler->registerClassPatch(new ClassPatch\ThrowablePatch());
            $doubler->registerClassPatch(new ClassPatch\DisableConstructorPatch());
            $doubler->registerClassPatch(new ClassPatch\ProphecySubjectPatch());
            $doubler->registerClassPatch(new ClassPatch\ReflectionClassNewInstancePatch());
            $doubler->registerClassPatch(new ClassPatch\HhvmExceptionPatch());
            $doubler->registerClassPatch(new ClassPatch\MagicCallPatch());
            $doubler->registerClassPatch(new ClassPatch\KeywordPatch());
        }
        $this->doubler = $doubler;
        $this->revealer = $revealer ?: new Revealer();
        $this->util = $util ?: new StringUtil();
    }
    /**
     * Creates new object prophecy.
     *
     * @param null|string $classOrInterface Class or interface name
     *
     * @return ObjectProphecy
     */
    public function prophesize($classOrInterface = null)
    {
        $this->prophecies[] = $prophecy = new ObjectProphecy(new LazyDouble($this->doubler), new CallCenter($this->util), $this->revealer);
        if ($classOrInterface && \class_exists($classOrInterface)) {
            return $prophecy->willExtend($classOrInterface);
        }
        if ($classOrInterface && \interface_exists($classOrInterface)) {
            return $prophecy->willImplement($classOrInterface);
        }
        return $prophecy;
    }
    /**
     * Returns all created object prophecies.
     *
     * @return ObjectProphecy[]
     */
    public function getProphecies()
    {
        return $this->prophecies;
    }
    /**
     * Returns Doubler instance assigned to this Prophet.
     *
     * @return Doubler
     */
    public function getDoubler()
    {
        return $this->doubler;
    }
    /**
     * Checks all predictions defined by prophecies of this Prophet.
     *
     * @throws Exception\Prediction\AggregateException If any prediction fails
     */
    public function checkPredictions()
    {
        $exception = new AggregateException("Some predictions failed:\n");
        foreach ($this->prophecies as $prophecy) {
            try {
                $prophecy->checkProphecyMethodsPredictions();
            } catch (PredictionException $e) {
                $exception->append($e);
            }
        }
        if (\count($exception->getExceptions())) {
            throw $exception;
        }
    }
}
